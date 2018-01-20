<?php
/*
Template Name: Single Report
*/
// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: ' . home_url() . '/client');
	exit();
}
require_once("wp-admin/includes/taxonomy.php");

get_header();
global $wp;

$options = get_option( 'taskrocket_settings' );
$date_format  = get_option('date_format');

$current_url = home_url(add_query_arg(array(),$wp->request));
$options = get_option( 'taskrocket_settings' );

// Conditionally show completed/un-tasked projects when option is enabled.
if($options['show_complete_projects_report'] == true) {
	$hide_empty = 0;
} else {
	$hide_empty = 1;
}

$project_ID = $_GET['projectid'];
$term = get_term( $project_ID );
$project_slug = $term->slug;

// Complete tasks
$all_completed_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'cat' 				=> $project_ID,
	'meta_key'  		=> 'tr_status',
	'meta_value' 		=> 'complete'
);
$all_completed_tasks_posts = new WP_Query($all_completed_tasks_args);
$all_completed_tasks = $all_completed_tasks_posts->post_count;

// All tasks count
$all_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'cat' 				=> $project_ID,
	'meta_key'          => 'tr_status',
	'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
);
$all_tasks_posts = new WP_Query($all_tasks_args);
$all_tasks = $all_tasks_posts->post_count;


// Incomplete tasks
$incomplete_tasks = $all_tasks - $all_completed_tasks; 

// Decsription
$description = strip_tags(category_description( $project_ID ));

// Project Manager
$pm = get_option( 'tr_project_manager_' . $project_ID);
$user_info = get_userdata($pm);

// Dates
$old_start_date = get_option( 'tr_start_date_' . $project_ID);
$new_start_date = new DateTime($old_start_date);

$old_due_date = get_option( 'tr_end_date_' . $project_ID);
$new_due_date = new DateTime($old_due_date);

// Job number
$job_number = get_option( 'tr_job_number_' . $project_ID);

// Allocated time
$allocated_time = get_option( 'tr_hrs_allocated_' . $project_ID);
if($allocated_time > 0) {
	if($allocated_time == 1) {
		$s = "hr";
	} else {
		$s = "hrs";
	}
}

// Time used

$args = array(
	'numberposts' 	=> -1,
	'offset' 		=> 0,
	'post_status' 	=> 'publish',
	'category' 		=> $project_ID
);
$alltimes = get_posts( $args );

$total = 0;
foreach( $alltimes as $logtimeID ) {
	$single = get_post_meta( $logtimeID->ID, 'logtime', true );
	$total += $single;
}

$minutes = $total;
$thours = floor($minutes / 60);
$tmins = $minutes - ($thours * 60);
$total_minutes = $allocated_time * 60;


$time_in_minutes = $allocated_time * 60;
$currency_symbol = $options['currency_symbol'];

// Project hourly rate
$standard_rate = $options['rate'];
$project_rate = get_option( 'tr_hourly_rate_' . $project_ID );
if($project_rate) { 
	$the_rate = $project_rate;
} else if($standard_rate) {
	$the_rate = $standard_rate;
} else {
	$the_rate = 0;
}

// Cost
$cost_in_hours = $minutes / 60;
$cost = $cost_in_hours * $the_rate;

$budget = $allocated_time * $the_rate;


// Alerts
if($allocated_time > 0) { // Only use the next condition if there is allocated time
	if($cost > $budget || $total > $total_minutes) {
		$alert = "alert";
		$overby = round($cost, 2) - round($budget, 2);
		$overby_text = "(over by $" . $overby . ")";
	}
}

// Time remaining
// Conditional because we can only show remaining time if the project has allocated time.
if($allocated_time > 0) {
	$remaining_time = $time_in_minutes - $total;

	$rminutes = $remaining_time;
	$rhours = floor($rminutes / 60);
	$rmin = $rminutes - ($rhours * 60);

	$new_timeallocated = $allocated_time * 60;
}



// Remaining tasks
$remaining_tasks = $all_tasks - $all_completed_tasks;

// Percentage of tasks completed so far
$task_percentage = ($all_completed_tasks / $all_tasks) * 100;

if ($task_percentage <= 25) {
	$colour = $GLOBALS[ 'red' ];
}	
if ($task_percentage > 25 && $inverse <= 50) {
	$colour = $GLOBALS[ 'orange' ];
}
if ($task_percentage > 50 && $inverse <= 75) {
	$colour = $GLOBALS[ 'yellow' ];
} 
if ($task_percentage > 75) {
	$colour = $GLOBALS[ 'green' ];
}
?>

		<?php // If the current user is an administrator...
		if($options['show_report_to_all'] == true || current_user_can( 'manage_options' )) { ?>

			<!--/ Start Report Content /-->
			<div class="content report">

			    <div class="container">

					<?php if($_GET['sent'] == "yes") { ?>
						<div class="message success">
							<p><?php _e( "You emailed the report to", "taskrocket" ); ?> <?php echo $_GET['recipient']; ?></p>
							<span class="close"></span>
			            </div>
					<?php } ?>
					
		            <h1><?php _e( "Report", "taskrocket" ); ?>: <?php 
					if($project_ID =="") {
						echo $unassigned_label;
						} else {
							echo get_cat_name($project_ID);
						}
					 ?></h1>
					<?php 
					if($project_ID !=="1") { ?>
					<div class="progress-bar" title="<?php echo round($task_percentage, 0); ?>% Progress">
						<span><?php echo round($task_percentage, 0); ?>%</span>
						<div style="width:<?php echo round($task_percentage, 0); ?>%; background:#<?php echo $colour; ?>"></div>
					</div>
					<?php
						} 
					?>
					
					<ul class="filter filter-report">
						<li><a class="toggle-all-rows active"><?php _e( "All", "taskrocket" ); ?></a></li>
						<li><a class="toggle-incomplete-tasks"><?php _e( "Incomplete", "taskrocket" ); ?></a></li>
						<li><a class="toggle-complete-tasks"><?php _e( "Complete", "taskrocket" ); ?></a></li>
						<li><a class="toggle-onhold-tasks"><?php _e( "On hold", "taskrocket" ); ?></a></li>
						<li><a class="toggle-inprogress-tasks"><?php _e( "In progress", "taskrocket" ); ?></a></li>
						<li><a class="toggle-overdue"><?php _e( "Overdue", "taskrocket" ); ?></a></li>
						<li><a class="toggle-private"><?php _e( "Private", "taskrocket" ); ?></a></li>
						<li><a class="toggle-low-priority"><?php _e( "Low Priority", "taskrocket" ); ?></a></li>
						<li><a class="toggle-normal-priority"><?php _e( "Normal Priority", "taskrocket" ); ?></a></li>
						<li><a class="toggle-high-priority"><?php _e( "High Priority", "taskrocket" ); ?></a></li>
						<li><a class="toggle-urgent-priority"><?php _e( "Urgent Priority", "taskrocket" ); ?></a></li>
					</ul>
					
					
					<!--/ Start Project Details /-->
					<div class="project-and-task-details">
						<h2><?php _e( "Project Details", "taskrocket" ); ?></h2>
						<div>
							<ul>
								<li><strong><?php _e( "Total tasks", "taskrocket" ); ?>: </strong><?php echo $all_tasks; ?></li>
								<li><strong><?php _e( "Outstanding tasks", "taskrocket" ); ?>: </strong><?php echo $incomplete_tasks; ?></li>
								<li><strong><?php _e( "Completed tasks", "taskrocket" ); ?>: </strong><?php echo $all_completed_tasks; ?></li>
								<li><strong><?php _e( "Project Manager", "taskrocket" ); ?>: </strong><?php if($pm) { ?><?php echo $user_info->user_firstname . " " . $user_info->user_lastname; ?><?php } else { echo "-"; } ?>
								</li>
								<li><strong><?php _e( "Job number", "taskrocket" ); ?>: </strong><?php if($job_number == TRUE) { echo $job_number; } else { echo "-"; } ?></li>
								<li><strong><?php _e( "Budget", "taskrocket" ); ?>: </strong><?php if($budget > 0) { echo $currency_symbol . round($budget, 2); } else { echo "-"; } ?></li>
							</ul>
							
							<ul>
								<li><strong><?php _e( "Start date", "taskrocket" ); ?>: </strong><?php if($old_start_date == TRUE) { echo $new_start_date->format($date_format); } else { echo "-"; } ?></li>
								<li><strong><?php _e( "Due Date", "taskrocket" ); ?>: </strong><?php if($old_due_date == TRUE) { echo $new_due_date->format($date_format); } else { echo "-"; } ?></li>
								<li><strong><?php _e( "Time allocated", "taskrocket" ); ?>: </strong><?php if($allocated_time > 0) { echo $allocated_time . $s; } else { echo "-"; }?></li>
								<li class="<?php echo $alert; ?>"><strong><?php _e( "Time used", "taskrocket" ); ?>:</strong>
									<?php if($total_minutes > 0) { ?>
										<?php echo $thours; ?> <?php _e( "hrs", "taskrocket" ); ?> 
										<?php echo $tmins; ?> <?php _e( "mins", "taskrocket" ); ?>  
										<?php } else { ?>
											-
										<?php } ?>
								<li class="<?php echo $alert; ?>">
									<strong><?php _e( "Time remaining", "taskrocket" ); ?>: </strong>
									<?php if($allocated_time > 0) { ?>
										<?php if($cost > $budget || $total > $total_minutes) { ?>
											None
										<?php } else { ?>
											<?php echo $rhours; ?> <?php _e( "hrs", "taskrocket" ); ?>  
											<?php echo $rmin; ?> <?php _e( "mins", "taskrocket" ); ?>  
										<?php } ?>
									<?php } else { ?>
										<?php if($total_minutes > 0) { ?>
										<span class="master-tooltip" title="<?php _e( "Remaining time can not be determined because this project does not have time allocated", "taskrocket" ); ?>"><?php _e( "Indeterminable", "taskrocket" ); ?></span>
										<?php } else { ?>
											-
										<?php } ?>
									<?php } ?>
								</li>
								<li class="<?php echo $alert; ?>"><strong><?php _e( "Cost", "taskrocket" ); ?>: </strong>
									<?php if($cost == "0") { ?>
										-
									<?php } else { ?>
										<?php echo $currency_symbol; ?><?php echo round($cost, 2); ?> (<?php echo $currency_symbol; ?><?php echo $the_rate; ?>/<?php _e( "hr", "taskrocket" ); ?>) <?php echo $overby_text; ?>
									<?php } ?>
								</li>
							</ul>
						</div>
					</div>
					<!--/ End Project Details /-->
					
					<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
			            if ($options['show_gantt_report_individual'] == true) { ?>
			            <div class="gantt"></div>
			            <?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
			        <?php } }?>		
					
					<!--/ Start Users In Project /-->
					<?php require_once($GLOBALS[ 'theme_includes' ] . "project-users-list.php"); ?>
					<!--/ End Users In Project /-->
					

					<!--/ Start Report Container /-->
					<div class="report-container">
						
						<h2><?php _e( "All Tasks", "taskrocket" ); ?></h2>
						
						<!--/ Start Table Scroll /-->
						<div class="table-scroll">
							
							<div class="scroll-message">
								<p><?php _e( "Reports can contain a lot of data. To see everything in the table, you may have to scroll left and right.", "taskrocket" ); ?></p>	
							</div>
						    
							<table border="0" cellspacing="0" cellpadding="0">
						    <thead>
						        <tr>
						            <th class="table-task" align="left"><?php _e( "Task", "taskrocket" ); ?></th>
						            <th class="table-date-added" align="left"><?php _e( "Date Added", "taskrocket" ); ?></th>
						            <th class="table-due-date" align="left"><?php _e( "Due Date", "taskrocket" ); ?></th>
						            <th class="table-priority" align="left"><?php _e( "Priority", "taskrocket" ); ?></th>
									<th class="table-status" align="left"><?php _e( "Status", "taskrocket" ); ?></th>
						            <th class="table-time-spent" align="left"><?php _e( "Time", "taskrocket" ); ?></th>
						            <th class="table-task-cost" align="left"><?php _e( "Cost", "taskrocket" ); ?></th>
						            <th class="table-owner" align="left"><?php _e( "Owner", "taskrocket" ); ?></th>
						        </tr>
						    </thead>
						    <tfoot>
						        <tr>
									<th class="table-task" align="left"><?php _e( "Task", "taskrocket" ); ?></th>
						            <th class="table-date-added" align="left"><?php _e( "Date Added", "taskrocket" ); ?></th>
						            <th class="table-due-date" align="left"><?php _e( "Due Date", "taskrocket" ); ?></th>
						            <th class="table-priority" align="left"><?php _e( "Priority", "taskrocket" ); ?></th>
									<th class="table-status" align="left"><?php _e( "Status", "taskrocket" ); ?></th>
						            <th class="table-time-spent" align="left"><?php _e( "Time", "taskrocket" ); ?></th>
						            <th class="table-task-cost" align="left"><?php _e( "Cost", "taskrocket" ); ?></th>
						            <th class="table-owner" align="left"><?php _e( "Owner", "taskrocket" ); ?></th>
						        </tr>
						    </tfoot>
						    
						    <?php
						    // The Query
						    query_posts( array ( 
						        'category_name'         => $project_slug, 
						        'posts_per_page'        => -1,
						        'post_status' 		    => 'publish',
						        'orderby'               => 'title',
							    'order'                 => 'ASC',
						    ) );
						    
						    
					        $i = 0;
					        
					        while ( have_posts() ) : the_post();
					         
					        $old_due_date = get_post_meta($post->ID, 'duedate', TRUE);
					        $new_due_date = new DateTime($old_due_date);
					        
					        $priority = get_post_meta($post->ID, 'priority', TRUE);
					        if($priority == "") {
					            $priority_state = "normal";
					        } else {
					            $priority_state = $priority;
					        }
							
							$status = get_post_meta($post->ID, 'tr_status', TRUE); 
							
							// Pretty names for task status alt attributes
							if($status == "incomplete") {
								$task_status = __( "Incomplete", "taskrocket" );
							}
							if($status == "complete") {
								$task_status = __( "Complete", "taskrocket" );
							} 
							if($status == "onhold") {
								$task_status = __( "On hold", "taskrocket" );
							}
							if($status == "inprogress") {
								$task_status = __( "In progress", "taskrocket" );
							}
							
						    ?>
						    
						    <tr class="
							<?php echo "row-" . ($i++ % 2); ?> 
							<?php echo $status; ?> 
							<?php if(get_post_meta($post->ID, 'duedate', TRUE) != '' ) { echo $overduetask; } ?>
							<?php if(get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { echo " private"; } ?>
							<?php if($priority == "low") { echo " low-priority";} ?>
							<?php if($priority == "normal") { echo " normal-priority";} ?>
							<?php if($priority == "high") { echo " high-priority";} ?>
							<?php if($priority == "urgent") { echo " urgent-priority";} ?>
							<?php echo "report-project-user-" . $post->post_author; ?>
							">
						        <td class="<?php echo get_post_status ( $post->ID ); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
						        <td><?php echo get_the_time($date_format); ?></td>
						        <td><?php if($old_due_date == TRUE) { echo $new_due_date->format($date_format); } ?></td>
						        <td><em class="priority-<?php echo $priority_state; ?>"title="<?php echo $priority_state; ?>"></em></td>
								<td><span class="status task-<?php echo $status; ?>" title="<?php echo $task_status; ?>"></span></td>
						        <td><?php require($GLOBALS[ 'theme_includes' ] . 'time-spent.php'); ?></td>
						        <td><?php require($GLOBALS[ 'theme_includes' ] . 'task-cost.php'); ?></td>
						        <td>
								<?php 
									$author = get_the_author();
									if($author !=="") {
									echo the_author_meta( 'user_firstname' , $post->post_author );?> <?php echo the_author_meta( 'user_lastname' , $post->post_author );
								} else { ?>
									<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
								<?php }
								?>
								</td>
						    </tr>
						    
						    <?php endwhile; 
						    wp_reset_query();
						    ?>
						    </table>
							
						</div>
						<!--/ End Table Scroll /-->

					</div>
					<!--/ End Report Container /-->
					
		        </div>
			</div>
			<!--/ End Report Content /-->
			
			<script>
			    jQuery(function ($) {
			        $('.table-owner').click(function() { 
			            $('tbody tr').fadeIn();
			            $('.project-users-list li').css('opacity', '1');
						$('.table-owner').removeClass('show-all');
			        });
			    });
			</script>
			
			
			<form action="<?php echo get_template_directory_uri(); ?>/email-report.php" id="email_report" name="email_report" method="post">
				<label><?php _e( "Email", "taskrocket" ); ?></label>
				<input type="email" id="report_recipient" name="report_recipient" class="text" required />
				<input type="hidden" name="project_name" id="project_name" value="<?php if($project_ID =="") { echo $unassigned_label; } else { echo get_the_category_by_ID($project_ID); } ?>" />
				<input type="hidden" name="project_slug" id="project_slug" value="<?php echo $project_slug; ?>" />
				<input type="hidden" name="total_tasks" id="total_tasks" value="<?php echo $all_tasks; ?>" />
				<input type="hidden" name="outstanding_tasks" id="outstanding_tasks" value="<?php echo $incomplete_tasks; ?>" />
				<input type="hidden" name="completed_tasks" id="completed_tasks" value="<?php echo $all_completed_tasks; ?>" />
				<input type="hidden" name="project_manager" id="project_manager" value="<?php if($pm !== "") { ?><?php echo $user_info->user_firstname . " " . $user_info->user_lastname; ?><?php } else { echo "-"; } ?>" />
				<input type="hidden" name="job_number" id="job_number" value="<?php if($job_number == TRUE) { echo $job_number; } else { echo "-"; } ?>" />
				<input type="hidden" name="budget" id="budget" value="<?php if($budget > 0) { echo $currency_symbol . round($budget, 2); } else { echo "-"; } ?>" />
				<input type="hidden" name="start_date" id="start_date" value="<?php if($old_start_date == TRUE) { echo $new_start_date->format($date_format); } else { echo "-"; } ?>" />
				<input type="hidden" name="due_date" id="due_date" value="<?php if($old_due_date == TRUE) { echo $new_due_date->format($date_format); } else { echo "-"; } ?>" />
				<input type="hidden" name="time_allocated" id="time_allocated" value="<?php if($allocated_time > 0) { echo $allocated_time . $s; } else { echo "-"; }?>" />
				<input type="hidden" name="time_used" id="time_used" value="<?php
				if($the_hours > 0 || $the_mins > 0) {
					echo $the_hours . " hours " . $the_mins . " mins";
				} else {
					echo "0";
				}
				?>" />
				<input type="hidden" name="time_remaining" id="time_remaining" value="<?php
				if($final_mins > 0) {
					echo $remaining_hours . " hours " . $remaining_mins . " mins";
				} else {
					echo "-";
				}?>" />
				<input type="hidden" name="cost" id="cost" value="<?php echo $currency_symbol; ?><?php echo round($cost, 2); ?>" />
				<input type="hidden" name="project_id" id="project_id" value="<?php echo $project_ID; ?>" />
				<input type="hidden" name="percent_complete" id="percent_complete" value="<?php echo round($task_percentage, 0); ?>%" />
				<input type="hidden" name="the_rate" id="the_rate" value="<?php echo $the_rate ?>" />
				
				<div class="submit-button-container">
					<input type="submit" name="submit" class="button submit" value="<?php _e( "Send", "taskrocket" ); ?>" />
					<img src="<?php echo get_template_directory_uri(); ?>/images/loader.gif" />
				</div>
				
			</form>

		<?php } ?>

<?php get_footer(); ?>
