<?php 
/*
Plugin Name: Task Rocket Task Follow
Plugin URI: https://taskrocket.info/task-follows/
Description: Follow any task (requires at least Task Rocket 4.5)
Version: 1.2.4
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-follows
Domain Path: /languages/
*/
 
// Look for translation file
function load_follows_textdomain() {
    load_plugin_textdomain( 'taskrocket-follows', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_follows_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/task-follow/info.json',
    __FILE__,
    'taskrocket-task-follow'
);

class TaskRocketFollows {

	const name = 'Task Rocket Task Follows';
	const slug = 'task_rocket_follows';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_follows' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_follows' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_follows() {
	}

} 
 
// Create DB Table
global $tr_follows_db_version;
$tr_follows_db_version = '1.0';

function tr_follows_install() {
	global $wpdb;
	global $tr_follows_db_version;

	$table_name = $wpdb->prefix . 'tr_follows';
    
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    	
    	$charset_collate = $wpdb->get_charset_collate();

    	$sql = "CREATE TABLE $table_name (
    		id mediumint(9) NOT NULL AUTO_INCREMENT,
    		user_id tinytext NOT NULL,
    		task_id tinytext NOT NULL,
            slug varchar(200) DEFAULT '' NOT NULL,
    		PRIMARY KEY (id)
    	) $charset_collate;";

    	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    	dbDelta( $sql );

    	add_option( 'tr_follows_db_version', $tr_follows_db_version );
    }
}
register_activation_hook( __FILE__, 'tr_follows_install' );


// Enqueue CSS and JS
add_action( 'wp_enqueue_scripts', 'task_follow_enqueue_scripts' );
function task_follow_enqueue_scripts() {
    
	wp_enqueue_style( 'follow', plugins_url( '/css/task-follow.css', __FILE__ ) );
	wp_enqueue_script( 'follow', plugins_url( '/js/min/task-follow.min.js', __FILE__ ), array('jquery'), '1.0', true );
    
}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_follows_plugin_version = $plugin_data['Version'];