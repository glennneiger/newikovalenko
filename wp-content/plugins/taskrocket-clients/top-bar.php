<?php 
global $current_user;
get_currentuserinfo(); 

$cat_id                 = get_query_var( 'cat' );
$categoryslug           = get_cat_slug($cat_id);
$prevent_delete_tasks   = $options['prevent_clients_delete_tasks'];
?>
    <div class="top-bar">
        
        <span class="toggle-left-pane">
        	<em class="bar-01"></em>
        	<em class="bar-02"></em>
        	<em class="bar-03"></em>
        </span>
        
        <?php if(is_single() && ($options['clients_edit_tasks'] == true)) { ?>
        <span class="icons-toggle"></span>
        <?php } ?>
        
        <?php
            if($options['clients_see_tasks'] == true) { 
                require_once("search.php");
            }
        ?>
        
        <div class="top-icons">
            <?php if(is_single() && ($options['clients_edit_tasks'] == true)) { 
                $terms = get_the_terms( $post->ID , 'category');
                if($terms) {
                    foreach( $terms as $term ) {
                        $cat_obj = get_term($term->term_id, 'category');
                        $cat_slug = $cat_obj->slug;
                    }
                }
                $project_path = home_url() . "/" . get_option( 'category_base' ) . "/" . $cat_slug;
                if ($post->post_author == $current_user->ID) { ?>
                <span class="top-toggle edit-task master-tooltip" title="<?php _e( 'Edit Task', 'taskrocket-clients' ); ?>"><?php _e( 'Edit Task', 'taskrocket-clients' ); ?></span>
                <?php if(!$prevent_delete_tasks) { ?>
                    <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=delete_task&type=task&owner_ID=<?php echo $user_ID; ?>&redirect=<?php echo $project_path ?>" class="top-toggle delete-task master-tooltip" title="<?php _e( "Delete this task", "taskrocket" ); ?>"><?php _e( "Delete", "taskrocket" ); ?></a>
                <?php } ?>
            <?php } } ?>
            
            <?php 
            $search_results_page = $_GET["s"];
            if(!is_search()) {
            if(!is_page( array( 'client', 'account', 'my-tasks', 'new-task') )) { ?>
                
                <?php if ($options['clients_create_tasks'] == true) { ?>
                    <?php if(!is_single()) { ?>
                        <span class="top-toggle new-task master-tooltip" title="<?php _e( "Create new task for this project", "taskrocket" ); ?>"><?php _e( "Create a new task", "taskrocket" ); ?></span>
                    <?php } ?>	
                <?php } ?>	
                
                <?php if(is_category()) { ?>
                    
                    <?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
    				if ($options['show_gantt_clients_project_pages'] == true) { ?>
    				<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?>"><?php _e( "SHow / Hide the Gantt Chart", "taskrocket" ); ?></span>
    			    <?php } } ?>

                    <?php if($options['clients_add_to_cal'] == true) { 
                            if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) { 
						        require(ABSPATH . 'wp-content/plugins/taskrocket-add-to-cal/cal-links.php');
						    }
                        }
					?>
                    
                <?php } ?>
                
                <?php if(!is_single()) { ?>
                    <span class="top-toggle simple-view master-tooltip" title="<?php _e( 'Simple task view', 'taskrocket-clients' ); ?>"><?php _e( 'Simple task view', 'taskrocket-clients' ); ?></span>
                <?php } ?>
                
                <?php if($options['clients_see_attachments'] == true && !is_single()) { ?>
                    <span class="top-toggle project-attachments active master-tooltip" title="<?php _e( 'Show / Hide attachments', 'taskrocket-clients' ); ?>"><?php _e( 'Show / Hide attachments', 'taskrocket-clients' ); ?></span>
                <?php } ?>
                
                <?php if($options['clients_see_attachments'] == true && !is_single()) { ?>
                <a href="<?php echo get_template_directory_uri(); ?>/download-attachments.php?projectid=<?php echo $cat_id; ?>&referer=project&project_name=<?php echo $categoryslug; ?>-files"><span class="top-toggle download-attachments master-tooltip" title="<?php _e( 'Download all attachments in this project', 'taskrocket-clients' ); ?>"><?php _e( 'Download all attachments in this project', 'taskrocket-clients' ); ?></span></a>
                <?php } ?>
                
                <?php if($options['clients_see_team'] == true && !is_single()) { ?>
                    <span class="top-toggle project-users master-tooltip" title="<?php _e( 'Show / Hide users in Project', 'taskrocket-clients' ); ?>"><?php _e( 'Show / Hide users in Project', 'taskrocket-clients' ); ?></span>
                <?php } ?>
                
                <?php if($options['clients_see_project_details'] == true && !is_single()) { ?>
                    <span class="top-toggle project-details master-tooltip" title="<?php _e( 'Show / Hide Project Details', 'taskrocket-clients' ); ?>"><?php _e( 'Project Details', 'taskrocket-clients' ); ?></span>
                <?php } ?>
                
            <?php } } ?>
            
            <?php if ($options['dash_message']) { ?>
                <span class="top-toggle dash-message-top <?php if(!is_home()) { echo "dash-message-top-not-home"; } ?>" title="<?php _e( 'Message', 'taskrocket-clients' ); ?>"></span>
            <?php } ?>
        
        </div>
        
        <span class="todays-date"><?php $date_format = get_option('date_format'); echo date($date_format); ?></span>

    </div>

    <?php if ($options['clients_dash_message']) { ?>
    <div class="dash-message message <?php echo $options['clients_dash_color']; ?>">
        <p><?php echo stripslashes($options['clients_dash_message']); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>