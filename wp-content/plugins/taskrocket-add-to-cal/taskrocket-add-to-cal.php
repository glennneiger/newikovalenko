<?php
/*
Plugin Name: Task Rocket Add To Cal
Plugin URI: https://taskrocket.info/add-to-cal/
Description: Dedicated Add To Calendar functionality for Task Rocket
Version: 3.4.3
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-add-to-cal
Domain Path: /languages/
*/

// Look for translation file
function load_add_to_cal_textdomain() {
    load_plugin_textdomain( 'taskrocket-add-to-cal', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_add_to_cal_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/add-to-cal/info.json',
    __FILE__,
    'task-rocket-add-to-cal'
);

class TaskRocketAddToCal {

	const name = 'Task Rocket Add To Cal';
	const slug = 'task_rocket_add_to_cal';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_add_to_cal' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_add_to_cal' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_add_to_cal() {
	}

}

// Add custom CSS into front-end head
	add_action('wp_head', 'tr_add_to_cal_css');
	function tr_add_to_cal_css() {
	$tr_add_to_cal_css = '
	<!--/ Task Rocket Add To Cal CSS /-->
	<link rel="stylesheet" href="' . plugins_url( '/css/tr-add-to-cal.css', __FILE__ ) . '" type="text/css" media="all" />';
	echo $tr_add_to_cal_css;
	print "\n";
}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_add_to_cal_plugin_version = $plugin_data['Version'];