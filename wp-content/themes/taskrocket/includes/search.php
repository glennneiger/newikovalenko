<!--/ Start Search /-->
<div class="searchform">
    <form method="get" action="<?php echo home_url(); ?>/" id="searchform">
        <label class="searchform-label"><?php _e( "Search", "taskrocket" ); ?></label>
        <fieldset>
            <input type="text" placeholder="<?php _e( "Search", "taskrocket" ); ?>" value="" name="s" id="s" title="<?php _e( "Search", "taskrocket" ); ?>" required />
            
            <?php if(!current_user_can( 'client' )) { ?>
            <!--/ Start Advanced Search Selection /-->
            <div class="advanced-cats">
                <label class="initially-active active">
                    <input type="radio" name="cat" value="" checked="checked" />
                    <span><?php _e( "Search in all projects", "taskrocket" ); ?></span>
                </label>
                <label class="">
                    <input type="radio" name="cat" value="0" />
                    <span><?php _e( "Search attachments only", "taskrocket" ); ?></span>
                </label>
                <label class="sep"></label>
                <?php 
                
                $args = array(
                    'orderby'            => 'name',
                    'order'              => 'ASC',
                    'hide_empty'		 => 0
                    );
                    $categories = get_categories($args);
                    foreach ($categories as $category) : 
                        $project = get_category( $category->cat_ID ); 
                        $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
                        if (!$project_archived) { ?>
                        <label><input type="radio" name="cat" value="<?php echo $category->cat_ID; ?>" /><?php echo $category->name; ?></label>
                        <?php
                        }
                    endforeach;
                ?>
            </div>
            <?php } ?>
            
            <?php if(current_user_can( 'client' )) { ?>
            <input type="hidden" name="cat" value="<?php echo $userdata->client_project ?>" />
            <?php } ?>
            
        </fieldset>
        <!--/ End Advanced Search Selection /-->
    </form>
</div>
<!--/ End Search /-->