<?php
wp_get_current_user();

global $user_ID;
global $userdata; wp_get_current_user();
global $wp;
global $wpdb;
global $post; 

if ($user_ID == '') {
	header('Location: '. wp_login_url());
	exit();
}
$options = get_option( 'taskrocket_settings' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="UTF-8" />
<title><?php if ( is_home() ) { bloginfo('name'); } else { echo bloginfo('name') . " - "; single_cat_title(); }?></title>

<!--/ Mobile Viewport Scale /-->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

<meta name="mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-capable" content="yes" />

<!--/ Icons /-->
<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/images/system/favicon.png" />
<link rel="apple-touch-icon" sizes="72x72" href="<?php echo get_template_directory_uri(); ?>/images/system/favicon-72x72.png" />
<link rel="apple-touch-icon" sizes="114x114" href="<?php echo get_template_directory_uri(); ?>/images/system/favicon-114x114.png" />
<link rel="apple-touch-icon" sizes="144x144" href="<?php echo get_template_directory_uri(); ?>/images/system/favicon-144x144.png" />

<?php 
	// User prefs
	$recent_count = $userdata->number_recent_tasks;
	$recent_comms = $userdata->number_recent_comments;
	$dash_pages = $userdata->number_recent_pages;
	$show_tips = $userdata->show_tips;
	
	$user_ID = get_current_user_id();

	$current_url = home_url(add_query_arg(array(),$wp->request));
	
	$unassigned_label = $options['unassigned_label'];
	if($unassigned_label =="") {
		$the_lable = "Unassigned";
	} else {
		$the_lable = $unassigned_label;
	}
?>
<?php wp_head();?>

<?php if ($options['shadows']) { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/shadowy.css" type="text/css" media="screen" />
<?php } ?>
<?php if ($options['custom_css'] !== "") { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/<?php echo $options['custom_css']; ?>" type="text/css" media="screen" />
<?php } ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/print.css" type="text/css" media="print" />

</head>

<body class="<?php if ( !is_home() ) {  echo 'page-' . $post->post_name; } else { echo 'page-home'; } ?> <?php if(is_single()) { echo "task-solo-page"; } ?> <?php if( is_category()) { echo "project-page"; } ?>">

<?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
	if( current_user_can('client')) { 
	$client_projects = get_user_meta( $user_ID, 'client_project', true );
    if (!is_array($client_projects)) { ?>
		<div class="intro client-intro">
		   <div class="start">
			   <h3><?php _e( 'Welcome to Task Rocket', 'taskrocket' ); ?></h3>
			   <p><?php _e( "Hi! If you're reading this it means you haven't been given access to a project. Please ask an administrator to assign you to a project and then reload this page.", "taskrocket" ); ?></p>
			   <a href="<?php echo home_url(); ?>/" class="button"><?php _e( 'Reload', 'taskrocket' ); ?></a>
		   </div>
	   </div>
<?php wp_footer(); ?>
</body>
</html>
<?php exit;
		} 
	} 
}
?>

<?php if($options['disable_loading_animation'] == false) { ?>
<div class="spinner-wrapper">
	<div class="spinner">
		<div class="dot1"></div>
		<div class="dot2"></div>
		<div class="dot3"></div>
	</div>
</div>
<?php } ?>

<?php if ($options['disable_welcome'] == false) {
	require_once($GLOBALS[ 'theme_includes' ] . "intros.php");
} ?>

<?php // If not a user profile page
if ( !is_page_template( 'page-user-profile.php' ) ) { ?>

<?php // If user is not a client
	if( !current_user_can('client')) { ?>

<!--/ Start Container /-->
<div id="container">

	<div class="top-bar">
		<?php require_once($GLOBALS[ 'theme_includes' ] . "search.php"); ?>
		
		<?php 
			if ($options['dash_message']) {
			$cookie_name = "dash_message";	
			$cookie_value = "read";
		} ?>
		
		<div class="top-icons">
			
			<?php if(is_home()) { ?>
				
				<span class="top-toggle stats-view active master-tooltip" title="<?php _e( "Stats", "taskrocket" ); ?>"><?php _e( "Stats", "taskrocket" ); ?></span>
				<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
					if ($options['show_gantt_project_pages'] == true) { ?>
					<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Gantt chart", "taskrocket" ); ?>"><?php _e( "Gantt chart", "taskrocket" ); ?></span>
				<?php } } ?>
				<?php if($recent_count > 0) { ?>
					<span class="top-toggle my-tasks-view active master-tooltip" title="<?php _e( "My latest tasks", "taskrocket" ); ?>"><?php _e( "My latest tasks", "taskrocket" ); ?></span>
				<?php } ?>
				<?php if ( is_plugin_active( 'taskrocket-task-follow/task-rocket-follow.php') ) { 
					require(ABSPATH . 'wp-content/plugins/taskrocket-task-follow/task-follow-ui.php'); ?>
				<?php
					}
				?>
				<?php if($dash_pages > 0) { ?>
					<span class="top-toggle dash-pages-view active master-tooltip" title="<?php _e( "Dash pages", "taskrocket" ); ?>"><?php _e( "Dash pages", "taskrocket" ); ?></span>
				<?php } ?>
				
				
				<?php $show_my_projects = $userdata->my_projects_dash;
			    if($show_my_projects == "yes") { ?>
					<span class="top-toggle my-projects-view active master-tooltip" title="<?php _e( "Projects I'm involved in", "taskrocket" ); ?>"><?php _e( "Projects I'm involved in", "taskrocket" ); ?></span>
				<?php } ?>
				
				
				<?php if($show_tips == "yes") { ?>
					<span class="top-toggle tips-view active master-tooltip" title="<?php _e( "Tips", "taskrocket" ); ?>"><?php _e( "Tips", "taskrocket" ); ?></span>
				<?php } ?>
				
			<?php } ?>
			
			<?php if(is_page('projects')) { ?>
				<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
					if ($options['show_gantt_project_pages'] == true) { ?>
					<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?>"><?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?></span>
				<?php } } ?>
				<span class="top-toggle all-projects-hide-inactive active master-tooltip" title="<?php _e( "Empty and Completed Projects", "taskrocket" ); ?>"><?php _e( "Empty and Completed Projects", "taskrocket" ); ?></span>
				<span class="top-toggle all-projects-alt-view master-tooltip" title="<?php _e( "Simple view", "taskrocket" ); ?>"><?php _e( "Simple view", "taskrocket" ); ?></span>
				<?php if ($options['unarchive_projects'] == true) { ?>
				<span class="top-toggle toggle-archived master-tooltip" title="<?php _e( "Show archived projects", "taskrocket" ); ?>"><?php _e( "Show archived projects", "taskrocket" ); ?></span>
				<?php } ?>
				<span class="top-toggle overdue-projects-view master-tooltip" title="<?php _e( "Only show Overdue Projects", "taskrocket" ); ?>"><?php _e( "Only show Overdue Projects", "taskrocket" ); ?></span>
			<?php } ?>
			
			<?php if(is_page('users')) { ?>
				<?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) { ?>
				<span class="top-toggle all-clients active master-tooltip" title="<?php _e( "Toggle clients", "taskrocket" ); ?>">"><?php _e( "Toggle clients", "taskrocket" ); ?>"></span>
				<?php } ?>
				<span class="top-toggle all-team active master-tooltip" title="<?php _e( "Toggle team members", "taskrocket" ); ?>">"><?php _e( "Toggle team members", "taskrocket" ); ?>"></span>
				<span class="top-toggle all-admins active master-tooltip" title="<?php _e( "Toggle administrators", "taskrocket" ); ?>">"><?php _e( "Toggle administrators", "taskrocket" ); ?>"></span>
			<?php } ?>
			
			<?php if(is_page('my-tasks')) { ?>
				<span class="top-toggle show-low active master-tooltip" title="<?php _e( "Low priority tasks", "taskrocket" ); ?>">"><?php _e( "Low priority tasks", "taskrocket" ); ?>"></span>
				<span class="top-toggle show-normal active master-tooltip" title="<?php _e( "Normal priority tasks", "taskrocket" ); ?>">"><?php _e( "Normal priority tasks", "taskrocket" ); ?>"></span>
				<span class="top-toggle show-high active master-tooltip" title="<?php _e( "High priority tasks", "taskrocket" ); ?>">"><?php _e( "High priority tasks", "taskrocket" ); ?>"></span>
				<span class="top-toggle show-urgent active master-tooltip" title="<?php _e( "Urgent priority tasks", "taskrocket" ); ?>">"><?php _e( "Urgent priority tasks", "taskrocket" ); ?>"></span>	
			<?php } ?>
			
			<?php if(is_single()) {
				
				$categories = get_the_category();
				$category_id = $categories[0]->cat_ID;
				
				// If project manager can modify tasks
				if ($options['pm_modify_tasks'] == true) {
				    $project_manager = get_option( 'tr_project_manager_' . $category_id );
				} else {
				    $project_manager = 0;
				}
			?>
				
				<?php if ($options['users_edit_tasks'] == true || current_user_can( 'manage_options' ) || $user_ID == $project_manager) { ?>
					<?php if ($post->post_author == $user_ID || current_user_can( 'manage_options' ) || $user_ID == $project_manager) { ?>
						<span class="top-toggle edit-task master-tooltip" title="Edit Task"><?php _e( "Edit Task", "taskrocket" ); ?></span>
					<?php } ?>
				<?php } ?>

				<?php 
				$attachments = get_posts( array(
					'post_type' => 'attachment',
					'posts_per_page' => -1,
					'post_parent' => $post->ID,
					'orderby' => 'title',
					'order' => 'ASC'
				) );
				if ( $attachments ) { ?>
				<span class="top-toggle toggle-attachments active master-tooltip" title="<?php _e( "Show / Hide Attachments", "taskrocket" ); ?>"><?php _e( "Show / Hide Attachments", "taskrocket" ); ?></span>
				<?php } ?>
				<?php $comments_count = wp_count_comments($post->ID); ?>
				<span class="top-toggle comment-number active master-tooltip" title="<?php _e( "Show / Hide Comments", "taskrocket" ); ?>">
					<em>
						<?php echo $comments_count->approved; ?> 
					</em>
				</span>
				<?php // Check that the ZipArchive class exists on server 
				if (class_exists('ZipArchive')) { ?>
				<a href="<?php echo get_template_directory_uri(); ?>/download-attachments.php?taskid=<?php echo $post->ID; ?>&referer=task&task_name=<?php echo $post->post_name; ?>---files"><span class="top-toggle download-attachments master-tooltip" title="<?php _e( "Download all attachments in this project", "taskrocket" ); ?>"><?php _e( "Download all attachments in this project", "taskrocket" ); ?></span></a>
				<?php } ?>
				
				<?php if ( is_plugin_active( 'taskrocket-task-follow/task-rocket-follow.php') ) { 
					require(ABSPATH . 'wp-content/plugins/taskrocket-task-follow/task-follow-ui.php'); ?>
				<?php
					}
				?>
				
				<?php if (current_user_can( 'manage_options' ) || $post->post_author == $current_user->ID && !current_user_can('client')) { 
					$terms = get_the_terms( $post->ID , 'category');
					if($terms) {
					    foreach( $terms as $term ) {
					        $cat_obj = get_term($term->term_id, 'category');
					        $cat_slug = $cat_obj->slug;
					    }
					}
					$project_path = home_url() . "/" . get_option( 'category_base' ) . "/" . $cat_slug;
				?>
				<a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $post->ID; ?>&action=delete_task&type=task&owner_ID=<?php echo $user_ID; ?>&redirect=<?php echo $project_path ?>" class="top-toggle delete-task master-tooltip" title="<?php _e( "Delete this task", "taskrocket" ); ?>"><?php _e( "Delete", "taskrocket" ); ?></a>
				<?php } ?>
				
			<?php } ?>
			
			<?php if(is_category()) { ?>
				<?php 
					// Quick fix: Menu items are appearing in search results.
					// This adds condition to only allow menu items when there is no 'cat' query string.
					if(!$_GET['cat']) { ?>
						
					<?php if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) { 
						require(ABSPATH . 'wp-content/plugins/taskrocket-add-to-cal/cal-links.php');
						}
					?>
					
					<?php if ($options['users_create_tasks'] == true || current_user_can( 'manage_options' )) { ?>
					<span class="top-toggle new-task master-tooltip" title="<?php _e( "Create new task for this project", "taskrocket" ); ?>"><?php _e( "Create a new task", "taskrocket" ); ?></span>
					<?php } ?>	
					
					<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
						if ($options['show_gantt_project_pages'] == true) { ?>
						<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?>"><?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?></span>
					<?php } } ?>	
					
					<?php // Show report icon if setting allows, or if you're an administrator.
						if($options['show_report_to_all'] == true || current_user_can( 'manage_options' )) { 
						//$slug = get_the_category(); ?>
						<?php // But don't show report icon if you're a client.
							if(!current_user_can( 'client' )) { ?>
						<a href="<?php echo home_url(); ?>/single-report/?projectid=<?php echo get_category(get_query_var('cat'))->cat_ID; ?>&referer=project"><span class="top-toggle project-report master-tooltip" title="<?php _e( "Show Project Report", "taskrocket" ); ?>"><?php _e( "Project Report", "taskrocket" ); ?></span></a>
						<?php } ?>
					<?php } ?>
					<span class="top-toggle simple-view master-tooltip" title="<?php _e( "Simple task view", "taskrocket" ); ?>"><?php _e( "Simple task view", "taskrocket" ); ?></span>
					<span class="top-toggle project-attachments active master-tooltip" title="<?php _e( "Show / Hide Attachments", "taskrocket" ); ?>"><?php _e( "Show / Hide Attachments", "taskrocket" ); ?></span>
					<?php // Check that the ZipArchive class exists on server 
						if (class_exists('ZipArchive')) { 
							
						// Only show the download button if there are more than 0 tasks in the project.
						$myposts = new WP_Query();
						$myposts->query('cat=' . get_category(get_query_var('cat'))->cat_ID);
						if ($myposts->post_count > 0) {
					?>
					<a href="<?php echo get_template_directory_uri(); ?>/download-attachments.php?projectid=<?php echo get_category(get_query_var('cat'))->cat_ID; ?>&referer=project&project_name=<?php echo get_category(get_query_var('cat'))->slug; ?>---files"><span class="top-toggle download-attachments master-tooltip" title="<?php _e( "Download all attachments in this project", "taskrocket" ); ?>"><?php _e( "Download all attachments in this project", "taskrocket" ); ?></span></a>
					<?php } } ?>
					
					<?php if ( is_plugin_active( 'taskrocket-kanban/taskrocket-kanban.php' ) ) { 
						require(ABSPATH . 'wp-content/plugins/taskrocket-kanban/kanban.php');
						}
					?>
					
					<?php if ($options['archive_projects'] == true || current_user_can( 'manage_options' )) { ?>
					<a href="<?php echo get_template_directory_uri(); ?>/archive-project.php?project_ID=<?php echo get_category(get_query_var('cat'))->cat_ID; ?>&project_action=archive&project_name=<?php echo get_category(get_query_var('cat'))->cat_name; ?>"><span class="top-toggle archive-project master-tooltip" title="<?php _e( "Archive this project", "taskrocket" ); ?>"><?php _e( "Archive this project", "taskrocket" ); ?></span></a>
					<?php } ?>	
					
					<?php if (current_user_can( 'manage_options' )) { ?>
					<a href="<?php echo get_admin_url(); ?>term.php?taxonomy=category&tag_ID=<?php echo get_category(get_query_var('cat'))->cat_ID; ?>" target="_blank"><span class="top-toggle master-tooltip edit-project" title="<?php _e( "Edit this project", "taskrocket" ); ?>"><?php _e( "Edit this project", "taskrocket" ); ?></span></a>
					<?php } ?>
					
					<span class="top-toggle project-users master-tooltip" title="<?php _e( "Show / Hide Users in Project", "taskrocket" ); ?>"><?php _e( "Show / Hide Users in Project", "taskrocket" ); ?></span>
					<span class="top-toggle project-details master-tooltip" title="<?php _e( "Show / Hide Project Details", "taskrocket" ); ?>"><?php _e( "Project Details", "taskrocket" ); ?></span>
				<?php } ?>
			<?php } ?>
			
			<?php if(is_page('single-report')) { ?>
				<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
					if ($options['show_gantt_project_pages'] == true) { ?>
					<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?>"><?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?></span>
				<?php } } ?>
				<?php 
					$return_to_reports = $_GET['referer'];
					if($return_to_reports == "reports") {
				?>
				<a href="<?php echo home_url(); ?>/reports/" class="top-toggle back-to-reports" title="<?php _e( "Go back to reports", "taskrocket" ); ?>"><?php _e( "Go back to reports", "taskrocket" ); ?></a>
				<?php } ?>
				<span class="top-toggle report-filter master-tooltip" title="<?php _e( "Filter", "taskrocket" ); ?>"><?php _e( "Filter", "taskrocket" ); ?></span>
				<a href="javascript:window.print();" class="top-toggle report-print master-tooltip" title="<?php _e( "Print this report", "taskrocket" ); ?>"><?php _e( "Print", "taskrocket" ); ?></a>
				<span class="top-toggle email-report master-tooltip" title="<?php _e( "Email this report", "taskrocket" ); ?>"><?php _e( "Email this report", "taskrocket" ); ?></span>
			<?php } ?>
			
			<?php if(is_page('reports')) { ?>
				<?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) {
					if ($options['show_gantt_project_pages'] == true) { ?>
					<span class="top-toggle toggle-gantt master-tooltip active" title="<?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?>"><?php _e( "Show / Hide the Gantt Chart", "taskrocket" ); ?></span>
				<?php } } ?>
				<span class="top-toggle complete-projects active master-tooltip" title="<?php _e( "Complete projects", "taskrocket" ); ?>"><?php _e( "Complete projects", "taskrocket" ); ?></span>
				<span class="top-toggle incomplete-projects active master-tooltip" title="<?php _e( "Incomplete projects", "taskrocket" ); ?>"><?php _e( "Incomplete projects", "taskrocket" ); ?></span>
			<?php } ?>
			
			<?php if($_GET['s']) { 
				if($_GET['cat'] !=="0") {
				?>
				<span class="top-toggle only-attachments master-tooltip" title="<?php _e( "Only show files", "taskrocket" ); ?>"><?php _e( "Attachments", "taskrocket" ); ?></span>
			<?php } } ?>
			
			<?php if ($options['dash_message']) { ?>
			<span class="top-toggle dash-message-top <?php if(!is_home()) { echo "dash-message-top-not-home"; } ?>" title="<?php _e( "Message", "taskrocket" ); ?>"></span>
			<?php } ?>
			
		</div>
		
		<span class="todays-date"><?php $date_format = get_option('date_format'); echo date($date_format); ?></span>
		
		<span class="toggle-left-pane">
			<em class="bar-01"></em>
			<em class="bar-02"></em>
			<em class="bar-03"></em>
		</span>
		
		<span class="search-toggle"></span>
		<span class="icons-toggle"></span>
	</div>

	<!--/ Start Left /-->
	<div class="main-nav">
		
		<!--/ Start Mini Bar /-->
		<ul class="mini-bar">
			<li title="<?php _e( "Edit my details", "taskrocket" ); ?>">
				<a href="<?php echo home_url(); ?>/account" class="edit-account">
					<?php require($GLOBALS[ 'theme_includes' ] . "avatar.php"); ?>
				</a>
			</li>
			<?php // My tasks
				$my_tasks_args = array(
					'posts_per_page' 	=> -1,
					'post_type' 		=> 'post',
					'post_status'		=> 'publish',
					'author' 			=> $user_ID,
					'meta_key'          => 'tr_status',
					'meta_value'        => array('incomplete', 'inprogress', 'onhold')
				);
				$my_tasks_posts = new WP_Query($my_tasks_args);
				$my_tasks_count = $my_tasks_posts->post_count;
			?>
			<li title="<?php _e( "My tasks", "taskrocket" ); ?>" class="no-hover">
				<a href="<?php echo home_url(); ?>/my-tasks/" class="task-count"><span><?php echo $my_tasks_count; ?></span></a>
			</li>
			<li title="<?php _e( "Dashboard", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/" class="dashboard"><?php _e( "Home", "taskrocket" ); ?></a></li>
			<?php if ($options['users_create_projects'] == true || current_user_can( 'manage_options' )) { ?>
	        <li title="<?php _e( "Start a new project", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/new-project" class="new-project"><?php _e( "New Project", "taskrocket" ); ?></a></li>
	        <?php } ?>
			<?php if ($options['users_create_tasks'] == true || current_user_can( 'manage_options' )) { ?>
	        <li title="<?php _e( "Create a new task", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/new-task" class="new-task"><?php _e( "New Task", "taskrocket" ); ?></a></li>
			<?php } ?>
			<li title="<?php _e( "Pages", "taskrocket" ); ?>"><a class="pages"><?php _e( "Pages", "taskrocket" ); ?></a></li>
			<?php if($options['show_report_to_all'] == true || current_user_can( 'manage_options' )) { ?>
			<li title="<?php _e( "Reports", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/reports/" class="report"><?php _e( "Reports", "taskrocket" ); ?></a></li>
			<?php } ?>
			<?php if ($options['disable_mini_chart'] == false) { ?>
			<li title="<?php _e( "Mini Chart", "taskrocket" ); ?>"><a class="mini-chart"><?php _e( "Mini Chart", "taskrocket" ); ?></a></li>
			<?php } ?>
			<?php if($recent_comms > 0) { ?>
			<li title="<?php _e( "Recent comments", "taskrocket" ); ?>"><a class="recent-comments"><?php _e( "Recent comments", "taskrocket" ); ?></a></li>
			<?php } ?>
			<li title="<?php _e( "Unowned tasks", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/unowned-tasks/" class="unowned-tasks"><?php _e( "Unowned tasks", "taskrocket" ); ?></a></li>
			<?php if ($options['show_users_link'] == true) { ?>
			<li title="<?php _e( "Users", "taskrocket" ); ?>"><a href="<?php echo home_url(); ?>/users/" class="users"><?php _e( "Users", "taskrocket" ); ?></a></li>
			<?php } ?>
			<?php if ( current_user_can('manage_options') ) { ?>
	        <li title="<?php _e( "Go to admin", "taskrocket" ); ?>"><a href="<?php echo get_admin_url(); ?>" class="go-to-admin"><?php _e( "Admin", "taskrocket" ); ?></a></li>
	        <?php } ?>
			<li title="<?php _e( "Logout", "taskrocket" ); ?>"><a href="<?php echo wp_logout_url(); ?>" class="logout"><?php _e( "Logout", "taskrocket" ); ?></a></li>
		</ul>
		<!--/ End Mini Bar /-->

		
		<nav>
			<span class="label"><?php _e( "Projects", "taskrocket" ); ?></span>
		    <ul>
				<?php // If you're an administrator, always see the full menu.
				if(current_user_can( 'manage_options' )) {
					require_once($GLOBALS[ 'theme_includes' ] . "all-projects-nav.php");
				}?>
				<?php // If you're NOT an administrator, show a menu based on condition:
				if(!current_user_can( 'manage_options' )) {
					if ($options['own_nav'] == true) {
						require_once($GLOBALS[ 'theme_includes' ] . "own-projects-nav.php");
		            } else {
						require_once($GLOBALS[ 'theme_includes' ] . "all-projects-nav.php");
		            }
		        } ?>
		    </ul>
			<div class="toggle-completed"></div>
		</nav>

	    <?php require_once($GLOBALS[ 'theme_includes' ] . "pages-nav.php"); ?>
		<?php require_once($GLOBALS[ 'theme_includes' ] . "recent-comments.php"); ?>
		<?php require_once($GLOBALS[ 'theme_includes' ] . "mini-chart.php"); ?>

	</div>
	<!--/ End Left /-->

	<?php // End If user is a client
	} ?>


	<?php // If not a user profile page
	} ?>

	<?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
		if(current_user_can( 'client' )) { ?>
	<!--/ Start Client View /-->
	<div class="clients-view">

		<?php require_once(ABSPATH . "/wp-content/plugins/taskrocket-clients/top-bar.php");  ?>

		<!--/ Start Left /-->
		<div class="main-nav">
			<?php 
				require_once(ABSPATH . "/wp-content/plugins/taskrocket-clients/mini-bar.php");
			?>
		</div>
		<!--/ End Left /-->

	</div>
	<?php } } ?>
	<!--/ End Client View /-->

	<?php
	if( !current_user_can('client')) {
	if ($options['dash_message']) {
		$cookie_name = "dash_message";	
		$cookie_value = "read";
		if($cookie_value == $_COOKIE[$cookie_name]) {
			$dash_message = "hidden";
		}
	?>
		<div class="dash-message message <?php echo $options['dash_color']; ?> <?php echo $dash_message; ?> <?php if(!is_home()) echo "not-home"; ?>">
			<p><?php echo $options['dash_message']; ?></p>
			<span class="close"></span>
		</div>
	<?php } } ?>