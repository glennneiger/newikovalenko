
<?php
$postType = "task_groups";
$count_posts = wp_count_posts( $postType )->publish;
$type = $postType;
if ( $count_posts > 0 ) { 
?>

<label for="group"><?php _e( 'Include tasks from this group', 'taskrocket-task-groups'); ?></label>
<select name="group" id="group">
    <option></option>
    <?php
    $all_groups = get_posts(array(
        'numberposts' 	=> 1000,
        'offset' 		=> 0,
        'post_type' 	=> $type,
        'post_status'	=> 'publish',
        'orderby'		=> 'title',
        'post_parent' 	=> 0
        )
    );
    foreach($all_groups as $post) :
    setup_postdata($post);
    ?>
    <option id="<?php echo get_the_ID(); ?>" value="<?php echo get_the_ID(); ?>"><?php the_title(); ?></option>
    <?php endforeach; ?>
</select>
<?php } else { ?>
    <p><?php _e( 'There are currently no task groups.', 'taskrocket-task-groups'); ?></p>
<?php } ?>