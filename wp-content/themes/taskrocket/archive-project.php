<?php 
    ob_start();
    define('WP_USE_THEMES', false);


        if ( !function_exists( 'get_home_path' ) ) {
            require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
            include_once(ABSPATH . 'wp-includes/pluggable.php');
        }
        
        if( !current_user_can('client')) { // If not a client
        
        if ($options['archive_projects'] == true || current_user_can( 'manage_options' )) {
            global $post;
            $location        = $_GET["location"];
            $project_ID      = $_GET["project_ID"];
            $project_action  = $_GET["project_action"];
            $project_name    = $_GET["project_name"];
            
            ///////////////////////////////////////////
            // Archive Project Handling
            ///////////////////////////////////////////
            if($project_action == "archive") {
                
                $archive_project = 'tr_project_archived_' . $project_ID;
                update_option( $archive_project, '1' );
                
                header("Location: " . home_url() . "/projects?archived=yes&project_name=" . $project_name . "&project_ID=" . $project_ID);
            } else 
            
            if($project_action == "undo-archive") {
                
                $undo_archive_project = 'tr_project_archived_' . $project_ID;
                update_option( $undo_archive_project, '' );
                
                header("Location: " . home_url() . "/projects?unarchived=yes&project_name=" . $project_name . "&project_ID=" . $project_ID);
            }
        }
    }  else { // Otherwise...
        echo '<div style="width:100px; height:100px; display:block; position: absolute; top: 50%; transform: translateY(-50%); left: calc(50% - 50px); background:url(' . get_template_directory_uri() . '/images/sprite.png); background-position:-1800px 0;"></div>';
    	exit;
    } // End if not a client
    ob_end_clean();
?>