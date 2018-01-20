<?php
/*
Plugin Name: Task Rocket Gantt
Plugin URI: https://taskrocket.info/gantt/
Description: Gantt charts for Task Rocket (requires at least Task Rocket 4.0)
Version: 1.3.3
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-gantt
Domain Path: /languages/
*/

// Look for translation file
function load_gantt_textdomain() {
    load_plugin_textdomain( 'taskrocket-gantt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_gantt_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/gantt/info.json',
    __FILE__,
    'task-rocket-gantt'
);

class TaskRocketGantt {

	const name = 'Task Rocket Gantt';
	const slug = 'task_rocket_gantt';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_gantt' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_gantt' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_gantt() {
	}

}

// Add custom CSS front-end head
	add_action('wp_head', 'tr_gantt_css');
	function tr_gantt_css() {
        
	$tr_gantt_css = '
	<!--/ Task Rocket Gantt CSS /-->
	<link rel="stylesheet" href="' . plugins_url( '/css/tr-gantt.css', __FILE__ ) . '" type="text/css" media="all" />';
	echo $tr_gantt_css;
	print "\n";
}

// Add custom JS into front-end footer
	add_action('wp_footer', 'tr_gantt_js', 100);
	function tr_gantt_js() {
        
    $tr_gantt_js = '
    <!--/ Task Rocket Gantt JS /-->
    <script src="' . plugins_url( '/js/min/gantt.min.js', __FILE__ ) . '"></script>';
    echo $tr_gantt_js;
    print "\n";
}


// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_gantt_plugin_version = $plugin_data['Version'];