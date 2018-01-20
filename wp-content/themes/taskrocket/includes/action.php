<?php 
    // Pretty names for task status alt attributes
    if(get_post_meta($post->ID, 'tr_status', TRUE) == "complete") {
        $task_status = __( "Complete", "taskrocket" );
    }
    if(get_post_meta($post->ID, 'tr_status', TRUE) == "incomplete") {
        $task_status = __( "Incomplete", "taskrocket" );
    }
    if(get_post_meta($post->ID, 'tr_status', TRUE) == "onhold") {
        $task_status = __( "On hold", "taskrocket" );
    }
    if(get_post_meta($post->ID, 'tr_status', TRUE) == "inprogress") {
        $task_status = __( "In progress", "taskrocket" );
    }
    
    $related_ID    	      = get_post_meta($post->ID, 'related', TRUE);
    
    // Get the TASK status of the related task
    $related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 
    
    // Get the POST status of the related task 
    $related_post_status       = get_post_status( $related_ID );
?>

<span class="action action-button" title="<?php echo $task_status; ?>">
    <em class="task-status action-<?php echo get_post_meta($post->ID, 'tr_status', TRUE); ?>"><?php echo get_post_meta($post->ID, 'tr_status', TRUE); ?>
        <em class="hide-statuses">
            <?php if(get_post_meta($post->ID, 'tr_status', TRUE) !=="complete") { 
            if(get_post_meta($post->ID, 'relation', TRUE) !== "is_blocked_by" || $related_tasks_status == 'complete' || $related_post_status == 'trash') { ?>
                <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=complete&type=task&user_ID=<?php echo $user_ID; ?>&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>" class="complete" title="<?php _e( "Complete", "taskrocket" ); ?>"><?php _e( "Complete", "taskrocket" ); ?></a>
            <?php  } else { ?>
                <a class="complete" title="<?php _e( "Complete", "taskrocket" ); ?>"><?php _e( "Complete", "taskrocket" ); ?></a>
            <?php } } ?>
            
            <?php if(get_post_meta($post->ID, 'tr_status', TRUE) !=="incomplete") { ?>
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=incomplete&type=task&user_ID=<?php echo $user_ID; ?>&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>" class="incomplete" title="<?php _e( "Incomplete", "taskrocket" ); ?>"><?php _e( "Incomplete", "taskrocket" ); ?></a>
            <?php } ?>

            <?php if(get_post_meta($post->ID, 'tr_status', TRUE) !=="onhold") { ?>
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=onhold&type=task&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>" class="onhold" title="<?php _e( "On hold", "taskrocket" ); ?>"><?php _e( "On hold", "taskrocket" ); ?></a>
            <?php } ?>
            
            <?php if(get_post_meta($post->ID, 'tr_status', TRUE) !=="inprogress") { 
            if(get_post_meta($post->ID, 'relation', TRUE) !== "is_blocked_by" || $related_tasks_status == 'complete' || $related_post_status == 'trash') { ?>
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=inprogress&type=task&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>" class="inprogress" title="<?php _e( "In progress", "taskrocket" ); ?>"><?php _e( "In progress", "taskrocket" ); ?></a>
            <?php  } else { ?>
                <a class="inprogress" title="<?php _e( "In progress", "taskrocket" ); ?>"><?php _e( "In progress", "taskrocket" ); ?></a>
            <?php } } ?>
            
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=delete_task&type=task&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>" class="delete" title="<?php _e( "Delete", "taskrocket" ); ?>"><?php _e( "Delete", "taskrocket" ); ?></a>
        </em>
    </em>
</span>