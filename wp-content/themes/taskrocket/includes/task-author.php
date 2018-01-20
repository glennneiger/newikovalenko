<div class="author">
	<?php 
	$authorID = get_the_author_id();
	$user = get_userdata($authorID);
	$attachment_id = $user->user_photo;
	$image_attributes = wp_get_attachment_image_src( $attachment_id );
	
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
</div>