<?php
$all_completed_tasks = get_posts(array(
	'numberposts' 		=> 1000,
	'offset' 			=> 0,
	'post_type' 		=> 'post',
	'category' 			=> $cat_id,
	'post_status'		=> 'publish',
	'meta_key'          => 'tr_status',
	'orderby' 			=> $_COOKIE['OrderBy'],
	'order' 			=> $_COOKIE['Order'],
	'meta_key' 			=> $_COOKIE['duedate'],
	'meta_value'        => array('complete')
)
);
$all_completed_tasks_posts = new WP_Query($all_completed_tasks);
$all_completed_project_tasks = $all_completed_tasks_posts->post_count;
?>
<!--/ Start All Completed Tasks /-->
<div class="task-list completed-list all-completed-list">

	<?php
	if($all_completed_project_tasks > 0) {
	$i = 0;
	foreach($all_completed_tasks as $post) :
	setup_postdata($post);
	if ($options['show_ID'] == true) {
		$showID = '<span class="task-id" title="' .__( "Task ID", "taskrocket" ) . '">' . get_the_ID() . '</span>';
	}
	?>

	<?php
		$_wpnonce = wp_create_nonce( 'untrash-post_' . get_the_ID() );
		$restore = admin_url( 'post.php?post=' . get_the_ID() . '&action=untrash&_wpnonce=' . $_wpnonce );
	?>
	<div class="task border-soft roundness <?php if(!current_user_can( 'client' )) { if(get_post_meta($post->ID, 'role', TRUE) !="" ) { echo " client-created-task"; } } ?> <?php echo "row-" . ($i++ % 2); ?> <?php if ($options['alternative_task_style'] == true) { echo "alt-task-style"; } ?> <?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { echo "higher-min-height"; } ?>">

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
			echo $GLOBALS[ 'nameless' ] . ' (unowned task)'; ?>
			<a href="<?php the_permalink() ?>?take=yes" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
		<?php }
	?>
	</span>

	<?php //If you are an administrator....
	if (current_user_can( 'manage_options' ) ) { ?>

	<?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { // If there is content... ?>
	<!--/ Start Options /-->
	<div class="options">
		<a class="toggle-show-more show-more-<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { echo get_post_meta($post->ID, 'priority', TRUE); } else { echo "normal"; } ?> linky"><?php _e( "More info", "taskrocket" ); ?></a>
		<pre>
			<?php // Return string with Links, if condition is met.
			if ($options['disable_make_clickable'] == false) {
				$all_completed_tasks_string = strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
				echo make_clickable( $all_completed_tasks_string );
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
				$all_completed_tasks_string = strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
				echo make_clickable( $all_completed_tasks_string );
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
		<h2><?php _e( "There are no completed tasks.", "taskrocket" ); ?></h2>
	</div>
<?php } ?>
</div>
<!--/ End All Completed Tasks /-->
