<?php // If home page or task page.
if(is_single() || is_home()) {
	$current_user_id = $user_ID;
	$task_id = $post->ID;
	$table = $wpdb->prefix . 'tr_follows';

	$tr_follows = $wpdb->get_results( 
		"
		SELECT user_id, task_id 
		FROM $table
		WHERE user_id = $current_user_id 
	    AND task_id = $task_id
		"
	);
	foreach ( $tr_follows as $tr_follow ) { 
	    //echo $tr_follow->user_id . "(user_id DB)<br />";
	    //echo $current_user_id . " (current user id)<br />"; 
	    //echo $tr_follow->task_id . "(task_id DB)<br />";
	    //echo $task_id . "(task_id)";
	}

	$follows_count = $wpdb->get_var( 
		"
		SELECT COUNT(*) 
		FROM $table
		WHERE user_id = $current_user_id 
		"
	);
} // End if home page or task page.
?>

<?php // If on a task page.
if(is_single()) { ?>
	<?php if($tr_follow->user_id == $current_user_id && $tr_follow->task_id == $task_id) { ?>
	    <a href="<?php echo plugin_dir_url( __FILE__ ) . 'toggle-follow.php'; ?>?user_ID=<?php echo $user_ID; ?>&task_ID=<?php echo get_the_ID(); ?>&action=unfollow&task_name=<?php echo $post->post_name; ?>"><span class="top-toggle starred master-tooltip active" title="<?php _e( "Stop following this task", "taskrocket-follows" ); ?>"><?php _e( "Stop following this task", "taskrocket-follows" ); ?></span></a>
	<?php } else { ?>
	    <a href="<?php echo plugin_dir_url( __FILE__ ) . 'toggle-follow.php'; ?>?user_ID=<?php echo $user_ID; ?>&task_ID=<?php echo get_the_ID(); ?>&action=follow&task_name=<?php echo $post->post_name; ?>"><span class="top-toggle not-starred master-tooltip" title="<?php _e( "Follow this task", "taskrocket-follows" ); ?>"><?php _e( "Follow this task", "taskrocket-follows" ); ?></span></a>
	<?php } ?>
<?php } ?>

<?php // If on the home page.
if(is_home()) { ?>
	<?php if($follows_count > 0) { ?>
		<span class="top-toggle toggle-follows master-tooltip active" title="<?php _e( "Tasks I'm following", "taskrocket-follows" ); ?>"><?php _e( "Tasks I'm following", "taskrocket-follows" ); ?></span>
	<?php } ?>
<?php } ?>