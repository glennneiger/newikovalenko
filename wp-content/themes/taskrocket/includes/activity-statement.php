<?php wp_count_terms( 'category');

$userID = $current_user->ID;

// Count projects
$projectCount = wp_count_terms( 'category', array( 'hide_empty' => TRUE));

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


// Completed tasks
$completed_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'meta_key'  		=> 'tr_status',
	'meta_value' 		=> 'complete'
);
$completed_tasks_posts = new WP_Query($completed_tasks_args);
$completed_tasks = $completed_tasks_posts->post_count;


// My tasks
$my_tasks_args = array(
	'posts_per_page' 	=> -1,
	'post_type' 		=> 'post',
	'post_status'		=> 'publish',
	'author' 			=> $userID,
	'meta_key'          => 'tr_status',
	'meta_value'        => array('incomplete', 'inprogress', 'onhold')
);
$my_tasks_posts = new WP_Query($my_tasks_args);
$my_tasks = $my_tasks_posts->post_count;


// Everyone elses tasks
$everyone_else = $all_tasks - $my_tasks;

// Fix division by zero
if ($my_tasks != 0) {
	$task_load = $my_tasks / $all_tasks * 100;
	$inverse = 100 - $task_load;
	$opposite = 100 - $task_load;
} else {
	$inverse = 0;
}

// Unassigned label
$unassigned_label = $options['unassigned_label'];
if($unassigned_label =="") {
	$the_lable = "Unassigned";
} else {
	$the_lable = $unassigned_label;
}

?>

<!--/ Start Stats /-->
<div class="stats-container">

	<ul class="panel stats">
		<!--/ Start My Tasks /-->
		<li class="your-tasks">
			<p class="value">
				<?php echo $my_tasks; ?> <span> / <?php echo $all_tasks; ?></span>
	        </p>
	    
			<p class="description">
				<strong><?php _e( "Your tasks", "taskrocket" ); ?></strong>
				<?php if ($my_tasks > 0) { ?>
			
					<?php printf( __( 'You have <span>%1$d</span> outstanding tasks out of all <span>%2$d</span> total tasks.', 'taskrocket' ), $my_tasks, $all_tasks); ?>
					
				<?php } else { ?>
					<?php _e( "Sweet! There aren't any tasks for you to do!", "taskrocket" ); ?>
				<?php } ?>
			</p>
			
			<?php
			if ($my_tasks > 0) {
				$low_priority_args = array(
					'posts_per_page'	=> -1,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'author' 			=> $userID,
					'meta_query' 		=> array(
						'relationship'  	=> 'AND',
						array(
							'key'   	=> 'priority',
							'value' 	=> 'low'
						),
						array(
							'key'     	=> 'tr_status',
							'value'   	=> array('incomplete', 'inprogress', 'onhold'),
							'compare' 	=> 'IN' 
						)
					)
				);
				$low_bar = new WP_Query($low_priority_args);
				$low_bar_count = $low_bar->post_count;
				$low_bar_percentage = $low_bar_count / $my_tasks * 100;
				
				$normal_priority_args = array(
					'posts_per_page'	=> -1,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'author' 			=> $userID,
					'meta_query' 		=> array(
						'relationship'  	=> 'AND',
						array(
							'key'   	=> 'priority',
							'value' 	=> array('normal', '')
						),
						array(
							'key'     	=> 'tr_status',
							'value'   	=> array('incomplete', 'inprogress', 'onhold', ''),
							'compare' 	=> 'IN' 
						)
					)
				);
				$normal_bar = new WP_Query($normal_priority_args);
				$normal_bar_count = $normal_bar->post_count;
				$normal_bar_percentage = $normal_bar_count / $my_tasks * 100;
				
				$high_priority_args = array(
					'posts_per_page'	=> -1,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'author' 			=> $userID,
					'meta_query' 		=> array(
						'relationship'  	=> 'AND',
						array(
							'key'   	=> 'priority',
							'value' 	=> 'high'
						),
						array(
							'key'     	=> 'tr_status',
							'value'   	=> array('incomplete', 'inprogress', 'onhold'),
							'compare' 	=> 'IN' 
						)
					)
				);
				$high_bar = new WP_Query($high_priority_args);
				$high_bar_count = $high_bar->post_count;
				$high_bar_percentage = $high_bar_count / $my_tasks * 100;

				
				$urgent_priority_args = array(
					'posts_per_page'	=> -1,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'author' 			=> $userID,
					'meta_query' 		=> array(
						'relationship'  	=> 'AND',
						array(
							'key'   	=> 'priority',
							'value' 	=> 'urgent'
						),
						array(
							'key'     	=> 'tr_status',
							'value'   	=> array('incomplete', 'inprogress', 'onhold'),
							'compare' 	=> 'IN' 
						)
					)
				);
				$urgent_bar = new WP_Query($urgent_priority_args);
				$urgent_bar_count = $urgent_bar->post_count;
				$urgent_bar_percentage = $urgent_bar_count / $my_tasks * 100;
			}
			
			// Floor usage
			// floor($low_bar_percentage * 100) / 100; // Round down to 2 decimal places.
			// floor($low_bar_percentage; // Round down to nearest whole number
			
			?>
			<?php if($my_tasks > 0) { ?>
				<span class="grey-bar">
					<span class="low" style="width:<?php echo floor($low_bar_percentage * 100) / 100; ?>%" title="<?php echo round(floor($low_bar_percentage * 100) / 100, 1); ?>% <?php _e( "of your tasks are low priority", "taskrocket" ); ?>"><em><?php echo round(floor($low_bar_percentage * 100) / 100, 1); ?><sup>%</sup></em></span>
					<span class="normal" style="width:<?php echo floor($normal_bar_percentage * 100) / 100; ?>%" title="<?php echo round(floor($normal_bar_percentage * 100) / 100, 1); ?>% <?php _e( "of your tasks are normal priority", "taskrocket" ); ?>"><em><?php echo round(floor($normal_bar_percentage * 100) / 100, 1); ?><sup>%</sup></em></span>
					<span class="high" style="width:<?php echo floor($high_bar_percentage * 100) / 100; ?>%" title="<?php echo round(floor($high_bar_percentage * 100) / 100, 1); ?>% <?php _e( "of your tasks are high priority", "taskrocket" ); ?>"><em><?php echo round(floor($high_bar_percentage * 100) / 100, 1); ?><sup>%</sup></em></span>
					<span class="urgent" style="width:<?php echo floor($urgent_bar_percentage * 100) / 100; ?>%" title="<?php echo round(floor($urgent_bar_percentage * 100) / 100, 1); ?>% <?php _e( "of your tasks are urgent priority", "taskrocket" ); ?>"><em><?php echo round(floor($urgent_bar_percentage * 100) / 100, 1); ?><sup>%</sup></em></span>
				</span>
			<?php } ?>

	    </li>
		<!--/ End My Tasks /-->

		<!--/ Start Load Carried /-->
	     <li>
			 <?php $loadDiff = 100 - round($task_load, 0);
	 		if ($task_load == 0) { ?>
	 		
	 			<p class="value">0<em class='percentage'>%</em></p>

	 		<?php } else { ?>

				<p class="value"><?php echo round($task_load, 0); ?><em class='percentage'>%</em></p>
	 		
	 		<?php } ?>
			
			<p class="description">
				<strong><?php _e( "Load carried", "taskrocket" ); ?></strong>
				<?php _e( "You are reponsible for", "taskrocket" ); ?>
				<?php if ($task_load !=0) { ?>
					<span><?php echo round($task_load, 0); ?>%</span> <?php _e( "of all tasks across all projects.", "taskrocket" ); ?>
				<?php } else { ?>
					<?php _e( "no tasks on any projects.", "taskrocket" ); ?>
				<?php } ?>
			</p>
			<div class="bar">
				<div style="width:<?php echo round($task_load, 0); ?>%" class="progress"></div>
			</div>
	    </li>
		<!--/ End Load Carried /-->

		<!--/ Start Tasks For Everyone Else /-->
	    <li>
	        <p class="value else<?php if ($everyone_else == 0) { echo " no-else"; } ?>"><?php
				echo $everyone_else;?>
	        </p>
	        <p class="description">
				<strong><?php _e( "Everyone else", "taskrocket" ); ?></strong>
				
				<?php if ($everyone_else > 1) { ?>
					<?php printf( __( 'There are <span>%1$d</span> outstanding tasks for other users, they carry <span>%2$d&#37</span> of the load.', 'taskrocket' ), $everyone_else, round($opposite, 0)); ?>
		        <?php } ?>

		        <?php  if ($everyone_else < 1) { ?>
		        	<?php _e( "There are no outstanding tasks for other people.", "taskrocket" ); ?>
		        <?php } ?>

		        <?php  if ($everyone_else == 1) { ?>
					<?php _e( 'There is <span>1</span> outstanding task for someone else.', 'taskrocket' ); ?>
		        <?php } ?>
			</p>
			<div class="bar others">
				<div style="width:<?php echo round($opposite, 0); ?>%" class="progress"></div>
			</div>
	    </li>
		<!--/ End Tasks For Everyone Else /-->

		<!--/ Start Total Outstanding Tasks /-->
	    <li>
	        <p class="value outstanding<?php if ($all_tasks == 0) { echo " no-outstanding"; } ?>"><?php // outstanding tasks
				echo $all_tasks;?>
	        </p>
			
	        <p class="description">
			<strong><?php _e( "Total outstanding tasks", "taskrocket" ); ?></strong>
			<?php if ($all_tasks > 0) { ?>
				<?php printf( __( 'There are <span>%1$d</span> outstanding tasks across all active projects.', 'taskrocket' ), $all_tasks); ?>
			<?php } else { ?>
	        	<?php _e( "There are no outstanding tasks across all active projects.", "taskrocket" ); ?>
	        <?php } ?>
			</p>
			<?php if($options['recent_tasks'] > 0) { ?>
			<a href="#recent" class="button-small view-tasks"><?php _e( "View Tasks", "taskrocket" ); ?></a>
			<?php } else { ?>
				<?php if ($options['users_create_tasks'] == true || current_user_can( 'manage_options' )) { ?>
					<a href="<?php echo home_url();?>/new-task/" class="button-small view-tasks"><?php _e( "Create Task", "taskrocket" ); ?></a>
				<?php } ?>
			<?php } ?>
			
	    </li>
		<!--/ End Total Outstanding Tasks /-->

		<!--/ Start Active Projects /-->
	    <li>
	        <p class="value active<?php if ($projectCount == 0) { echo " no-active"; } ?>"><?php // active projects
				echo $projectCount; ?>
	        </p>
	        <p class="description">
				<strong><?php _e( "Active projects", "taskrocket" ); ?></strong>
	        <?php if ($projectCount > 1) { ?>
	        	<?php printf( __( 'There is a total of <span>%1$d</span> active projects including', 'taskrocket' ), $projectCount); ?> <?php echo $the_lable; ?>.
	        <?php } else { ?>
	        	<?php _e( "There are no active projects including", "taskrocket" ); ?> <?php echo $the_lable; ?>.
	        <?php } ?>
			</p>
			<a href="<?php echo home_url(); ?>/<?php echo get_option( 'category_base' ); ?>/" class="button-small"><?php _e( "View Projects", "taskrocket" ); ?></a>
	    </li>
		<!--/ End Active Projects /-->

	</ul>
</div>
<!--/ End Stats /-->