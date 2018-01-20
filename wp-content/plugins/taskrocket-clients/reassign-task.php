<!--/ Start Reassign /-->    
<div class="half-container reassign">
        
    <label for="reassign" class="notify-label">
    <input type="checkbox" id="reassign" name="reassign" value="yes" /> <?php _e( "Reassign this task?", "taskrocket" ); ?>
    </label>
    
    <span class="task-owner">
        <?php 
        if (get_the_author_meta( 'first_name') !== "" ) {
            echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
        } else {
            echo $GLOBALS[ 'nameless' ];
        }
        ?>
    </span>
    
    <div class="reassign-all-users-list">
        <select name="project_contributor" id="project_contributor">
            <?php if ($options['allow_unowned_tasks'] == true) { ?>
            <option id="0000000" value="0000000"><?php _e( "Nobody", "taskrocket" ); ?></option>
            <?php } ?>



            <?php if ($options['clients_assign_and_reassign_tasks_to_anyone'] == true) {

                // All users
                $user_args = array(
                    'orderby'     => 'display_name',
                    'order'       => 'ASC'
                );
                $all_users = get_users( $user_args );
                foreach ( $all_users as $user ) { ?>
                    <option <?php if ($user->ID == get_the_author_meta( 'ID' )) echo 'selected';?> value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>">
                    <?php if ($user->first_name !== "") {
                    echo $user->first_name . " " . $user->last_name;
                        } else {
                    echo $GLOBALS[ 'nameless' ];
                    } ?> 
                    (<?php echo $user->user_email; ?>)</option>
                <?php }

            } else {

                // Get users involved with this project
                if ($wp_query->found_posts > 0) {
                    $cat_authors = array();
                    
                    $categories = get_the_category();
                    $project_id = $categories[0]->cat_ID;
                    
                    $args = array(
                        'posts_per_page'     => -1,
                        'category'           => $project_id,
                        'orderby' 			 => 'name',
                        'order' 			 => 'ASC',
                        'post_status'        => 'publish'
                    );
                    $allposts=get_posts($args);
                    if ($allposts) {
                        foreach($allposts as $authorpost) {
                            $cat_authors[$authorpost->post_author]+=1;
                        }

                        arsort($cat_authors);

                        foreach($cat_authors as $key => $author_post_count) {
                        
                            $user = get_userdata($key);
                            $author_post_url=get_author_posts_url($user->ID, $user->nicename);
                            $attachment_id = $user->user_photo;
                            $image_attributes = wp_get_attachment_image_src( $attachment_id );
                            $user_post_count = count_user_posts( $user->ID , 'post' );
                        ?>
                            

                            <option <?php if ($user->ID == get_the_author_meta( 'ID' )) echo 'selected';?> value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>">
                            <?php if ($user->first_name !== "") {
                            echo $user->first_name . " " . $user->last_name;
                                } else {
                            echo $GLOBALS[ 'nameless' ];
                            } ?> 
                            (<?php echo $user->user_email; ?>)</option>
                                                    

                        <?php }
                    }
                }

            }
            ?>


        </select>
    </div>

</div>
<!--/ End Reassign /--> 