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

<li class="projects <?php if(is_page('projects')) { echo "current-cat"; } ?>"><a href="<?php echo home_url(); ?>/projects"><?php _e( "My Active Projects", "taskrocket" ); ?></a>
<span class="active-projects-count"><?php echo $my_projects; ?></span></li>

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
        $title = __( "There are no active tasks.", "taskrocket" );
        $highlight = "highlight";
    } else if($project == 1) {
        $title = __( "1 task remaining", "taskrocket" );
        $highlight = "";
    } else {
        $title = $project . " " . __( "Tasks remaining", "taskrocket" );
        $highlight = "";
    }
    ?>
	<?php 
	if($project !==0) { 
		if (!$project_archived) { ?>
    <li class="<?php echo $highlight; ?> <?php if($project_id == $category->cat_ID) { echo 'current-cat'; } ?>" title="<?php echo $title; ?>">
        <a href="<?php echo get_category_link( $category->cat_ID ); ?>">
            <?php echo $category->cat_name; ?>
            <span><?php echo $project; ?></span>
        </a>
    </li>
	<?php } } ?>

<?php } wp_reset_postdata(); ?>