<?php
$options = get_option( 'taskrocket_settings' );

if ($options['files_new_tab'] == true) {
	$newTab = ' target="_blank"';
}

// Attachments
$attachments = get_posts( array(
	'post_type' => 'attachment',
	'posts_per_page' => -1,
	'post_parent' => $post->ID,
	'orderby' => 'title',
	'order' => 'ASC'
) );

if ($options['files_new_tab'] == true) {
	$newTab = ' target="_blank"';
}
$ai = 0;
$userID = get_current_user_id();
?>

	<?php // If Attachments
	if ( $attachments ) { ?>

	<?php // If clients are allowed to see attachments or if an admin
	if($options['clients_see_attachments'] == true || current_user_can( 'manage_options') || current_user_can( 'editor')) { ?>

	<div class="attachments">
		
		<h3><?php _e( "Attachments", "taskrocket" ); ?></h3>

		<div class="attachments-container">

			<ul class="file-images">
				<?php // Start loop
					foreach ( $attachments as $attachment ) {
					$filethumb = wp_get_attachment_thumb_url( $attachment->ID);	 // Path to the thumbnail
					$filepath = wp_get_attachment_url( $attachment->ID);			// Path to the original file
					$filename = $attachment->post_title;
					$filesize = @filesize( get_attached_file( $attachment->ID ) );
					$filesize = size_format($filesize, 2);
					$deleteAttachment = wp_nonce_url(home_url() . "/wp-admin/post.php?action=delete&amp;post=".$attachment->ID."", 'delete-post_' . $attachment->ID);
					$authorID = $post->post_author;
					$author_info = get_userdata($authorID);
					if($author_info !="") {
						$author_first_name = $author_info->first_name;
						$author_last_name = $author_info->last_name;
						$author_role = implode(', ', $author_info->roles);
					} else {
						$author_first_name = __( "Nobody", "taskrocket" );
						$author_last_name = "";
						$author_role = "";
					}
					if($author_role == "client") {
						$role_icon = '<em class="role-icon client" title="' . __( "This file was uploaded by a client", "taskrocket" ) . '">C</em>';
					}
				?>
				<?php // If attachment type is an image
				if ( wp_attachment_is_image( $attachment->ID ) ) { ?>

					<li>
						<a href="<?php echo $filepath; ?>?TB_iframe=true" rel="task-<?php echo get_the_ID(); ?>-images" class="image-anchor <?php $options = get_option( 'taskrocket_settings' ); if ($options['use_thickbox'] == true) { echo "thickbox"; } ?>" title="<?php echo $filename; ?>">
							<img src="<?php echo $filethumb; ?>" />
						</a>
						<a href="<?php echo $filepath; ?>" class="file-name" download>
							<?php echo $filename; ?>
							<span><?php _e( "Uploaded by", "taskrocket" ); ?> <?php echo $author_first_name; ?> <?php echo $author_last_name; ?> <?php echo $role_icon; ?> <?php _e( "on", "taskrocket" ); ?> <?php echo get_the_time($date_format, $attachment->ID); ?></span>
							<span><?php echo $filesize; ?></span>
						</a>
						<?php
						if( $authorID == $userID || current_user_can( 'manage_options')) { ?>
						<a class="show-delete" title="<?php _e( "Delete this image", "taskrocket" ); ?>">&#215;</a>
						<span class="delete-file-confirmation">
							<span>
								<strong><?php _e( "Delete this image", "taskrocket" ); ?></strong>
								<a href="<?php echo $deleteAttachment; ?>" target="deletey" class="button-small delete-yes "><?php _e( "Yes", "taskrocket" ); ?></a>
						    	<a class="button-small delete-no"><?php _e( "No", "taskrocket" ); ?></a>
							</span>
						</span>
						<?php } ?>
					</li>

				<?php // End loop
				} ?>

			<?php // End if attachment type is an image
		   	} ?>
			</ul>


			<ul class="file-others">
				<?php // Start loop
					foreach ( $attachments as $attachment ) {
					$filethumb = wp_get_attachment_thumb_url( $attachment->ID);	 // Path to the thumbnail
					$filepath = wp_get_attachment_url( $attachment->ID);			// Path to the original file
					$filename = $attachment->post_title;
					$filesize = @filesize( get_attached_file( $attachment->ID ) );
					$filesize = size_format($filesize, 2);
					$deleteAttachment = wp_nonce_url(home_url() . "/wp-admin/post.php?action=delete&amp;post=".$attachment->ID."", 'delete-post_' . $attachment->ID);
					$authorID = $post->post_author;
					$author_info = get_userdata($authorID);
					if($author_info !="") {
						$author_first_name = $author_info->first_name;
						$author_last_name = $author_info->last_name;
						$author_role = implode(', ', $author_info->roles);
					} else {
						$author_first_name = __( "Nobody", "taskrocket" );
						$author_last_name = "";
						$author_role = "";
					}
					if($author_role == "client") {
						$role_icon = '<em class="role-icon client" title="' . __( "This file was uploaded by a client", "taskrocket" ) . '">C</em>';
					}
				?>
				<?php // If attachment type is NOT an image
				if ( !wp_attachment_is_image( $attachment->ID ) ) { ?>
					<li>
						<a href="<?php echo $filepath; ?>" title="<?php echo $filename; ?>" target="_blank" class="the-file-name-anchor">
							<span class="the-file-name"><?php echo substr($filename, 0, 50); ?>.<?php echo get_icon_for_attachment($attachment->ID); ?></span>
							<span class="filesize"><?php echo $filesize; ?></span> 
							<span><?php _e( "Uploaded by", "taskrocket" ); ?> <?php echo $author_first_name; ?> <?php echo $author_last_name; ?> <?php _e( "on", "taskrocket" ); ?> <?php echo get_the_time($date_format, $attachment->ID); ?> <?php echo $role_icon; ?></span>
						</a>
						<?php if( $authorID == $userID || current_user_can( 'manage_options')) { ?>
						<a class="show-delete" title="<?php _e( "Delete this file", "taskrocket" ); ?>">&#215;</a>
						<span class="delete-file-confirmation">
							<span>
								<strong><?php _e( "Delete this file", "taskrocket" ); ?></strong>
								<a href="<?php echo $deleteAttachment; ?>" target="deletey" class="button-small delete-yes"><?php _e( "Yes", "taskrocket" ); ?></a>
						    	<a class="button-small delete-no"><?php _e( "No", "taskrocket" ); ?></a>
							</span>
						</span>
						<?php } ?>
					</li>

				<?php // End loop
				} ?>

			<?php // End if attachment type is NOT an image
		   	} ?>
			</ul>

		</div>
	</div>
	<?php // End if clients are allowed to see attachments or if an admin
	} ?>

<?php // End If Attachments
} ?>
