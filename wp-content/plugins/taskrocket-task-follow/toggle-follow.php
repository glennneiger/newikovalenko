<?php 
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

global $userdata; wp_get_current_user();
global $wpdb;

$user_id   = $_GET['user_ID'];
$task_id   = $_GET['task_ID'];
$task_name = $_GET['task_name'];
$action    = $_GET['action'];
$source    = $_GET['source'];

if( get_permalink( $task_id ) ) { // If post exists (to prevent manual ID fluking)

    // If the action is 'follow', add to the database
    if($action == 'follow') {
        $wpdb->insert( 
        	$wpdb->prefix . 'tr_follows', 
        	array( 
        		'user_id' => $user_id, 
        		'task_id' => $task_id,
                'slug'    => $task_name 
        	), 
        	array( 
        		'%s', 
        		'%d' 
        	) 
        );
        header("Location: " . home_url() . "/" . $task_name . "?following=yes");
        
        // Otherwise if the action is 'unfollow', remove it from the database
    } else if($action == 'unfollow') {
        
        $wpdb->delete( 
        	$wpdb->prefix . 'tr_follows', 
        	array( 
        		'user_id' => $user_id, 
        		'task_id' => $task_id 
        	), 
        	array( 
        		'%s', 
        		'%d' 
        	) 
        );
        
        if($source !== "dashboard") {
            header("Location: " . home_url() . "/" . $task_name . "?following=no");
        }
    }
    
} // End if post exists.