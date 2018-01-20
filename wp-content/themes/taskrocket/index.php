<?php
ob_start();
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
global $wpdb;
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
get_header();

// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: '.home_url().'/client');
	exit();
}
$options = get_option( 'taskrocket_settings' );

global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));

?>

<?php // If user is not a client
	if( !current_user_can('client')) { ?>

    <div class="content">
		<div class="container">

	    	<?php require($GLOBALS[ 'theme_includes' ] . 'messages-task-statuses.php'); ?>
			
	 		<h1><?php _e( "Dashboard", "taskrocket" ); ?></h1>
	
			<?php require_once($GLOBALS[ 'theme_includes' ] . 'activity-statement.php'); ?>	
			
			<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
				if ($options['show_gantt_dashboard'] == true) { ?>
				<div class="gantt"></div>
				<?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
			<?php } }?>
			
			<?php $recent_count = $userdata->number_recent_tasks;
			if($recent_count > 0) {
				require_once($GLOBALS[ 'theme_includes' ] . 'recent.php');
			} ?>	
			
			<?php if ( is_plugin_active( 'taskrocket-task-follow/task-rocket-follow.php') ) { 
				require(ABSPATH . 'wp-content/plugins/taskrocket-task-follow/my-follows.php'); ?>
			<?php
				}
			?>
			
			<?php $dash_pages = $userdata->number_recent_pages;
		    if($dash_pages > 0) {
				require_once($GLOBALS[ 'theme_includes' ] . 'dash-pages.php'); 
			} ?>
			
			<?php 
			$show_my_projects = $userdata->my_projects_dash;
		    if($show_my_projects == "yes") {
				require_once($GLOBALS[ 'theme_includes' ] . 'projects-involved-in.php');
			} ?>
			
			<?php 
			$show_tips = $userdata->show_tips;
		    if($show_tips == "yes") {
				require_once($GLOBALS[ 'theme_includes' ] . 'tips.php');
			} ?>

	    </div>
	</div>

<?php // If user is not a client
} ?>

<?php get_footer(); ?>
