v<?php if(!current_user_can( 'client' ) || $options['clients_see_project_details'] == true) { ?>
<div class="project-details-pane">

    <?php // Category ID
        $categories       = get_the_category();
        $category         = $categories[0]->cat_ID;
        $project_rate 	  = get_option( 'tr_hourly_rate_' . $cat_id );
        $standard_rate 	  = $options['rate'];
    ?>

    <div class="project-scroll">

        <h1>
            <?php // Show title in different way depending on role.
            if(current_user_can( 'client' )) {
                 echo get_cat_name($category);
            } else {
                single_cat_title();
            }?>
        </h1>
        
        <?php if(get_option( 'tr_job_number_' . $category)) { ?>
        <p>
            <span><?php _e( "Job number", "taskrocket" ); ?></span>
            <?php echo get_option( 'tr_job_number_' . $category); ?>
        </p>
        <?php } ?>
        
        <?php // Project hourly rate
            if($project_rate) { 
                $the_rate = $currency_symbol . $project_rate;
            } else if($standard_rate) {
                $the_rate = $currency_symbol . $standard_rate;
            } else {
                $the_rate = $currency_symbol . "0";
            } ?>
        <p>
            <span><?php _e( "Rate", "taskrocket" ); ?></span>
            <?php echo $the_rate; ?>/<?php _e( "hr", "taskrocket" ); ?>
        </p>
        
        <?php $pm_specified = get_option( 'tr_project_manager_' . $category); ?>
        <?php if($pm_specified == "") { ?>
            <p class="pm-box"><span><?php _e( "Project Manager", "taskrocket" ); ?></span>
            <?php _e( "No Project Manager.", "taskrocket" ); ?>
            <?php if ( current_user_can( 'manage_options' ) ) { ?>
            <?php 
            $assign_pm_url = admin_url() . "term.php?action=edit&taxonomy=category&tag_ID=" . $category . "#pm_pos";
            printf( __( 'Do you need to <a href="%s" target="_blank">assign a PM?</a>', 'taskrocket' ), $assign_pm_url); ?>
            
            <?php } ?>
            </p>
        <?php } else { ?>
            <p class="pm-box">
            <span><?php _e( "Project Manager", "taskrocket" ); ?></span>
            <?php
                $user_info = get_userdata($pm_specified);
                if ($options['show_gravatars'] == true) {
                    echo get_avatar( $user_info, 64 );
                }
                echo '<a href="mailto:' . $user_info->user_email . '">' . $user_info->user_firstname . " " . $user_info->user_lastname . '</a>';
            ?>
            <?php if ( current_user_can( 'manage_options' ) ) { ?>
                <a href="<?php echo admin_url(); ?>term.php?action=edit&taxonomy=category&tag_ID=<?php echo $category; ?>#pm_pos" target="_blank" class="change">(<?php _e( "Change", "taskrocket" ); ?>)</a>
            <?php } ?>
            </p>
        <?php } ?>
        <?php // If user is a client, the category ID is actually the project ID.
        if(current_user_can( 'client' )) {
            $category_id = $category;
        }
        if(category_description( $category_id ) !="") { ?>
            <p>
                <span><?php _e( "Description", "taskrocket" ); ?></span>
                <?php echo strip_tags(category_description( $category_id )); ?>
            </p>
        <?php } else { ?>
            <p>
                <span><?php _e( "Description", "taskrocket" ); ?></span>
                <?php _e( "No description provided.", "taskrocket" ); ?>
                <?php if ( current_user_can( 'manage_options' ) ) { ?>
                    <?php $add_description_url = admin_url() . "term.php?action=edit&taxonomy=category&tag_ID=" . $category;
                    printf( __( 'Do you need to <a href="%s" target="_blank">add a description?</a>', 'taskrocket' ), $add_description_url); ?>
                <?php } ?>
            </p>
        <?php } ?>

        <?php if(get_option( 'tr_details_' . $category)) { ?>

            <div class="project-description">
                <span class="det-desc"><?php _e( "More details", "taskrocket" ); ?></span>
                <pre>
                    <?php // Return string with Links, if condition is met.
                    if ($options['disable_make_clickable'] == false) {
                        $string = get_option( 'tr_details_' . $category ); echo make_clickable( $string );
                    } else {
                        echo get_option( 'tr_details_' . $category );
                    } ?>
                </pre>
            </div>

        <?php } else { ?>
            <p>
                <span><?php _e( "More details", "taskrocket" ); ?></span>
                <?php _e( "No Details provided.", "taskrocket" ); ?>
                <?php if ( current_user_can( 'manage_options' ) ) { ?>
                    <?php $add_details_url = admin_url() . "term.php?action=edit&taxonomy=category&tag_ID=" . $category;
                    printf( __( 'Do you need to <a href="%s" target="_blank">add more details?</a>', 'taskrocket' ), $add_details_url); ?>
                <?php } ?>
            </p>
        <?php } ?>

    </div>

</div>
<?php } ?>
