<!--/ Start Recent Comments /-->
<div class="recent-comments-pane">
    
    <div class="nav-label">
        <?php _e( "Recent comments", "taskrocket" ); ?>
    </div>
    
     <ul>
        <?php
        $args = array(
        	'status' => 'approve',
        	'number' => $recent_comms
        );
        $comments = get_comments($args);
        $i = 0;
        foreach($comments as $comment) :
        $comment_author_id = get_comment(get_comment_ID())->user_id;  
        $comment_post_id = $comment->comment_post_ID;   
        $user = get_userdata($comment_author_id);
        $priority = get_post_meta( $comment_post_id, 'priority', true );
        $project_name = get_the_category( $comment_post_id )[0]->name;
        ?>
        	<li class="<?php echo "row-" . ($i++ % 2); ?>">
                <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
                <?php if (get_option('show_avatars')) { 
                    echo get_avatar( $comment->comment_author_email, 150 );
                } else { ?>
                    
                    <?php 
                    $attachment_id = $user->user_photo;
            		$image_attributes = wp_get_attachment_image_src( $attachment_id );
                    if( $image_attributes ) { ?> 
                    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" />
                    <?php } else { ?>
                        <span class="no-photo"></span>
                    <?php } ?>

                <?php } ?>	
                <strong>    
                <?php
                    $user_info = get_userdata($comment_author_id);
                    if ($user_info->user_firstname !== "" ) {
       				     echo $user_info->user_firstname . " " . $user_info->user_lastname;
           			 } else {
           				 echo $GLOBALS[ 'nameless' ];
           			 }
       			 ?>
                 </strong>
                
                    <span class="task-name"><?php echo $project_name; ?> &#10140; <?php echo get_the_title($comment_post_id); ?></span>
                    
                    <span class="comment-body">
                        <?php if($priority) { ?>
                        <span class="priority <?php echo $priority; ?>-priority"><?php echo $priority; ?></span>
                        <?php } ?>
                        <p>
                            <?php 
                                $comment_string_limit = 250;
                                $comment_string = $comment->comment_content;
                                $comment_length = strlen($comment_string);
                                echo substr($comment_string, 0, $comment_string_limit);
                            
                                if($comment_length > $comment_string_limit) {
                                    echo "...";
                                }
                            ?>
                        </p>
                    </span>
                
                
                <span class="time-date"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></span>
                </a>
            </li>
        <?php endforeach;
        ?>
    </ul>
</div>
<!--/ End Recent Comments /-->