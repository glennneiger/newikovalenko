
<?php 
$options = get_option( 'taskrocket_settings' );
global $userdata; wp_get_current_user();
$ta_count = $userdata->number_team_activity;
$comment_count = $userdata->number_recent_comments;
?>

<!--/ Start Recent Team Activity /-->
<div class="team-activity-container">

    <?php  // Recent activity
    if($ta_count > 0) {
        $args = array( 
            'posts_per_page'  => $ta_count,
            'orderby'         => 'modified',
            'order'           => 'DESC',
            'post_status'     => 'publish, trash'
        );
    ?>
	<ul class="panel team-activity">
        
        <li class="column-header">
            <em class="team-activity-title">
                <?php _e( "Recent Team Activity", "taskrocket" ); ?>
            </em>
        </li>

        <?php 
        $the_query = new WP_Query( $args );
        $i = 0;
        while ($the_query -> have_posts()) : $the_query -> the_post();
        
        $author = get_the_author();
        $authorID = get_the_author_id();
        $user = get_userdata($authorID);
        $attachment_id = $user->user_photo;
        $image_attributes = wp_get_attachment_image_src( $attachment_id );
        
        $post_status = get_post_status( $ID );
        $date_format = get_option('date_format') . " g:i a";
        $category = get_the_category();
        if( get_post_meta($post->ID, 'private', TRUE) == 'yes') {
            $private_text = "private";
        }
        if($post_status == "publish") {
            $decsription = __( "Has a new", "taskrocket" ) . " " . $private_text . " " . __( "Task", "taskrocket" );
            $complete_added = "<span>" . __( "Added", "taskrocket" ) . "</span>";
        }
        if($post_status == "trash") {
            $decsription = "Completed a task: ";
            $complete_added = "<span class='completed'>" . __( "Completed", "taskrocket" ) . "</span>";
        }
        ?>
            
            <li class="<?php echo $post_status; ?> <?php echo "row-" . ($i++ % 2); ?>">
                <?php 
                if (get_option('show_avatars')) {
                echo get_avatar( $authorID , '150');
                } else {
                    if( $image_attributes ) { ?>
                    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>">
                    <?php } else { ?>
                        <span class="no-photo"></span>
                    <?php } ?>
                <?php }?>
                <p>
                    <strong>
                    <?php 
                    $author = get_the_author();
                    if($author !=="") {
                        if ($user->user_firstname !== "" ) {
                            echo $user->user_firstname . " " . $user->user_lastname;
                        } else {
                            echo $GLOBALS[ 'nameless' ];
                        }
                    } else {
                        echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket" ) . ')';
                    }
                    ?>
                    </strong>
                    <?php echo $decsription; ?> 
                    
                    <?php if( get_post_meta($post->ID, 'private', TRUE) !== 'yes' || current_user_can( 'manage_options' ) ) { ?>
                    <a href="<?php the_permalink() ?>"> 
                        <?php the_title(); ?>
                    </a> 
                    <?php } else { ?>
                        
                    <?php } ?>
                    
                    (<a href="<?php echo get_category_link($category[0]->term_id ); ?>" class="project-name"><?php echo $category[0]->cat_name; ?></a>) 
                    <?php if ($options['users_edit_tasks'] == true || current_user_can( 'manage_options' )) { ?>
                        <?php if($author =="") { ?>
                            <a href="<?php the_permalink() ?>?take=yes" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
                        <?php } ?>
                    <?php } ?>
                    <span class="date"><?php echo $complete_added . get_the_modified_date($date_format); ?></span>
                </p>
            </li>

        <?php 
        endwhile;
        wp_reset_postdata();
        ?>
    </ul>
    <?php } ?>

    <?php  // Recent comments
    if($comment_count > 0) {
        $args = array(
            'status' => 'approve',
            'number' => $comment_count
        ); 
    ?>
    <ul class="panel team-activity">
        
        <li class="column-header">
            <em class="team-activity-title">
                <?php _e( "Recent comments", "taskrocket" ); ?>
            </em>
        </li>
        <?php
            $i = 1;
            $comments = get_comments($args);
            foreach($comments as $comment) : 
            $the_comment = $comment->comment_content;
            $the_comment_date = get_the_modified_date($comment->comment_date);
            $date_format = get_option('date_format') . " g:i a";
            $comment_post_ID = $comment->comment_post_ID;
            $comment_ID = $comment->comment_ID;
            $comment_user_id = get_comment($comment_ID)->user_id;
            
            $comment_user = get_userdata($comment_user_id);
            $comment_attachment_id = $comment_user->user_photo;
            $comment_image_attributes = wp_get_attachment_image_src( $comment_attachment_id );
            ?>
            	<li class="<?php echo "row-" . ($i++ % 2); ?> dash-comment">
                    <?php 
                    if (get_option('show_avatars')) {
                    echo get_avatar( $comment->user_id , '150');
                    } else {
                        if( $comment_image_attributes ) { ?>
                        <img src="<?php echo $comment_image_attributes[0]; ?>" width="<?php echo $comment_image_attributes[2]; ?>" height="<?php echo $comment_image_attributes[2]; ?>">
                        <?php } else { ?>
                            <span class="no-photo"></span>
                        <?php } ?>
                    <?php }?>
                    <p>
                    <strong>
                    <?php 
                    $comment_author = get_comment_author($comment_ID);
                    $comment_author_first_name = get_the_author_meta('first_name', $comment_user_id);
                    $comment_author_last_name = get_the_author_meta('last_name', $comment_user_id);

                    echo $comment_author_first_name . " " . $comment_author_last_name;

                    ?>
                </strong>Commented: 
                        <a href="<?php echo get_comment_link() ?>" class="the-dash-comment"><?php echo substr($the_comment, 0, 75); ?><?php if (strlen($the_comment) > 75) { echo "..."; } ?></a>
                        <span class="date">
                            <span class='added'>
                                <a href="<?php the_permalink($comment->comment_post_ID) ?>">
                                    <?php echo get_the_title($comment_post_ID); ?>
                                </a>
                            </span>
                            <?php echo get_comment_date($date_format); ?>
                        </span>
                    </p>
                </li>
            <?php endforeach;?>

    </ul>
    

</div>
<!--/ End Recent Team Activity /-->
<?php } ?>