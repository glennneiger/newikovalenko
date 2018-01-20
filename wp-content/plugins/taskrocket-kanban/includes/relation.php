<?php if(get_post_meta($post->ID, 'relation', TRUE) && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
    <span class="task-relation">
        <?php _e( "This task", "taskrocket" ); ?> <?php echo $relation; ?> <a href="<?php echo get_the_permalink($related_ID); ?>"><?php echo $related_title; ?></a>
    </span>
<?php } ?>