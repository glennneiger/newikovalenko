<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprCheckoutCtrl extends MeprBaseCtrl {
  public function load_hooks() {
    add_action('wp_enqueue_scripts', array($this,'enqueue_scripts'));
    add_filter('mepr_signup_form_payment_description', array($this, 'maybe_render_payment_form'), 10, 3);
    add_shortcode('mepr-ga-ecommerce-tracking', array($this, 'ga_ecommerce_tracking'));
  }

  public function ga_ecommerce_tracking($atts, $content="") {
    if(isset($atts['account']) && isset($_GET['trans_num']) && !empty($_GET['trans_num'])) {
      $ga_account = $atts['account'];

      $txn = MeprTransaction::get_one_by_trans_num($_GET['trans_num']);

      if(!empty($txn) && is_object($txn) && $txn->id != 0) {
        $txn = new MeprTransaction($txn->id);

        // We'll use the subscription object instead if this is a subscription checkout
        if(($txn->txn_type == MeprTransaction::$subscription_confirmation_str ||
            $txn->txn_type == 'sub_account') &&
           ($txn->status == MeprTransaction::$confirmed_str ||
            $txn->status == MeprTransaction::$complete_str) &&
           ($sub = $txn->subscription())) {
          $txn = $sub;
        }

        $user = new MeprUser($txn->user_id);
        $product = new MeprProduct($txn->product_id);

        ob_start();
        MeprView::render("/checkout/ga_ecommerce_tracking", get_defined_vars());
        $return_value = ob_get_clean();

        return $return_value;
      }
    }

    return '';
  }

  /** Enqueue gateway specific js/css if required */
  public function enqueue_scripts() {
    global $post;
    $mepr_options = MeprOptions::fetch();

    if(MeprProduct::is_product_page($post)) {
      if(((isset($_REQUEST['action']) &&
           $_REQUEST['action'] === 'checkout' &&
           ( (isset($_REQUEST['mepr_transaction_id']) &&
             ($txn = new MeprTransaction($_REQUEST['mepr_transaction_id']))) ||
             (isset($_REQUEST['txn']) &&
             ($txn = new MeprTransaction($_REQUEST['txn'])))
           ) &&
           $txn->id > 0 &&
           ($pm = $txn->payment_method())) ||
          (MeprUtils::is_user_logged_in() &&
           isset($_REQUEST['action']) &&
           $_REQUEST['action'] === 'update' &&
           isset($_REQUEST['sub']) &&
           ($sub = new MeprSubscription($_REQUEST['sub'])) &&
           $sub->id > 0 &&
           ($pm = $sub->payment_method()))) &&
         ($pm instanceof MeprBaseRealGateway)) {
        wp_register_script('mepr-checkout-js', MEPR_JS_URL . '/checkout.js', array('jquery', 'jquery.payment'), MEPR_VERSION);
        $pm->enqueue_payment_form_scripts();
      }
    }
  }

  /**
  * Renders the payment form if SPC is enabled and supported by the payment method
  * Called from: mepr_signup_form_payment_description filter
  * Returns: description includding form for SPC if enabled
  */
  public function maybe_render_payment_form($description, $payment_method, $first) {
    $mepr_options = MeprOptions::fetch();
    if($mepr_options->enable_spc && $payment_method::$has_spc_form) {
      // TODO: Maybe we queue these up from wp_enqueue_scripts?
      wp_register_script('mepr-checkout-js', MEPR_JS_URL . '/checkout.js', array('jquery', 'jquery.payment'), MEPR_VERSION);
      wp_enqueue_script('mepr-checkout-js');
      $description = MeprView::get_string("/checkout/payment_form", get_defined_vars());
    }
    return $description;
  }

  public function display_signup_form($product) {
    $mepr_options = MeprOptions::fetch();
    $mepr_blogurl = home_url();
    $mepr_coupon_code = '';

    extract($_REQUEST);

    //See if Coupon was passed via GET
    if(isset($_GET['coupon']) && !empty($_GET['coupon'])) {
      if(MeprCoupon::is_valid_coupon_code($_GET['coupon'], $product->ID)) {
        $mepr_coupon_code = $_GET['coupon'];
      }
    }

    if(MeprUtils::is_user_logged_in()) {
      $mepr_current_user = MeprUtils::get_currentuserinfo();
    }

    $first_name_value = '';
    if(isset($user_first_name)) {
      $first_name_value = esc_attr(stripslashes($user_first_name));
    }
    elseif(MeprUtils::is_user_logged_in()) {
      $first_name_value = (string)$mepr_current_user->first_name;
    }

    $last_name_value = '';
    if(isset($user_last_name)) {
      $last_name_value = esc_attr(stripslashes($user_last_name));
    }
    elseif(MeprUtils::is_user_logged_in()) {
      $last_name_value = (string)$mepr_current_user->last_name;
    }

    if(isset($errors) and !empty($errors)) {
      MeprView::render("/shared/errors", get_defined_vars());
    }

    MeprView::render('/checkout/form', get_defined_vars());
  }

  /** Gets called on the 'init' hook ... used for processing aspects of the signup
    * form before the logic progresses on to 'the_content' ...
    */
  public function process_signup_form() {
    $mepr_options = MeprOptions::fetch();

    // Validate the form post
    $errors = MeprHooks::apply_filters('mepr-validate-signup', MeprUser::validate_signup($_POST, array()));
    if(!empty($errors)) {
      $_POST['errors'] = $errors; //Deprecated?
      $_REQUEST['errors'] = $errors;
      return;
    }

    // Check if the user is logged in already
    $is_existing_user = MeprUtils::is_user_logged_in();

    if($is_existing_user) {
      $usr = MeprUtils::get_currentuserinfo();
    }
    else { // If new user we've got to create them and sign them in
      $usr = new MeprUser();
      $usr->user_login = ($mepr_options->username_is_email)?sanitize_email($_POST['user_email']):sanitize_user($_POST['user_login']);
      $usr->user_email = sanitize_email($_POST['user_email']);

      //Have to use rec here because we unset user_pass on __construct
      $usr->set_password($_POST['mepr_user_password']);

      try {
        $usr->store();

        // Log the new user in
        wp_signon(
          array(
            'user_login'    => $usr->user_login,
            'user_password' => $_POST['mepr_user_password']
          ),
          MeprUtils::is_ssl() //May help with the users getting logged out when going between http and https
        );

        MeprEvent::record('login', $usr); //Record the first login here
      }
      catch(MeprCreateException $e) {
        $_POST['errors'] = array(__( 'The user was unable to be saved.', 'memberpress'));  //Deprecated?
        $_REQUEST['errors'] = array(__( 'The user was unable to be saved.', 'memberpress'));
        return;
      }
    }

    // Create a new transaction and set our new membership details
    $txn = new MeprTransaction();
    $txn->user_id = $usr->ID;

    // Get the membership in place
    $txn->product_id = sanitize_text_field($_POST['mepr_product_id']);
    $prd = $txn->product();

    // If we're showing the fields on logged in purchases, let's save them here
    if(!$is_existing_user || ($is_existing_user && $mepr_options->show_fields_logged_in_purchases)) {
      MeprUsersCtrl::save_extra_profile_fields($usr->ID, true, $prd, true);
      $usr = new MeprUser($usr->ID); //Re-load the user object with the metadata now (helps with first name last name missing from hooks below)
    }

    // Set default price, adjust it later if coupon applies
    $price = $prd->adjusted_price();

    // Default coupon object
    $cpn = (object)array('ID' => 0, 'post_title' => null);

    // Adjust membership price from the coupon code
    if(isset($_POST['mepr_coupon_code']) && !empty($_POST['mepr_coupon_code'])) {
      // Coupon object has to be loaded here or else txn create will record a 0 for coupon_id
      $cpn = MeprCoupon::get_one_from_code(sanitize_text_field($_POST['mepr_coupon_code']));

      if(($cpn !== false) || ($cpn instanceof MeprCoupon)) {
        $price = $prd->adjusted_price($cpn->post_title);
      }
    }

    $txn->set_subtotal($price);

    // Set the coupon id of the transaction
    $txn->coupon_id = $cpn->ID;

    // Figure out the Payment Method
    if(isset($_POST['mepr_payment_method']) && !empty($_POST['mepr_payment_method'])) {
      $txn->gateway = sanitize_text_field($_POST['mepr_payment_method']);
    }
    else {
      $txn->gateway = MeprTransaction::$free_gateway_str;
    }

    // Let's checkout now
    if($txn->gateway === MeprTransaction::$free_gateway_str) {
      $signup_type = 'free';
    }
    elseif(($pm = $txn->payment_method()) && ($pm instanceof MeprBaseRealGateway)) {
      // Create a new subscription
      if($prd->is_one_time_payment()) {
        $signup_type = 'non-recurring';
      }
      else {
        $signup_type = 'recurring';

        $sub = new MeprSubscription();
        $sub->user_id = $usr->ID;
        $sub->gateway = $pm->id;
        $sub->load_product_vars($prd, $cpn->post_title, true);
        $sub->maybe_prorate(); // sub to sub
        $sub->store();

        $txn->subscription_id = $sub->id;
      }
    }
    else {
      $_POST['errors'] = array(__('Invalid payment method', 'memberpress'));
      return;
    }

    $txn->store();

    if(empty($txn->id)) {
      // Don't want any loose ends here if the $txn didn't save for some reason
      if($signup_type==='recurring' && ($sub instanceof MeprSubscription)) {
        $sub->destroy();
      }
      $_POST['errors'] = array(__('Sorry, we were unable to create a transaction.', 'memberpress'));
      return;
    }

    try {
      if(('free' !== $signup_type) && ($pm instanceof MeprBaseRealGateway)) {
        $pm->process_signup_form($txn);
      }

      // DEPRECATED: These 2 actions here for backwards compatibility ... use mepr-signup instead
      MeprHooks::do_action('mepr-track-signup',   $txn->amount, $usr, $prd->ID, $txn->id);
      MeprHooks::do_action('mepr-process-signup', $txn->amount, $usr, $prd->ID, $txn->id);

      // Signup type can be 'free', 'non-recurring' or 'recurring'
      MeprHooks::do_action("mepr-{$signup_type}-signup", $txn);
      MeprHooks::do_action('mepr-signup', $txn);

      MeprUtils::wp_redirect(MeprHooks::apply_filters('mepr-signup-checkout-url', $txn->checkout_url(), $txn));
    }
    catch(Exception $e) {
      $_POST['errors'] = array($e->getMessage());
    }
  }

  public function display_payment_page() {
    $mepr_options = MeprOptions::fetch();

    $txn_id = $_REQUEST['txn'];
    $txn = new MeprTransaction($txn_id);

    if($txn->gateway === MeprTransaction::$free_gateway_str || $txn->amount <= 0.00) {
      MeprTransaction::create_free_transaction($txn);
    }
    else if(($pm = $mepr_options->payment_method($txn->gateway)) && $pm instanceof MeprBaseRealGateway) {
      $pm->display_payment_page($txn);
    }

    // Artificially set the payment method params so we can use them downstream
    // when display_payment_form is called in the 'the_content' action.
    $_REQUEST['payment_method_params'] = array(
      'method'         => $txn->gateway,
      'amount'         => $txn->amount,
      'user'           => $txn->user(),
      'product_id'     => $txn->product_id,
      'transaction_id' => $txn->id
    );
  }

  // Called in the 'the_content' hook ... used to display a signup form
  public function display_payment_form() {
    $mepr_options = MeprOptions::fetch();

    if(isset($_REQUEST['payment_method_params'])) {
      extract($_REQUEST['payment_method_params']);

      if(isset($_POST['errors']) && !empty($_POST['errors'])) {
        $errors = $_POST['errors'];
        MeprView::render('/shared/errors', get_defined_vars());
      }

      if(($pm = $mepr_options->payment_method($method)) &&
         ($pm instanceof MeprBaseRealGateway)) {
        $pm->display_payment_form($amount, $user, $product_id, $transaction_id);
      }
    }
  }

  public function process_payment_form() {
    if(isset($_POST['mepr_process_payment_form']) && isset($_POST['mepr_transaction_id']) && is_numeric($_POST['mepr_transaction_id'])) {
      $txn = new MeprTransaction($_POST['mepr_transaction_id']);

      if($txn->rec != false) {
        $mepr_options = MeprOptions::fetch();
        if(($pm = $mepr_options->payment_method($txn->gateway)) && $pm instanceof MeprBaseRealGateway) {
          $errors = $pm->validate_payment_form(array());

          if(empty($errors)) {
            // process_payment_form either returns true
            // for success or an array of $errors on failure
            try {
              $pm->process_payment_form($txn);
            }
            catch(Exception $e) {
              MeprHooks::do_action('mepr_payment_failure', $txn);
              $errors = array($e->getMessage());
            }
          }

          if(empty($errors)) {
            //Reload the txn now that it should have a proper trans_num set
            $txn = new MeprTransaction($txn->id);
            $product = new MeprProduct($txn->product_id);
            $sanitized_title = sanitize_title($product->post_title);
            MeprUtils::wp_redirect($mepr_options->thankyou_page_url("membership={$sanitized_title}&trans_num={$txn->trans_num}"));
          }
          else {
            // Artificially set the payment method params so we can use them downstream
            // when display_payment_form is called in the 'the_content' action.
            $_REQUEST['payment_method_params'] = array(
              'method' => $pm->id,
              'amount' => $txn->amount,
              'user' => new MeprUser($txn->user_id),
              'product_id' => $txn->product_id,
              'transaction_id' => $txn->id
            );
            $_REQUEST['mepr_payment_method'] = $pm->id;
            $_POST['errors'] = $errors;
            return;
          }
        }
      }
    }

    $_POST['errors'] = array(__('Sorry, an unknown error occurred.', 'memberpress'));
  }
}
