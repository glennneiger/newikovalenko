<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpdtMembersApi extends MpdtBaseApi {

  protected function after_create($args, $request, $response) {
    $member_data = (object)$response->get_data();

    if(isset($args['send_password_email']) && !empty($args['send_welcome_email'])) {
      MeprUtils::include_pluggables('wp_new_user_notification');
      wp_new_user_notification($member_data->id, null, 'user');
    }

    if(isset($args['transaction']) && is_array($args['transaction'])) {
      $args['transaction']['member'] = $member_data->id; // hard code current member
      $transaction_request = new WP_REST_Request('POST');
      $transaction_request->set_body_params($args['transaction']);
      $transaction_api = new MpdtTransactionsApi();
      $transaction_response = $transaction_api->create_item($transaction_request);

      if(!is_wp_error($transaction_response) && isset($args['send_welcome_email']) && !empty($args['send_welcome_email'])) {
        $transaction_data = (object)$transaction_response->get_data();
        $transaction = new MeprTransaction($transaction_data->id);

        // Send welcome email
        MeprUtils::send_signup_notices($transaction, true, false);
      }

      // Refresh member object
      $get_req = new WP_REST_Request('GET');
      $get_req->set_url_params(array('id'=>$member_data->id));
      $data = $this->get_item($get_req);

      $response = rest_ensure_response($data);
    }

    return $response;
  }
}

