<?php
/*
Template Name: Projects
*/
// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: '.home_url().'/client');
	exit();
}
get_header();

$options = get_option( 'taskrocket_settings' );

wp_count_terms( 'category');
$project_count = wp_count_terms( 'category', array( 'hide_empty' => TRUE));

// All tasks
// This is every task that is incomplete or in progress.
$all_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'meta_key'          => 'tr_status',
	'meta_value'        => array('incomplete', 'inprogress', 'onhold')
);
$all_tasks_posts = new WP_Query($all_tasks_args);
$all_tasks = $all_tasks_posts->post_count;

// On hold tasks
$onhold_tasks_project_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'cat'			 	=> $cat_ID,
	'meta_key'  		=> 'tr_status',
	'meta_value' 		=> array('onhold')
);
$onhold_tasks_posts_project = new WP_Query($onhold_tasks_project_args);
$onhold_tasks_project = $onhold_tasks_posts_project->post_count;

// On hold tasks
$inprogress_tasks_project_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'cat'			 	=> $cat_ID,
	'meta_key'  		=> 'tr_status',
	'meta_value' 		=> array('inprogress')
);
$inprogress_tasks_posts_project = new WP_Query($inprogress_tasks_project_args);
$inprogress_tasks_project = $inprogress_tasks_posts_project->post_count;

?>



<div class="content all-projects">
	<div class="container">

	    <h1><?php the_title(); ?></h1>
		
		<?php if($_GET['deleted'] == "yes") { ?>
		<div class="message success">
			<p><?php echo $_GET['project_name']; ?> <?php _e( "and associated tasks have been deleted.", "taskrocket" ); ?></p>
			<span class="close"></span>
		</div>
		<?php } ?>
		
		<?php if($_GET['archived'] == "yes") { ?>
		<div class="message success">
			<p><?php echo $_GET['project_name']; ?> <?php _e( "has been archived.", "taskrocket" ); ?> <a href="<?php echo get_template_directory_uri(); ?>/archive-project.php?project_ID=<?php echo $_GET["project_ID"]; ?>&project_action=undo-archive&project_name=<?php echo $_GET['project_name']; ?>" class="undo button-small"><?php _e( 'Undo', 'taskrocket' ); ?></a></p>
			<span class="close"></span>
		</div>
		<?php } ?>
		
		<?php if($_GET['unarchived'] == "yes") { ?>
		<div class="message success">
			<p><?php echo $_GET['project_name']; ?> <?php _e( "has been pulled from the archives.", "taskrocket" ); ?> <a href="<?php echo get_template_directory_uri(); ?>/archive-project.php?project_ID=<?php echo $_GET["project_ID"]; ?>&project_action=archive&project_name=<?php echo $_GET["project_name"]; ?>" class="undo button-small"><?php _e( 'Re-archive', 'taskrocket' ); ?></a></p>
			<span class="close"></span>
		</div>
		<?php } ?>

	    <ul class="panel stats">
			<li>
				<p class="value"><?php echo $project_count ?></p>
				<p class="description">
					<strong><?php _e( "Active projects", "taskrocket" ); ?></strong>
				</p>
			</li>
			
	        <li>
	        	<p class="value"><?php echo $all_tasks; ?></p>
				<p class="description">
					<strong><?php _e( "Tasks outstanding", "taskrocket" ); ?></strong>
				</p>
	        </li>
			
			<li>
	        	<p class="value"><?php echo $onhold_tasks_project; ?></p>
				<p class="description">
					<strong><?php _e( "Tasks on hold", "taskrocket" ); ?></strong>
				</p>
	        </li>
			
			<li>
	        	<p class="value"><?php echo $inprogress_tasks_project; ?></p>
				<p class="description">
					<strong><?php _e( "Task", "taskrocket" ); ?><?php if($inprogress_tasks_project > 1) { echo "s"; } ?> <?php _e( "In progress", "taskrocket" ); ?></strong>
				</p>
	        </li>
			
	    </ul>
		
		<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
			if ($options['show_gantt_all_projects'] == true) { ?>
			<div class="gantt"></div>
			<?php require(ABSPATH . 'wp-content/plugins/taskrocket-gantt/gantt/script.php'); ?>
		<?php } }?>

		<div class="all-active-projects">

			<?php
	            $categories = get_categories('hide_empty=0&order=ASC&orderby=name'); // Get all categories
				$i = 1;
				wp_count_terms( 'category');
				
				// Start foreach
	            foreach ($categories as $cat) {
					
					$cat_ID = $cat->cat_ID;
					
					// Project archive
					$project_archived = get_option( 'tr_project_archived_' . $cat_ID );
					
					// All tasks
					$all_tasks_project_args = array(
						'posts_per_page' 	=> -1,
						'post_type' 		=> 'post',
						'post_status'		=> 'publish',
						'cat'			 	=> $cat_ID,
						'meta_key'          => 'tr_status',
						'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
					);
					$all_tasks_posts_project = new WP_Query($all_tasks_project_args);
					$all_tasks_project = $all_tasks_posts_project->post_count;
					
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
					$completed_tasks_project = $completed_tasks_posts_project->post_count;
					
					// Incomplete tasks
					$incomplete_tasks_project_args = array(
						'posts_per_page' 	=> -1,
						'post_type' 		=> 'post',
						'post_status'		=> 'publish',
						'cat'			 	=> $cat_ID,
						'meta_key'  		=> 'tr_status',
						'meta_value' 		=> array('incomplete', 'inprogress', 'onhold')
					);
					$incomplete_tasks_posts_project = new WP_Query($incomplete_tasks_project_args);
					$incomplete_tasks_project = $incomplete_tasks_posts_project->post_count;
					
					// Unassigned tasks
					$unassigned_tasks_project_args = array(
						'posts_per_page' 	=> -1,
						'post_type' 		=> 'post',
						'post_status'		=> 'publish',
						'cat'			 	=> 1,
						'meta_key'  		=> 'tr_status',
						'meta_value' 		=> array('incomplete', 'inprogress', 'onhold')
					);
					$unassigned_tasks_posts_project = new WP_Query($unassigned_tasks_project_args);
					$unassigned_tasks_project = $unassigned_tasks_posts_project->post_count;
					
		            if ( $cat->cat_name == 'Unassigned' ) {
		                $catclass = "unassigned";
		            }

					// Date stuff
					$old_start_date_format = get_option( 'tr_start_date_' . $cat_ID );
					$new_start_date_format = new DateTime($old_start_date_format);

					$old_end_date_format = get_option( 'tr_end_date_' . $cat_ID );
					$new_end_date_format = new DateTime($old_end_date_format);

					$late = get_option( 'tr_end_date_' . $cat_ID );
					$date_format = get_option('date_format');
					
					// Determine if project is complete and
					// fix division by zero.
					if($all_tasks_project > 0 && $all_tasks_project == $completed_tasks_project && $incomplete_tasks_project == 0) {
						$project_status = "project-complete";
						$task_percentage = 100;
					} else {
						$project_status = "";
						if ($incomplete_tasks_project == 0) {
							$task_percentage = 0;
						} else {
							$task_percentage = ($completed_tasks_project / $all_tasks_project) * 100;
						}
					}
					
					// Colours for the progress bars.
					if ($task_percentage <= 25) {
						$colour = $GLOBALS[ 'red' ];
					} 
					if ($task_percentage > 25 && $task_percentage <= 50) {
						$colour = $GLOBALS[ 'orange' ];
					} 
					if ($task_percentage > 50 && $task_percentage <= 75) {
						$colour = $GLOBALS[ 'yellow' ];
					} 
					if ($task_percentage > 75) {
						$colour = $GLOBALS[ 'green' ];
					}
	        ?>
			
			<?php if(!$project_archived) { // If project is not archived ?>
	        <div class="project <?php echo $catclass . " " . $project_status; ?> <?php echo "row-" . ($i++ % 2); ?> <?php if($all_tasks_project == $incomplete_tasks_project) { echo "inactive-project"; } ?> <?php if (new DateTime() < new DateTime($late . " 23:59:59")) { echo "not-overdue"; } ?>">
				
				<!--/ Start First Box /-->
				<div class="box-01 flex-box">
					<h2><a href="<?php echo home_url() . "/" . get_option( 'category_base' ) . "/" . $cat->category_nicename; ?>"><?php echo $cat->cat_name; ?></a></h2>
					
					<?php if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) {
						require(ABSPATH . 'wp-content/plugins/taskrocket-add-to-cal/cal-links.php');
					} ?>
					
					<?php if ( $cat->cat_name !== 'Unassigned') { ?>
						<p class="status">
							<strong><?php _e( "Tasks", "taskrocket" ); ?>:</strong> <?php echo $completed_tasks_project; ?> <?php _e( "of", "taskrocket" ); ?> <?php echo $all_tasks_project; ?> <?php _e( "Complete", "taskrocket" ); ?><?php if ($task_percentage < 100) { ?>, <?php echo $incomplete_tasks_project; ?> <?php _e( "outstanding", "taskrocket" ); ?>.<?php } ?>
						</p>
	
						<?php 
							$project_description = $cat->category_description;
							if($project_description) {
						?>
						<!--/ Start Description /-->
						<p class="project-desc">
							<?php echo $cat->category_description; ?>
						</p>
						<!--/ End Description /-->
						<?php } ?>
						
						<?php if(get_option( 'tr_details_' . $cat_ID )) { ?>
						<span class="toggle-more-info"><?php _e( "More info", "taskrocket" ); ?></span>
						<?php } ?>
						
						<pre class="project-more-info">
							<?php // Return string with Links, if condition is met.
							if ($options['disable_make_clickable'] == false) {
								$details_string = get_option( 'tr_details_' . $cat_ID );
								echo make_clickable( $details_string );
							} else {
								echo get_option( 'tr_details_' . $cat_ID );
							} ?>
						</pre>

						<div class="progress">
							<div style="width:<?php echo $task_percentage; ?>%; background:#<?php echo $colour; ?>"></div>
						</div>
					<?php } else { ?>
						<p class="status"><?php echo $unassigned_tasks_project; ?> <?php if ($unassigned_tasks_project == 1 ) { _e( "task is", "taskrocket" ); } else { _e( "tasks are", "taskrocket" ); }?> unassigned.</p>
					<?php } ?>
				</div>
				<!--/ End First Box /-->
				
				<!--/ Start Time Details /-->
				<div class="time-details box-02 flex-box">
					<div>
						
						<?php $job_number = get_option( 'tr_job_number_' . $cat_ID ); 
						if($job_number) { ?>
						<span><?php _e( "Job", "taskrocket" ); ?>: <?php echo $job_number; ?></span>
						<?php } ?>
						
						<?php // Time allocated
						if(get_option( 'tr_hrs_allocated_' . $cat_ID )) {
							$allocated = get_option( 'tr_hrs_allocated_' . $cat_ID );
							echo "<span title='" . __( "The time allocated to this project", "taskrocket" ) . "'>";
							echo $allocated . "hrs allocated";
							echo "</span>";
						} else {
							echo "<span title='" . __( "No time has been allocated to this project", "taskrocket" ) . "'>";
							_e( "No time allocated", "taskrocket" );
							echo "</span>";
						}
						?>

						<?php // Time used
						if(get_option( 'tr_hrs_allocated_' . $cat_ID )) {
					        $args = array(
					            'numberposts' 	=> -1,
					            'offset' 		=> 0,
								'post_status' 	=> array('trash', 'publish'),
					            'category'	 	=> $cat_ID
					        );
					        $alltimes = get_posts( $args );

					        $total = 0;
					        foreach( $alltimes as $logtimeID ) {
					            $single = get_post_meta( $logtimeID->ID, 'logtime', true );
					            $total += $single;
					        }

							$minutes = $total;
				            $hours = floor($minutes / 60);
				            $min = $minutes - ($hours * 60);

							if($total != "0" ) {
								echo "<span>";
				            	echo $hours . __( "Hours", "taskrocket" ) . " " . $min . __( "mins used", "taskrocket" );
								echo "</span>";
							} else {
								echo "<span title='" . __( "No time has been recorded on this project", "taskrocket" ) . "'>";
								_e( "No time recorded", "taskrocket" );
								echo "</span>";
							}
						}
						//wp_reset_postdata();
				        ?>


						<?php // Time remaining

							// Condition is that the project needs to have time allocated
							if(get_option( 'tr_hrs_allocated_' . $cat_ID )) {

								// Set the number of hours as a variable in minutes
								$timeinminutes = $allocated * 60;

								$remainingtime = $timeinminutes - $total;

								$rminutes = $remainingtime;
								$rhours = floor($rminutes / 60);
								$rmin = $rminutes - ($rhours * 60);

								$new_timeallocated = $hoursallocated * 60;

								if($remainingtime > 0 ) {
									echo "<span>";
									echo $rhours . __( "hrs", "taskrocket" ) . " " . $rmin . __( "mins remaining", "taskrocket" );
									echo "</span>";
								} else {
									echo "<span class='over-time' title='" . __( "This project has exceeded the allocated time", "taskrocket" ) . "'>";
									_e( "Over time by", "taskrocket" ) . " ";

									// Calculated over time is the new time allocated minnus the remaining time minus the new time allocated.
									$calc_over_time = $new_timeallocated - $remainingtime - $new_timeallocated;

									$over_hours = floor($calc_over_time / 60);
									$over_minutes = $calc_over_time % 60;

									// Apostrophe if there is more than one hr over time.
									if ($over_hours > 1) {
										$apostrophe = "s";
									}

									echo $over_hours . __( "hr", "taskrocket" ) . $apostrophe . " " . __( "and", "taskrocket" ) . " " . $over_minutes . " " . __( "mins", "taskrocket" );

									echo "</span>";
								}
							}
						?>

						<?php  // Costs

							if ($options['show_cost'] == true || current_user_can( 'manage_options' )) { ?>
							<?php
								$hoursallocated = get_option( 'tr_hrs_allocated_' . $cat_ID );

								$cost = $options['rate'] * $hoursallocated;

								$hours = $hours * 60;
								$min = $min;

								$time_used = $hours + $min;
								$final_time = $time_used / 60;
								$current_cost = $final_time * $options['rate'];
								$currency_symbol = $options['currency_symbol'];
								if ($currency_symbol == "") {
									$currency_symbol = "$";
								}


								if($cost > 0) {

									if($current_cost > $cost) {
										$costnote = " class='over-cost' title='" . __( "This project has exceeded the allocated budget", "taskrocket" ) . "'";
									} else {
										$costnote = " class='within-cost' title='" . __( "This project is still within the allocated budget", "taskrocket" ) . "'";
									}

									echo "<span". $costnote .">";
									echo $currency_symbol . round($current_cost , 0) . " / " . $currency_symbol . $cost;
									echo "</span>";
								}
							?>
						<?php } ?>

						<?php // Start and End Dates
				        if(get_option( 'tr_start_date_' . $cat_ID )) {
				            
							if (new DateTime() > new DateTime($late . " 23:59:59")) {
								$lateclass = "project-is-late";
								echo "<span class='" . $lateclass . "' title='" . __( "The time frame for this project has passed", "taskrocket" ) . "'>" . __( "Deadline", "taskrocket" ) . ": ";
					            echo $new_end_date_format->format($date_format);
								echo "</span>";
								wp_reset_postdata();
							} else {
								echo "<span title='" . __( "The time frame for this project", "taskrocket" ) . "'>";
								echo $new_start_date_format->format($date_format) . " - ";
								echo $new_end_date_format->format($date_format);
								echo "</span>";
							}
						}
				        ?>
						
					</div>
					
				</div>
				<!--/ End Time Details /-->
				
				<!--/ Start project percentage is greater than or equal to 100 /-->
				<?php if ($task_percentage >= 100) { ?>
					
		            <span class="status complete-text box-03 flex-box">
						<span><?php _e( "All tasks complete!", "taskrocket" ); ?></span>
					</span>
					
				<?php } else { // otherwise... ?>
					
	                <?php if ( $cat->cat_name == 'Unassigned') { ?>
		                <span class="unassigned-tasks box-03 flex-box">
							<span><?php echo $unassigned_tasks_project; ?><em><?php _e( "Tasks", "taskrocket" ); ?></em></span>
						</span>
	                <?php } else { ?>
						<span class="percent box-03 flex-box">
							<span>
								<?php echo (round($task_percentage)) ?><em>%</em>
							</span>
						</span>
	                <?php } ?>
					
				<?php } ?>
				<!--/ End project percentage is greater than or equal to 100 /-->


				<!--/ Start Delete Project /-->
	            <?php $options = get_option( 'taskrocket_settings' );
	                if ($options['delete_projects'] == true || current_user_can( 'manage_options' )) {  ?>
	            <?php if ( $cat->cat_name !== 'Unassigned' ) { ?><span class="show-delete" title="<?php _e( "Delete this project", "taskrocket" ); ?>">&#215;</span><?php } ?>
	            <div class="deleter">
	                <div>
	                    <p><?php _e( "Really delete the", "taskrocket" ); ?> <?php echo $cat->cat_name; ?> project?</p>
						<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?project_ID=<?php echo $cat_ID; ?>&project_action=delete&project_name=<?php echo $cat->cat_name; ?>" class="master-tooltip confirm-delete button-small" title="<?php _e( "If this project is deleted, any associated tasks will also be deleted", "taskrocket" ); ?>."><?php _e( "Yes, do it", "taskrocket" ); ?></a>
	                    <a class="hide-delete button-small"><?php _e( "No!", "taskrocket" ); ?></a>
						<?php if ($options['archive_projects'] == true || current_user_can( 'manage_options' )) { ?>
						<a href="<?php echo get_template_directory_uri(); ?>/archive-project.php?project_ID=<?php echo $cat_ID; ?>&project_action=archive&project_name=<?php echo $cat->cat_name; ?>" class="master-tooltip archive-this button-small" title="<?php _e( "You will be able to pull this project from the archives at a later time if you wish", "taskrocket" ); ?>."><?php _e( "Archive", "taskrocket" ); ?></a>
						<?php } ?>
	                </div>
	            </div>
	            <?php } ?>
				<!--/ End Delete Project /-->

	        </div>
			<?php  // Else if project is not archived 
			} else { ?>
				<?php if ($options['unarchive_projects'] == true) { ?>
				<div class="project archived-project <?php echo $catclass . " " . $project_status; ?> <?php echo "row-" . ($i++ % 2); ?>">
					<h2><?php echo $cat->cat_name; ?></h2>
					<a href="<?php echo get_template_directory_uri(); ?>/archive-project.php?project_ID=<?php echo $cat_ID; ?>&project_action=undo-archive&project_name=<?php echo $cat->cat_name; ?>" class="undo button-small"><?php _e( 'Unarchive', 'taskrocket' ); ?></a>
				</div>
				<?php } ?>
			
			<?php } ?>

		<?php
	} // End foreach
	    ?>
	    </div>
	</div>
</div>

<?php get_footer(); ?>
