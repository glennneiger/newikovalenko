<?php
/*
Plugin Name: Task Rocket Kanban
Plugin URI: https://taskrocket.info/kanban/
Description: Kanban board overview for Task Rocket projects
Version: 1.7.4
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-kanban
Domain Path: /languages/
*/

// Look for translation file
function load_kanban_textdomain() {
    load_plugin_textdomain( 'taskrocket-kanban', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_kanban_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/kanban/info.json',
    __FILE__,
    'task-rocket-kanban'
);

class TaskRocketKanban {

	const name = 'Task Rocket Kanban';
	const slug = 'task_rocket_kanban';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_kanban' ) );

		//H ook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_kanban' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_kanban() {
	}

}

// Add custom CSS into front-end head
	add_action('wp_head', 'tr_kanban_css');
	function tr_kanban_css() {
	$tr_kanban_css = '
	<!--/ Task Rocket Kanban CSS /-->
	<link rel="stylesheet" href="' . plugins_url( '/css/tr-kanban.css', __FILE__ ) . '" type="text/css" media="all" />';
	echo $tr_kanban_css;
	print "\n";
}

// Output into footer
	add_action('wp_footer', 'tr_kanban_js', 1000);
	function tr_kanban_js() {
    
    $cat_id = get_query_var('cat');
    $thecat = get_category ($cat_id);
        
    $tr_kanban_js = '
    <div class="kanban-board-container">
        <div class="kanban-board">
        </div>
    </div>
    <!--/ Task Rocket Kanban JS /-->
    <script>
    $(document).ready(function() {
        $(".view-kanban").click(function() {
            $(".kanban-board-container").fadeIn();
            $(".kanban-board").load("' . plugin_dir_url( __FILE__ ) . 'kanban-board.php?kanbanID=' . $cat_id . '");
        });
        $("body").on("click",".close-kanban",function() {
            $(".kanban-board-container").fadeOut();
            $(".view-kanban").removeClass("active");
        });
        $("body").on("click",".reload-kanban",function() {
            $(".item-container").fadeOut();
            $(".kanban-board").load("' . plugin_dir_url( __FILE__ ) . 'kanban-board.php?kanbanID=' . $cat_id . '");
        });
    });
    </script>';
    echo $tr_kanban_js;
    print "\n";
}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_kanban_plugin_version = $plugin_data['Version'];