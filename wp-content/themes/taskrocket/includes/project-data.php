<?php

	global $post;

	$slug = get_the_category();

	// If current user is a client, get the postID of the latest post from the category.
	// $cat_id is declared on client plug-in page plugins/taskrocket-client/clients.php
	if(current_user_can( 'client' )) {
		$postsincat = get_posts(array("cat" => $cat_id, "showposts" => 1, 'post_status' => 'publish'));
		$latestPostID = $postsincat[0]->ID;
	}

	// Default variable, used for plugins/taskrocket-clients/clients.php
	$categories = get_the_category($latestPostID);

	// If we are on the category.php page, then we need to change the $categories variable.
	// Explanation: Because this server side include is used in two places (category.php and plugins/taskrocket-clients/clients.php),
	// get_the_category() needs to be either the category ID (when on category.php) or
	// the ID of the latest post (plugins/taskrocket-clients/clients.php).
	// So, we just use is_page_template to so that $categories = get_the_category() when on the category.php page.
	if ( is_page_template( 'category.php' ) ) {
		$categories = get_the_category();
	}

	foreach ($categories as $cat) {
	$posts = new WP_Query( array(
		'post_type' 	=> 'post', 
		'showposts'		=> -1, 
		'post_status' 	=> array('publish'), 
		'cat' 			=> $cat->cat_ID
	));
	
	// All tasks count
	$all_tasks_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $cat_id,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
	);
	$all_tasks_posts = new WP_Query($all_tasks_args);
	$all_tasks = $all_tasks_posts->post_count;
	
	// My tasks count
	$userID = get_current_user_id();
	$my_tasks_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $cat_id,
		'author' 			=> $userID,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('incomplete', 'inprogress', 'onhold')
	);
	$my_tasks_posts = new WP_Query($my_tasks_args);
	$my_tasks = $my_tasks_posts->post_count;
	
	// My complete tasks count
	$my_completed_tasks_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $cat_id,
		'author' 			=> $userID,
		'meta_key'  		=> 'tr_status',
		'meta_value' 		=> 'complete'
	);
	$my_completed_tasks_posts = new WP_Query($my_completed_tasks_args);
	$my_completed_tasks = $my_completed_tasks_posts->post_count;
	
	// All completed tasks count
	$all_completed_tasks_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $cat_id,
		'meta_key'  		=> 'tr_status',
		'meta_value' 		=> 'complete'
	);
	$all_completed_tasks_posts = new WP_Query($all_completed_tasks_args);
	$all_completed_tasks = $all_completed_tasks_posts->post_count;
	
	// Incomplete tasks
	$incomplete_tasks_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat'			 	=> $cat_ID,
		'meta_key'  		=> 'tr_status',
		'meta_value' 		=> array('incomplete', 'inprogress', 'onhold')
	);
	$incomplete_tasks_posts = new WP_Query($incomplete_tasks_args);
	$incomplete_tasks = $incomplete_tasks_posts->post_count;
	
	wp_reset_query();
	
	// Remaining tasks
	$remaining_tasks = $all_tasks - $all_completed_tasks;
	
	// Percentage of tasks completed so far
	$task_percentage = ($all_completed_tasks / $all_tasks) * 100;
	
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

<?php $current_category = single_cat_title("", false); if ($current_category !== "Unassigned") { // Don't show the project data box if unassigned category ?>

	<div class="progress-bar" title="<?php echo round($task_percentage, 0); ?>% Progress">
		<span><?php echo round($task_percentage, 0); ?>%</span>
		<div style="width:<?php echo round($task_percentage, 0); ?>%; background:#<?php echo $colour; ?>"></div>
	</div>
	
	<?php if($task_percentage == 100) { ?>
		<div class="all-done message message-restored">
			<p>
				<?php _e( "Yay! This project appears to complete.", "taskrocket" ); ?>
				<?php if($options['show_report_to_all'] == true || current_user_can( 'manage_options' )) { ?>
					<?php if(!current_user_can( 'client' )) { ?>
						<a href="<?php echo home_url() . "/single-report/?projectid=" . get_category(get_query_var('cat'))->cat_ID; ?>&referer=project" class="undo button-small"><?php _e( "View Report", "taskrocket" ); ?></a>
					<?php } ?>
				<?php } ?>
				<span class="close"></span>
			</p>
		</div>
	<?php } ?>

	<div>
		<ul class="panel stats">
			
			<li>
				<p class="value">
					<?php echo (round($task_percentage)) ?>
					<em class="percentage">%</em> 
				</p>
				<p class="description">
					<strong><?php _e( "Project Progress", "taskrocket" ); ?></strong>
				</p>
			</li>
				
			<li class="tasks-total">
				<p class="value">
					<?php echo $all_tasks; ?>
				</p>
				
				<p class="description">
					<strong><?php if ($all_tasks == 1) { _e( "Task", "taskrocket" ); } else { _e( "Total tasks", "taskrocket" ); } ?></strong>
				</p>
			</li>
			
			<li class="tasks-complete">
				<p class="value">
					<?php echo $all_completed_tasks; ?>
				</p>
				
				<p class="description">
					<strong><?php if ($all_completed_tasks == 1) { _e( "Task complete", "taskrocket" ); } else { _e( "Tasks complete", "taskrocket" ); } ?></strong>
				</p>
			</li>
			
			<li class="tasks-remain">	
				<p class="value">
					<?php echo $remaining_tasks; ?>
				</p>
				
				<p class="description">
					<strong><?php if ($remaining_tasks == 1) { _e( "Task remains", "taskrocket" ); } else { _e( "Tasks remaining", "taskrocket" );} ?></strong>
				</p>
			</li>
			
			<li class="my-tasks">
				<p class="value">
					<?php echo $my_tasks; ?>
				</p>
				
				<p class="description">
					<strong><?php _e( "Tasks belonging to me", "taskrocket" ); ?></strong>
				</p>
			</li>
			
		</ul>
		
		<?php if(!current_user_can( 'client' ) || $options['clients_see_team'] == true) {
			require($GLOBALS[ 'theme_includes' ] . 'project-users-list.php');
		} ?>

		<!--/ Start Time Details /-->
		<span class="time-details time-details-project">
			<?php // If the category is not "unassigned"
				$current_category = single_cat_title("", false); if ($current_category !== "Unassigned") {  ?>


						<?php // Time allocated
							$hoursallocated = get_option( 'tr_hrs_allocated_' . $cat_id );
							if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
								if($hoursallocated > 0) {
									echo "<span class='time-allocated master-tooltip' title='" . __( "How much time has been allocated to this project", "taskrocket" ) . "'>";
									echo $hoursallocated . " " . __( "Hrs allocated", "taskrocket" );
									echo "</span>";
								} else {
									echo "<span class='time-allocated master-tooltip' title='" . __( "No time has been allocated to this project", "taskrocket" ) . "'>";
									echo  __( "No time allocated", "taskrocket" );
									echo "</span>";
								}
							}

							// Set the number of hours as a variable in minutes
							$timeinminutes = $hoursallocated * 60;
						?>



						<?php // Time used
						if($hoursallocated > 0) {
							$args = array(
								'numberposts' 	=> -1,
								'offset' 	    => 0,
								'post_status' 	=> 'publish',
								'category' 	 	=> $cat_id
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

							if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
								if($total != "0" ) {
									echo "<span class='time-used master-tooltip' title='" . __( "How much time has been used on this project", "taskrocket" ) . "'>";
									echo $hours . " " . __( "Hours", "taskrocket" ) . " " . $min . " " . __( "mins used", "taskrocket" );
									echo "</span>";
								} else {
									echo "<span class='time-used master-tooltip' title='" . __( "No time has been recorded on this project", "taskrocket" ) . "'>";
									_e( "No time recorded", "taskrocket" );
									echo "</span>";
								}
							}
						}
						?>



						<?php // Time remaining
							if($hoursallocated > 0) {
								$remainingtime = $timeinminutes - $total;
								$rminutes = $remainingtime;
								$rhours = floor($rminutes / 60);
								$rmin = $rminutes - ($rhours * 60);
								$new_timeallocated = $hoursallocated * 60;


								if($remainingtime >= 0 ) {
									if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
										echo "<span class='time-remaining master-tooltip' title='" . __( "How much time is remaining on this project", "taskrocket" ) . "'>";
										echo $rhours . " " . __( "Hours", "taskrocket" ) . " " . $rmin . " " . __( "mins remaining", "taskrocket" );
										echo "</span>";
									}
								} else {
									if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
										echo "<span class='over-time master-tooltip' title='" . __( "This project has exceeded the allocated time", "taskrocket" ) . "'>";
										echo "<strong>" . __( "Over time by", "taskrocket" ) . ": </strong>";
									}

									// Calculated over time is the new time allocated minnus the remaining time minus the new time allocated.
									$calc_over_time = $new_timeallocated - $remainingtime - $new_timeallocated;
									$over_hours = floor($calc_over_time / 60);
									$over_minutes = $calc_over_time % 60;

									// Apostrophe if there is more than one hr over time.
									if ($over_hours > 1) {
										$apostrophe = "s";
									}

									if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
										echo $over_hours . __( "hr", "taskrocket" ) . $apostrophe . " and " . $over_minutes . " " . __( "mins", "taskrocket" );
										echo "</span>";
									}
								}
							}
						?>

						<?php // Start and End Dates
							if(get_option( 'tr_start_date_' . $cat_id )) {
								$date_format = get_option('date_format');
								$old_start_date_format = get_option( 'tr_start_date_' . $cat_id );
								$new_start_date_format = new DateTime($old_start_date_format);
								$old_end_date_format = get_option( 'tr_end_date_' . $cat_id );
								$new_end_date_format = new DateTime($old_end_date_format);

								// Let's see if this project is behind schedule
								$late = get_option( 'tr_end_date_' . $cat->cat_ID );
								if (new DateTime() > new DateTime($late . " 23:59:59")) {
									$lateclass = "project-is-late";

									if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
										echo "<span class='" . $lateclass . " master-tooltip' title='" . __( "The time frame for this project has passed", "taskrocket" ) . "'><strong>" . __( "Deadline passed", "taskrocket" ) . ":</strong> ";
										echo $new_end_date_format->format($date_format);
										echo "</span>";
									}
									wp_reset_postdata();
								} else {
									if ($options['clients_see_project_time_info'] == true || current_user_can( 'manage_options') || current_user_can( 'editor') ) {
										echo "<span class='time-frame master-tooltip' title='" . __( "The time frame for this project", "taskrocket" ) . "'><strong>" . __( "Schedule", "taskrocket" ) . ": </strong>";
										echo $new_start_date_format->format($date_format) . " &#10140; ";
										echo $new_end_date_format->format($date_format);
										echo "</span>";
									}
								}
							}
						?>

					
						<?php // Job number
	                    if(get_option( 'tr_job_number_' . $cat_id )) { ?> 
	                    <span class='job-number master-tooltip' title="<?php _e( "Job number for this project", "taskrocket" ); ?>">
	                        <strong><?php _e( "Job", "taskrocket" ); ?> #:</strong> <?php echo get_option( 'tr_job_number_' . $cat_id ); ?>
	                    </span>
	                    <?php } ?>


					<?php // If the show_cost option is enabled and clients are allowed to see costs,
						  // or if you're an administrator, then show the costs.

						 // Costs
						 	$standard_rate = $options['rate'];
							$project_rate = get_option( 'tr_hourly_rate_' . $cat_id );
							
							if($project_rate) { 
								$the_rate = $project_rate;
							} else if($standard_rate) {
								$the_rate = $standard_rate;
							} else {
								$the_rate = 0;
							}
							
							$cost = $the_rate * $hoursallocated;
							$hours = $hours * 60;
							$min = $min;
							$time_used = $hours + $min;
							$final_time = $time_used / 60;
							$current_cost = $final_time * $the_rate;
							$currency_symbol = $options['currency_symbol'];
							if ($currency_symbol == "") {
				                $currency_symbol = "$";
				            }

							if($current_cost > "0") {

								if($current_cost > $cost) {
									$costnote = " class='the-cost over-cost master-tooltip' title='" . __( "This project has exceeded the allocated budget", "taskrocket" ) . "'";
								} else {
									$costnote = " class='the-cost within-cost master-tooltip' title='" . __( "This project is still within the allocated budget", "taskrocket" ) . "'";
								}

								// If show costs is enabled, or if you're an administrator...
								if ($options['show_cost'] == true || current_user_can( 'manage_options') ){

									// If you're an administrator or an editor
									if( current_user_can( 'manage_options') || current_user_can( 'editor')) {
										echo "<span". $costnote .">";
										echo $currency_symbol . round($current_cost , 2) . " / " . $currency_symbol . $cost;
										echo "</span>";
									}

									// If clients are allowed to see costs.
									if($options['clients_see_costs'] == true && !current_user_can( 'manage_options')) {
										echo "<span". $costnote .">";
										echo $currency_symbol . round($current_cost , 2) . " / " . $currency_symbol . $cost;
										echo "</span>";
									}

								}

							}
						?>

			<?php } ?>
		</span>
		<!--/ End Time Details /-->

	</div>

<?php } ?>

<?php } ?>
