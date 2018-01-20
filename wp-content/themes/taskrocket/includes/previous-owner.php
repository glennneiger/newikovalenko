<!--/ Start previous owner /-->
<?php
$the_task = get_post( $post->ID );
$coID = $the_task->post_author;

if(get_post_meta($post->ID, 'previousownerID', TRUE) == "" ) {
    $poID = 0;
} else {
   $poID = get_post_meta($post->ID, 'previousownerID', TRUE);
}
$poFirstName = get_the_author_meta( 'first_name', $poID );
$poLastName = get_the_author_meta( 'last_name', $poID );

// Only show the previous owner if it's not the same as the current owner.
if($poID != "") {
if ($poID !== $coID) { ?>

<span class="small-button previous-owner">

    <?php 
    $author_id = get_the_author_meta('ID');
    $user = get_userdata($poID);
    $attachment_id = $user->user_photo;
    $image_attributes = wp_get_attachment_image_src( $attachment_id );
    //echo $author_id;
    
    if (get_the_author_meta( 'first_name') !== "" ) {
        $previous_owner_name = $poFirstName . " " . $poLastName;
    } else {
        $previous_owner_name = $GLOBALS[ 'nameless' ];
    }
    
    if (get_option('show_avatars')) {
    echo get_avatar( $user->ID , '100');
    } else {
        if( $image_attributes ) { ?>
            <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" title="<?php _e( "Previous owner", "taskrocket" ); ?>: <?php echo $previous_owner_name; ?>" class="master-tooltip" />
        <?php } else { ?>
            <span class="no-photo master-tooltip" title="<?php _e( "Previous owner: Nobody", "taskrocket" ); ?>"></span>
        <?php } ?>
    <?php }?>
    
    <strong><?php _e( "Previous Owner", "taskrocket" ); ?>: </strong> 
    <?php 
        echo $previous_owner_name;
    ?>
</span>
<?php } } ?>
<!--/ End previous owner /-->