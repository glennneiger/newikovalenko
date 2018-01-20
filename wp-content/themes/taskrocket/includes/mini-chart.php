<!--/ Start Mini Chart /-->
<?php
$options = get_option( 'taskrocket_settings' );
$date_format = get_option('date_format');
if ($options['disable_mini_chart'] == false) { ?>
    <div class="the-mini-chart">
        <h2>
        	<?php _e( "Projects", "taskrocket" ); ?> 
        	<span class="project-count">
        		<?php
        		wp_count_terms( 'category');
        		$projectCount = wp_count_terms( 'category', array( 'hide_empty' => TRUE));
        		?>
        		<span class="value" title="<?php echo $projectCount ?> active <?php if ($projectCount == 1 ) { _e( "Project", "taskrocket" ); } else { _e( "Projects", "taskrocket" ); }?> <?php _e( "including all unassigned", "taskrocket" ); ?>"><?php echo $projectCount ?></span>
            </span>
        </h2>
        
        <div class="chart">
        
        	<?php if(is_home()) { ?>
                <?php if ($projectCount == 0) { ?>
                    <p class="advice"><?php _e( "There are no active projects.", "taskrocket" ); ?>
                    <?php if ($options['users_create_projects'] == true || current_user_can( 'manage_options' )) { ?>
        				<a href="<?php echo home_url(); ?>/new-project/"><?php _e( "Create one?", "taskrocket" ); ?></a>
        			<?php } else { ?>
        				<?php _e( "Unless the option is enabled, only administrators can create projects.", "taskrocket" ); ?>
        			<?php } ?>
                    </p>
                <?php } ?>
            <?php } ?>
        	
            <ul>
            <?php
        		$options = get_option( 'taskrocket_settings' );
        
        		$categories = get_categories(array(
        			'hide_empty'	=> 0,		// Get all categories.
        			'hierarchical'	=> 1, 
        			'parent' 		=> 0,		// Only show parent categories.
        			'order'			=> 'ASC'
        		));
        		
                foreach ($categories as $category) {
                    
                    $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
                    
                    $all_tasks_args = array(
                    	'posts_per_page' 	=> -1,
                    	'post_type' 		=> 'post',
            			'showposts'         => -1,
            			'post_status'       => array('publish'),
            			'cat'               => $category->cat_ID,
                    	'meta_key'          => 'tr_status',
                    	'meta_value'        => array('incomplete', 'inprogress', 'onhold', 'complete')
                    );
                    $all_tasks_tasks_posts = new WP_Query($all_tasks_args);
                    $all_tasks = $all_tasks_tasks_posts->post_count;
                    
                    // Completed tasks
                    $completed_tasks_args = array(
                        'posts_per_page' 	=> -1,
                    	'post_type' 		=> 'post',
            			'showposts'         => -1,
            			'post_status'       => array('publish'),
            			'cat'               => $category->cat_ID,
                    	'meta_key'          => 'tr_status',
                    	'meta_value'        => array('complete')
                    );
                    $completed_tasks_posts = new WP_Query($completed_tasks_args);
                    $completed_tasks = $completed_tasks_posts->post_count;

                    
                    if ( $category->cat_name == 'Unassigned' ) {
                        $catclass = " unassigned";
                    }

                    // $remaining_tasks is $all_tasks minus the $completed_tasks (this gives us the difference).
                    $remaining_tasks = $all_tasks - $completed_tasks;
            
                    // But there's a problem: division by zero! Let's "fix" it!
                    if ($remaining_tasks == 0) {
                        if($all_tasks == 0) {
                            $percentagecomplete = 0;
                        } else {
                            $percentagecomplete = 100;
                        }
                    } else {
                        $percentagecomplete = ($completed_tasks / $all_tasks) * 100;
                    }
            
                    // Colours for the progress bars.
                    if ($percentagecomplete <= 25) {
                		$colour = $GLOBALS[ 'red' ];
                	} 
                	if ($percentagecomplete > 25 && $percentagecomplete <= 50) {
                		$colour = $GLOBALS[ 'orange' ];
                	} 
                	if ($percentagecomplete > 50 && $percentagecomplete <= 75) {
                		$colour = $GLOBALS[ 'yellow' ];
                	} 
                	if ($percentagecomplete > 75) {
                		$colour = $GLOBALS[ 'green' ];
                	}
            		
                
        			// Conditionally show completed/un-tasked projects when option is enabled.
        			if($options['show_complete_projects_report'] == true) {
        				$show_complete_projects_report = -1;
        			} else {
        				$show_complete_projects_report = 0;
        			}
        
    			// Conditional baded on setting.
    			if ($completed_tasks > $show_complete_projects_report) {
    
    				// Get the hours allocated
    				$hoursallocated = get_option( 'tr_hrs_allocated_' . $category->cat_ID );
    	            // Set the number of hours as a variable in minutes
    	            $timeinminutes = $hoursallocated * 60;
                    
                    if(!$project_archived) {
        
        			?>
            		<li <?php if ( $category->cat_name == 'Unassigned' ) { echo ' class="unassigned"'; } ?>>
        
        				<?php
        					$check_for_start_date = get_option( 'tr_start_date_' . $category->cat_ID );
        					$check_for_end_date = get_option( 'tr_end_date_' . $category->cat_ID );
        					$check_for_time_allocated = get_option( 'tr_hrs_allocated_' . $category->cat_ID );
        				?>
        			
        				<span class="project-container">
        		            <?php if ( $category->cat_name == 'Unassigned' ) { // If unassigned ?>
        
        		            	<span class="project" title="<?php printf( __( '%1$d of %2$d complete, %3$d outstanding' ), $completed_tasks, $all_tasks, $remaining_tasks); ?>"><?php echo $category->cat_name; ?></span>
        		                <span class="unassigned-remaining"><?php echo $remaining_tasks; ?></span>

        		            <?php } else { ?>
        
        						<?php if($percentagecomplete == "100") { ?>
        		            		<span class="percent-100"></span>
        						<?php } else { ?>
        							<span class="percent"><?php echo round($percentagecomplete); ?>%</span>
        						<?php } ?>
        
        						<span class="project" title="<?php printf( __( '%1$d of %2$d complete, %3$d outstanding' ), $completed_tasks, $all_tasks, $remaining_tasks); ?>"><?php echo $category->cat_name; ?></span>
        						<span class="tasks"><?php printf( __( '%1$d of %2$d complete, %3$d outstanding' ), $completed_tasks, $all_tasks, $remaining_tasks); ?></span>
        					<?php } ?>
        				</span>
        				
        				<!--/ Start Details /-->
        				<span class="chart-items">
        
        					<span class="heading"><?php _e( "Tasks", "taskrocket" ); ?></span>
        					
        					<span class="tasks-done wide">
        						<span class="label"><?php _e( "Complete", "taskrocket" ); ?></span>
        						<span class="value"><?php echo $completed_tasks; ?></span>
        					</span>
        					<span class="tasks-total wide">
        						<span class="label"><?php _e( "Total", "taskrocket" ); ?></span>
        						<span class="value"><?php echo $all_tasks; ?></span>
        					</span>
        					<span class="tasks-left wide">
        						<span class="label"><?php _e( "Remaining", "taskrocket" ); ?></span>
        						<span class="value"><?php echo $remaining_tasks; ?></span>
        					</span>
        				
        					<span class="heading"><?php _e( "Details", "taskrocket" ); ?></span>
        					
        					<!--/ Start time details /-->
        					<span class="time-details">
        	
        						<?php // Time allocated
        							if($hoursallocated > 0) { ?>
        								<span class="wide">
        									<span class="label"><?php /* translators: Hrs is short for Hours */ _e( "Hrs allocated", "taskrocket" ); ?></span> 
        									<span class="value"><?php echo $hoursallocated; ?></span>
        								</span>
        						<?php } else { ?>
        							<span class="label no"><?php _e( "No time allocated", "taskrocket" ); ?></span>
        						<?php
        							}
        						?>
        	
        						<?php // Time used
        							if($hoursallocated > 0) {
                                        $args = array(
                                        	'numberposts' 	=> -1,
                                        	'offset' 		=> 0,
                                        	'post_status' 	=> 'publish',
                                        	'category' 		=> $category->cat_ID
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
        	
        								if($total != "0" ) { ?>
        									<span class="wide">
        										<span class="label"><?php _e( "Time used", "taskrocket" ); ?></span>
        											<span class="value"><?php echo $thours; ?> <?php _e( "hrs", "taskrocket" ); ?> <?php echo $tmins; ?> <?php /* translators: Mins is short for Minutes */ _e( "mins", "taskrocket" ); ?>
        										</span>
        									</span>
        								<?php } else { ?>
        									<!--span class='label'>No time recorded</span-->
        							<?php
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
        	
        								if($remainingtime > 0 ) { ?>
        									<span class="wide">
        										<span class="label"><?php _e( "Hrs remaining", "taskrocket" ); ?></span>
        										<span class="value"><?php echo $rhours; ?> <?php _e( "hrs", "taskrocket" ); ?> <?php echo $rmin; ?> <?php _e( "mins", "taskrocket" ); ?></span>
        									</span>
        								<?php } else { ?>
        									<span class="over-time roundness" title="<?php _e( "This project has exceeded the allocated time", "taskrocket" ); ?>">
        										<?php _e( "Over time by", "taskrocket" ); ?>
        										<?php 
        										// Calculated over time is the new time allocated minnus the remaining time minus the new time allocated.
        										$calc_over_time = $new_timeallocated - $remainingtime - $new_timeallocated;
        		
        										$over_hours = floor($calc_over_time / 60);
        										$over_minutes = $calc_over_time % 60;
        		
        										/* translators: Example: this is how long the project was overtime. For example: 2hrs and 10 mins */
        										if ($over_hours > 1) {
        											printf( __( '%1$d hrs and %2$d mins' ), $over_hours, $over_minutes); 
                                                } else {
                                                    printf( __( '%1$d hr and %2$d mins' ), $over_hours, $over_minutes); 
                                                }
                                                ?>
        									</span>
        									
        								<?php }
        							}
        						?>
        	
        						<?php // Start and End Dates
        							if(get_option( 'tr_start_date_' . $category->cat_ID )) {
        	
        								$old_start_date_format = get_option( 'tr_start_date_' . $category->cat_ID );
        								$new_start_date_format = new DateTime($old_start_date_format);
        	
        								$old_end_date_format = get_option( 'tr_end_date_' . $category->cat_ID );
        								$new_end_date_format = new DateTime($old_end_date_format);
        	
        								// Let's see if this project is behind schedule
        								$late = get_option( 'tr_end_date_' . $category->cat_ID );
        								if (new DateTime() > new DateTime($late . " 23:59:59")) {
        	
        									$lateclass = "project-is-late roundness"; ?>
        									<span class="wide">
        										<span class="label"><?php _e( "Start date", "taskrocket" ); ?></span>
        										<span class="value"><?php echo $new_start_date_format->format($date_format); ?></span>
        									</span>
        									<span class="<?php echo $lateclass; ?>"><?php _e( "Deadline", "taskrocket" ); ?>:
        										<?php echo $new_end_date_format->format($date_format); ?>
        									</span>
        								<?php } else { ?>
        									<span class="wide">
        										<span class="label"><?php _e( "Start date", "taskrocket" ); ?></span>
        										<span class="value"><?php echo $new_start_date_format->format($date_format); ?></span>
        									</span>
        									
        									<span class="wide">
        										<span class="label"><?php _e( "End date", "taskrocket" ); ?></span>
        										<span class="value"><?php echo $new_end_date_format->format($date_format); ?></span>
        									</span>
        							<?php	}
        						} else { ?>
        								<span class="label no"><?php _e( "No project deadline", "taskrocket" ); ?></span>
        						<?php	}
        	
        						?>
        	
        						<?php if ($options['show_cost'] == true || current_user_can( 'manage_options' )) { ?>
        							
        							<?php // Costs
                                    
                                        $project_rate 	  = get_option( 'tr_hourly_rate_' . $category->cat_ID );
                                        $standard_rate 	  = $options['rate'];
                                        
                                        if($project_rate) { 
                                            $the_rate = $project_rate;
                                        } else if($standard_rate) {
                                            $the_rate = $standard_rate;
                                        } else {
                                            $the_rate = "0";
                                        }
                                    
                                        $budget           = $the_rate * $hoursallocated;
                                        $hrs_used         = ($minutes / 60);
                                        $actual_cost      = $the_rate * $hrs_used;

        								$currency_symbol = $options['currency_symbol'];
        								if ($currency_symbol == "") {
        									$currency_symbol = "$";
        								}
        	
        								if($budget > 0) {
        	
        									if($actual_cost > $budget) {
        										$budgetnote = " class='over-cost value' title='" . __( "This project has exceeded the allocated budget", "taskrocket" ) . "'";
        									} else {
        										$budgetnote = " class='within-cost value' title='" . __( "This project is still within the allocated budget", "taskrocket" ) . "'";
        									} ?>
        									
        									<span class="wide">
        										<span class="label"><?php _e( "Budget", "taskrocket" ); ?></span>
                                                <span<?php echo $budgetnote; ?>><?php echo $currency_symbol . $budget; ?></span>
        									</span>
        									
        									<span class="wide">
    										    <span class="label"><?php _e( "Used", "taskrocket" ); ?></span>
                                                <span<?php echo $budgetnote; ?>><?php echo $currency_symbol . round($actual_cost, 2); ?></span>
        									</span>
        																		
        							<?php	}
        							?>
        
        						<?php } ?>
        					</span>
        					<!--/ End time details /-->
        				
        					<a href="<?php echo get_category_link($category->cat_ID); ?>" class="button-small"><?php _e( "Go to Project", "taskrocket" ); ?></a>
                            <a href="<?php echo home_url(); ?>/single-report/?projectid=<?php echo $category->cat_ID; ?>" class="button-small report-button"><?php _e( "Report", "taskrocket" ); ?></a>
        					
        				</span>
        				<!--/ End Details /-->
        
        				<span class="bar" style="width:<?php echo $percentagecomplete; ?>%; background:#<?php echo $colour; ?>"></span>
                        
                        <?php if($budget > 0) { 
                            if($actual_cost > $budget) { ?>
                            <span class="alert"></span>
                        <?php } } ?>
        
                    </li>
                    <?php } wp_reset_postdata(); ?>
        
        	<?php  } } ?>
            </ul>
        </div>

    </div>
<?php } ?>
<!--/ End Mini Chart /-->