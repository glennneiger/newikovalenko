<?php $categories = get_categories(array(
    'echo'			   => 0,
    'hide_empty'       => 0,
    'taxonomy'         => 'category',
    'hierarchical'     => 1,
    'show_count' 	   => 1,
    'depth' 		   => 0
)); ?>

<?php wp_count_terms( 'category');
$project_count = wp_count_terms( 'category', array( 'hide_empty' => TRUE)); ?>

<li class="projects <?php if(is_page('projects')) { echo "current-cat"; } ?>"><a href="<?php echo home_url(); ?>/projects"><?php _e( "All Active Projects", "taskrocket" ); ?></a>
<span class="active-projects-count"><?php echo $project_count; ?></span></li>

<?php foreach ($categories as $category) {
    
    $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
    
    $project_args = array(
        'posts_per_page' 	=> -1,
        'post_type' 		=> 'post',
        'showposts'         => -1,
        'post_status'       => array('publish'),
        'cat'               => $category->cat_ID,
        'meta_key'          => 'tr_status',
        'meta_value'        => array('incomplete', 'inprogress', 'onhold')
    );
    $project_tasks_posts = new WP_Query($project_args);
    $project = $project_tasks_posts->post_count;
    if($project == 0) {
        $title = __( "There are no active tasks.", "taskrocket" );
        $zero = "zero";
    } else if($project == 1) {
        $title = __( "1 task remaining", "taskrocket" );
        $zero = "";
    } else {
        $title = $project . " " . __( "Tasks remaining", "taskrocket" );
        $zero = "";
    }
    if (!$project_archived) { 
    ?>
    <li class="<?php echo $zero; ?> <?php if($cat == $category->cat_ID) { echo 'current-cat'; } ?>" title="<?php echo $title; ?>">
        <a href="<?php echo get_category_link( $category->cat_ID ); ?>">
            <?php echo $category->cat_name; ?>
            <span><?php echo $project; ?></span>
        </a>
    </li>

<?php } } wp_reset_postdata(); ?>