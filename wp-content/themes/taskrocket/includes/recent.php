<!--/ Start My Recent Tasks /-->
<div class="recent-container" id="recent">

		<div class="panel dash-tasks">
			<?php
			$mytasks = count_user_posts( $user_ID );
			if ($mytasks == 0) { ?>

				<p class="no-tasks"><?php _e( "You don't have any tasks yet.", "taskrocket" ); ?>
				<?php if($options['users_create_tasks'] == true || current_user_can( 'manage_options' )) { ?>
					<a href="<?php echo home_url(); ?>/new-task/" class="button-small"><?php _e( "Create one?", "taskrocket" ); ?></a>
				</p>
				<?php } else { ?>
					<?php _e( "Unless the option is enabled, only administrators can create tasks.", "taskrocket" ); ?>
				<?php } ?>

			<?php } else { ?>

			<ul>
				<li class="column-header">
					<em class="task-project">
						<?php _e( "My latest", "taskrocket" ); ?> <?php echo $recent_count ?> task<?php if($recent_count > 1 || $recent_count == 0) { echo "s"; } ?>
					</em>
					<em><?php _e( "Priority", "taskrocket" ); ?></em>
					<?php if(current_user_can( 'manage_options' )) { ?>
					<em class="privacy"><?php _e( "Privacy", "taskrocket" ); ?></em>
					<?php } ?>
					<em class="date-created"><?php _e( "Created", "taskrocket" ); ?></em>
					<em><?php _e( "Due", "taskrocket" ); ?></em>
					<em><?php _e( "Time", "taskrocket" ); ?></em>
					<em class="task-cost"><?php _e( "Cost", "taskrocket" ); ?></em>
					<em class="attachments"><?php _e( "Attachments", "taskrocket" ); ?></em>
					<em class="action"><?php _e( "Action", "taskrocket" ); ?></em>
				</li>
				<?php
					$author_query = array(
						'posts_per_page'	=> $recent_count, 
						'author' 			=> $current_user->ID,
						'post_status' 		=> 'publish',
						'meta_key'          => 'tr_status',
						'meta_value'        => array('incomplete', 'inprogress', 'onhold')
					);
					$author_posts = new WP_Query($author_query);
					$i = 0;
					while($author_posts->have_posts()) : $author_posts->the_post();
					
					$related_ID           = get_post_meta($post->ID, 'related', TRUE);
					$relationship_details = get_post_meta($post->ID, 'elaboration', TRUE);
					$related_title        = get_the_title($related_ID);
					
					// Get the TASK status of the related task
					$related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 
					
					// Get the POST status of the related task 
					$related_post_status       = get_post_status( $related_ID );
					
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
				<li class="task-project <?php echo "row-" . ($i++ % 2); ?>">
					
					<h3>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php if( date('zY') == get_the_time('zY') ) { ?><em class="button-small"><?php _e( "New", "taskrocket" ); ?></em><?php } ?>
						<span>
							<?php $category = get_the_category(); ?>
							<a href="<?php echo get_category_link($category[0]->term_id ); ?>" class="project-name"><?php echo $category[0]->cat_name; ?></a>
						</span>
	
						<?php if(get_post_meta($post->ID, 'relation', TRUE) && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
							<p class="relationship">
								<span class="show-relationship"><?php _e( "This task", "taskrocket" ); ?> <?php echo $relation; ?></span> 
								<a href="<?php echo get_the_permalink($related_ID); ?>"><?php echo $related_title; ?></a> <?php if($relationship_details) { echo '<em class="dots">...</em>'; } ?>
							</p>
						<?php } ?>
					</h3>
					
					<span class="priority">
						<em class="<?php if( get_post_meta($post->ID, 'priority', TRUE) != '' ) { echo "priority-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " priority-normal"; } ?>" title="<?php echo get_post_meta($post->ID, 'priority', TRUE); ?> priority"></em>	
					</span>
					
					<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
					<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
					
					<?php if(get_post_meta($post->ID, 'relation', TRUE) && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
				
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
				<?php
					endwhile;
				?>
			</ul>
			
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

			<?php } ?>

		</div>
	
</div>
<!--/ End  My Recent Tasks /-->