<div class="project-users-list">
    <h2><?php _e( "Users involved in this project", "taskrocket" ); ?></h2>
    <ul>
    <?php
    // Display users involved with this project
    if ($wp_query->found_posts > 0) {
        $cat_authors = array();
        
        // If on category page, get the category ID...
        if(is_category()) {
            $categories = get_the_category();
            $project_id = $categories[0]->cat_ID;
        } else { // otherwise, get the query string value.
            $project_id = $_GET['projectid'];
        }
        
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
            ?>
                
                <li class="project-user-<?php echo $user->ID; ?>">
                    <?php 
                    if (get_option('show_avatars')) {
                    echo get_avatar( $user->ID , '100');
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
                    </strong>
                    <?php global $current_user;
                    if ($user->roles[0] == "administrator") {
                        echo "<span class='admin-icon' title='" . __( "Administrator", "taskrocket" ) . "'></span>";
                    }
                    ?>
                </li>
                <?php if(is_page('single-report')) { ?>
                <script>
                    jQuery(function ($) {
                        $('.project-user-<?php echo $user->ID; ?>').click(function() { 
                            $('tbody tr').fadeOut();
                            $('.report-project-user-<?php echo $user->ID; ?>').fadeIn();
                            $('.project-users-list li').css('opacity', '.4');
                            $(this).css('opacity', '1');
                            $('.table-owner').addClass('show-all');
                        });
                    });
                </script>
                <?php } ?>
                
            <?php }
        }
    }
    ?>
    </ul>
</div>