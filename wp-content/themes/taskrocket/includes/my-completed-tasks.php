<?php
$author = get_current_user_id();

$my_completed_tasks = get_posts(array(
	'numberposts' 		=> 1000,
	'offset' 			=> 0,
	'category' 			=> $cat_id,
	'post_status'		=> 'publish',
	'post_type'         => 'post',
	'author' 			=> $author,
	'orderby' 			=> $_COOKIE['OrderBy'],
	'order' 			=> $_COOKIE['Order'],
	'meta_key'          => 'tr_status',
	'meta_value'        => array('complete')
)
);
$my_completed_tasks_posts = new WP_Query($my_completed_tasks);
$my_completed_project_tasks = $my_completed_tasks_posts->post_count;
?>
<!--/ Start My Completed Tasks /-->
<div class="task-list my-completed-list">

	<?php
	if($my_completed_project_tasks > 0) {
	$i = 0;
	foreach($my_completed_tasks as $post) :
	setup_postdata($post);
	if ($options['show_ID'] == true) {
		$showID = '<span class="task-id" title="' . __( "Task ID", "taskrocket" ) . '">' . get_the_ID() . '</span>';
	}
	?>

	<?php
	$_wpnonce = wp_create_nonce( 'untrash-post_' . get_the_ID() );
	$restore = admin_url( 'post.php?post=' . get_the_ID() . '&action=untrash&_wpnonce=' . $_wpnonce );
	?>
	<div class="task <?php echo "row-" . ($i++ % 2); ?> <?php if ($options['alternative_task_style'] == true) { echo "alt-task-style"; } ?> <?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { echo "higher-min-height"; } ?>">

	<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>

	<h2>
		<?php if ($post->post_author == $current_user->ID) { // If the post author is the current user ?>
		<?php require($GLOBALS[ 'theme_includes' ] . 'action.php'); ?>
		<?php } ?>
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
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
			echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket" ) . ')'; ?>
			<a href="<?php the_permalink() ?>?take=yes" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
		<?php }
	?>
	</span>
	
	<?php if( get_post_meta($post->ID, 'minfo', TRUE) != '' ) { // If there is content... ?>
		
	<!--/ Start Options /-->
	<div class="options">
		<a class="toggle-show-more show-more-<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { echo get_post_meta($post->ID, 'priority', TRUE); } else { echo "normal"; } ?> linky"><?php _e( "More info", "taskrocket" ); ?></a>
		<pre>
			<?php // Return string with Links, if condition is met.
			if ($options['disable_make_clickable'] == false) {
				$my_completed_tasks_string = strip_tags(get_post_meta($post->ID, 'minfo', TRUE));
				echo make_clickable( $my_completed_tasks_string );
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
	
	</div>
	<?php endforeach; ?>
	<?php wp_reset_postdata();
} else { ?>
	<div class="tasks-empty">
		<h2><?php _e( "You haven't completed any tasks.", "taskrocket" ); ?></h2>
	</div>
<?php } ?>

</div>
<!--/ End My Completed Tasks /-->