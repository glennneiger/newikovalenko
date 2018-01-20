<?php
/*
Plugin Name: Task Rocket Clients
Plugin URI: https://taskrocket.info/client-control/
Description: Dedicated clients functionality for Task Rocket (requires at least Task Rocket 3.0)
Version: 3.4.5
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-clients
Domain Path: /languages/
*/

// Look for translation file
function load_client_control_textdomain() {
    load_plugin_textdomain( 'taskrocket-clients', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_client_control_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/client-control/info.json',
    __FILE__,
    'task-rocket-clients'
);

class TaskRocketClients {

	const name = 'Task Rocket Clients';
	const slug = 'task_rocket_clients';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_clients' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_clients' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_clients() {
	}

}

// Add custom CSS into front-end head
	add_action('wp_head', 'tr_clients_css');
	function tr_clients_css() {
	$tr_clients_css = '
	<!--/ Task Rocket Clients CSS /-->
	<link rel="stylesheet" href="' . plugins_url( '/css/tr-clients.css', __FILE__ ) . '" type="text/css" media="all" />';
    if(current_user_can( 'client')) {
	    echo $tr_clients_css;
    }
	print "\n";
}


// Output this JS to footer, priority 100, if viewing as a client.
add_action('wp_footer', function() {
	if(current_user_can( 'client')) {
?>
	<script>
	$(document).ready(function(){
		$(".my-active-list").show();
	});
	</script>
	 <?php
	}
}, 100);

// Metabox for pages: allow clients to access pages.
// Usage: client_access_get_meta( 'client_access_let_clients_view_this_page' )
function client_access_get_meta( $value ) {
    global $post;

    $ca_field = get_post_meta( $post->ID, $value, true );
    if ( ! empty( $ca_field ) ) {
        return is_array( $ca_field ) ? stripslashes_deep( $ca_field ) : stripslashes( wp_kses_decode_entities( $ca_field ) );
    } else {
        return false;
    }
}

function client_access_add_meta_box() {
    add_meta_box(
        'client_access-client-access',
        __( 'Client Access', 'client_access' ),
        'client_access_html',
        'page',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'client_access_add_meta_box' );

function client_access_html( $post) {
    wp_nonce_field( '_client_access_nonce', 'client_access_nonce' ); ?>

    <p>

        <input type="checkbox" name="client_access_let_clients_view_this_page" id="client_access_let_clients_view_this_page" value="let-clients-view-this-page" <?php echo ( client_access_get_meta( 'client_access_let_clients_view_this_page' ) === 'let-clients-view-this-page' ) ? 'checked' : ''; ?>>
        <label for="client_access_let_clients_view_this_page"><?php _e( 'Let all clients view this page', 'client_access' ); ?></label>	</p><?php
}

function client_access_save( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['client_access_nonce'] ) || ! wp_verify_nonce( $_POST['client_access_nonce'], '_client_access_nonce' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['client_access_let_clients_view_this_page'] ) )
        update_post_meta( $post_id, 'client_access_let_clients_view_this_page', esc_attr( $_POST['client_access_let_clients_view_this_page'] ) );
    else
        update_post_meta( $post_id, 'client_access_let_clients_view_this_page', null );
}
add_action( 'save_post', 'client_access_save' );


// Add column to admin pages table
add_filter( 'manage_edit-page_columns', 'admin_page_header_columns', 10, 1);
add_action( 'manage_pages_custom_column', 'admin_page_data_row', 10, 2);

function admin_page_header_columns($columns) {
    
    if (!isset($columns['Client Access']))
        $columns['client_access'] = "Clients";
    return $columns;
}

function admin_page_data_row($column_name, $post_id) {
    
    switch($column_name) {
        case 'client_access':
        $client_access = get_post_meta( $post_id, 'client_access_let_clients_view_this_page', true );
        if ($client_access) echo "Yes";
            break;

        default:
        break;
    }
}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_client_plugin_version = $plugin_data['Version'];


// Show message upon plugin activation
register_activation_hook( __FILE__, 'clients_admin_notice_activation_hook' );
 
// Runs only when the plugin is activated
function clients_admin_notice_activation_hook() {
 
    /* Create transient data */
    set_transient( 'clients-admin-notice', true, 1000 );
}

/* Add admin notice */
add_action( 'admin_notices', 'clients_admin_notice' );
 
// Admin Notice on Activation
function clients_admin_notice(){
 
    /* Check transient, if available display notice */
    if( get_transient( 'clients-admin-notice' ) ){
        ?>
        <div class="updated notice is-dismissible">
            <?php $presentation_options_url = admin_url() . 'admin.php?page=task-rocket-settings'; ?>
            <p><?php printf( __( 'The <strong>Client Control</strong> plugin has settings that can be changed <a href="%s">here</a>. Look for the new tab called <strong>Client Control</strong>.', 'client_access' ), $presentation_options_url); ?></p>
        </div>
        <?php
        /* Delete transient, only display this notice once. */
        delete_transient( 'clients-admin-notice' );
    }
}