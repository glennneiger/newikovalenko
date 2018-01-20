<div class="content">
	<div class="container dash">
		<h1><?php _e( 'Welcome', 'taskrocket-clients' ); ?></h1>
		<p>
			<?php _e( 'Hello', 'taskrocket-clients' ); ?> <?php echo $userdata->first_name ?>. 
			<?php _e( 'The projects you are involved with are:', 'taskrocket-clients' ); ?>
		</p>
		<ul class="dash">
		<?php
		// List of projects for currently logged in user
        if(current_user_can( 'client' )) {
    		$userID = get_current_user_id();
            $cat_base = get_option( 'category_base' );
    		$client_project = get_user_meta( $userID, 'client_project', true );
			if (is_array($client_project)) {
	    		foreach ($client_project as $project_ID) : 
	    			$project = get_category( $project_ID ); 
					$pm = get_option( 'tr_project_manager_' . $project_ID);
					$pm_user_info = get_userdata($pm);
					$project_archived = get_option( 'tr_project_archived_' . $project_ID );
					if (!$project_archived) { 
					?>
						<li class="<?php if($options['clients_see_project_details'] == true) { echo "visible-details"; } else { echo "no-visible-details"; } ?>">
							<a href="<?php echo home_url() . '/' . $cat_base . '/' . $project->slug;?>" class="project-name"><?php echo $project->name;?></a>
							
							<?php 
								if($options['clients_see_project_details'] == true) {
									$project_description = $project->category_description;
									if($project_description) {
										echo $project->category_description;
									} else {
										echo "No project description.";
									}?>
									<a href="<?php echo home_url() . '/' . $cat_base . '/' . $project->slug;?>" class="button-small">View Project</a>
								<?php }
							?>
							
							<?php if($options['clients_see_project_details'] == true) { ?>
								<div class="client-project-details">
									<?php 
									$authorID = $pm_user_info->ID;
									$user = get_userdata($authorID);
									$attachment_id = $user->user_photo;
									$image_attributes = wp_get_attachment_image_src( $attachment_id );
									
									if($pm) {
										if (get_option('show_avatars')) {
											echo get_avatar( $user->ID , '200');
										} else {
											if( $image_attributes ) { ?>
											<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" class="photo" />
											<?php } else { ?>
												<span class="no-photo"></span>
											<?php } ?>
										<?php }?>
									
										<?php global $current_user;
										if ($user->roles[0] == "administrator") { ?>
											<span class='admin-icon' title='Administrator'></span>
										<?php } ?>
										
										<span><?php _e( 'Project Manager', 'taskrocket-clients' ); ?></span> <?php echo $pm_user_info->user_firstname . " " . $pm_user_info->user_lastname; ?>
									<?php } ?>
								</div>
							<?php } ?>
							
							
							
						</li>
	    			<?php
					}
	    		endforeach;
	            }
			}
		?>
		</ul>
	</div>
</div>

