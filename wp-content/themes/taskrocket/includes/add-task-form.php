<!--/ Start Add Task /-->
<div class="add-task">

	<script>
	// Count remaining chars
	jQuery(function($) {
		var max = <?php echo $titlecharcount; ?>;
		$('#title').keyup(function() {
			if($(this).val().length > max) {
				$(this).val($(this).val().substr(0, max));
			}
			$('#title-chars').html((max - $(this).val().length) + ' characters left');
		});
	});
	</script>

	<!--/ Start Form /-->
	<form action="<?php echo get_template_directory_uri(); ?>/post-logic.php" name="taskForm" id="new_post" method="post" enctype="multipart/form-data">

		
		<!--/ Start Fleft /-->
		<div class="fleft">
			<div class="section task-name <?php if ( !current_user_can( 'manage_options' ) ) { ?>full-width<?php } ?>">
				<label for="title"><?php _e( "Task name", "taskrocket" ); ?></label>
				<input type="text" id="title" name="title" class="text" maxlength="<?php echo $titlecharcount; ?>" required />
				<em id="title-chars" class="chars"></em>
			</div>
			
			<?php if ( current_user_can( 'manage_options' ) ) { ?>
			<div class="section is-private">
				<label for="private"><?php _e( "Private", "taskrocket" ); ?>
					<input type="hidden" name="private" value="no" />
					<input type="checkbox" id="private" name="private" value="yes" />
				</label>
			</div>
			<?php } ?>

			<?php // If not the project page
			if(!is_category() ) { ?>
			<div class="section halves">
				<?php if ( is_page_template( 'page-new-task.php' ) ) { ?>
					<div class="half-container">
						<label for="cat"><?php _e( "Project", "taskrocket" ); ?></label>
						
						<?php if(current_user_can('client')) { ?>
					
							<select name="categoryID" id="categoryID" required>
								<option></option>
								<?php 
								$userID = get_current_user_id();
								$cat_base = get_option( 'category_base' );
								$client_project = get_user_meta( $userID, 'client_project', true );
								if (is_array($client_project)) {
									foreach ($client_project as $project_ID) : 
										$project = get_category( $project_ID ); 
										$project_archived = get_option( 'tr_project_archived_' . $project_ID );
										if (!$project_archived) {  ?>
										<option value="<?php echo $project_ID;?>"><?php echo $project->name;?></option>
										<?php
										}
									endforeach;
									} 
								?>
							</select>
						
						<?php } else { ?>
							
							<select name="categoryID" id="categoryID" required>
								<option></option>
								<?php 
									$categories = get_categories('hide_empty=0&order=ASC&orderby=name');
									foreach ($categories as $cat) :
										$project = get_category( $cat->cat_ID ); 
										$project_archived = get_option( 'tr_project_archived_' . $cat->cat_ID );
										if (!$project_archived) {  ?>
										<option value="<?php echo $cat->cat_ID;?>"><?php echo $cat->cat_name;?></option>
										<?php
										}
									endforeach;
								?>
							</select>
							
						<?php } ?>
					</div>
				<?php } ?>
				
				<?php if ( is_page_template( 'page-new-task.php' ) ) { ?>
					<?php if ($options['users_reassign_tasks'] == true || current_user_can( 'manage_options' )) { ?>
						<div class="half-container">
							<label for="project_contributor"><?php _e( "Assign this task to", "taskrocket" ); ?>:</label>
							<select name="project_contributor" id="project_contributor">
							<?php if ($options['allow_unowned_tasks'] == true) { ?><option id="0000000" value="0000000"><?php _e( "Nobody", "taskrocket" ); ?></option><?php } ?>
							<?php
								$trusers = get_users('blog_id=1&orderby=nicename');
								foreach ($trusers as $user) { ?>
								<option <?php if ($user->ID == get_current_user_id()) echo 'selected';?> value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>">
								<?php if ($user->first_name !== "") {
								echo $user->first_name . " " . $user->last_name;
								} else {
								_e( "Nobody", "taskrocket" );
								}
								?> (<?php echo $user->user_email; ?>)</option>
								<?php
									}
								?>
							</select>
						</div>
					<?php } else { ?>
						<?php echo $user->first_name . " " . $user->last_name; ?>
						<input type="hidden" name="project_contributor" id="project_contributor" value="<?php echo get_current_user_id(); ?>" />
					<?php } ?>
				<?php } ?>
			</div>
			<?php // End if not the project page
			} ?>


			<div class="section thirds priority-and-dates">
				
				<div class="third-container priority">
					<label><?php _e( "Priority", "taskrocket" ); ?></label>
					<label class="radio new-task-priority-low"><input type="radio" name="priority" value="low" id="low"><?php _e( "Low", "taskrocket" ); ?></label>
					<label class="radio new-task-priority-normal"><input type="radio" name="priority" value="normal" id="normal" checked><?php _e( "Normal", "taskrocket" ); ?></label>
					<label class="radio new-task-priority-high"><input type="radio" name="priority" value="high" id="high"><?php _e( "High", "taskrocket" ); ?></label>
					<label class="radio new-task-priority-urgent"><input type="radio" name="priority" value="urgent" id="urgent"><?php _e( "Urgent", "taskrocket" ); ?></label>
				</div>
				
				<div class="third-container start-date">
					<label><?php _e( "Start date", "taskrocket" ); ?></label>
					<input type="text" class="text date" id="startdate" name="startdate" />
					<em class="clear-field clear-start-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
				</div>
				
				<div class="third-container end-date">
					<label><?php _e( "End date", "taskrocket" ); ?></label>
					<input type="text" class="text date" id="duedate" name="duedate" />
					<em class="clear-field clear-end-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
				</div>
			</div>
			
			<?php // If Task Relations are not disabled
			if ($options['disable_task_relations'] == false) { ?>
				<?php if(!current_user_can( 'client' )) { // if not a client ?>
				<div class="section">
					<label for="block"><?php _e( "This task", "taskrocket" ); ?></label>
					
					<select name="relation" class="relation">
						<option value=""></option>
						<option value="has_issues_with"><?php _e( "Has issues with", "taskrocket" ); ?></option>
						<option value="is_blocked_by"><?php _e( "Is blocked by", "taskrocket" ); ?></option>
						<option value="is_similar_to"><?php _e( "Is similar to", "taskrocket" ); ?></option>
						<option value="relates_to"><?php _e( "Relates to", "taskrocket" ); ?></option>
					</select>
					
					<?php if(is_page('new-task')) { ?>
						<select name="related" class="related" disabled>
							<option value=""></option>
							<?php
							$cat_args = array(
								'orderby'	 => 'name',
								'order'		 => 'ASC'
							);
							$categories = get_categories($cat_args);
							
							foreach($categories as $category) {
								$args = array(
								'showposts' 		=> -1,
								'category__in'		=> array($category->term_id),
								'caller_get_posts'	=> 1,
								'orderby' 		    => 'title', 
								'order' 		    => 'ASC'
								);
								
								$task_args = array(
									'category__in'		=> array($category->term_id),
									'posts_per_page' 	=> -1,
									'offset'            => 0,
									'meta_key'          => 'tr_status',
									'meta_value'        => array('incomplete', 'inprogress', 'onhold'),
									'orderby' 		    => 'title', 
									'order' 		    => 'ASC'
								);
								$project_archived = get_option( 'tr_project_archived_' . $category->term_id );
								$posts=get_posts($task_args);
									if ($posts) {
									if (!$project_archived) { 
									echo '<optgroup label="' . $category->name . '">';
									foreach($posts as $post) {
										
										$private = get_post_meta($post->ID, 'private', TRUE);
										
										if (current_user_can( 'manage_options' ) && $private == 'yes') {
											$view_private = __( "(Private task)", "taskrocket" );
										} else {
											$view_private = "";
										}
										
										setup_postdata($post);
										
									?>
										
										<?php if (current_user_can( 'manage_options' )) { ?>
											<option value="<?php echo $post->ID; ?>" <?php if($private == 'yes') { echo 'class="is-private"'; } ?>><?php the_title(); ?> <?php echo $view_private; ?></option>
										<?php } else { ?>
											<?php if(!$private) { ?>
												<option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
											<?php } ?>
										<?php } ?>
										
										
									<?php
									}
									echo '</optgroup> ';
									}
								}
							}
							?>
						</select>
							
						<?php } else { ?>
							<select name="related" class="related">
								<option value=""></option>
								<?php 
								global $post;
								$args = array( 
									'numberposts'    => -1,
									'orderby' 		 => 'title', 
									'order' 		 => 'ASC',
									'posts_per_page' => -1,
									'post_status' 	 => 'publish',
									'cat'		     => $cat_id,
									'meta_key'       => 'tr_status',
									'meta_value'     => array('incomplete', 'inprogress', 'onhold')
								); 
								$posts = get_posts($args); 
								foreach( $posts as $post ) : setup_postdata($post);
								$private = get_post_meta($post->ID, 'private', TRUE);
								if (current_user_can( 'manage_options' ) && $private == 'yes') {
									$view_private = __( "(Private task)", "taskrocket" );
								} else {
									$view_private = "";
								}
								?>
									<?php if (current_user_can( 'manage_options' )) { ?>
										<option value="<?php echo $post->ID; ?>"><?php the_title(); ?> <?php echo $view_private; ?></option>
									<?php } else { ?>
										<?php if(!$private) { ?>
											<option value="<?php echo $post->ID; ?>"><?php the_title(); ?></option>
										<?php } ?>
									<?php } ?>
								<?php endforeach; wp_reset_query(); ?>
							</select>
						<?php } ?>
						
						<div class="elaboration">
							<label for="block"><?php _e( "Elaboration", "taskrocket" ); ?></label>
							<textarea name="elaboration"></textarea>
						</div>
						
						<script>
						jQuery('.related').change(function() {
							if (jQuery(this).val() != '') {
								jQuery('.elaboration').slideDown();
							}
							if (jQuery(this).val() === '') {
								jQuery('.elaboration').slideUp();
								jQuery('.relation, .related, textarea').val('');
								jQuery('.related').attr('disabled', 'disabled');
							}
						});
						jQuery('.relation').change(function() {
							if (jQuery(this).val() === '') {
								jQuery('.elaboration').slideUp();
								jQuery('.relation, .related, textarea').val('');
								jQuery('.related').attr('disabled', 'disabled');
							} else {
								jQuery('.related').removeAttr('disabled');
								jQuery('.related').attr('required', 'required');
							}
						});
						</script>
						
					</div>
					<?php } // End if not a client ?>
				<?php } // fnd If Task Relations are not disabled ?>
			
			<div class="section halves job-number-and-attachments">
				
				<?php if($options['clients_create_job_numbers'] == true || !current_user_can( 'client')) { ?>
				<div class="half-container job-number">
					<label><?php _e( "Job number", "taskrocket" ); ?></label>
					<?php if($options['auto_job_numbers'] == true) { ?>
						<span class="job-num-notice"><?php _e( "Auto Generated", "taskrocket" ); ?> <i class="tip master-tooltip" title="<?php _e( "A job number will be automatically assigned when you create this task.", "taskrocket" ); ?>"></i></span>
						<input type="hidden" id="job_number_task" name="job_number_task" value="<?php if ($options['job_number_prefix'] == true) { echo $options['job_number_prefix']; } ?><?php echo strtoupper(date("YMd")); ?>-T" />
					<?php } else { ?>
						<input type="text" class="text job-number" id="task" name="job_number_task" value="<?php echo $options['job_number_prefix']; ?>" />
					<?php } ?>
				</div>
				<?php } ?>
				
				<?php // If file uploads are enabled, and the use is NOT a client...
				if($options['disable_file_uploads'] == false && !current_user_can( 'client')) { ?>
	
					<div class="half-container upload-attachment">
						<label><?php _e( "Attach files", "taskrocket" ); ?></label>
						<input type="file" name="tr_multiple_attachments[]"  multiple="multiple" />
					</div>
	
				<?php } ?>

				<?php // If client file attachments are enabled, and the use IS a client...
				if($options['clients_attachments'] == true && current_user_can( 'client')) { ?>
	
					<div class="section upload-attachment">
						<label><?php _e( "Attach files", "taskrocket" ); ?></label>
						<input type="file" name="tr_multiple_attachments[]"  multiple="multiple" />
					</div>
	
				<?php } ?>
				
			</div>

			<span class="cancel-task">&#215;</span>
			
		</div>
		<!--/ End Fleft /-->
		
		
		<!--/ Start Fright /-->
		<div class="fright">
			<div class="section textarea-right">
				<span class="nbm full-width">
					<label for="minfo"><?php _e( "Additional information", "taskrocket" ); ?></label>
					<textarea name="minfo" id="minfo" class="text textarea" rows="10" cols="20"></textarea>
				</span>
				
				<div class="submit-button-container">
					<input type="submit" name="submit" class="button submit" value="<?php _e( "Create Task", "taskrocket" ); ?>" />
					<img src="<?php echo get_template_directory_uri(); ?>/images/loader.gif" />
				</div>
				
			</div>
		</div>
		<!--/ End Fright /-->

		<?php wp_nonce_field('post_nonce', 'post_nonce_field');

		// If a client, the category is the project ID the client is allowed to see.
		if(current_user_can( 'client' )) {
            $category = $projectID;
        // Otherwise, the category is just the usual category ID.
        } else {
            $category = get_cat_id( single_cat_title("",false) );
        }
		$pm_specified = get_option( 'tr_project_manager_' . $category);
		$user_info = get_userdata($pm_specified);

		?>

		<input type="hidden" name="submitted" id="submitted" value="true" />

		<?php if ( is_page_template( 'page-new-task.php' ) ) { } else { ?>
		<input type="hidden" name="categoryID" id="categoryID" value="<?php echo $cat_id; ?>" />
		<?php } ?>

		<input type="hidden" name="categorySlug" id="categorySlug" value="<?php echo $categoryslug; ?>" />

		<?php if ( !is_page_template( 'page-new-task.php' ) ) { ?>
		<input type="hidden" name="project_contributor" id="project_contributor" value="<?php echo get_current_user_id(); ?>" />
		<?php } ?>

		<?php if ( is_page_template( 'page-new-task.php' ) ) {
			$task_form_type = "new-task-solo";
		} else if(current_user_can( 'client')) {
			$task_form_type = "client";
		} else {
			$task_form_type = "new-task";
		}?>

		<input type="hidden" name="task-form-type" id="task-form-type" value="<?php echo $task_form_type; ?>" />

		<?php // If there is a project manager, use their email address.
		if($pm_specified == true) { ?>
		<input type="hidden" name="pm_email" id="pm_email" value="<?php echo $user_info->user_email; ?>" />
		<?php // otherwise, use the default Wordpress admin email address.
		} else { ?>
		<input type="hidden" name="pm_email" id="pm_email" value="<?php echo get_option( 'admin_email' ); ?>" />
		<?php } ?>

		<?php if (current_user_can( 'client')) { ?>
		<input type="hidden" name="role" id="role" value="client" />
		<?php } ?>

		<?php if ($options['send_plain'] == true) { ?>
		<input type="hidden" name="send_plain" id="send_plain" value="yes" />
		<?php } ?>

		<?php if ($options['no_emails'] == true) { ?>
		<input type="hidden" name="no_emails" id="no_emails" value="yes" />
		<?php } ?>

		<?php if ($options['show_gravatars'] == true) { ?>
		<input type="hidden" name="gravatars" id="gravatars" value="yes" />
		<?php } ?>
		
		<input type="hidden" name="tr_status" id="tr_status" value="incomplete" />
		
		<span class="close-new-task-form"></span>

	</form>
	<!--/ End Form /-->
	
</div>
<!--/ End Add Task /-->