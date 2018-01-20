<?php 
$related_ID           = get_post_meta($post->ID, 'related', TRUE);
$related_title        = get_the_title($related_ID);

// Get the TASK status of the related task
$related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 

// Get the POST status of the related task 
$related_post_status       = get_post_status( $related_ID );

if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to") {
    $relation = __( "Relates to", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with") { 
    $relation = __( "Has issues with", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") { 
    $relation = __( "Is blocked by", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to") { 
    $relation = __( "Is similar to", "taskrocket" );
}