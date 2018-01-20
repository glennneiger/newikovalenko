<?php 
    ob_start();

    define('WP_USE_THEMES', false);

    if ( !function_exists( 'get_home_path' ) ) {
        require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
    }

    global $post;
    $user_ID         = $current_user->ID; // ID of the current user.
    $task_ID         = $_GET["task_ID"];
    $task_name       = $_GET["task_name"];
    $task_owner      = $_GET["task_owner"];
    $task_owner_ID   = get_post_field( 'post_author', $task_ID ); // Get the author ID of the task owner.
    $type            = $_GET["type"];
    $location        = $_GET["location"];
    $redirect        = $_GET["redirect"];
    $project_action  = $_GET["project_action"];
    $project_name    = $_GET["project_name"];
    $action          = $_GET["action"];
    $relation        = $_GET["relation"];
    $elaboration     = $_GET["elaboration"];
    
    $GLOBALS['project_ID'] = $_GET["project_ID"]; // Needs to be set as a global otherwise it won't work within a function.
    
    // If the project manager has same ID as current user
    if ($options['pm_modify_tasks'] == true) {
        $project_manager = get_option( 'tr_project_manager_' . $GLOBALS['project_ID'] );
    } else {
        $project_manager = 0;
    }
    
    ///////////////////////////////////////////
    // Task Status Handling
    ///////////////////////////////////////////
    if($type == "task") {
        if($user_ID == $task_owner_ID || $user_ID == $project_manager || current_user_can( 'manage_options' )) {
            
            // First check that current user ID is same ID of the task owner - to prevent shenanigans.
            if($action == "complete") {
                update_post_meta( $task_ID, 'tr_status', sanitize_text_field( $action ) );
                
                if($relation == "none") {
                    update_post_meta( $task_ID, 'relation', sanitize_text_field( "" ) );
                    update_post_meta( $task_ID, 'related', sanitize_text_field( "" ) );
                    update_post_meta( $task_ID, 'elaboration', sanitize_text_field( "" ) );
                }
                
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                if($options['no_emails'] == false) {
                    require($GLOBALS[ 'theme_includes' ] . "task-status-notify.php");
                }
                header("Location: " . $location . "/?updated=yes&user=$task_owner_ID&task_ID=$task_ID");
            }
            
            // Mark task as incomplete
            if($action == "incomplete") {
                update_post_meta( $task_ID, 'tr_status', sanitize_text_field( $action ) );
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                if($options['no_emails'] == false) {
                    require($GLOBALS[ 'theme_includes' ] . "task-status-notify.php");
                }
                header("Location: " . $location . "/?incomplete=yes&user=$task_owner_ID&task_ID=$task_ID");
            }
            
            // Delete a task
            if($action == "delete_task") {
                wp_trash_post($task_ID);
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                if($redirect) {
                    header("Location: " . $redirect . "/?deleted=yes&task_ID=$task_ID");
                } else {
                    header("Location: " . $location . "/?deleted=yes&task_ID=$task_ID");
                }
                
            }
            
            // Un-delete a task
            if($action == "undelete") {
                wp_publish_post($task_ID);
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                header("Location: " . $location . "/?undeleted=yes&user=$task_owner_ID&task_ID=$task_ID");
            }
            
            // Mark task in progress
            if($action == "inprogress") {
                update_post_meta( $task_ID, 'tr_status', sanitize_text_field( $action ) );
                
                if($relation == "none") {
                    update_post_meta( $task_ID, 'relation', sanitize_text_field( "" ) );
                    update_post_meta( $task_ID, 'related', sanitize_text_field( "" ) );
                    update_post_meta( $task_ID, 'elaboration', sanitize_text_field( "" ) );
                }
                
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                if($options['no_emails'] == false) {
                    require($GLOBALS[ 'theme_includes' ] . "task-status-notify.php");
                }
                header("Location: " . $location . "/?inprogress=yes&user=$task_owner_ID&task_ID=$task_ID");
            }
            
            // Mark task on hold
            if($action == "onhold") {
                update_post_meta( $task_ID, 'tr_status', sanitize_text_field( $action ) );
                $the_post = array(
                      'ID'               => $task_ID,
                      'post_modified'    => the_modified_time()
                );
                wp_update_post($the_post);
                if($options['no_emails'] == false) {
                    require($GLOBALS[ 'theme_includes' ] . "task-status-notify.php");
                }
                header("Location: " . $location . "/?onhold=yes&user=$task_owner_ID&task_ID=$task_ID");
            }
        }
    }
    // End if type is a task
    
    
    ///////////////////////////////////////////
    // Delete Project Handling
    ///////////////////////////////////////////
    // If coming from the projects page.
    // a. Move all associated tasks into the trash.
    // b. Delete the category.
    // c. Redirect back to the projects page.
    if($project_action == "delete") {

        // a) Convert all posts in the category to 'trash' status.
        function wp_trash_posts($trash_projects_tasks_params = null) {
        $trash_projects_tasks_args = array(
            'nopaging'     => true,
            'post_status'  => 'publish', 
            'cat'	       => $GLOBALS['project_ID']
        );

        $trash_projects_tasks_query = new WP_Query( $trash_projects_tasks_args );
        if ( $trash_projects_tasks_query->have_posts() ) {
        
                while ( $trash_projects_tasks_query->have_posts() ) {
                    $trash_projects_tasks_query->the_post();
                    $updated = wp_update_post( array('ID' => $trash_projects_tasks_query->post->ID, 'post_status' => 'trash' ));
                }
            }
        }
        wp_trash_posts();
        
        // b) delete the category (project)
        wp_delete_term($GLOBALS['project_ID'], 'category');
        
        // c) Redirect back to the projects page.
        header("Location: " . home_url() . "/projects?deleted=yes&project_name=" . $project_name);
    }
    
    ob_end_clean();
?>