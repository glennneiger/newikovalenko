<?php
/*
Template Name: My Tasks
*/
get_header();

global $userdata; wp_get_current_user();

global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));

//$user_ID = $userdata->id;

// If there is a query string...
if($_GET['user']) {
	$user_ID = $_GET['user']; // ...the $user_ID will be the query string value...
	$other_user_info = get_userdata($user_ID); // ...get the user info...
	$title = $other_user_info->first_name . " " . $other_user_info->last_name . "'s"; // ...and the title will be prefixed with the user's name.
} else { // Otherwise...
	$user_ID = $userdata->id; // ...the $user_ID is the current user's ID...
	$title = __( "My", "taskrocket" ); // ...and the title will be prefixed "My".
}

$options = get_option( 'taskrocket_settings' );

$user_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'author' 			=> $user_ID,
	'meta_key'          => 'tr_status',
	'meta_value'        => array('incomplete', 'inprogress', 'onhold')
);
$user_tasks_posts = new WP_Query($user_tasks_args);
$user_tasks_count = $user_tasks_posts->post_count;

// Pagination settings condition
$pagination_results = $options['my_tasks_pagination'];
if($pagination_results == "") {
	$pagination_results = 20;
} else {
	$pagination_results = $options['my_tasks_pagination'];
}
?>


        <div class="content my-tasks">
			<div class="container">

				<?php require($GLOBALS[ 'theme_includes' ] . 'messages-task-statuses.php'); ?>
				
	        	<h1><?php echo $title; ?> <?php _e( "Tasks", "taskrocket" ); ?> <span class="user-task-count"><?php echo $user_tasks_count; ?></span></h1>

				<ul>
				<li class="column-header">
					<em class="column-task"><?php _e( "Task", "taskrocket" ); ?></em>
					<em class="column-priority"><?php _e( "Priority", "taskrocket" ); ?></em>
					<em class="column-created"><?php _e( "Modified", "taskrocket" ); ?></em>
					<em class="column-due"><?php _e( "Due", "taskrocket" ); ?></em>
					<em class="column-time"><?php _e( "Time", "taskrocket" ); ?></em>
					<em class="column-cost"><?php _e( "Cost", "taskrocket" ); ?></em>
					<em class="column-status"><?php _e( "Status", "taskrocket" ); ?></em>
					<em class="column-action"><?php _e( "Action", "taskrocket" ); ?></em>
				</li>
				<?php
				$paged = get_query_var( 'paged' ) ?: ( get_query_var( 'page' ) ?: 1 );
				
				$wp_query = new WP_Query(array(
					'author' 			=> $user_ID,
					'posts_per_page' 	=> $pagination_results,
					'post_type'			=> 'post',
					'orderby'			=> 'modified',
					'order'  			=> 'DESC',
					'paged' 			=> $paged,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'meta_key'  		=> 'tr_status',
					'meta_value' 		=> array('incomplete', 'inprogress', 'onhold')
				)); 
				
				$i = 2;
				while ($wp_query->have_posts()) : $wp_query->the_post();
				$date_format = get_option('date_format');
				$olddateformat = get_post_meta($post->ID, 'duedate', TRUE);
				$newdateformat = new DateTime($olddateformat);
				$category = get_the_category();
				
				$related_ID    	      = get_post_meta($post->ID, 'related', TRUE);
				$relationship_details = get_post_meta($post->ID, 'elaboration', TRUE);
				$related_title 		  = get_the_title($related_ID);
				
				// Get the TASK status of the related task
			    $related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 
			    
			    // Get the POST status of the related task 
			    $related_post_status  = get_post_status( $related_ID );
				
				if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to") {
					$relation = __( "Relates to", "taskrocket" );
				} else if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with") { 
					$relation = __( "Has issues with", "taskrocket" );
				} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") { 
					$relation = __( "Is blocked by", "taskrocket" );
				} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to") { 
					$relation = __( "Is similar to", "taskrocket" );
				}
				
				?>
				
					<li class="panel row-<?php echo $i++ % 2; ?> task-priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>">
						<h2>
						<?php 
						// If an admin, owner of the task, and not a client
						if (current_user_can( 'manage_options' ) || $post->post_author == $current_user->ID && !current_user_can('client')) { ?>
							<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
						<?php } ?>
						
						<?php // if a client that can mark own tasks as complete, and you own the task
						if (current_user_can('client') && $options['clients_mark_own_tasks_complete'] == true && $post->post_author == $current_user->ID) { ?>
							<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
						<?php } ?>

						<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
							
							<?php if(current_user_can( 'manage_options' )) { ?>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<?php } else { ?>
								<?php _e( "Private Task", "taskrocket" ); ?>
							<?php } ?>
						
						<?php } else { ?>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php } ?>

						</h2>
						<span class="priority">
							<?php if(get_post_meta($post->ID, 'priority', TRUE) =="") { ?>
								<em class="priority-normal"></em>
							<?php } else { ?>
								<em class="priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>"></em>
							<?php } ?>
						</span>
						<span class="task-attribute date-added"><strong><?php _e( "Start date", "taskrocket" ); ?>:</strong><?php echo get_the_modified_date($date_format); ?></span>
						
							<?php if($olddateformat) { ?>
								<span class="task-attribute date-due"><strong><?php _e( "Due Date", "taskrocket" ); ?>:</strong> <?php echo $newdateformat->format($date_format); ?></span>
							<?php } else { ?>
								<span class="empty">-</span>
							<?php } ?>
						</span>
						<?php require($GLOBALS[ 'theme_includes' ] . 'time-spent.php'); ?>
						<?php require($GLOBALS[ 'theme_includes' ] . 'task-cost.php'); ?>
						
						<span class="status">
							<?php 
							// Pretty names for task status alt attributes
						    if(get_post_meta($post->ID, 'tr_status', TRUE) == "complete") {
						        $task_status = __( "Complete", "taskrocket" );
						    }
						    if(get_post_meta($post->ID, 'tr_status', TRUE) == "incomplete") {
						        $task_status = __( "Incomplete", "taskrocket" );
						    }
						    if(get_post_meta($post->ID, 'tr_status', TRUE) == "onhold") {
						        $task_status = __( "On hold", "taskrocket" );
						    }
						    if(get_post_meta($post->ID, 'tr_status', TRUE) == "inprogress") {
						        $task_status = __( "In progress", "taskrocket" );
						    }
							echo $task_status; ?>
						</span>
						
						<p><a href="<?php echo home_url() . "/" . get_option( 'category_base' ) . "/" . $category[0]->slug; ?>"><?php echo $category[0]->cat_name; ?></a></p>
						
						<?php if(get_post_meta($post->ID, 'relation', TRUE) && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
							<p class="relationship">
								<span class="show-relationship"><?php _e( "This task", "taskrocket" ); ?> <?php echo $relation; ?></span> 
								<a href="<?php echo get_the_permalink($related_ID); ?>"><?php echo $related_title; ?></a> <?php if($relationship_details) { echo '<em class="dots">...</em>'; } ?>
							</p>
							<?php if($relationship_details) { ?>
								<div class="relationship-details">
									<div>
										<p><strong>'<?php the_title(); ?>' <strong class="emphasis"><?php echo $relation; ?></strong> '<?php echo $related_title; ?>'</strong></p>
										<p><?php echo $relationship_details; ?></p>
										<a class="button-small"><?php _e( "OK", "taskrocket" ); ?></a>
									</div>
								</div>
							<?php } ?>
						<?php } ?>
						
						<?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by" && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') {  ?>
						<div class="blocked-alert">
							<div>
								<p>
									<strong>
										<?php printf( __('This task is currently blocked by <a href="%1$s">%2$s</a>.', 'taskrocket' ), $related_URL, $related_title ); ?>
									</strong>
								</p>
								
								<p>
									<?php _e('Change its status and unblock anyway?', 'taskrocket' ); ?>
								</p>
								
								<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=complete&type=task&user_ID=<?php echo $user_ID; ?>&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>&relation=none" class="button-small delete-yes delete-yes-complete"><?php _e( "Yes", "taskrocket" ); ?></a>
								
								<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=inprogress&type=task&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>&relation=none" class="button-small delete-yes delete-yes-inprogress"><?php _e( "Yes", "taskrocket" ); ?></a>
								
								<a class="button-small delete-no"><?php _e( "No", "taskrocket" ); ?></a>
								
							</div>
						</div>
						<?php } ?>
						
					</li>
				<?php endwhile; ?>
				</ul>
				
				<?php pagination($wp_query->max_num_pages);
				wp_reset_query();
				?>
				
	        </div>
			
			<script>
				jQuery('.inprogress').click(function () {
					jQuery( event.target ).closest('li').find('.blocked-alert').fadeIn();
					jQuery( event.target ).closest('li').find('.delete-yes-inprogress').fadeIn();
				});
				jQuery('.complete').click(function () {
					jQuery( event.target ).closest('li').find('.blocked-alert').fadeIn();
					jQuery( event.target ).closest('li').find('.delete-yes-complete').fadeIn();
				});
				jQuery('.delete-no').click(function () {
					jQuery( event.target ).closest('.blocked-alert').fadeOut();
					jQuery( event.target ).closest('li').find('.delete-yes').fadeOut();
				});
				jQuery('.show-relationship, .relationship em').click(function () {
			        jQuery( event.target ).closest('li').find('.relationship-details').fadeIn();
			    });
			    jQuery('.relationship-details .button-small').click(function () {
			        jQuery( event.target ).closest('.relationship-details').fadeOut();
			    });
			</script>
			
		</div>

<?php get_footer(); ?>