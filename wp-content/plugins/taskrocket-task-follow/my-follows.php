<?php 
global $current_user;
global $userdata; wp_get_current_user();

$current_user_id = $current_user->ID;
$table = $wpdb->prefix . 'tr_follows';

$tr_follows = $wpdb->get_results( 
	"
	SELECT user_id, task_id, slug 
	FROM $table
	WHERE user_id = $current_user_id 
	ORDER BY slug ASC
	"
);
$follows_count = $wpdb->get_var(  // For the count
	"
	SELECT COUNT(*) 
	FROM $table
	WHERE user_id = $current_user_id 
	"
);
if($follows_count > 0) {
?>

<ul class="my-follows">
    <li class="header">
        <?php _e( "Tasks I'm following", "taskrocket-follows" ); ?> <span class="count"><?php echo $follows_count; ?></span>
    </li>
<?php 
foreach ( $tr_follows as $tr_follow ) { 
    $category_detail = get_the_category( $tr_follow->task_id ); ?>
	<li>

		<?php 
			$auth = get_post($tr_follow->task_id);
			$authid = $auth->post_author;
			
			if($authid == "0") {
				$authid = "000000";
			} else {
				$authid = $auth->post_author;
			}
			
			$first_name = get_the_author_meta('first_name',$authid);
			$last_name = get_the_author_meta('last_name',$authid);
			$photo = get_the_author_meta('user_photo',$authid);
			$image_attributes = wp_get_attachment_image_src( $photo );
			$task_name = $category_detail[0]->name;
			
			if (get_option('show_avatars')) {
				echo get_avatar( $authid , '200');
			} else {
				if( $image_attributes ) { ?>
				<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" class="photo" />
				<?php } else { ?>
					<span class="no-photo"></span>
				<?php } ?>
		<?php } 
		?>

		<?php if (current_user_can( 'manage_options' ) ) { ?>

	            <strong><a href="<?php echo get_the_permalink($tr_follow->task_id); ?>"><?php echo get_the_title($tr_follow->task_id); ?></a></strong>
	            <a href="<?php echo get_option( 'category_base' ) . "/" . $category_detail[0]->slug; ?>"><?php echo $task_name; ?></a> 
				&#10140;
				<?php if ($first_name !== "" ) {
					echo $first_name . " " . $last_name;
				} else {
					echo $GLOBALS[ 'nameless' ];
				} ?>
	        
		<?php } else { ?>
			
			<?php if(get_post_meta($tr_follow->task_id, 'private', TRUE) =="yes") { ?>
				
		            <strong><a><?php _e( "Private Task", "taskrocket-follows" ); ?></a></strong>
		            <a href="<?php echo get_option( 'category_base' ) . "/" . $category_detail[0]->slug; ?>"><?php echo $task_name; ?></a>
					&#10140;
					<?php if ($first_name !== "" ) {
						echo $first_name . " " . $last_name;
					} else {
						echo $GLOBALS[ 'nameless' ];
					} ?>
		        
			<?php } else { ?>
				
				<strong><a href="<?php echo get_the_permalink($tr_follow->task_id); ?>"><?php echo get_the_title($tr_follow->task_id); ?></a></strong>
				<a href="<?php echo get_option( 'category_base' ) . "/" . $category_detail[0]->slug; ?>"><?php echo $task_name; ?></a>
				&#10140;
				<?php if ($first_name !== "" ) {
					echo $first_name . " " . $last_name;
				} else {
					echo $GLOBALS[ 'nameless' ];
				} ?>
				
			<?php } ?>
			
		<?php } ?>
		
		<a href="<?php echo plugin_dir_url( __FILE__ ) . 'toggle-follow.php'; ?>?user_ID=<?php echo $current_user_id; ?>&task_ID=<?php echo $tr_follow->task_id; ?>&action=unfollow&source=dashboard" class="unfollow-now" title="<?php _e( "Unfollow", "taskrocket-follows" ); ?>" target="deletey"><?php _e( "Unfollow", "taskrocket-follows" ); ?></a>
	
	</li>
     
<?php } ?>
</ul>
<?php } ?>

<iframe id="deletey" name="deletey" width="0" height="0" frameborder="0"></iframe>