<?php if( !current_user_can('client')) { ?>


        <?php if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) { ?>
            <span class="small-button time-spent master-tooltip" title="<?php _e( "The time spent on this task so far", "taskrocket" ); ?>">
            <strong><?php _e( "Time", "taskrocket" ); ?>:</strong>
                <?php // Convert minutes into human readable hours and minutes
                    $minutes = get_post_meta($post->ID, 'logtime', TRUE);
                    $hours = floor($minutes / 60);
                    $min = $minutes - ($hours * 60);
                    echo $hours . " hrs " . $min . " mins";
                ?>
            </span>
        <?php } else { ?>
            <span class="empty">-</span>
        <?php } ?>
        
        

<?php } else { ?>

    <?php if($options['clients_see_task_times'] == true) { ?>
        
                <?php if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) { ?>
                    <span class="small-button time-spent master-tooltip" title="<?php _e( "The time spent on this task so far", "taskrocket" ); ?>">
                    <strong><?php _e( "Time", "taskrocket" ); ?>:</strong>
                        <?php // Convert minutes into human readable hours and minutes
                            $minutes = get_post_meta($post->ID, 'logtime', TRUE);
                            $hours = floor($minutes / 60);
                            $min = $minutes - ($hours * 60);
                            echo $hours . " hrs " . $min . " mins";
                        ?>
                    </span>
                <?php } else { ?>
                    <span class="empty">-</span>
                <?php } ?>
        
    <?php } ?>

<?php } ?>