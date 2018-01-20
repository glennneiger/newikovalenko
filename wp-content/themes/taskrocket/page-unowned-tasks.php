<?php
/*
Template Name: My Tasks
*/
get_header();

global $userdata; wp_get_current_user();

global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));
$options = get_option( 'taskrocket_settings' );
$pagination_results = 20;
?>


        <div class="content unowned-tasks">
			<div class="container">

				<?php require($GLOBALS[ 'theme_includes' ] . 'messages-task-statuses.php'); ?>
				
	        	<h1><?php _e( "Unowned Tasks", "taskrocket" ); ?></h1>

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
					'posts_per_page' 	=> $pagination_results,
					'post_type'			=> 'post',
					'orderby'			=> 'modified',
					'order'  			=> 'DESC',
					'paged' 			=> $paged,
					'post_status'		=> 'publish',
					'meta_key'  		=> 'tr_status',
					'meta_value' 		=> array('incomplete', 'inprogress', 'onhold')
				)); 
				
				$i = 2;
				while ($wp_query->have_posts()) : $wp_query->the_post();
				$date_format = get_option('date_format');
				$olddateformat = get_post_meta($post->ID, 'duedate', TRUE);
				$newdateformat = new DateTime($olddateformat);
				
				$task_owner = get_post($post->ID);
				$task_id = $task_owner->post_author;
				$task_owner_id = get_the_author_meta('ID',$task_id);

				$category = get_the_category(); 
				if($task_owner_id == 0000000 || $task_owner_id == 0) {
				?>
				
					<li class="panel row-<?php echo $i++ % 2; ?> task-priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>">
                        
						<div class="claimed">
                            <em>
                                <?php _e( "You claimed the task:", "taskrocket" ); ?> 
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                <a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&action=undo&task_URL=none" class="undo button-small" target="deletey"><?php _e( 'Undo', 'taskrocket' ); ?></a>
                            </em>
                        </div>
                        
						<h2>
    						<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=none" class="claim" target="deletey"><?php _e( "Take", "taskrocket" ); ?></a>

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
					</li>
				<?php }
				endwhile; ?>
				</ul>
				
				<?php pagination($wp_query->max_num_pages);
				wp_reset_query();
				?>
				
	        </div>
		</div>
		
		<iframe id="deletey" name="deletey" width="0" height="0" frameborder="0"></iframe>

<?php get_footer(); ?>