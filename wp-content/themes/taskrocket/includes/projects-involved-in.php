<ul class="my-projects">
    <?php 
global $current_user;
wp_get_current_user();

// Count categories for this author
$args = array( 
	'author' 		   => $current_user->ID, 
	'echo'			   => 0,
    'hide_empty'       => 0,
    'taxonomy'         => 'category',
    'hierarchical'     => 1,
    'depth' 		   => 0
);
$get_posts = new WP_Query( $args );

	if($get_posts->have_posts()) {
		  while($get_posts->have_posts()) { $get_posts->the_post();
			$post_categories = wp_get_post_categories( get_the_ID() );
			foreach($post_categories as $c){
				$cat = get_category( $c );
				$cats[] = $cat->name .','.get_category_link( $cat->term_id ) ;
				$i++;
			}
		} //endwhile

		$vals = sort($cats);
		$vals = array_count_values($cats);
		$my_projects = count($vals);
	}
?>
    <li class="header">
        <?php _e( "Projects I'm involved in", "taskrocket-follows" ); ?>
    </li>

<?php 
$categories = get_categories(array(
    'echo'			   => 0,
    'hide_empty'       => 0,
    'taxonomy'         => 'category',
    'hierarchical'     => 1,
    'depth' 		   => 0
)); 

// Current project ID
$projects = get_category( get_query_var( 'cat' ) );
$project_id = $projects->cat_ID;

foreach ($categories as $category) {
	
	$project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
    
    $project_args = array(
        'posts_per_page' 	=> -1,
        'post_type' 		=> 'post',
        'showposts'         => -1,
        'post_status'       => array('publish'),
        'cat'               => $category->cat_ID,
		'author' 		    => $current_user->ID, 
        'meta_key'          => 'tr_status',
        'meta_value'        => array('incomplete', 'inprogress', 'onhold')
    );
    $project_tasks_posts = new WP_Query($project_args);
    $project = $project_tasks_posts->post_count;
    if($project == 0) {
        $title = __( "There are no active tasks.", "taskrocket-follows" );
        $highlight = "highlight";
    } else if($project == 1) {
        $title = __( "1 task remaining", "taskrocket-follows" );
        $highlight = "";
    } else {
        $title = $project . " " . __( "Tasks remaining", "taskrocket-follows" );
        $highlight = "";
    }
    ?>
	<?php 
	if($project !==0) { 
		if (!$project_archived) { ?>
            <li>
                <a href="<?php echo get_option( 'category_base' ) . '/' . $category->slug; ?>">
                    <?php echo $category->name; ?> <span class="my-task-count" title="<?php echo $project; ?> <?php _e( "tasks for me", "taskrocket-follows" ); ?> "><?php echo $project; ?></span>
                    <?php // Start if there is a PM 
                    $pm = get_option( 'tr_project_manager_' . $category->cat_ID);
                    if($pm) { ?>
                    <span class="pm">
                        <?php _e( "Project Manager:", "taskrocket-follows" ); ?> 
                        <?php
                            $user_info = get_userdata($pm);
                            echo $user_info->user_firstname . " " . $user_info->user_lastname;
                        ?>
                    </span>
                    <?php } // End if there is a PM ?>
                </a>
            </li>
	<?php } } ?>

<?php } wp_reset_postdata(); ?>
</ul>