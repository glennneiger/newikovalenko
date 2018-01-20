<?php
ob_start();
/*
Template Name: Clients
*/
global $wpdb;
wp_get_current_user();
global $user_ID;
$category = get_cat_id( single_cat_title("",false) );
get_header();

// Current User ID
$user_ID = get_current_user_id();

// Project ID
$projectID = $current_user->client_project;


// Check if Clients plugin is activated...
if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
    $trClientPlugin = ABSPATH . 'wp-content/plugins/taskrocket-clients/dashboard.php';

    // ... then Grab the client plugin.
    require_once($trClientPlugin); ?>

<?php // Otherwise show the read-only client view.
    } else { ?>

    <?php
    if($projectID =="") {  ?>

    <div class="content">
        <?php // Intro for clients
       	 if(current_user_can( 'client' )) { ?>
       	 	<div class="intro client-intro">
       	 		<div class="start">
       	 			<h3><?php _e( "Welcome to Task Rocket", "taskrocket" ); ?></h3>
       	 			<p><?php _e( "Hi! If you're reading this it means you haven't been assigned to any project. Please notify an administrator and then reload this page.", "taskrocket" ); ?></p>
       	 			<a href="<?php echo home_url(); ?>/" class="button"><?php _e( "Reload", "taskrocket" ); ?></a>
       	 		</div>
       	 	</div>
       	 <?php } ?>
    </div>

    <?php } else { ?>

    <!--/ Start the client view /-->
    <div class="standard-client-view">

    	<?php 
        // All tasks count
    	$all_tasks_args = array(
    		'posts_per_page' 	=> -1,
    		'post_type' 		=> 'post',
    		'post_status'		=> 'publish',
    		'cat' 				=> $projectID,
    		'meta_key'          => 'tr_status',
    		'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
    	);
    	$all_tasks_posts = new WP_Query($all_tasks_args);
    	$all_tasks = $all_tasks_posts->post_count;
        
        // All completed tasks count
    	$all_completed_tasks_args = array(
    		'posts_per_page' 	=> -1,
    		'post_type' 		=> 'post',
    		'post_status'		=> 'publish',
    		'cat' 				=> $projectID,
    		'meta_key'  		=> 'tr_status',
    		'meta_value' 		=> 'complete'
    	);
    	$all_completed_tasks_posts = new WP_Query($all_completed_tasks_args);
    	$all_completed_tasks = $all_completed_tasks_posts->post_count;
        
        // Remaining tasks
    	$remaining_tasks = $all_tasks - $all_completed_tasks;
        
        // Percentage of tasks completed so far
    	$task_percentage = ($all_completed_tasks / $all_tasks) * 100;
        ?>

    	<div class="container">

            <h1><?php echo get_the_category_by_ID($projectID); ?></h1>
            
            <p class="progress">
                <?php
                    echo str_repeat("<i></i>", 9);
                ?>
                <span class="client-progress-bar" style="width:<?php echo (round($task_percentage)) ?>%">
                	<em class="marker"><?php echo (round($task_percentage)) ?>%</em>
                </span>
            </p>

            <ul>
                <li class="total"><span><?php echo $all_tasks; ?></span> <?php _e( "Tasks total", "taskrocket" ); ?></li>
                <li class="complete"><span><?php echo $all_completed_tasks; ?></span> <?php _e( "Tasks complete", "taskrocket" ); ?></li>
                <li class="remain"><span><?php echo $remaining_tasks; ?></span> <?php _e( "Tasks remaining", "taskrocket" ); ?></li>
                <li class="percent"><span><?php echo (round($task_percentage)) ?><em>%</em></span><?php _e( "Progress", "taskrocket" ); ?></li>
                <li class="client-logout"><a href="<?php echo wp_logout_url(); ?>"><span></span><?php _e( "Logout", "taskrocket" ); ?></a></li>
            </ul>

        </div>

    </div>
    <!--/ End the client view /-->
    <?php } ?>


<?php
    }
?>

<?php get_footer(); ?>
