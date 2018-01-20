<!--/ Start Search /-->
<div class="searchform">
    <form method="get" action="<?php echo home_url(); ?>/" id="searchform">
        <label class="searchform-label"><?php _e( "Search", "taskrocket" ); ?></label>
        <fieldset>
            <input type="text" placeholder="<?php _e( "Search", "taskrocket" ); ?>" value="" name="s" id="s" title="<?php _e( "Search", "taskrocket" ); ?>" required />
            <input type="hidden" name="cat" value="
            <?php
                // Code for list of projects for currently logged in user.
                if(current_user_can( 'client' )) {
                $userID = get_current_user_id();
                $cat_base = get_option( 'category_base' );
                $client_project = get_user_meta( $userID, 'client_project', true );
                if (is_array($client_project)) {
                    foreach ($client_project as $project_ID) : 
                        $project = get_category( $project_ID );
                        $project_archived = get_option( 'tr_project_archived_' . $project_ID );
                        echo $project_ID . ',';
                    endforeach;
                    }
                }
            ?>
            " />
        </fieldset>
        <!--/ End Advanced Search Selection /-->
    </form>
</div>
<!--/ End Search /-->