<?php
/*
Plugin Name: Task Rocket Comment Notify
Plugin URI: https://taskrocket.info/comment-notify/
Description: Comment Notify for Task Rocket (requires at least Task Rocket 4.0)
Version: 1.4.2
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-comment-notify
Domain Path: /languages/
*/

// Look for translation file
function load_comment_notify_textdomain() {
    load_plugin_textdomain( 'taskrocket-comment-notify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_comment_notify_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/comment-notify/info.json',
    __FILE__,
    'task-rocket-comment-notify'
);

class TaskRocketCommentNotify {

	const name = 'Task Rocket Comment Notify';
	const slug = 'task_rocket_comment_notify';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_comment_notify' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_comment_notify' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_comment_notify() {
	}

}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_comment_notify_plugin_version = $plugin_data['Version'];