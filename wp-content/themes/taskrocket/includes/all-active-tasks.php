<?php 
$all_active_tasks = get_posts(array(
	'numberposts' 		=> 1000,
	'offset' 			=> 0,
	'post_type' 		=> 'post',
	'category' 			=> $cat_id,
	'post_status'		=> 'publish',
	'orderby' 			=> $_COOKIE['OrderBy'],
	'order' 			=> $_COOKIE['Order'],
	'meta_key' 			=> $_COOKIE['duedate'],
	'meta_value'        => array('incomplete', 'inprogress', 'onhold')
)
);

$all_active_tasks_posts = new WP_Query($all_active_tasks);
$all_active_project_tasks = $all_active_tasks_posts->post_count;
?>
<!--/ Start All Active Tasks /-->
<div class="task-list all-active-list">

	<?php
	if($all_active_project_tasks > 0) {
	$i = 0;
	foreach($all_active_tasks as $post) :
		
	$related_ID           = get_post_meta($post->ID, 'related', TRUE);
	$relationship_details = get_post_meta($post->ID, 'elaboration', TRUE);
	$related_title        = get_the_title($related_ID);
	
	// Get the TASK status of the related task
	$related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 
	
	// Get the POST status of the related task 
	$related_post_status       = get_post_status( $related_ID );
	
	if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to") {
		$relation = __( "Relates to", "taskrocket" );
	} else if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with") { 
		$relation = __( "Has issues with", "taskrocket" );
	} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") { 
		$relation = __( "Is blocked by", "taskrocket" );
	} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to") { 
		$relation = __( "Is similar to", "taskrocket" );
	}
		
	setup_postdata($post);
	if ($options['show_ID'] == true) {
		$showID = '<span class="task-id" title="' . __( "Task ID", "taskrocket" ) . '">' . get_the_ID() . '</span>';
	}
	?>
	<div class="task border-soft roundness<?php if( get_post_meta($post->ID, 'priority', TRUE) != "" ) { echo " task-priority-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " task-priority-normal"; } ?> <?php if(get_post_meta($post->ID, 'role', TRUE) !="" ) { echo " client-created-task"; } ?> <?php echo "row-" . ($i++ % 2); ?> <?php if ($options['alternative_task_style'] == true) { echo "alt-task-style"; } ?> <?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { echo "higher-min-height"; } ?>">

	<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>

	<h2>
		<?php 
		// If an admin, project manager, owner of the task, and not a client
		if (current_user_can( 'manage_options' ) || $current_user_id == $project_manager || $post->post_author == $current_user->ID && !current_user_can('client')) { ?>
		<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
		<?php } ?>
		
		<?php // if a client that can mark own tasks as complete, and you own the task
		if (current_user_can('client') && $options['clients_mark_own_tasks_complete'] == true && $post->post_author == $current_user->ID) { ?>
		<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
		<?php } ?>

		<?php // If you are an administrator....
		if (current_user_can( 'manage_options' ) ) { ?>

			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

			<?php // ... otherwise you must be a project contributor.
			} else { ?>

			<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
			<?php _e( "Private Task", "taskrocket" ); ?>
			<?php } else { ?>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php } ?>

		<?php } ?>
	</h2>
	
	<?php if(get_post_meta($post->ID, 'relation', TRUE) && $related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
		<p class="relationship">
			<span class="show-relationship"><?php _e( "This task", "taskrocket" ); ?> <?php echo $relation; ?></span> 
			<a href="<?php echo get_the_permalink($related_ID); ?>"><?php echo $related_title; ?></a> <?php if($relationship_details) { echo '<em class="dots">...</em>'; } ?>
		</p>
		<?php if($relationship_details) { ?>
			<div class="relationship-details">
				<div>
					<p><strong>'<?php the_title(); ?>' <strong class="emphasis"><?php echo $relation; ?></strong> '<?php echo $related_title; ?>'</strong></p>
					<p><?php echo $relationship_details; ?></p>
					<a class="button-small"><?php _e( "OK", "taskrocket" ); ?></a>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	
	<span class="author-name">
	<?php 
		$author = get_the_author();
		if($author !=="") {
			if (get_the_author_meta( 'first_name') !== "" ) {
				echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
			} else {
				echo $GLOBALS[ 'nameless' ];
			}
		} else {
			echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket" ) . ')'; ?>
			<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
		<?php }
	?>
	</span>

	<span class="priority">
		<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
			<em class="priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>" title="<?php echo get_post_meta($post->ID, 'priority', TRUE); ?> priority"><?php echo get_post_meta($post->ID, 'priority', TRUE); ?> <?php _e( "Priority", "taskrocket" ); ?></em>
		<?php } else { ?>
			<em class="priority-normal" title="<?php _e( "Normal Priority", "taskrocket" ); ?>"><?php _e( "Normal Priority", "taskrocket" ); ?></em>
		<?php } ?>
	</span>

	<?php //If you are an administrator....
	if (current_user_can( 'manage_options' ) ) { ?>

	<?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { // If there is content... ?>
	<!--/ Start Options /-->
	<div class="options">
		<a class="toggle-show-more show-more-<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { echo get_post_meta($post->ID, 'priority', TRUE); } else { echo "normal"; } ?> linky"><?php _e( "More Info", "taskrocket" ); ?></a>
		<pre>
			<?php // Return string with Links, if condition is met.
			if ($options['disable_make_clickable'] == false) {
				$all_active_tasks_string = strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
				echo make_clickable( $all_active_tasks_string );
			} else {
				echo strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
			} ?>
		</pre>
	</div>
	<!--/ End Options /-->
	<?php } ?>

	<?php require($GLOBALS[ 'theme_includes' ] . 'attachments.php'); ?>
	
	<p class="task-details">
		<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
	</p>
	
	<?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") {  ?>
	<div class="blocked-alert">
		<div>
			<p>
				<?php printf( __('This task is currently blocked by <a href="%1$s">%2$s</a>. Change its status and unblock anyway?', 'taskrocket' ), $related_URL, $related_title ); ?>
			</p>
			<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=complete&type=task&user_ID=<?php echo $user_ID; ?>&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>&relation=none" class="button-small delete-yes delete-yes-complete"><?php _e( "Yes", "taskrocket" ); ?></a>
			
			<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=inprogress&type=task&project_ID=<?php echo $cat_id; ?>&owner_ID=<?php echo get_the_author_meta('ID'); ?>&location=<?php echo $current_url; ?>&relation=none" class="button-small delete-yes delete-yes-inprogress"><?php _e( "Yes", "taskrocket" ); ?></a>
			
			<a class="button-small delete-no"><?php _e( "No", "taskrocket" ); ?></a>
			
		</div>
	</div>
	<?php } ?>

	<?php // ... otherwise you must be a project contributor.
	} else { ?>

	<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>

	<?php } else { ?>

	<?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { // If there is content... ?>
	<!--/ Start Options /-->
	<div class="options">
		<a class="toggle-show-more show-more-<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { echo get_post_meta($post->ID, 'priority', TRUE); } else { echo "normal"; } ?> linky"><?php _e( "More info", "taskrocket" ); ?></a>
		<pre>
			<?php // Return string with Links, if condition is met.
			if ($options['disable_make_clickable'] == false) {
				$all_active_tasks_string = strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
				echo make_clickable( $all_active_tasks_string );
			} else {
				echo strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
			} ?>
		</pre>
	</div>
	<!--/ End Options /-->
	<?php } ?>

	<?php require($GLOBALS[ 'theme_includes' ] . 'attachments.php'); ?>
	<p class="task-details">
		<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
	</p>
	
	<?php } ?>

	<?php } ?>

	</div>
	<?php endforeach; ?>
	<?php wp_reset_postdata();
} else { ?>
	<div class="tasks-empty">
		<h2><?php _e( "There are no tasks in this project.", "taskrocket" ); ?></h2>
	</div>
<?php } ?>

<script>
	jQuery('.all-active-list .inprogress').click(function () {
		jQuery( event.target ).closest('div').find('.blocked-alert').fadeIn();
		jQuery( event.target ).closest('div').find('.delete-yes-inprogress').fadeIn();
	});
	jQuery('.all-active-list .complete').click(function () {
		jQuery( event.target ).closest('div').find('.blocked-alert').fadeIn();
		jQuery( event.target ).closest('div').find('.delete-yes-complete').fadeIn();
	});
	jQuery('.all-active-list .delete-no').click(function () {
		jQuery('.blocked-alert').fadeOut();
		jQuery( event.target ).closest('div').find('.delete-yes').fadeOut();
	});
</script>

</div>
<!--/ End All Active Tasks /-->
