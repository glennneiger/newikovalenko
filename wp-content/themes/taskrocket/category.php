<?php
// If the user role is 'client' redirect to the client page.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
    if( current_user_can('client')) {
        header('Location: '.home_url().'/client');
        exit();
    }
}

$cat_id = get_query_var('cat');
$thecat = get_category ($cat_id);
$categoryslug = $thecat->slug;

$project_archived = get_option( 'tr_project_archived_' . $cat_id );
if($project_archived) {
    echo '<div style="padding:20px;text-align:center; font-family:arial; font-size:14px; color:#606777; background:#f3f3e7; width:400px; position: absolute; top: 50%; transform: translateY(-50%); left: calc(50% - 200px); border: solid 1px #eaeade; border-radius: 2px;"><strong style="display: block; margin: 0 0 15px 0;">' . __( "This project has been archived.", "taskrocket" ) . '</strong> ' . __( "If you require access to this project an administrator will need to pull it from the archives.", "taskrocket" ) .'</div>';
	exit;
}


// Current author
if (is_user_logged_in()) { $current_user = wp_get_current_user(); }
global $wp;
global $userdata; wp_get_current_user();
$author = get_current_user_id();

// Prevent client from seeing projects they don't have access to
if( current_user_can('client')) {
    $client_projects = get_user_meta( $author, 'client_project', true );
    if (is_array($client_projects) && in_array($cat_id, $client_projects)) {
        // Do nothing...
    } else {
        header('Location: ' . home_url() . '/client');
        exit;
    }
}

require_once($GLOBALS[ 'theme_includes' ] . "cookie-control.php");

$currentLink = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

get_header();

$current_url = home_url(add_query_arg(array(),$wp->request));
$current_user_id = $current_user->ID;
$options = get_option( 'taskrocket_settings' );

$tab_1 = $userdata->tab_1;
$tab_2 = $userdata->tab_2;
$tab_3 = $userdata->tab_3;
$tab_4 = $userdata->tab_4;

// If the project manager has same ID as current user
if ($options['pm_modify_tasks'] == true) {
    $project_manager = get_option( 'tr_project_manager_' . $cat_id );
} else {
    $project_manager = 0;
}
?>

<!--/ Start Content /-->
<div class="content">
    <div class="container">

        <!--/ Start Project Messages /-->
        <?php require_once($GLOBALS[ 'theme_includes' ] . "messages-task-statuses.php"); ?>
        <!--/ End Project Messages /-->

        <h1><?php single_cat_title(); ?></h1>

        <!--/ Start Project Data /-->
        <?php require_once($GLOBALS[ 'theme_includes' ] . "project-data.php"); ?>
        <!--/ End Project Data /-->
        
        <?php if(!current_user_can( 'client' )) {
        if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
            if ($options['show_gantt_project_pages'] == true) {
                if($cat->cat_ID !== 1) { ?>
            <div class="gantt"></div>
            <?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
        <?php } } } } ?>
        
        <?php if(current_user_can( 'client' )) {
        if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
            if ($options['show_gantt_clients_project_pages'] == true) {
                if($cat->cat_ID !== 1) { ?>
            <div class="gantt"></div>
            <?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
        <?php } } } } ?>

        <!--/ Start Toolbar /-->
        <?php require_once($GLOBALS[ 'theme_includes' ] . "project-toolbar.php"); ?>
        <!--/ End Toolbar /-->

        <?php if ($options['users_create_tasks'] == true || $options['clients_create_tasks'] == true || current_user_can( 'manage_options' )) {
            require_once($GLOBALS[ 'theme_includes' ] . "add-task-form.php");
        } ?>


        <!--////////////  Start Tasks List ////////////-->
        <div class="all-tasks-list">

            <?php // If the user has not set any preferences, then use this default set
                if($tab_1 == "") {
                    // Only output the MAT task list if the clints has a task
    				if (!current_user_can( 'client' )) {
                        require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                    } else if ($my_tasks > 0) {
                        require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                    }
                    // Only output the MCT task list if the clints has a completed task
                    if (!current_user_can( 'client' )) {
                        require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
                    } else if ($my_completed_tasks > 0) {
    					require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
    				}
                    
                    if(current_user_can( 'client' )) { // If the uset is a client
                        if($options['clients_see_tasks'] == "1") { 
                            require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
                        }
                    } else { // Otherwise, user must be an editor or administrator
                        if($options['users_only_see_own_tasks'] !== "1" || current_user_can( 'manage_options' )) { 
                            require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
                        }
                    }
                
                // Otherwise, they must have set a preference, so do this instead:
                } else { 
                
                // Tab 1
                if($tab_1 =="mat") {
                    if ($wp_query->found_posts > 0) {
                        require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                    }
    			} else if($tab_1 =="mct") {
    				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
    			} else if($tab_1 =="aat") {
                    if ($wp_query->found_posts > 0) {
                        require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                    }
    			} else if($tab_1 =="act") {
    				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
    			} 
                
    			 // Tab 2
                 if($tab_2 =="mat") {
                     if ($wp_query->found_posts > 0) {
                         require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                     }
     			} else if($tab_2 =="mct") {
     				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
     			} else if($tab_2 =="aat") {
                     if ($wp_query->found_posts > 0) {
                         require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                     }
     			} else if($tab_2 =="act") {
     				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
     			} 
                
                
                if(current_user_can( 'client' )) { // If the uset is a client
                    if($options['clients_see_tasks'] == "1") { 
                        // Tab 3
                        if($tab_3 =="mat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                            }
            			} else if($tab_3 =="mct") {
            				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
            			} else if($tab_3 =="aat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            }
            			} else if($tab_3 =="act") {
            				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
            			} 
       				 // Tab 4
                        if($tab_4 =="mat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                            }
            			} else if($tab_4 =="mct") {
            				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
            			} else if($tab_4 =="aat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            }
            			} else if($tab_4 =="act") {
            				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
            			} 
                    }
                } else { // Otherwise, user must be an editor or administrator
                    if($options['users_only_see_own_tasks'] !== "1" || current_user_can( 'manage_options' )) { 
                        // Tab 3
                        if($tab_3 =="mat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                            }
            			} else if($tab_3 =="mct") {
            				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
            			} else if($tab_3 =="aat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            }
            			} else if($tab_3 =="act") {
            				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
            			} 
       				 // Tab 4
                        if($tab_4 =="mat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "my-active-tasks.php");
                            }
            			} else if($tab_4 =="mct") {
            				require_once($GLOBALS[ 'theme_includes' ] . "my-completed-tasks.php");
            			} else if($tab_4 =="aat") {
                            if ($wp_query->found_posts > 0) {
                                require_once($GLOBALS[ 'theme_includes' ] . "all-active-tasks.php");
                            }
            			} else if($tab_4 =="act") {
            				require_once($GLOBALS[ 'theme_includes' ] . "all-completed-tasks.php");
            			} 
                    }
                }
    			
				 
    		}
            ?>

        </div>
        <!--////////////  End Tasks List ////////////-->
    </div>
</div>
<!--/ End Content /-->

<script>
    jQuery('.show-relationship, .relationship em').click(function () {
        jQuery( event.target ).closest('div').find('.relationship-details').fadeIn();
    });
    jQuery('.relationship-details .button-small').click(function () {
        jQuery( event.target ).closest('.relationship-details').fadeOut();
    });
</script>

<!--/ Start Project Details Pane /-->
<?php require_once($GLOBALS[ 'theme_includes' ] . "project-details-pane.php"); ?>
<!--/ End Project Details Pane /-->

<iframe id="deletey" name="deletey" width="0" height="0" frameborder="0"></iframe>


<?php get_footer(); ?>
