<?php
/*
Template Name: Report
*/
// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: ' . home_url() . '/client');
	exit();
}
require_once("wp-admin/includes/taxonomy.php");

get_header();
global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));
$options = get_option( 'taskrocket_settings' );

// Conditionally show completed/un-tasked projects when option is enabled.
if($options['show_complete_projects_report'] == true) {
	$hide_empty = 0;
} else {
	$hide_empty = 1;
}

?>

		<?php // If the current user is an administrator...
		if($options['show_report_to_all'] == true || current_user_can( 'manage_options' )) { ?>

			<!--/ Start Report Content /-->
			<div class="content report">
			    <div class="container">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		            <h1><?php the_title(); ?></h1>
		            <?php endwhile; endif; ?>

					<!--/ Start Report Container /-->
					<div class="stats-container">

						<?php 
						wp_count_terms( 'category');
						$project_count = wp_count_terms( 'category', array( 'hide_empty' => TRUE));
						
						// All tasks
						// This is every task that is incomplete or in progress.
						$all_tasks_args = array(
							'posts_per_page' 	=> -1,
							'post_type' 		=> 'post',
							'post_status'		=> 'publish',
							'meta_key'          => 'tr_status',
							'meta_value'        => array('incomplete', 'inprogress', 'onhold')
						);
						$all_tasks_posts = new WP_Query($all_tasks_args);
						$all_tasks = $all_tasks_posts->post_count;
						
						// On hold tasks
						$onhold_tasks_project_args = array(
							'posts_per_page' 	=> -1,
							'post_type' 		=> 'post',
							'post_status'		=> 'publish',
							'cat'			 	=> $cat_ID,
							'meta_key'  		=> 'tr_status',
							'meta_value' 		=> array('onhold')
						);
						$onhold_tasks_posts_project = new WP_Query($onhold_tasks_project_args);
						$onhold_tasks_project = $onhold_tasks_posts_project->post_count;
						
						// On hold tasks
						$inprogress_tasks_project_args = array(
							'posts_per_page' 	=> -1,
							'post_type' 		=> 'post',
							'post_status'		=> 'publish',
							'cat'			 	=> $cat_ID,
							'meta_key'  		=> 'tr_status',
							'meta_value' 		=> array('inprogress')
						);
						$inprogress_tasks_posts_project = new WP_Query($inprogress_tasks_project_args);
						$inprogress_tasks_project = $inprogress_tasks_posts_project->post_count;

						?>

						<ul class="panel stats">

							<li>
								<p class="value"><?php echo $project_count ?></p>
								<p class="description">
									<strong><?php _e( "Active projects", "taskrocket" ); ?></strong>
								</p>
							</li>
							
					        <li>
					        	<p class="value"><?php echo $all_tasks; ?></p>
								<p class="description">
									<strong><?php _e( "Tasks outstanding", "taskrocket" ); ?></strong>
								</p>
					        </li>
							
							<li>
					        	<p class="value"><?php echo $onhold_tasks_project; ?></p>
								<p class="description">
									<strong><?php _e( "Tasks on hold", "taskrocket" ); ?></strong>
								</p>
					        </li>
							
							<li>
					        	<p class="value"><?php echo $inprogress_tasks_project; ?></p>
								<p class="description">
									<strong><?php _e( "Task", "taskrocket" ); ?><?php if($inprogress_tasks_project > 1) { echo "s"; } ?> <?php _e( "In progress", "taskrocket" ); ?></strong>
								</p>
					        </li>

						</ul>
					</div>
					<!--/ End Report Container /-->
					
					<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
						if ($options['show_gantt_report'] == true) { ?>
						<div class="gantt"></div>
						<?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
					<?php } }?>
					
					<?php require($GLOBALS[ 'theme_includes' ] . 'report-all.php'); ?>

		        </div>
			</div>
			<!--/ End Report Content /-->

		<?php } ?>

<?php get_footer(); ?>
