<?php 
    ob_start();

    define('WP_USE_THEMES', false);

    if ( !function_exists( 'get_home_path' ) ) {
        require_once( dirname(__FILE__) . '/../../../../wp-blog-header.php' );
    }

    global $post;
    $user_ID         = $current_user->ID;
    $task_ID         = $_GET["task_ID"];
    $task_URL        = $_GET["task_URL"];
    $action          = $_GET["action"];
    
    // Check to see if the post author is 0.
    // When the post author is 0 it means it is not owned by anyone and therefor can be claimed.
    $post = get_post($task_ID);
    $task_author_ID = $post->post_author;
    
    if($task_author_ID == "0") {
        $claim_task = array(
            'ID'            => $task_ID,
            'post_author'   => $user_ID
        );
        wp_update_post( $claim_task );
    
    }
    
    // If undo....
    if($action == "undo") {
        $claim_task = array(
            'ID'            => $task_ID,
            'post_author'   => "0000000"
        );
        wp_update_post( $claim_task );
    }

    // If claiming a task from the 'Unowned Tasks' page...
    if($task_URL == "none") {
        
        exit; // ...Exit.
        
    } else {
        
        // Otherwise, redirect with appropriate query string.
        if($task_author_ID == "0") {
            header("Location: " . $task_URL . "?taken=yes");
        } else {
            header("Location: " . $task_URL . "?taken=failed");
        }
        
    }

    

    ob_end_clean();
?>