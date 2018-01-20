<!--/ Start Users List /-->
<div class="tr-users">

	<ul>
	<?php 
		$lastnames = $wpdb->get_col("SELECT user_id FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key = 'first_name' ORDER BY $wpdb->usermeta.meta_value ASC");
		foreach ($lastnames as $user_ID) {
		$user = get_userdata($user_ID);
		
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
		
		$author_posts_url = get_author_posts_url($user->ID); 
		$attachment_id = $user->user_photo;
		$image_attributes = wp_get_attachment_image_src( $attachment_id );
		global $current_user;
	?>

			<?php if ($user->roles[0] == "administrator") { ?>
				<li title="<?php echo $user->user_firstname; ?> has <?php echo $user_tasks_count;?> outstanding tasks" class="role role-admin">
					<a href="#" class="profile-link" page="<?php echo home_url(); ?>/user-profile/?userID=<?php echo $user->ID; ?>">
						<?php 
						if (get_option('show_avatars')) {
						echo get_avatar( $user->ID , '200');
						} else {
							if( $image_attributes ) { ?>
							<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>">
							<?php } else { ?>
								<span class="no-photo"></span>
							<?php } ?>
						<?php }?>
						<strong>
						<?php if ($user->user_firstname !== "" ) {
							echo $user->user_firstname . " " . $user->user_lastname;
						} else {
							echo $GLOBALS[ 'nameless' ];
						}
						?>
						<em>
							<?php global $current_user;
							if ($options['admin_title'] !== "") {
						        echo $options['admin_title'] . "<span class='admin-icon' title='" . __( "Administrator", "taskrocket" ) . "'></span>";
						    } else {
						        echo "Administrator<span class='admin-icon' title='" . __( "Administrator", "taskrocket" ) . "'></span>";
						    }
							?>
							<span class="number-of-tasks"> - <?php echo $user_tasks_count;?> <?php _e( "Tasks", "taskrocket" ); ?></span>
						</em>
						</strong>
					</a>
					<a href="<?php echo home_url(); ?>/my-tasks/?user=<?php echo $user->ID; ?>" class="button-small"><?php _e( "Tasks", "taskrocket" ); ?></a>
				</li>
			<?php } ?>

			<?php if ($user->roles[0] == "editor") { ?>
				<li title="<?php echo $user->user_firstname; ?>" class="role role-team">
					<a href="#" class="profile-link" page="<?php echo home_url(); ?>/user-profile/?userID=<?php echo $user->ID; ?>">
						<?php 
						if (get_option('show_avatars')) {
						echo get_avatar( $user->ID , '200');
						} else {
							if( $image_attributes ) { ?>
							<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>">
							<?php } else { ?>
								<span class="no-photo"></span>
							<?php } ?>
						<?php }?>
						<strong>
						<?php if ($user->user_firstname !== "" ) {
							echo $user->user_firstname . " " . $user->user_lastname;
						} else {
							echo $GLOBALS[ 'nameless' ];
						}
						?>
						<em><?php _e( "Team Member", "taskrocket" ); ?>
							<span class="number-of-tasks"> - <?php echo $user_tasks_count;?> <?php _e( "Tasks", "taskrocket" ); ?></span>
						</em>
						</strong>
					</a>
					<a href="<?php echo home_url(); ?>/my-tasks/?user=<?php echo $user->ID; ?>" class="button-small"><?php _e( "Tasks", "taskrocket" ); ?></a>
				</li>
			<?php } ?>

			<?php if ($user->roles[0] == "client") { ?>
				<li title="<?php echo $user->user_firstname; ?>" class="role role-client">
					<a href="#" class="profile-link" page="<?php echo home_url(); ?>/user-profile/?userID=<?php echo $user->ID; ?>">
					<?php 
					if (get_option('show_avatars')) {
					echo get_avatar( $user->ID , '200');
					} else {
						if( $image_attributes ) { ?>
						<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>">
						<?php } else { ?>
							<span class="no-photo"></span>
						<?php } ?>
					<?php }?>
					<strong>
					<?php if ($user->user_firstname !== "" ) {
						echo $user->user_firstname . " " . $user->user_lastname;
					} else {
						echo $GLOBALS[ 'nameless' ];
					}
					?>
					<em>
						<?php _e( "Client", "taskrocket" ); ?>
						<span class="number-of-tasks"> - <?php echo $user_tasks_count;?> <?php _e( "Tasks", "taskrocket" ); ?></span>
					</em>
					</strong>
					</a>
					<a href="<?php echo home_url(); ?>/my-tasks/?user=<?php echo $user->ID; ?>" class="button-small"><?php _e( "Tasks", "taskrocket" ); ?></a>
				</li>
			<?php } ?>

		<?php }
		?>
	</ul>

</div>
<!--/ End Users List /-->
