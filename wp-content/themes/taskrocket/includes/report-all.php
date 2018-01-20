<?php
// Show all projects and tasks.
$cat_args = array(
    'orderby' 		=> 'name',
    'hide_empty'	=> $hide_empty,
    'order' 		=> 'ASC'
);
$categories = get_categories($cat_args);

$i = 0;
// The tasks to be listed
foreach($categories as $category) {
    
    $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
    
    $cat_ID = $category->cat_ID;
    
    $project_args = array(
        'showposts' 		=> -1,
        'category__in' 		=> $cat_ID,
        'post_status' 		=> 'publish', // Condition based on setting.
        'orderby' 			=> 'title',
        'order' 			=> 'ASC',
        'caller_get_posts' 	=> 1
    );
    $posts = get_posts($project_args);


    if ($posts) {
     // Count all the tasks
    $all_tasks_args = array(
        'posts_per_page' 	=> -1,
        'post_type' 		=> 'post',
        'post_status'		=> 'publish',
        'cat'			 	=> $cat_ID,
        'meta_key'          => 'tr_status',
        'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
    );
    $all_tasks_posts_project = new WP_Query($all_tasks_args);
    $all_tasks = $all_tasks_posts_project->post_count;
    
    // Complete tasks
    $completed_tasks_project_args = array(
        'posts_per_page' 	=> -1,
        'post_type' 		=> 'post',
        'post_status'		=> 'publish',
        'cat'			 	=> $cat_ID,
        'meta_key'  		=> 'tr_status',
        'meta_value' 		=> 'complete'
    );
    $completed_tasks_posts_project = new WP_Query($completed_tasks_project_args);
    $completed_tasks = $completed_tasks_posts_project->post_count;
    
    $remaining_tasks = $all_tasks - $completed_tasks;



    // Fix division by zero.
    if ($all_tasks !== 0) {
        $percentage_complete = ($completed_tasks / $all_tasks) * 100;
    } else {
        $percentage_complete = 0;
    }
    

    $task_count = $category->category_count;
    if($task_count == 1) {
        $grammar = " remains";
    } else {
        $grammar = " remain";
    }
    

    // Colours for the progress bars.
    if ($percentage_complete <= 25) {
        $colour = $GLOBALS[ 'red' ];
            } else
        if ($percentage_complete > 25 && $percentage_complete <= 50) {
            $colour = $GLOBALS[ 'orange' ];
            } else
        if ($percentage_complete > 50 && $percentage_complete <= 75) {
        $colour = $GLOBALS[ 'yellow' ];
            } else
        if ($percentage_complete > 75) {
        $colour = $GLOBALS[ 'green' ];
    }

    // Percentage
    $percentage = round($percentage_complete, 0);

    // Decsription
    $description = strip_tags(category_description( $cat_ID ));

    // Date stuff
    $old_start_date_format = get_option( 'tr_start_date_' . $cat_ID );
    $new_start_date_format = new DateTime($old_start_date_format);

    $old_end_date_format = get_option( 'tr_end_date_' . $cat_ID );
    $new_end_date_format = new DateTime($old_end_date_format);

    $late = get_option( 'tr_end_date_' . $cat_ID );
    $date_format = get_option('date_format');

    if (!$project_archived) {
?>

    <div id="<?php echo $category->category_nicename; ?>" class="report-item <?php echo "row-" . ($i++ % 2); ?> <?php if($percentage == 100) { echo "project-is-complete"; } else { echo "project-is-incomplete"; } ?>">
            <a href="<?php echo home_url(); ?>/single-report/?projectid=<?php echo $cat_ID; ?>&referer=reports" class="project-name"><?php echo $category->name; ?><span class="button-small"><?php _e( "Report", "taskrocket" ); ?></span></a>
            
            <?php if($description !=="") { ?>
            <p class="description"><?php echo $description; ?></p>
            <?php } ?>
                
                <p class="task-details">
                <?php if ($cat_ID !== 1) { ?>
                    
                    <span class="tasks">
                        <strong><?php _e( "Tasks", "taskrocket" ); ?>:</strong> <?php printf( __( '%1$d of %2$d complete, %3$d outstanding' ), $completed_tasks, $all_tasks, $remaining_tasks); ?>
                    </span>
                    
                    <?php if(get_option( 'tr_start_date_' . $cat_ID )) { ?>
                    <span class="schedule">
                        <strong><?php _e( "Schedule", "taskrocket" ); ?>:</strong> <?php echo $new_start_date_format->format($date_format); ?> &#10140; <?php echo $new_end_date_format->format($date_format); ?>
                    </span>
                    <?php } ?>
                    
                    <?php // Job number
                    if(get_option( 'tr_job_number_' . $cat_ID )) { ?> 
                    <span class="jobnum">
                        <strong><?php _e( "Job", "taskrocket" ); ?> #:</strong> <?php echo get_option( 'tr_job_number_' . $cat_ID ); ?>
                    </span>
                    <?php } ?>
                
                    <?php 
                    $pm = get_option( 'tr_project_manager_' . $cat_ID);
                    if($pm !="") { ?>
                        <span class="pm">
                        <strong><?php 
                            /* translators: PM refers to Project Manager */
                            _e( "PM", "taskrocket" ); ?>:</strong>    
                        <?php
                            $user_info = get_userdata($pm);
                            echo $user_info->user_firstname . " " . $user_info->user_lastname;
                        ?>
                        </span>
                    <?php } ?>
                    
                <?php } ?>
                </p>
        
        <?php if ($cat_ID !== 1) { ?>
            <span class="percent <?php if($percentage == 100) { echo "complete"; } ?>">
                <?php echo $percentage; ?><em>%</em>
            </span>
            <?php if($completed_tasks !==0) { ?>
                <div class="progress-bar" title="<?php echo round($percentage_complete, 0); ?>% <?php _e( "Progress", "taskrocket" ); ?>">
                    <div class="bar" style="width:<?php echo round($percentage_complete, 0); ?>%; background:#<?php echo $colour; ?>"></div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <span class="percent">
                <?php echo $remaining_tasks; ?><em> <?php _e( "Tasks", "taskrocket" ); ?></em>
            </span>
        <?php } ?>
    </div>

    
<?php }
    } 
}
?>