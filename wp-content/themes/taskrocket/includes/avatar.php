<?php if (get_option('show_avatars')) {
    echo get_avatar( $user_ID, 100 ); ?>
<?php } else { ?>
    <?php $attachment_id = $userdata->user_photo;
    $image_attributes = wp_get_attachment_image_src( $attachment_id );
    if( $image_attributes ) { ?> 
    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>">
    <?php } else { ?>
        <span class="no-photo"></span>
    <?php } ?>
<?php } ?>