<?php
	$options = get_option( 'taskrocket_settings' );
	global $userdata; wp_get_current_user();
	$tab_1 = $userdata->tab_1;
	$tab_2 = $userdata->tab_2;
	$tab_3 = $userdata->tab_3;
	$tab_4 = $userdata->tab_4;
?>
	
	<?php 
		function mat() { 
		global $my_tasks; ?>
    	<li><a class="small-button light-grey my-active"><?php _e( "My Active Tasks", "taskrocket" ); ?> <span class="count"><?php if ($my_tasks > 0) { echo $my_tasks; } else { echo "0"; } ?></span></a></li>
	<?php } ?>
	
	<?php 
		function mct() { 
		global $my_completed_tasks; ?>
    	<li><a class="small-button light-grey my-completed"><?php _e( "My Completed Tasks", "taskrocket" ); ?> <span class="count"><?php if ($my_completed_tasks > 0) { echo $my_completed_tasks; } else { echo "0"; } ?></span></a></li>
	<?php } ?>
	
	<?php 
		function aat() { 
		global $remaining_tasks; ?>
    	<li><a class="small-button light-grey all-active"><?php _e( "All Active Tasks", "taskrocket" ); ?> <span class="count"><?php if ($remaining_tasks > 0) { echo $remaining_tasks; } else { echo "0"; } ?></span></a></li>
	<?php } ?>
	
	<?php 
		function act() { 
		global $all_completed_tasks; ?>
    	<li><a class="small-button light-grey all-completed"><?php _e( "All Completed Tasks", "taskrocket" ); ?> <span class="count"><?php if ($all_completed_tasks > 0) { echo $all_completed_tasks; } else { echo "0"; } ?></span></a></li>
	<?php } ?>

<!--/ Start Toolbar /-->
<div class="toolbar">
	
	<ul>
		<?php 
			// If the user has not set any preferences, then use this default set
			if($tab_1 == "") {
				// Only show the MAT tab if the clints has a task
				if (!current_user_can( 'client' )) {
					mat();
				} else if ($my_tasks > 0) {
					mat();
				}
				// Only show the MCT tab if the clints has a completed task
				if (!current_user_can( 'client' )) {
					mct();
				} else if ($my_completed_tasks > 0) {
					mct();
				}
				if(current_user_can( 'client' )) { // If the uset is a client
					if($options['clients_see_tasks'] == "1") { 
						aat();
						act();
					}
				} else { // Otherwise, user must be an editor or administrator
					if($options['users_only_see_own_tasks'] !== "1" || current_user_can( 'manage_options' )) { 
						aat();
						act();
					}
				}
			// Otherwise, they must have set a preference set, so do this instead:	
			} else {
		
				// Tab 1
				if($tab_1 =="mat") {
					mat();
				} else if($tab_1 =="mct") {
					mct();
				} else if($tab_1 =="aat") {
					aat();
				} else if($tab_1 =="act") {
					act();
				} 
				 // Tab 2
				if($tab_2 =="mat") {
					mat();
				} else if($tab_2 =="mct") {
					mct();
				} else if($tab_2 =="aat") {
					aat();
				} else if($tab_2 =="act") {
					act();
				} 
				
				if(current_user_can( 'client' )) { // If the uset is a client
					if($options['clients_see_tasks'] == "1") { 
						 // Tab 3
						if($tab_3 =="mat") {
							mat();
						} else if($tab_3 =="mct") {
							mct();
						} else if($tab_3 =="aat") {
							aat();
						} else if($tab_3 =="act") {
							act();
						} 
					}
				} else {  // Otherwise, user must be an editor or administrator
					if($options['users_only_see_own_tasks'] !== "1" || current_user_can( 'manage_options' )) { 
						
						// Tab 3
					   if($tab_3 =="mat") {
						   mat();
					   } else if($tab_3 =="mct") {
						   mct();
					   } else if($tab_3 =="aat") {
						   aat();
					   } else if($tab_3 =="act") {
						   act();
					   } 
						
						// Tab 4
					   if($tab_4 =="mat") {
						   mat();
					   } else if($tab_4 =="mct") {
						   mct();
					   } else if($tab_4 =="aat") {
						   aat();
					   } else if($tab_4 =="act") {
						   act();
					   } 
						   
					}
				}
			}
		?>

		<li><a class="show-sorter"><?php /* translators: meaning 'to change the order of' */ _e( "Sort", "taskrocket" ); ?></a></li>
	</ul>
	
</div>
<!--/ End Toolbar /-->

<ul class="<?php echo $_COOKIE['OrderBy'];?> sorter">
	<li><a href="?orderby=comment_count" class="comment_count"><?php _e( "Comments", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=date" class="date"><?php _e( "Date Added", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=duedate" class="duedate"><?php _e( "Due Dated", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=modified" class="modified"><?php _e( "Modified", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=author" class="author"><?php _e( "Owner", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=rand" class="rand"><?php _e( "Random", "taskrocket" ); ?></a></li>
	<li><a href="?orderby=title" class="title"><?php _e( "Title", "taskrocket" ); ?></a></li>
	<li class="c-asc"><a href="?&order=asc" class="asc <?php if ($_COOKIE['Order'] == 'asc') { echo " active"; } ?>"><?php /* translators: short for 'ascending' */ _e( "ASC", "taskrocket" ); ?></a></li>
	<li class="c-desc"><a href="?&order=desc" class="desc <?php if ($_COOKIE['Order'] == 'desc') { echo " active"; } ?>"><?php /* translators: short for 'descending' */ _e( "DESC", "taskrocket" ); ?></a></li>
</ul>