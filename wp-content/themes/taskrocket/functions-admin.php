<?php
if(is_admin()) {
    
    // Trash cycle
    $trash_cycle = $options['trash_cycle'];
    if($trash_cycle) {
    	define('EMPTY_TRASH_DAYS', $trash_cycle );
    }
	
	// Licence API and Service dependencies
	// Note: Removing this will break Task Rocket
	require_once('includes/license-api.php');
	require_once('includes/license-service.php');
	
	// Fetch license details, saves calling the API multiple times.
	$currentLicenseKey = TaskRocketLicenseApi::GetCurrentLicenseKey();
	$currentLicense = TaskRocketLicenseApi::GetCurrentLicense();
	
	// Remove FTP nag for updates
	define('FS_METHOD','direct');
	
	// Set default thumbnail size when theme is activated
	if ( ! function_exists( 'activated_theme' ) ) {
	    function activated_theme() {
	        update_option( 'thumbnail_size_w', 150 );
	        update_option( 'thumbnail_size_h', 150 );
	    }
	    add_action( 'after_switch_theme', 'activated_theme' );
	}

	// Change permalink structure
	add_action('init', 'changePermalink');
	function changePermalink() {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	
	// Change the category base
	add_action('init', 'categoryBase');
	function categoryBase() {
		global $wp_rewrite;
		$wp_rewrite->set_category_base( 'projects' );
	}

	
	// Remove the parent selection box in categories (admin)
	// add_action( 'admin_head-edit-tags.php', 'tr_remove_parent_category' );
	add_action( 'admin_head-edit-tags.php', 'tr_remove_parent_category' );

	function tr_remove_parent_category() {

	    $parent = 'parent()';

	    if ( isset( $_GET['action'] ) )
	        $parent = 'parent().parent()';

	    ?>
	        <script type="text/javascript">
	            jQuery(document).ready(function($)
	            {
	                $('label[for=parent]').<?php echo $parent; ?>.remove();
	            });
	        </script>
	    <?php
	}



	// Remove these columns from posts table in admin
	function tasks_columns_filter( $columns ) {
		unset($columns['tags']);
	//  unset($columns['comments']);
		return $columns;
	}
	add_filter( 'manage_edit-post_columns', 'tasks_columns_filter', 10, 1 );



	// Add column to admin posts table
	add_filter( 'manage_edit-post_columns', 'admin_post_header_columns', 10, 1);
	add_action( 'manage_posts_custom_column', 'admin_post_data_row', 10, 2);

	function admin_post_header_columns($columns) {
		
		if (!isset($columns['task_owner']))
			$columns['task_owner'] = "Task Owner";
		if (!isset($columns['tr_project']))
			$columns['tr_project'] = "Project";
		if (!isset($columns['priority']))
			$columns['priority'] = "Priority";
		if (!isset($columns['duedate']))
			$columns['duedate'] = "Due Date";
		if (!isset($columns['tr_status']))
			$columns['tr_status'] = "Status";

		return $columns;
	}

	function admin_post_data_row($column_name, $post_id) {
		
		switch($column_name) {
			case 'task_owner':
				echo get_the_author();
				break;

			default:
			break;
		}
		
		switch($column_name) {
			case 'tr_project':
				echo the_category();
				break;

			default:
			break;
		}
		
		switch($column_name) {
			case 'priority':
				$priority = get_post_meta($post_id, 'priority', true);
				if ($priority) echo $priority;
				break;

			default:
			break;
		}
		switch($column_name) {
			case 'duedate':
				$duedate = get_post_meta($post_id, 'duedate', true);
				$date_format = get_option('date_format');
				$newdateformat = new DateTime($duedate);
				if ($duedate) echo $newdateformat->format($date_format);
				break;

			default:
			break;
		}
		switch($column_name) {
			case 'tr_status':
				$tr_status = get_post_meta($post_id, 'tr_status', true);
				if ($tr_status == "complete") {
					echo "<span class='complete' title='". __( "Complete", "taskrocket" ) ."'></span>";
				}
				if ($tr_status == "incomplete") {
					echo "<span class='incomplete' title='". __( "Incomplete", "taskrocket" ) ."'></span>";
				}
				if ($tr_status == "inprogress") {
					echo "<span class='inprogress' title='". __( "In progress", "taskrocket" ) ."'></span>";
				}
				if ($tr_status == "onhold") {
					echo "<span class='onhold' title='". __( "On hold", "taskrocket" ) ."'></span>";
				}
				break;

			default:
			break;
		}
	}
	
	// Remove categories column (added a 'Projects' column anyway)
	function remove_date_column( $columns ) {
	  unset($columns['categories']);
	  return $columns;
	}
	function remove_date_column_init() {
	  add_filter( 'manage_posts_columns' , 'remove_date_column' );
	}
	add_action( 'admin_init' , 'remove_date_column_init' );
	
	// Remove author column (added a 'Task Owner' column anyway)
	function remove_author_column( $columns ) {
	  unset($columns['author']);
	  return $columns;
	}
	function remove_author_column_init() {
	  add_filter( 'manage_posts_columns' , 'remove_author_column' );
	}
	add_action( 'admin_init' , 'remove_author_column_init' );


	// Add stylesheet to admin
	function custom_admin_style() {
		wp_enqueue_style('my-admin-style', get_template_directory_uri() . '/style-admin.css');
	}
	add_action('admin_enqueue_scripts', 'custom_admin_style');


	// Add date picker script to posts in admin (both new posts and existing posts).
	function custom_admin_js( $hook ) {

		if ( $hook == 'post.php' || $hook == 'post-new.php' || $hook == 'edit-tags.php' || $hook == 'term.php' ) {

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
			wp_register_style('jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/base/jquery-ui.css');
			wp_enqueue_style( 'jquery-ui' );

		} ?>
		<script src="//code.jquery.com/jquery-latest.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
		<script>
			$(function() {
				jQuery("#startdate, #duedate").datepicker({
					dateFormat : "dd-mm-yy"
				});
				jQuery("#tr_start_date").datepicker({
					dateFormat : "dd-mm-yy"
				});
				jQuery("#tr_end_date").datepicker({
					dateFormat : "dd-mm-yy"
				});
			});
		</script>
		<?php
	}
	add_action('admin_enqueue_scripts','custom_admin_js',10,1);


	// Remove roles
	remove_role('subscriber');
	// remove_role('editor');
	remove_role('author');
	remove_role('contributor');
	remove_role('basic_contributor');


	// Create a role for Clients
	add_role('client', 'Client', array(
	    'read' => true,
	    'edit_posts' => true,
	    'delete_posts' => true,
	));
	// Give client a little capability
	function add_client_delete_cap() {
	    $role = get_role( 'client' );
	    $role->add_cap( 'delete_posts' );
		$role->add_cap( 'edit_posts' );
	    $role->add_cap( 'delete_published_posts' );
		$role->add_cap( 'edit_published_posts' );
	}
	add_action( 'admin_init', 'add_client_delete_cap');


	// Remove metaboxes from posts
	function remove_themeta_boxes() {
	    remove_meta_box( 'postexcerpt' , 'post' , 'normal' );
	    remove_meta_box( 'trackbacksdiv' , 'post' , 'normal' );
	    remove_meta_box( 'tagsdiv-post_tag' , 'post' , 'normal' );
	    remove_meta_box( 'slugdiv' , 'post' , 'normal' );
		remove_meta_box( 'postimagediv' , 'post' , 'normal' );
	}
	add_action('admin_init', 'remove_themeta_boxes');


	// Remove the posts editor
	function remove_posts_editor(){
	    remove_post_type_support( 'post', 'editor' );
	}
	add_action( 'init', 'remove_posts_editor' );


	// Remove metaboxes from admin dashboard
	function remove_dashboard_widgets(){
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
	    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
	    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
	    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
	    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
	    remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
	    remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News
	}
	add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

	// Remove welcome panel from admin dashboard
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	// Remove admin menu items
	add_action( 'admin_menu', 'my_remove_menu_pages' );
	function my_remove_menu_pages() {

		remove_submenu_page('edit.php','edit-tags.php?taxonomy=post_tag');
		remove_submenu_page('themes.php','nav-menus.php');
		remove_submenu_page('themes.php','customize.php');
		remove_submenu_page('themes.php','theme-editor.php');
	}

	// Rename admin labels
	function change_post_menu_label() {
	    global $menu;
	    global $submenu;
	    $menu[5][0] = 'Tasks';
	    $submenu['edit.php'][5][0] = __( "Task Items", "taskrocket" );
	    $submenu['edit.php'][10][0] = __( "Add New Task", "taskrocket" );
		$submenu['edit.php'][15][0] = __( "Projects", "taskrocket" );
	}
	add_action( 'admin_menu', 'change_post_menu_label' );


	// Hide the featured image metabox because it can't be done using remove_meta_box
	add_action('do_meta_boxes', 'remove_thumbnail_box');
	function remove_thumbnail_box() {
	    remove_meta_box( 'postimagediv','post','side' );
	}

	// Featured images
	add_theme_support( 'post-thumbnails' );
	if ( function_exists( 'add_theme_support' ) ) {
	    add_theme_support( 'post-thumbnails' );
	    set_post_thumbnail_size( 620, 400, $crop = true );
	}

	// Remove the theme editor
	function remove_editor_menu() {
	  remove_action('admin_menu', '_add_themes_utility_last', 101);
	}
	add_action('_admin_menu', 'remove_editor_menu', 1);


	// Hide titles attributes from category list
	function wp_list_categories_remove_title_attributes($output) {
	    $output = preg_replace('` title="(.+)"`', '', $output);
	    return $output;
	}
	add_filter('wp_list_categories', 'wp_list_categories_remove_title_attributes');

	// Add selection field to user profile
	// Todo: Only show if the role of the user being edited is 'client'.
	add_action( 'show_user_profile', 'show_extra_profile_fields' );
	add_action( 'edit_user_profile', 'show_extra_profile_fields' );

	function show_extra_profile_fields( $user ) { ?>

		<?php 
        // Only show this if user is a client.
        $userID = $user->ID;
        $user_info = get_userdata($userID);
        if($user_info->roles[0] =="client") { ?>

		<h3 class="client-access"><?php _e( "Client Access", "taskrocket" ); ?></h3>
        <?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) { ?>
    		<?php require_once(WP_PLUGIN_DIR . '/taskrocket-clients/client-access.php'); ?>
        <?php } else { ?>
            <table class="form-table">
    			<tr>
    				<th><label>Project</label></th>
    				<td>
    					<select name="client_project" id="client_project" >
    	                    <option></option>
    	                	<?php
    						foreach (get_categories('sort_order=asc&hide_empty=0') as $category){ ?>
    	                    <option value="<?php echo $category->cat_ID; ?>" <?php selected( $category->cat_ID, get_the_author_meta( 'client_project', $user->ID ) ); ?>><?php echo $category->name; ?></option>
    	                	<?php } ?>
    					</select>
    	                <br />
    	                <span class="description">Allow
    	                    <?php
    	                    $userID = $_GET['user_id'];
    	                    $user_info = get_userdata($userID);
    	                    echo "<strong>" . $user_info->user_firstname . " " . $user_info->user_lastname . "</strong>(" . $user_info->user_login . ")";
    	                    ?>
    	                    to view this project.</span>
    				</td>
    			</tr>
    		</table>
        <?php } ?>

		<?php } ?>

	<?php }

	add_action( 'personal_options_update', 'save_extra_profile_fields' );
	add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );

	function save_extra_profile_fields( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
			update_user_meta( $user_id, 'client_project', $_POST['client_project'] );
	}

	// Setup required pages and downloads directory
	if (isset($_GET['activated']) && is_admin()) {
	    add_action('init', 'setup_required_pages');
	}
	function setup_required_pages() {
	
		$pages = array(
			// Array of Pages and associated Templates
		    'Account' => array(
		        ''=>'page-account.php'),
	
		    'New Project' => array(
		        'New Project'=>'page-new-project.php'),
	
			'New Task' => array(
		        ''=>'page-new-task.php'),
	
		    'Projects' => array(
		        ''=>'page-projects.php'),
	
			'Users' => array(
		        ''=>'page-users.php'),
	
			'Client' => array(
		        ''=>'page-clients.php'),
	
			'User Profile' => array(
		        ''=>'page-user-profile.php'),
				
			'Single Report' => array(
		        ''=>'page-single-report.php'),
				
			'My Tasks' => array(
		        ''=>'page-my-tasks.php'),
	
			'Reports' => array(
		        ''=>'page-report.php'),
            
            'Unowned Tasks' => array(
		        ''=>'page-unowned-tasks.php')
		);
	
		foreach($pages as $page_url_title => $page_meta) {
		        $id = get_page_by_title($page_url_title);
	
		    foreach ($page_meta as $page_content=>$page_template){
				$page = array(
					'post_type'   => 'page',
					'post_title'  => $page_url_title,
					'post_name'   => $page_url_title,
					'post_status' => 'publish',
					'post_content' => $page_content,
					'post_author' => 1,
					'post_parent' => ''
				);
	
				if(!isset($id->ID)){
					$new_page_id = wp_insert_post($page);
					if(!empty($page_template)){
							update_post_meta($new_page_id, '_wp_page_template', $page_template);
					}
				}
			 }
		}
	}
	
	// Convert the categories checkboxes into radio buttons so that only one category can be selected.
	function convert_root_cats_to_radio() {
	global $post_type;
	?>
	<script type="text/javascript">
	jQuery("#categorychecklist>li>input").each(function(){
	    this.disabled = "disabled";
	});
	jQuery("#categorychecklist>li>label input").each(function(){
	    this.type = 'radio';
	});
	// Hide the 'most used' tab
	jQuery("#category-tabs li:odd").hide();
	</script> <?php
	}
	add_action( 'admin_footer-post.php',     'convert_root_cats_to_radio' );
	add_action( 'admin_footer-post-new.php', 'convert_root_cats_to_radio' );
	
	// Register settings
	function taskrocket_settings_init(){
	    register_setting( 'taskrocket_settings', 'taskrocket_settings' );
	}

	// Add settings page to menu
	// https://codex.wordpress.org/Function_Reference/add_menu_page
	// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	function add_settings_page() {
	$icon_url = get_option('siteurl').'/wp-content/themes/'.basename(dirname(__FILE__)).'/images/admin-icon.png';
	add_menu_page( __( 'Task Rocket' ), __( 'Task Rocket' ), 'manage_options', 'task-rocket-settings', 'taskrocket_settings_page' ,$icon_url, '81');
	}

	// Add actions
	add_action( 'admin_init', 'taskrocket_settings_init' );
	add_action( 'admin_menu', 'add_settings_page' );

	// Define your variables
	$color_scheme = array('default','blue','green',);

	// Start settings page
	function taskrocket_settings_page() {

		if($_GET['page'] == "task-rocket-settings") {
	    	// Fetch license details, saves calling the API multiple times.
	    	$currentLicenseKey = TaskRocketLicenseApi::GetCurrentLicenseKey();
	    	$currentLicense = TaskRocketLicenseApi::GetCurrentLicense();
		}

	?>

	<?php 
	// ---------------------------
	// Task Rocket Settings Page -
	// ---------------------------
	
	// If on the Task Rocket settings page
	if($_GET['page'] == "task-rocket-settings") { 
        
        $tr_active_theme  = wp_get_theme();
        $theme_version 	  = $tr_active_theme->Version;
    
    ?>
	
	<!--// Start Wrap //-->
	<div class="wrap">
	<h2><?php _e( "Task Rocket Settings", "taskrocket" ); ?></h2>

	<?php // show saved options message
	if($_GET['settings-updated'] == 'true') { ?>

	    <?php
	        $key = TaskRocketLicenseApi::GetCurrentLicenseKey();
	        TaskRocketLicenseApi::processLicense($key);
	    ?>

		<div id="message" class="updated fade"><p><strong><?php _e( "Options saved", "taskrocket" ); ?></strong></p></div>
	<?php } ?>

	<form method="post" action="options.php">
		<?php settings_fields( 'taskrocket_settings' ); ?>
	    <?php $options = get_option( 'taskrocket_settings' ); ?>

	    <div class="tr-wrap">


			<ul id="tabs">
				<li class="active"><?php _e( "Home", "taskrocket" ); ?></li>
				<li class="nav-user"><?php _e( "User", "taskrocket" ); ?></li>
				<li class="nav-presentation"><?php _e( "Presentation", "taskrocket" ); ?></li>
                
                <?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) { ?>
                <li class="nav-gantt"><?php _e( "Gantt", "taskrocket" ); ?></li>
                <?php } ?>
                
				<li class="nav-time-costs"><?php _e( "Time & Costs", "taskrocket" ); ?></li>
				<li class="nav-report"><?php _e( "Reports", "taskrocket" ); ?></li>
                <li class="nav-notifications"><?php _e( "Notifications", "taskrocket" ); ?></li>
				<li class="nav-comments"><?php _e( "Comments", "taskrocket" ); ?></li>
				<?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) { ?>
				<li class="nav-client"><?php _e( "Client Control", "taskrocket" ); ?></li>
				<?php } ?>
				<?php if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) { ?>
				<li class="nav-add-to-cal"><?php _e( "Add to Cal", "taskrocket" ); ?></li>
				<?php } ?>
				<li class="nav-other"><?php _e( "Other", "taskrocket" ); ?></li>
				<li class="nav-maintenance"><?php _e( "Maintenance", "taskrocket" ); ?></li>
				<li class="nav-addons"><?php _e( "Add-ons", "taskrocket" ); ?></li>
                <li class="nav-system"><?php _e( "System", "taskrocket" ); ?></li>
                <li class="nav-credits"><?php _e( "Credits", "taskrocket" ); ?></li>
			</ul>

			<ul id="tab">

				<li class="active tr-floater trcol0">
			    <h3><?php _e( "Task Rocket Settings", "taskrocket" ); ?></h3>
			    <table class="form-table tr-admin">
			        <tbody>
			            <tr>
			                <td>
								
								<div class="admin-home-panel">
									
			                        <input
			                            id="taskrocket_settings[license_key]"
			                            name="taskrocket_settings[license_key]"
			                            type="text" class="license-key"
			                            value="<?php esc_attr_e( $options['license_key'] ); ?>" 
										placeholder="<?php _e( "Enter your activation key", "taskrocket" ); ?>"
			                        />
									
									<input name="submit" class="button button-primary" value="<?php _e( "Activate", "taskrocket" ); ?>" type="submit" />
									
									<?php if (!TaskRocketLicenseApi::GetCurrentLicenseKey()): ?>
			                            <div class="licence-info">
			                                <p><?php 
                                            $site_url = 'https://taskrocket.info/downloads/';
                                            $site_link = sprintf( __(  'You can find your activation key by logging in to the <a href="%s" target="_blank">downloads</a> page.', 'taskrocket' ), $site_url );
                                            echo $site_link;
                                            ?>
                                            </p>
			                            </div>
			                        <?php endif ?>

			                        <?php if ($currentLicense->result == "success"): ?>

			                            <div class="license-details">
		
											<p><span class="tr-list"><?php _e( "Task Rocket Version", "taskrocket" ); ?>:</span>
		                                    <span class="righty"><?php echo $theme_version; ?>  
												<?php if($currentLicense->status !== "expired") { ?>
												(<a href="<?php echo admin_url(); ?>/update-core.php"><?php _e( "Check for update", "taskrocket" ); ?></a>)
												<?php } ?>
											</span></p>
			
											<p><span class="tr-list"><?php _e( "License status", "taskrocket" ); ?>:</span>
		                                    <span class="righty">
	                                            <?php

	                                            if (!$currentLicenseKey) { // No key entered

	                                                _e( "You have not entered your activation key.", "taskrocket" );

	                                            } else { // Key has been entered

	                                                if ($currentLicense->status == "pending") { // Key status pending

	                                                    _e( "Your key is still pending activation.", "taskrocket" );

	                                                } elseif ($currentLicense->status == "blocked") {  // Key status blocked

	                                                    _e( "Your activation key has been blocked.", "taskrocket" );

	                                                } elseif ($currentLicense->status == "expired") { // Key status expired
                                                    
	                                                    _e( "Your 12 month support period has ended. You will need to extend your license in order to receive updates and support.", "taskrocket" );

	                                                } elseif ($currentLicense->status == "active") {

	                                                    $validForInstall = TaskRocketLicenseApi::GetIsCurrentLicenseValidForInstall();

	                                                    if (!$validForInstall) {
	                                                        _e( "Your activation key may be valid for a different domain. If you think this is incorrect, please try activating again and then refresh this page.", "taskrocket" );
	                                                    } else {
	                                                        _e( "Activation key is valid.", "taskrocket" ) . '(<a href="https://taskrocket.info/downloads/" target="_blank">"' . __( "Activation key is valid.", "taskrocket" ) . '"</a>)';
	                                                    }

	                                                }

	                                            }

	                                            ?>
	                                        </span></p>
			
		                                    <p><span class="tr-list"><?php _e( "Registered to", "taskrocket" ); ?>:</span>
		                                    <span class="righty"><?php echo $currentLicense->email ?></span></p>
		
		                                    <p><span class="tr-list"><?php _e( "Status", "taskrocket" ); ?>:</span>
		                                    <span class="righty"><?php echo $currentLicense->status ?></span></p>
                                            
                                            <p><span class="tr-list"><?php _e( "Support expires", "taskrocket" ); ?>:</span>
    		                                    <span class="righty">
                                                    <?php 
                                                        $support_expiry_date = $currentLicense->date_expiry;
                                                        $pretty_support_expiry_date = new DateTime($support_expiry_date);
                                                        $date_format = get_option('date_format'); 
                                                        echo $pretty_support_expiry_date->format($date_format);
                                                    ?> (<a href="https://taskrocket.info/support-policy/?type=faq" target="_blank">Help</a>)
                                                </span>
                                            </p>
		
		                                    <p><span class="tr-list"><?php _e( "Max allowed domains", "taskrocket" ); ?>:</span>
		                                    <span class="righty"><?php echo $currentLicense->max_allowed_domains ?></span></p>
		
		                                    <p><span class="tr-list"><?php _e( "Registered domains", "taskrocket" ); ?>:</span>
		                                    <?php foreach ($currentLicense->registered_domains as $registeredDomain): ?>
		                                    <span class="righty"><?php echo $registeredDomain->registered_domain ?></span>
		                                    <?php endforeach ?>
											</p>

			                            </div>

	                                <?php elseif($currentLicense->result == "error"): ?>

	                                    <p><span class="tr-list"><?php _e( "License status", "taskrocket" ); ?>:</span>
	                                    <span class="righty"><?php _e( "Error", "taskrocket" ); ?> <?php echo $currentLicense->message ?></span></p>

			                        <?php endif ?>
								</div>

								
								<div class="admin-home-panel external-links">
									<p><span class="tr-list"><?php _e( "Support", "taskrocket" ); ?>:</span> <span class="righty"><a href="https://taskrocket.info/support" target="_blank">taskrocket.info/support</a></span></p>
									<p><span class="tr-list"><?php _e( "Blog", "taskrocket" ); ?>:</span> <span class="righty"><a href="https://taskrocket.info/blog" target="_blank">taskrocket.info/blog</a></span></p>
				                    <p><span class="tr-list"><?php _e( "FAQ", "taskrocket" ); ?>:</span> <span class="righty"><a href="https://taskrocket.info/faq" target="_blank">taskrocket.info/faq</a></span></p>
				                    <p><span class="tr-list"><?php _e( "Twitter feed", "taskrocket" ); ?>:</span> <span class="righty"><a href="//twitter.com/search?f=realtime&q=%23taskrocket" target="_blank">#taskrocket</a></span></p>
				                    <p><span class="tr-list"><?php _e( "Twitter page", "taskrocket" ); ?>:</span> <span class="righty"><a href="//twitter.com/TaskRocketeer" target="_blank">twitter.com/taskrocketeer</a></span></p>
				                    <p><span class="tr-list"><?php _e( "Google+", "taskrocket" ); ?>:</span> <span class="righty"><a href="//plus.google.com/u/0/b/104083683527758932126/+TaskrocketInformer/posts" target="_blank">taskrocketinformer</a></span></p>
				                    <p><span class="tr-list"><?php _e( "Developer website", "taskrocket" ); ?>:</span> <span class="righty"><a href="//michaelott.id.au/" target="_blank">michaelott.id.au</a></span></p>
				                    <p><span class="tr-list"><?php _e( "Recommended hosting", "taskrocket" ); ?>:</span> <span class="righty"><a href="//digitalocean.com/?refcode=230533c475ff" target="_blank"><?php _e( "Digital Ocean", "taskrocket" ); ?></a></span></p>
								</div>
								
			                    
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</li>
			<li class="tr-floater trcol1">
			    <h3><?php _e( "User Settings", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
			            <tr>
			                <td><strong><input id="taskrocket_settings[users_create_tasks]" name="taskrocket_settings[users_create_tasks]" type="checkbox" value="1" <?php checked( '1', $options['users_create_tasks'] ); ?> /> <?php _e( 'Let users create tasks', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[users_create_tasks]"><?php _e( 'Let users create tasks on the front-end', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[users_edit_tasks]" name="taskrocket_settings[users_edit_tasks]" type="checkbox" value="1" <?php checked( '1', $options['users_edit_tasks'] ); ?> /> <?php _e( 'Let users edit tasks', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[users_edit_tasks]"><?php _e( 'Let users edit their own tasks on the front-end. Note: When disabled users can still mark their own tasks as complete, but they just can\'t make any edits to the task', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[users_create_projects]" name="taskrocket_settings[users_create_projects]" type="checkbox" value="1" <?php checked( '1', $options['users_create_projects'] ); ?> /> <?php _e( 'Let users create projects', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[users_create_projects]"><?php _e( 'Let users create projects on the front-end', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
						<tr>
			                <td><strong><input id="taskrocket_settings[delete_projects]" name="taskrocket_settings[delete_projects]" type="checkbox" value="1" <?php checked( '1', $options['delete_projects'] ); ?> /> <?php _e( 'Let users delete projects', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[delete_projects]"><?php _e( 'Let users delete projects on the front-end', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
                        <tr>
			                <td><strong><input id="taskrocket_settings[archive_projects]" name="taskrocket_settings[archive_projects]" type="checkbox" value="1" <?php checked( '1', $options['archive_projects'] ); ?> /> <?php _e( 'Let users archive projects', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[archive_projects]"><?php _e( 'Let users archive projects on the front-end', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[users_specify_time]" name="taskrocket_settings[users_specify_time]" type="checkbox" value="1" <?php checked( '1', $options['users_specify_time'] ); ?> /> <?php _e( 'Let users specify time frames and time allocation on projects', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[users_specify_time]"><?php _e( 'Let users specify time frames and time allocation when creating projects on the front-end. Requires that "Let users create projects" is be enabled', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[users_reassign_tasks]" name="taskrocket_settings[users_reassign_tasks]" type="checkbox" value="1" <?php checked( '1', $options['users_reassign_tasks'] ); ?> /> <?php _e( 'Let users assign and reassign tasks', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[users_reassign_tasks]"><?php _e( 'Let users assign and reassign tasks on the front-end. Requires that "Let users edit tasks" is enabled', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[allow_choose_pm]" name="taskrocket_settings[allow_choose_pm]" type="checkbox" value="1" <?php checked( '1', $options['allow_choose_pm'] ); ?> /><?php _e( 'Let users choose a Project Manager', 'taskrocket' ); ?>	</strong>
			                    <label for="taskrocket_settings[allow_choose_pm]"><?php _e( 'Let users choose a Project Manager when creating projects on the front-end. Requires that "Let users create projects" is be enabled', 'taskrocket' ); ?>.</label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[own_nav]" name="taskrocket_settings[own_nav]" type="checkbox" value="1" <?php checked( '1', $options['own_nav'] ); ?> /> <?php _e( 'Only show the users projects in the navigation', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[own_nav]"><?php _e( 'For each user, let them only see projects they are involved with in the main navigation', 'taskrocket' ); ?>. (<a href="https://taskrocket.info/how-do-i-allow-users-to-only-see-their-own-projects-in-the-main-nav/?type=faq" target="_blank"><?php _e( 'Important Note', 'taskrocket' ); ?>)</a></label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[users_only_see_own_tasks]" name="taskrocket_settings[users_only_see_own_tasks]" type="checkbox" value="1" <?php checked( '1', $options['users_only_see_own_tasks'] ); ?> />
			                    <?php _e( 'Users can only see tabs for their own tasks in projects', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[users_only_see_own_tasks]">
			                    <?php _e( 'While viewing a project, users will only have access to \'My Active Tasks\' and \'My Completed Tasks\' tabs.', 'taskrocket' ); ?>. (<a href="https://taskrocket.info/how-do-i-allow-users-to-only-see-their-own-tasks-in-projects/?type=faq" target="_blank"><?php _e( 'Important Note', 'taskrocket' ); ?></a>)
			                    </label>
			                </td>
			            </tr>
			        </tbody>
			    </table>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<li class="tr-floater trcol2">
			    <h3><?php _e( "Presentation Settings", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
                        <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[disable_mini_chart]" name="taskrocket_settings[disable_mini_chart]" type="checkbox" value="1" <?php checked( '1', $options['disable_mini_chart'] ); ?> />
			                    <?php _e( 'Disable the mini chart', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[disable_mini_chart]">
			                    <?php _e( 'Do not show the mini chart in the left pane. May also improve performance when you have lots of projects and tasks.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
								<?php _e( 'Tasks pagination' ); ?>
			                    </strong>
			                    <input id="taskrocket_settings[my_tasks_pagination]" name="taskrocket_settings[my_tasks_pagination]" type="number" size="3" value="<?php esc_attr_e( $options['my_tasks_pagination'] ); ?>" min="5" max="100" size="2" />
			                    <label for="taskrocket_settings[my_tasks_pagination]">
			                    <?php _e( 'Show this many results per page when viewing the \'My Tasks\' page. Also applies when viewing tasks owned by other users. The default is 20, the minimum is 5, maximum is 100.', 'taskrocket' ); ?>
			                    </label>							
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[use_thickbox]" name="taskrocket_settings[use_thickbox]" type="checkbox" value="1" <?php checked( '1', $options['use_thickbox'] ); ?> />
			                    <?php _e( 'Thumbnail lightbox', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[use_thickbox]">
			                    <?php _e( 'Open attachment images in a lightbox when clicked.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_users_link]" name="taskrocket_settings[show_users_link]" type="checkbox" value="1" <?php checked( '1', $options['show_users_link'] ); ?> />
			                    <?php _e( 'Users link in nav', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_users_link]">
			                    <?php _e( 'Show "Users" in the main navigation.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_ID]" name="taskrocket_settings[show_ID]" type="checkbox" value="1" <?php checked( '1', $options['show_ID'] ); ?> />
			                    <?php _e( 'Show the task ID', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_ID]">
			                    <?php _e( 'Display the ID next to each task on the front-end.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <input id="taskrocket_settings[child_pages_list_expanded]" name="taskrocket_settings[child_pages_list_expanded]" type="checkbox" value="1" <?php checked( '1', $options['child_pages_list_expanded'] ); ?> />
			                    <?php _e( 'Keep child pages expanded', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[child_pages_list_expanded]">
			                    <?php _e( 'Expand the child pages in the navigation by default.', 'taskrocket'); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <?php _e( 'Pages navigation label', 'taskrocket' ); ?>
			                    </strong>
			                    <input id="taskrocket_settings[pages_nav_label]" name="taskrocket_settings[pages_nav_label]" type="text" value="<?php esc_attr_e( $options['pages_nav_label'] ); ?>" />
			                    <label for="taskrocket_settings[pages_nav_label]">
			                    <?php _e( 'The label that appears above the pages navigation.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <?php _e( 'Unassigned label', 'taskrocket' ); ?>
			                    </strong>
			                    <input id="taskrocket_settings[unassigned_label]" name="taskrocket_settings[unassigned_label]" type="text" value="<?php esc_attr_e( $options['unassigned_label'] ); ?>" />
			                    <label for="taskrocket_settings[unassigned_label]">
			                    <?php _e( 'Instead of \'Unassigned\', change the label to something else (eg: Orphans).', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <?php _e( 'Administrator owned task indicator label', 'taskrocket' ); ?>
			                    </strong>
			                    <input id="taskrocket_settings[admin_title]" name="taskrocket_settings[admin_title]" type="text" value="<?php esc_attr_e( $options['admin_title'] ); ?>" />
			                    <label for="taskrocket_settings[admin_title]">
			                    <?php _e( 'Indicates when a task is owned by an administrator (examples: Project Manager or Team Leader). Leave empty to disable.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <input id="taskrocket_settings[disable_welcome]" name="taskrocket_settings[disable_welcome]" type="checkbox" value="1" <?php checked( '1', $options['disable_welcome'] ); ?> />
			                    <?php _e( 'Unwelcome', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[disable_welcome]">
			                    <?php _e( 'Disable the welcome screen for new users (not recommended).', 'taskrocket'); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <input id="taskrocket_settings[disable_loading_animation]" name="taskrocket_settings[disable_loading_animation]" type="checkbox" value="1" <?php checked( '1', $options['disable_loading_animation'] ); ?> />
			                    <?php _e( 'Disable loading animation', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[disable_loading_animation]">
			                    <?php _e( 'Disable the fancy animation between page loads.', 'taskrocket'); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <input id="taskrocket_settings[disable_fancy_tooltips]" name="taskrocket_settings[disable_fancy_tooltips]" type="checkbox" value="1" <?php checked( '1', $options['disable_fancy_tooltips'] ); ?> />
			                    <?php _e( 'Disable fancy tool tips', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[disable_fancy_tooltips]">
			                    <?php _e( 'Disable the fancy tool tips and rely on the title attribute instead.', 'taskrocket'); ?>
			                    </label>
			                </td>
			            </tr>
						
						
						<tr class="logo-image">
			                <td><strong>
								<?php _e( 'Change the logo', 'taskrocket' ); ?>
			                    </strong>
								<div class="tr-uploader">
									<input id="branding" name="taskrocket_settings[branding]" type="text" value="<?php esc_attr_e( $options['branding'] ); ?>" />
									<input id="branding_button" class="button" name="branding_button" type="text" value="<?php _e( 'Browse', 'taskrocket'); ?>" />
									<script>
										jQuery(document).ready(function($) {
										var _custom_media = true,
										_orig_send_attachment = wp.media.editor.send.attachment;

										$('.tr-uploader .button').click(function(e) {
											var send_attachment_bkp = wp.media.editor.send.attachment;
											var button = $(this);
											var id = button.attr('id').replace('_button', '');
											_custom_media = true;
											wp.media.editor.send.attachment = function(props, attachment){
												if ( _custom_media ) {
													$("#"+id).val(attachment.url);
												} else {
													return _orig_send_attachment.apply( this, [props, attachment] );
												};
											}

											wp.media.editor.open(button);
											return false;
										});

										$('.add_media').on('click', function(){
											_custom_media = false;
										});
									});
									</script>
									<?php wp_enqueue_media(); ?>
								</div>
			                    <label for="taskrocket_settings[branding]">
			                    <?php _e( 'The logo shows on the login and welcome screens.', 'taskrocket'); ?>
			                    </label>
								
								<?php
								// Get the full logo image path
								$logo = $options['branding'];
								if($logo) {
								$part = pathinfo($logo);
								$dir_path =  $part['dirname'];
								$file_extension =  $part['extension'];
								$just_file_name = $part['filename'];
								?>
								<img src="<?php echo $dir_path . '/' . $just_file_name . '.' . $file_extension; ?>" />
								<?php } ?>
			                </td>
			            </tr>
						
                        <tr class="new">
			                <td><strong>
			                    <input id="taskrocket_settings[shadows]" name="taskrocket_settings[shadows]" type="checkbox" value="1" <?php checked( '1', $options['shadows'] ); ?> />
			                    <?php _e( 'Shadows', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[shadows]">
			                    <?php _e( 'Add sweet shadow effects to some elements on the the front-end.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						
			        </tbody>
			    </table>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
            
            <?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) { ?>
			<!--/ Start Gantt Settings /-->
			<li class="tr-floater trcol2">
			    <?php // Pull in the Gantt settings menu from the plug-in directory
			        $gantt_options_path = WP_PLUGIN_DIR . "/taskrocket-gantt/options.php";
			        require_once($gantt_options_path); ?>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<!--/ End Gantt Settings /-->
			<?php } ?>
            
			<li class="tr-floater trcol3">
			    <h3><?php _e( "Time & Cost Settings", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
                        
                        <tr>
                            <td><strong><?php _e( 'Currency symbol', 'taskrocket'); ?></strong>
                                <input id="taskrocket_settings[currency_symbol]" name="taskrocket_settings[currency_symbol]" type="text" class="hrate" value="<?php esc_attr_e( $options['currency_symbol'] ); ?>" />
                                <label for="taskrocket_settings[currency_symbol]">
                                <?php _e( 'The currency symbol to show next to costs on the front-end. If not specified, the $ symbol will be shown.', 'taskrocket' ); ?>
                                </label>
                            </td>
                        </tr>
                    
			            <tr>
			                <td>
			                    <strong><?php _e( 'Hourly rate', 'taskrocket'); ?></strong>
			                    <input id="taskrocket_settings[rate]" name="taskrocket_settings[rate]" type="number" min="0" size="5" class="hrate" value="<?php esc_attr_e( $options['rate'] ); ?>" />
			                    <label for="taskrocket_settings[rate]">
			                    <?php _e( 'The default hourly rate your company charges to do work. If you do not specify an hourly rate when creating a new project, this hourly rate will be used.', 'taskrocket'); ?>
			                    </label>
			                </td>
			            </tr>
                        
                        <tr class="new">
			                <td><strong>
			                    <input id="taskrocket_settings[allow_specify_project_rate]" name="taskrocket_settings[allow_specify_project_rate]" type="checkbox" value="1" <?php checked( '1', $options['allow_specify_project_rate'] ); ?> />
			                    <?php _e( 'Allow anyone to specify an hourly rate when creating a project', 'taskrocket'); ?>
			                    </strong>
			                    <label for="taskrocket_settings[allow_specify_project_rate]">
			                    <?php _e( 'If disabled, Administrators can still specify an hourly rate when creating a project.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            
			            <tr class="tr-time-log">
			                <td>
			                    <strong>
			                    <?php _e( 'Time log increments'	 ); ?>
			                    </strong>
			                    <select name="taskrocket_settings[time_log_increments]">
			                        <option></option>
			                        <option value="1" <?php if ( $options['time_log_increments'] == 1 ) echo 'selected="selected"'; ?>>1 <?php _e( "Minute", "taskrocket" , 'taskrocket'); ?></option>
			                        <option value="5" <?php if ( $options['time_log_increments'] == 5 ) echo 'selected="selected"'; ?>>5 <?php _e( "Minute", "taskrocket" , 'taskrocket'); ?></option>
			                        <option value="10" <?php if ( $options['time_log_increments'] == 10 ) echo 'selected="selected"'; ?>>10 <?php _e( "Minute", "taskrocket" , 'taskrocket'); ?></option>
			                        <option value="15" <?php if ( $options['time_log_increments'] == 15 ) echo 'selected="selected"'; ?>>15 <?php _e( "Minute", "taskrocket", 'taskrocket' ); ?></option>
			                        <option value="30" <?php if ( $options['time_log_increments'] == 30 ) echo 'selected="selected"'; ?>>30 <?php _e( "Minute", "taskrocket", 'taskrocket' ); ?></option>
			                        <option value="60" <?php if ( $options['time_log_increments'] == 60 ) echo 'selected="selected"'; ?>>60 <?php _e( "Minute", "taskrocket", 'taskrocket' ); ?></option>
			                    </select>
			                    <label for="taskrocket_settings[force_hour]">
			                    <?php _e( 'Time logged on tasks will be forced to use these increments. If not specified, 1 minute increments will be used.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_cost]" name="taskrocket_settings[show_cost]" type="checkbox" value="1" <?php checked( '1', $options['show_cost'] ); ?> />
			                    <?php _e( 'Show project costs on the front-end', 'taskrocket'); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_cost]">
			                    <?php _e( 'Display the running costs of projects on the front-end.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_task_cost]" name="taskrocket_settings[show_task_cost]" type="checkbox" value="1" <?php checked( '1', $options['show_task_cost'] ); ?> />
			                    <?php _e( 'Show task costs on the front-end', 'taskrocket'); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_task_cost]">
			                    <?php _e( 'Display the cost of each task on the front-end.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			        </tbody>
			    </table>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<li class="tr-floater trcol4">
			    <h3><?php _e( "Report settings", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_report_to_all]" name="taskrocket_settings[show_report_to_all]" type="checkbox" value="1" <?php checked( '1', $options['show_report_to_all'] ); ?> />
			                    <?php _e( 'Report access', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_report_to_all]">
			                    <?php _e( 'Give everyone (except clients) access to reports on the front-end.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[show_complete_projects_report]" name="taskrocket_settings[show_complete_projects_report]" type="checkbox" value="1" <?php checked( '1', $options['show_complete_projects_report'] ); ?> />
			                    <?php _e( 'Show completed projects in reports and charts', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[show_complete_projects_report]">
			                    <?php _e( 'Display projects in reports/charts that are complete.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			        </tbody>
			    </table>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
            
            <li class="tr-floater trcol5">
                <h3><?php _e( "Notification Settings", "taskrocket" ); ?></h3>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td>
                                <strong><?php _e( 'When a task is added send email notification to:', 'taskrocket' ); ?></strong>
                                
                                <span class="checky first-checky">
                                    <input id="taskrocket_settings[task_added_notify_pm]" name="taskrocket_settings[task_added_notify_pm]" type="checkbox" value="1" <?php checked( '1', $options['task_added_notify_pm'] ); ?> /><?php _e( 'The project manager for the project', 'taskrocket' ); ?>
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_added_notify_admin]" name="taskrocket_settings[task_added_notify_admin]" type="checkbox" value="1" <?php checked( '1', $options['task_added_notify_admin'] ); ?> /><?php _e( 'The website administrator', 'taskrocket'); ?> (<?php echo get_option( 'admin_email' ); ?>)
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_added_notify_project_team]" name="taskrocket_settings[task_added_notify_project_team]" type="checkbox" value="1" <?php checked( '1', $options['task_added_notify_project_team'] ); ?> /><?php _e( 'The entire project team', 'taskrocket' ); ?>
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_added_notify_task_owner]" name="taskrocket_settings[task_added_notify_task_owner]" type="checkbox" value="1" <?php checked( '1', $options['task_added_notify_task_owner'] ); ?> /><?php _e( 'The task owner', 'taskrocket' ); ?>
                                </span>
                                
                                <br /><br />
                                <strong><?php _e( 'Also send notification to:', 'taskrocket'); ?></strong>
                                <input id="taskrocket_settings[task_added_notify_others]" name="taskrocket_settings[task_added_notify_others]" type="text" value="<?php esc_attr_e( $options['task_added_notify_others'] ); ?>" placeholder="someone@domain.com" class="notify-others" />
                                <label for="taskrocket_settings[task_added_notify_others]">
                                <?php _e( 'To specify more than one email address, separate with a comma (without spaces). Eg: someguy@domain.com,somegirl@domain.com', 'taskrocket' ); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <tr>
                            <td>
                                <strong><?php _e( 'When a task changes status send email notification to:', 'taskrocket' ); ?></strong>
                                
                                <span class="checky first-checky">
                                    <input id="taskrocket_settings[task_changes_status_notify_pm]" name="taskrocket_settings[task_changes_status_notify_pm]" type="checkbox" value="1" <?php checked( '1', $options['task_changes_status_notify_pm'] ); ?> /><?php _e( 'The project manager for the project', 'taskrocket' ); ?>
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_changes_status_notify_admin]" name="taskrocket_settings[task_changes_status_notify_admin]" type="checkbox" value="1" <?php checked( '1', $options['task_changes_status_notify_admin'] ); ?> /><?php _e( 'The website administrator', 'taskrocket'); ?> (<?php echo get_option( 'admin_email' ); ?>)
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_changes_status_notify_project_team]" name="taskrocket_settings[task_changes_status_notify_project_team]" type="checkbox" value="1" <?php checked( '1', $options['task_changes_status_notify_project_team'] ); ?> /><?php _e( 'The entire project team', 'taskrocket' ); ?>
                                </span>
                                
                                <span class="checky">
                                    <input id="taskrocket_settings[task_changes_status_notify_task_owner]" name="taskrocket_settings[task_changes_status_notify_task_owner]" type="checkbox" value="1" <?php checked( '1', $options['task_changes_status_notify_task_owner'] ); ?> /><?php _e( 'The task owner', 'taskrocket' ); ?>
                                </span>
                                
                                <br /><br />
                                <strong><?php _e( 'Also send notification to:', 'taskrocket'); ?></strong>
                                <input id="taskrocket_settings[task_changes_notify_others]" name="taskrocket_settings[task_changes_notify_others]" type="text" value="<?php esc_attr_e( $options['task_changes_notify_others'] ); ?>" placeholder="someone@domain.com" class="notify-others" />
                                <label for="taskrocket_settings[task_changes_notify_others]">
                                <?php _e( 'To specify more than one email address, separate with a comma (without spaces). Eg: someguy@domain.com,somegirl@domain.com', 'taskrocket' ); ?>
                                </label>
                            </td>
                        </tr>
                        
                        <?php if ( is_plugin_active( 'taskrocket-comment-notify/taskrocket-comment-notify.php' ) ) { ?>
                            <?php // Pull in the comment control option from the plug-in directory
            			        $trcn_options_path = WP_PLUGIN_DIR . "/taskrocket-comment-notify/options.php";
            			        require_once($trcn_options_path); ?>
        				<?php } ?>
                        
                        <tr>
                            <td><strong>
                                <input id="taskrocket_settings[send_plain]" name="taskrocket_settings[send_plain]" type="checkbox" value="1" <?php checked( '1', $options['send_plain'] ); ?> />
                                <?php _e( 'Plain email notifications', 'taskrocket' ); ?>
                                </strong>
                                <label for="taskrocket_settings[send_plain]">
                                <?php _e( 'Send task notification emails as plain text. This will not be honoured if "No email notifications" is enabled.', 'taskrocket' ); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>
                                <input id="taskrocket_settings[no_emails]" name="taskrocket_settings[no_emails]" type="checkbox" value="1" <?php checked( '1', $options['no_emails'] ); ?> />
                                <?php _e( 'No email notifications', 'taskrocket' ); ?>
                                </strong>
                                <label for="taskrocket_settings[no_emails]">
                                <?php _e( 'Disable all email notifications', 'taskrocket' ); ?>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
            </li>
            
            <li class="tr-floater trcol6">
                <h3><?php _e( "Comment Settings", "taskrocket" ); ?></h3>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td><strong><input id="taskrocket_settings[allow_comments]" name="taskrocket_settings[allow_comments]" type="checkbox" value="1" <?php checked( '1', $options['allow_comments'] ); ?> /><?php _e( 'Allow comments on tasks', 'taskrocket' ); ?></strong>
                                <label for="taskrocket_settings[allow_comments]"><?php _e( 'Allow users to comment on tasks', 'taskrocket' ); ?> (<a href="https://taskrocket.info/why-is-the-setting-to-allow-comments-not-working/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)</label>
                            </td>
                        </tr>
                        <tr>
                            <td><strong><input id="taskrocket_settings[allow_comments_pages]" name="taskrocket_settings[allow_comments_pages]" type="checkbox" value="1" <?php checked( '1', $options['allow_comments_pages'] ); ?> /><?php _e( 'Allow comments on pages', 'taskrocket' ); ?></strong>
                                <label for="taskrocket_settings[allow_comments_pages]"><?php _e( 'Allow users to comment on pages', 'taskrocket' ); ?> (<a href="https://taskrocket.info/why-is-the-setting-to-allow-comments-not-working/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
            </li>
            
			<?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) { ?>
			<!--/ Start Client Settings /-->
			<li class="tr-floater trcol7 tr-addon-clients-icon">
			    <?php // Pull in the client settings menu from the plug-in directory
			        $client_options_path = WP_PLUGIN_DIR . "/taskrocket-clients/options.php";
			        require_once($client_options_path); ?>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<!--/ End Client Settings /-->
			<?php } ?>
			<?php if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) { ?>
			<!--/ Start Add To Cal Settings /-->
			<li class="tr-floater trcol20 tr-addon-cal-icon">
			    <?php // Pull in the client settings menu from the plug-in directory
			        $add_to_cal_options_path = WP_PLUGIN_DIR . "/taskrocket-add-to-cal/options.php";
			        require_once($add_to_cal_options_path); ?>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<!--/ End Add To Cal Settings /-->
			<?php } ?>
			<li class="tr-floater trcol22">
			    <h3><?php _e( "Other Settings", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
						<tr>
			                <td><strong>
			                    <?php _e( 'Trash Cycle' ); ?>
			                    </strong>
			                    <input id="taskrocket_settings[trash_cycle]" name="taskrocket_settings[trash_cycle]" type="text" value="<?php esc_attr_e( $options['trash_cycle'] ); ?>" placeholder="Example: 100" />
			                    <label for="taskrocket_settings[trash_cycle]">
			                    <?php _e( 'How often (specified in days) should WordPress empty the trash? If not specified, the WordPress default (30 days) will be used. Set to 0 to disable the trash (which means if you delete a task or page, it is permanent).', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong><input id="taskrocket_settings[allow_unowned_tasks]" name="taskrocket_settings[allow_unowned_tasks]" type="checkbox" value="1" <?php checked( '1', $options['allow_unowned_tasks'] ); ?> /> <?php _e( 'Allow tasks with no owner' ); ?></strong>
			                    <label for="taskrocket_settings[allow_unowned_tasks]"><?php _e( 'This is ideal for tasks that are not specific to anyone. Any user who wants the task can take ownership by reassigning it to themselves.', 'taskrocket' ); ?> <a href="https://taskrocket.info/unowned-tasks/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a></label>
			                </td>
			            </tr>
                        <tr>
			                <td><strong><input id="taskrocket_settings[unarchive_projects]" name="taskrocket_settings[unarchive_projects]" type="checkbox" value="1" <?php checked( '1', $options['unarchive_projects'] ); ?> />  <?php _e( 'Show archived projects', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[unarchive_projects]"><?php _e( 'Archived projects will listed on the All Active Projects page, and can be unarchived from there.' ); ?> </label>
			                </td>
			            </tr>
                        <tr class="new">
			                <td><strong><input id="taskrocket_settings[disable_task_relations]" name="taskrocket_settings[disable_task_relations]" type="checkbox" value="1" <?php checked( '1', $options['disable_task_relations'] ); ?> />  <?php _e( 'Disable task relations', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[disable_task_relations]"><?php _e( 'The abililty to specify task relations will be disabled. Any existing task relations will stil be visible.' ); ?> </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong><input id="taskrocket_settings[enable_warning]" name="taskrocket_settings[enable_warning]" type="checkbox" value="1" <?php checked( '1', $options['enable_warning'] ); ?> /> <?php _e( 'Enable a warning on Tasks and Project pages', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[enable_warning]"><?php _e( 'A warning appears when accidentally attempting to leave the New Task or New Project pages without first completing the form.', 'taskrocket' ); ?></label>
			                </td>
			            </tr>
                        <tr>
    		                <td><strong><input id="taskrocket_settings[pm_modify_tasks]" name="taskrocket_settings[pm_modify_tasks]" type="checkbox" value="1" <?php checked( '1', $options['pm_modify_tasks'] ); ?> /> <?php _e( 'Project managers change any task status', 'taskrocket' ); ?></strong>
    		                    <label for="taskrocket_settings[pm_modify_tasks]"><?php _e( 'Let project managers change the status of tasks owned by anyone on the front-end.', 'taskrocket' ); ?></label>
    		                </td>
		                </tr>
						<tr class="tr-auto-job-numbers">
			                <td>
			                    <strong>
								<input id="taskrocket_settings[auto_job_numbers]" name="taskrocket_settings[auto_job_numbers]" type="checkbox" value="1" <?php checked( '1', $options['auto_job_numbers'] ); ?> />
			                    <?php _e( 'Automatic job numbers', 'taskrocket'); ?>
			                    </strong>
			                    <label for="taskrocket_settings[auto_job_numbers]">
			                    <?php _e( 'Job numbers will be created automatically when you create a new task or project.', 'taskrocket'); ?>
								(<a href="https://taskrocket.info/job-numbers/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)
			                    </label>
			                </td>
			            </tr>
						<tr>
			                <td><strong>
			                    <?php _e( 'Job number prefix', 'taskrocket'); ?>
			                    </strong>
			                    <input id="taskrocket_settings[job_number_prefix]" name="taskrocket_settings[job_number_prefix]" type="text" value="<?php esc_attr_e( $options['job_number_prefix'] ); ?>" placeholder="Example: TR-" />
			                    <label for="taskrocket_settings[job_number_prefix]">
			                    <?php _e( 'The prefix for job numbers. Typically a few characters is enough. If specified, the prefix will be applied to both manual and automatic job numbers.', 'taskrocket' ); ?>
								(<a href="https://taskrocket.info/job-numbers/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong>
			                    <input id="taskrocket_settings[disable_make_clickable]" name="taskrocket_settings[disable_make_clickable]" type="checkbox" value="1" <?php checked( '1', $options['disable_make_clickable'] ); ?> />
			                    <?php _e( 'Disable auto generated clickable links', 'taskrocket' ); ?>
			                    </strong>
			                    <label for="taskrocket_settings[disable_make_clickable]">
			                    <?php _e( 'Do not automatically convert text URLs in tasks and project descriptions into clickable links.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
			            <tr>
			                <td><strong><input id="taskrocket_settings[disable_file_uploads]" name="taskrocket_settings[disable_file_uploads]" type="checkbox" value="1" <?php checked( '1', $options['disable_file_uploads'] ); ?> /><?php _e( 'Disable file uploads', 'taskrocket' ); ?></strong>
			                    <label for="taskrocket_settings[disable_file_uploads]"><?php _e( 'Do not let users attach files to tasks', 'taskrocket' ); ?> </label>
			                </td>
			            </tr>
			            
			            <tr>
			                <td>
			                    <strong>
			                    <?php _e( 'Dashboard message'); ?>
			                    </strong>
			                    <textarea id="taskrocket_settings[dash_message]" name="taskrocket_settings[dash_message]" cols="30" rows="5" style="width:calc(100% - 20px); height:100px;"><?php esc_attr_e( $options['dash_message'] ); ?></textarea>
			                    <label for="taskrocket_settings[dash_message]">
			                    <?php _e( 'Display a message on the dashboard.', 'taskrocket' ); ?>
			                    </label>
			                </td>
			            </tr>
						
						<tr>
							<td>
								<div class="dash-bg">
			                        <strong>
			                        <?php _e( 'Dashboard message background colour', 'taskrocket'); ?>
			                        </strong>
			                        <input type="radio" class="dash_red" name="taskrocket_settings[dash_color]" value="dash_red"<?php checked( 'dash_red' == $options['dash_color'] ); ?> /> <?php _e( 'Red', 'taskrocket'); ?>
			                        <input type="radio" class="dash_blue" name="taskrocket_settings[dash_color]" value="dash_blue"<?php checked( 'dash_blue' == $options['dash_color'] ); ?> /> <?php _e( 'Blue', 'taskrocket'); ?>
			                        <input type="radio" class="dash_orange" name="taskrocket_settings[dash_color]" value="dash_orange"<?php checked( 'dash_orange' == $options['dash_color'] ); ?> /> <?php _e( 'Orange', 'taskrocket'); ?>
			                        <input type="radio" class="dash_green" name="taskrocket_settings[dash_color]" value="dash_green"<?php checked( 'dash_green' == $options['dash_color'] ); ?> /> <?php _e( 'Green', 'taskrocket'); ?>
			                    </div>
							</td>
						</tr>
						
			            <tr class="tr-custom-css">
			                <td><strong>
			                    <?php _e( 'Custom front-end style sheet', 'taskrocket'); ?>
			                    </strong>
			                    <input id="taskrocket_settings[custom_css]" name="taskrocket_settings[custom_css]" type="text" class="custom-css" value="<?php esc_attr_e( $options['custom_css'] ); ?>" />
			                    <label for="taskrocket_settings[custom_css]">
			                    <?php _e( 'The file name of your custom CSS file (eg: my-custom-styles.css)', 'taskrocket' ); ?> <a href="https://taskrocket.info/can-i-make-edits-to-the-theme/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>
			                    </label>
			                </td>
			            </tr>
                        
                        <tr class="tr-custom-css">
			                <td><strong>
			                    <?php _e( 'Custom login style sheet', 'taskrocket'); ?>
			                    </strong>
			                    <input id="taskrocket_settings[custom_login_css]" name="taskrocket_settings[custom_login_css]" type="text" class="custom-css" value="<?php esc_attr_e( $options['custom_login_css'] ); ?>" />
			                    <label for="taskrocket_settings[custom_login_css]">
			                    <?php _e( 'The file name of your custom login CSS file (eg: my-custom-login-styles.css)', 'taskrocket' ); ?> <a href="https://taskrocket.info/can-i-make-edits-to-the-theme/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>
			                    </label>
			                </td>
			            </tr>
						
						<tr class="tr-custom-css">
			                <td><strong>
			                    <?php _e( 'Custom JS', 'taskrocket'); ?>
			                    </strong>
			                    <input id="taskrocket_settings[custom_js]" name="taskrocket_settings[custom_js]" type="text" class="custom-css" value="<?php esc_attr_e( $options['custom_js'] ); ?>" />
			                    <label for="taskrocket_settings[custom_js]">
			                    <?php _e( 'The file name of your custom Javascript file (eg: my-custom-scripts.js)', 'taskrocket' ); ?> <a href="https://taskrocket.info/can-i-add-custom-javascript/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>
			                    </label>
			                </td>
			            </tr>		
			        </tbody>
			    </table>
			    <p><input name="submit" class="button button-primary" value="<?php _e( "Save Settings", "taskrocket" ); ?>" type="submit" /></p>
			</li>
			<li class="tr-floater trcol24">
			    <h3><?php _e( "Maintenance", "taskrocket" ); ?></h3>
			    <table class="form-table tr-maintenance">
			        <tbody>
			            <tr>
			                <td>
			                    <strong><?php _e( 'Maintenance', 'taskrocket'); ?></strong>
			                    <label for="taskrocket_settings[maintenance]">
			                    <?php _e( 'If you are experiencing problems or not seeing new changes after an update, try hitting the button below', 'taskrocket' ); ?>.
			                    </label>
			                    <a href="<?php echo admin_url(); ?>themes.php?action=activate&stylesheet=taskrocket&_wpnonce=<?php echo wp_create_nonce("switch-theme_taskrocket");?>" class="tr-button"><?php _e( "Perform Maintenance", "taskrocket" ); ?></a>
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</li>
			<li class="tr-floater trcol26">
			    <h3><?php _e( "Extend Task Rocket with these add-ons", "taskrocket" ); ?></h3>
			    <table class="form-table tr-addons">
			        <tbody>
			            <tr>
			                <td>
								<a href="https://taskrocket.info/add-ons/" target="_blank"><?php _e( "Extend Task Rocket with these useful addons", "taskrocket" ); ?></a>.
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</li>
            
            <li class="tr-floater trcol28">
			    <h3><?php _e( "System", "taskrocket" ); ?></h3>
			    <table class="form-table">
			        <tbody>
			            <tr>
			                <td>
                                
                                <div class="system-left">
                                    <h4><?php _e( "Configuration", "taskrocket" ); ?></h4>
                                    <p>
                                       <span class="tr-list">PHP:</span>
                                       <span class="righty">
                                           <?php
                                               preg_match("#^\d+(\.\d+)*#", PHP_VERSION, $match);
                                                           //echo $match[0];
                                               
                                               if ($match[0] >= '5.4') {
                                                   echo "<span class='spec-ok'>OK</span> " . $match[0];
                                               } else {
                                                   echo "<span class='spec-notok'>". __( "Not OK", "taskrocket" ) . "</span> " . $match[0] . " (<a href='https://taskrocket.info/what-are-the-minimum-requirements-to-host-task-rocket/?type=faq' target='_blank'>" . __( "Help", "taskrocket" ) . "</a>)";
                                               }
                                           ?>
                                       </span>
                                       </span>
                                   </p>
                                   
                                   <p>
                                       <span class="tr-list">PHP <?php _e( "Memory", "taskrocket" ); ?>:</span>
                                       <span class="righty">
                                           <?php
                                               $ramused = round(memory_get_usage() / 1024 / 1024, 2);
                                               $ramavailable = ini_get('memory_limit');
                                               $rampercentage = ($ramused * 100) / $ramavailable;
                                               if ($rampercentage <= 10) { }
                                               if ($rampercentage > 10 && ($rampercentage < 75)) { }
                                               if ($rampercentage >= 75 && ($rampercentage < 90)) { $warning = "first-warning"; }
                                               if ($rampercentage > 90) { $warning = "second-warning"; }
                                               // echo $ramused;
                                           ?>
                                            <span class='spec-ok <?php echo $warning; ?>'><?php _e( "OK", "taskrocket" ); ?></span> <?php echo round($ramused, 1); ?>MB [<?php echo round($rampercentage, 1); ?>%] / <?php echo $ramavailable; ?>B <?php _e( "Used", "taskrocket" ); ?>
                                        </span>
                                   </p>
                                   
                                   <p>
                                       <span class="tr-list">WP <?php _e( "Memory limit", "taskrocket" ); ?>:</span> 
                                       <span class="righty">
                                           <?php $wp_memory = WP_MEMORY_LIMIT; ?>
                                           <?php if($wp_memory <= 40) { ?>
                                               <span class='spec-notok'><?php _e( "OK", "taskrocket" ); ?></span> <?php echo $wp_memory; ?>B (<a href="https://taskrocket.info/what-are-the-minimum-requirements-to-host-task-rocket/?type=faq&source=search" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)
                                           <?php } else { ?>
                                               <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php echo $wp_memory; ?>B
                                           <?php } ?> 
                                        </span>
                                   </p>
                                   <p>
                                       <span class="tr-list">WP <?php _e( "Memory in use", "taskrocket" ); ?>:</span>
                                       <span class="righty">
                                           <?php 
                                               $wp_memory_in_use = size_format(@memory_get_usage(TRUE), 2);
                                               $critical_mem_usage = $wp_memory_in_use / $wp_memory * 100;
                                           ?>
                                           <?php if($critical_mem_usage > 90) { ?>
                                               <span class='spec-notok'><?php _e( "OK", "taskrocket" ); ?></span> <?php echo $wp_memory_in_use; ?> [<?php echo round($critical_mem_usage, .1); ?>%] (<?php _e( "Critical", "taskrocket" ); ?>)
                                           <?php } else { ?>
                                               <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php echo $wp_memory_in_use; ?> [<?php echo round($critical_mem_usage, .1); ?>%]
                                           <?php } ?> 	 
                                        </span>
                                   </p>
                                   
                                   <p>
                                       <span class="tr-list"><?php _e( "Upload max filesize", "taskrocket" ); ?>:</span>
                                       <span class="righty">
                                           <?php $maxUpload = (int)(ini_get('upload_max_filesize'));
                                               if($maxUpload > 8) {
                                                   echo "<span class='spec-ok'>". __( "OK", "taskrocket" ) ."</span> " . $maxUpload . "MB";
                                               } else {
                                                   echo "<span class='spec-notok'>". __( "Not OK", "taskrocket" ) ."</span> (<a href='https://taskrocket.info/large-files-wont-upload-with-my-task/?type=faq' target='_blank'>". __( "Help", "taskrocket" ) ."</a>)";
                                               }
                                           ?>
                                           </span>
                                   </p>
                                   <p>
                                       <span class="tr-list"><?php _e( "Post max size", "taskrocket" ); ?>:</span>
                                       <span class="righty">
                                           <?php $maxPost = (int)(ini_get('post_max_size'));
                                               if ($maxPost > $maxUpload ) {
                                                   echo "<span class='spec-ok'>" . __( "OK", "taskrocket" ) . "</span> " . $maxPost . "MB";
                                               } else {
                                                   echo "<span class='spec-notok'>" . __( "Not OK", "taskrocket" ) . "</span>" . $maxPost . "MB (<a href='https://taskrocket.info/my-post-max-size-is-not-higher-than-the-max-upload-filesize/?type=faq' target='_blank'>" . __( "Help", "taskrocket" ) . "</a>)";
                                               }
                                           ?>
                                       </span>
                                   </p>
                                   <p>
                                       <span class="tr-list">ZipArchive class:</span>
                                       <span class="righty">
                                           <?php if (class_exists('ZipArchive')) { ?>
                                               <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "All Good", "taskrocket" ); ?>
                                           <?php } else { ?>
                                               <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Not installed", "taskrocket" ); ?> (<a href="https://taskrocket.info/why-cant-i-download-all-project-and-task-attachments-in-a-single-zip/?type=faq" target="_blank"><?php _e( "Help", "taskrocket" ); ?></a>)
                                           <?php } ?>
                                       </span>
                                   </p>
                                </div>
                                
                                <div class="system-right">
                                    <h4><?php _e( "Add-ons", "taskrocket" ); ?></h4>
                                    <?php if ( is_plugin_active( 'taskrocket-add-to-cal/taskrocket-add-to-cal.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Add to Cal", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < 3) { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 3", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                    
                                    <?php if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Client Control", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < 3) { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 3", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                    
                                    <?php if ( is_plugin_active( 'taskrocket-comment-notify/taskrocket-comment-notify.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Comment Notify", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < 4) { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 4", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                    
                                    <?php if ( is_plugin_active( 'taskrocket-kanban/taskrocket-kanban.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Kanban", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < "4.4.2") { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 4.4.2", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                    
                                    <?php if ( is_plugin_active( 'taskrocket-gantt/taskrocket-gantt.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Gantt", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < 4) { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 4", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                    
                                    <?php if ( is_plugin_active( 'taskrocket-task-groups/taskrocket-task-groups.php' ) ) { ?>
                                    <p>
                                        <span class="tr-list"><?php _e( "Task Groups", "taskrocket" ); ?>:</span>
                                        <span class="righty">
                                            <?php if($theme_version < 3) { ?>
                                                <span class='spec-notok'><?php _e( "Not OK", "taskrocket" ); ?></span> <?php _e( "Requires Task Rocket 2.7", "taskrocket" ); ?> 
                                            <?php } else { ?>
                                                <span class='spec-ok'><?php _e( "OK", "taskrocket" ); ?></span> <?php _e( "OK", "taskrocket" ); ?>
                                            <?php } ?> 
                                        </span>
                                    </p>
                                    <?php } ?>
                                </div>
								
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</li>
            
            <li class="tr-floater trcol30">
			    <h3><?php _e( "Additional Contributions", "taskrocket" ); ?></h3>
			    <table class="form-table tr-credits">
			        <tbody>
			            <tr>
			                <td>
								<?php _e( 'These superstars have kindly donated their time and energy into helping improve Task Rocket.', 'taskrocket'); ?>
                
                                <p>
                                    <strong>Bastien Berenguier</strong>
                                    <em>(French translation)</em>
                                    <a href="https://twitter.com/bberenguier" target="_blank">@bberenguier</a>
                                </p>
                                
                                <p>
                                    <strong>Dominik Kucharski</strong>
                                    <em>(Polish translation)</em>
                                    <a href="https://mino.pro/" target="_blank">mino.pro</a>
                                </p>
                                
                                <p>
                                    <strong>Jefferson Henrique</strong>
                                    <em>(Portuguese translation)</em>
                                    <a href="mailto:jeffersonoh@gmail.com" target="_blank">jeffersonoh@gmail.com</a>
                                </p>
                                
                                <p>
                                    <strong>Mattias Tengblad</strong>
                                    <em>(Swedish translation)</em>
                                    <a href="http://wpsv.se" target="_blank">wpsv.se</a>
                                </p>
                                
                                <p>
                                    <strong>Oriol Carbonell</strong>
                                    <em>(Catalan translation)</em>
                                    <a href="http://eina.io" target="_blank">eina.io</a>
                                </p>
            
                                <p>
                                    <strong>Ramon Pérez</strong>
                                    <em>(Spanish translation)</em>
                                    <a href="mailto:raperez@bizkaia.eu" target="_blank">raperez@bizkaia.eu</a>
                                </p>
    
			                </td>
			            </tr>
			        </tbody>
			    </table>
			</li>
			</ul>

			<script>

				$("ul#tabs li").click(function(e){
				if (!$(this).hasClass("active")) {
				    var tabNum = $(this).index();
				    var nthChild = tabNum+1;
				    $("ul#tabs li.active").removeClass("active");
				    $(this).addClass("active");
				    $("ul#tab li.active").removeClass("active");
				    $("ul#tab li:nth-child("+nthChild+")").addClass("active");
				}
				});

			</script>

	    </div>

	    <!--/ Lame way to inititiate the new permalink structure without user intervention /-->
	    <iframe src="<?php echo home_url(); ?>/wp-admin/options-permalink.php" width="0" height="0" style="display:none;"></iframe>
	</form>

	</div>
	<!--/ End Wrap /-->

	<?php 
		} // End if on the Task Rocket settings page
	}
	//sanitize and validate
	function options_validate( $input ) {
	    global $select_options, $radio_options;
	    if ( ! isset( $input['option1'] ) )
	        $input['option1'] = null;
	    $input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
	    $input['sometext'] = wp_filter_nohtml_kses( $input['sometext'] );
	    if ( ! isset( $input['radioinput'] ) )
	        $input['radioinput'] = null;
	    if ( ! array_key_exists( $input['radioinput'], $radio_options ) )
	        $input['radioinput'] = null;
	    $input['sometextarea'] = wp_filter_post_kses( $input['sometextarea'] );
	    return $input;
	}

	// Start license Nags
	if($_GET['page'] == "task-rocket-settings") {
		if (!$currentLicenseKey) { // No key entered

			add_action( 'admin_notices', function(){
		        $class = 'tr-license-notice-error';
		        $message = 'Your have not entered your Task Rocket activation key.';

		        printf( '<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message );
		    });

		} else { // Key has been entered

		    if ($currentLicense->result == "success") { // license successfully retrieved

		        if ($currentLicense->status == "pending") { // Key status pending

		            add_action('admin_notices', function () {
		                $class = 'tr-license-notice-error';
		                $message = 'Your Task Rocket activation key is still pending activation';

		                printf('<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message);
		            });

		        } elseif ($currentLicense->status == "blocked") {  // Key status blocked

		            add_action('admin_notices', function () {
		                $class = 'tr-license-notice-error';
		                $message = 'Your Task Rocket activation key has been blocked.';

		                printf('<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message);
		            });

		        } elseif ($currentLicense->status == "expired") { // Key status expired

		            add_action('admin_notices', function () {
		                $class = 'tr-license-notice-error';
		                $message = 'Your Task Rocket 12 month support period has ended. Extend your support at taskrocket.info.';

		                printf('<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message);
		            });

		        } elseif ($currentLicense->status == "active") { // Key status valid

		            $validForInstall = TaskRocketLicenseApi::GetIsCurrentLicenseValidForInstall();

		            if (!$validForInstall) {

		                add_action('admin_notices', function () {
		                    $class = 'tr-license-notice-error';
		                    $message = 'Your activation key may be valid for a different domain. If you think this is incorrect, please try activating again and then refresh this page.';

		                    printf('<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message);
		                });

		            }

		        }

		    } elseif ($currentLicense->result == "error") {

		        add_action('admin_notices', function () {

		            $class = 'tr-license-notice-error';
		            $message = 'Your Task Rocket activation key is invalid.';

		            printf('<div class="%1$s"><p><strong>%2$s</strong></p></div>', $class, $message);
		        });

		    }

		}
	}
	// End license Nags

	// Create a new metabox for additional task information
	class more_info_box {

	    var $plugin_dir;
	    var $plugin_url;

	    function  __construct() {

	        add_action( 'add_meta_boxes', array( $this, 'moreinfo_meta_box' ) );
	        add_action( 'save_post', array($this, 'save_data') );
	    }

		// Add the meta boxes to post types
	    function moreinfo_meta_box(){
			
			$post_types = array ( 'post', 'task_groups' ); // Post types
			
			foreach( $post_types as $post_type ) {
			
		        add_meta_box(
				'more_info', 							// metabox ID, it also will be it id HTML attribute
				'Additional Information',  				// title
				array( &$this, 'meta_box_content' ),
				$post_type,					 			// post types
				'normal', 				   				// position of the screen where metabox should be displayed (normal, side, advanced)
				'high' 				     				// priority over another metaboxes on this page (default, low, high, core)
			  );
		  }
	    }

	    function meta_box_content(){
	        global $post;
            $related_ID = get_post_meta($post->ID, 'related', TRUE);
            $related_title = get_the_title($related_ID);
            $related_URL = get_the_permalink($related_ID);
            $elaboration = get_post_meta($post->ID, 'elaboration', TRUE);

            // Get the TASK status of the related task
            $related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 

            // Get the POST status of the related task 
            $related_post_status  = get_post_status( $related_ID );
            
	        // Use nonce for verification
	        wp_nonce_field( plugin_basename( __FILE__ ), 'more_info_box_nounce' );
	        // The actual fields for data entry
			?>

			<div class="new-task-admin">
				
				<label><?php _e( "More details about this task", "taskrocket" ); ?>:</label>
				<textarea id="minfo" name="minfo" size="20" class="large-text" rows="20"><?php echo get_post_meta($post->ID, 'minfo', TRUE)?></textarea>

				<div class="task-specifics">

					<div class="spec job-number">
						<label>
							<strong><?php _e( "Job", "taskrocket" ); ?> #</strong>
							<?php 
							$options = get_option( 'taskrocket_settings' ); 
							
							if($options['auto_job_numbers'] == true) { ?>
								
								<?php if($_GET['action'] == "edit") { ?>
									<input id="job_number_task" name="job_number_task" class="regular-text code" value="<?php echo get_post_meta($post->ID, 'job_number_task', TRUE)?>" />
								<?php } else { ?>
									<p class="tip"><?php _e( "Will be auto generated", "taskrocket" ); ?></p>
									<input type="hidden" id="job_number_task" name="job_number_task" value="<?php echo $options['job_number_prefix']; ?><?php echo strtoupper(date("YMd")); ?>-T<?php echo $post->ID; ?>" />
								<?php } ?>
								
							<?php } else { ?>
								
								<input id="job_number_task" name="job_number_task" class="regular-text code" value="<?php echo get_post_meta($post->ID, 'job_number_task', TRUE)?>" />
								
							<?php } 
							?>
						</label>
					</div>
					
					<div class="spec start-date">
						<label><strong><?php _e( "Start date", "taskrocket" ); ?></strong>
							<input id="startdate" name="startdate" class="regular-text code" value="<?php echo get_post_meta($post->ID, 'startdate', TRUE)?>" />
						</label>
						<label><strong><?php _e( "Due Date", "taskrocket" ); ?></strong>
							<input id="duedate" name="duedate" class="regular-text code" value="<?php echo get_post_meta($post->ID, 'duedate', TRUE)?>" />
						</label>
					</div>


					<div class="spec priority">
						<strong><?php _e( "Priority", "taskrocket" ); ?></strong>

						<label>
							<input type="radio" name="priority" value="low" id="low" <?php echo (get_post_meta($post->ID, 'priority', TRUE)=="low" ? "checked" : '') ?> /> <?php _e( "Low", "taskrocket" ); ?>
						</label>

						<label>
							<input type="radio" name="priority" value="normal" id="normal" <?php echo (get_post_meta($post->ID, 'priority', TRUE)=="normal" ? "checked" : '') ?> />
							<?php _e( "Normal", "taskrocket" ); ?>
						</label>

						<label>
							<input type="radio" name="priority" value="high" id="high" <?php echo (get_post_meta($post->ID, 'priority', TRUE)=="high" ? "checked" : '') ?> />
							<?php _e( "High", "taskrocket" ); ?>
						</label>

						<label>
							<input type="radio" name="priority" value="urgent" id="urgent" <?php echo (get_post_meta($post->ID, 'priority', TRUE)=="urgent" ? "checked" : '') ?> />
							<?php _e( "Urgent", "taskrocket" ); ?>
						</label>
					</div>
					
					<div class="spec status">
						<strong><?php _e( "Status", "taskrocket" ); ?></strong>

						<label>
							<input type="radio" name="tr_status" value="complete" id="complete" <?php echo (get_post_meta($post->ID, 'tr_status', TRUE)=="complete" ? "checked" : '') ?> /> <?php _e( "Complete", "taskrocket" ); ?>
						</label>
						
						<label>
							<input type="radio" name="tr_status" value="incomplete" id="incomplete" <?php echo (get_post_meta($post->ID, 'tr_status', TRUE)=="incomplete" ? "checked" : '') ?> /> <?php _e( "Incomplete", "taskrocket" ); ?>
						</label>

						<label>
							<input type="radio" name="tr_status" value="inprogress" id="inprogress" <?php echo (get_post_meta($post->ID, 'tr_status', TRUE)=="inprogress" ? "checked" : '') ?> /> <?php _e( "In progress", "taskrocket" ); ?>
						</label>
						
						<label>
							<input type="radio" name="tr_status" value="onhold" id="onhold" <?php echo (get_post_meta($post->ID, 'tr_status', TRUE)=="onhold" ? "checked" : '') ?> /> <?php _e( "On hold", "taskrocket" ); ?>
						</label>
						
						<?php // Preselect the incomplete radio button when creating a new task. 
							if(!$_GET['action']) { ?>
							<script>
								$("#incomplete").prop("checked", true)
							</script>
						<?php
							}
						?>

					</div>
					
					<div class="spec privacy">
						<label>
							<strong><?php _e( "Make Private", "taskrocket" ); ?></strong>
							<input type="checkbox" name="private" value="yes" id="private" <?php echo (get_post_meta($post->ID, 'private', TRUE)=="yes" ? "checked" : '') ?> />
							<?php _e( "Only visible to administrators.", "taskrocket" ); ?>
						</label>
					</div>

					<div style="display:block; clear:both;"></div>
                    
                    <div class="spec relate">
                        
                        <strong><?php _e( "This task", "taskrocket" ); ?></strong>
        				
        				<select name="relation" class="relation">
                            <option value=""></option>
                            <option value="is_blocked_by" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Is blocked by", "taskrocket" ); ?></option>
                            <option value="is_similar_to" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Is similar to", "taskrocket" ); ?></option>
                            <option value="has_issues_with" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Has issues with", "taskrocket" ); ?></option>
                            <option value="relates_to" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Relates to", "taskrocket" ); ?></option>
        				</select>
        				
                        <select name="related" class="related" disabled>
    						<option value=""></option>
    						<?php
    						$cat_args = array(
    							'orderby'	 => 'name',
    							'order'		 => 'ASC'
    						);
    						$categories = get_categories($cat_args);
    						foreach($categories as $category) {
    							$args = array(
    							'showposts' 		=> -1,
    							'category__in'		=> array($category->term_id),
    							'caller_get_posts'	=> 1,
    							'orderby' 		    => 'title', 
    							'order' 		    => 'ASC'
    							);
    							
    							$task_args = array(
    								'category__in'		=> array($category->term_id),
    								'posts_per_page' => -1,
    								'meta_key'          => 'tr_status',
    								'meta_value'        => array('incomplete', 'inprogress', 'onhold')
    							);
    							
    							$posts=get_posts($task_args);
    								if ($posts) {
    								echo '<optgroup label="' . $category->name . '">';
    								foreach($posts as $post) {
                                        
                                        $private = get_post_meta($post->ID, 'private', TRUE);    
                                        
    									setup_postdata($post); ?>
    									<option value="<?php echo $post->ID; ?>" <?php if($related_ID == $post->ID) { echo "selected"; } ?>><?php the_title(); ?> <?php if ($private == 'yes') { _e( "(Private task)", "taskrocket" );	}  ?></option>
    								<?php
    								}
    								echo '</optgroup> ';
    							}
    						}
    						?>
    					</select>
                        <br /><br />
                        <strong><?php _e( "Elaboration", "taskrocket" ); ?></strong>
						<textarea name="elaboration"><?php if($elaboration) { echo $elaboration; } ?></textarea>
                        
                        <script>
                        jQuery('.related').change(function() {
                            if (jQuery(this).val() != '') {
                                jQuery('.elaboration').slideDown();
                            }
                            if (jQuery(this).val() === '') {
                                jQuery('.elaboration').slideUp();
                                jQuery('.relation, .related, textarea').val('');
                                jQuery('.related').attr('disabled', 'disabled');
                            }
                        });
                        jQuery('.relation').change(function() {
                            if (jQuery(this).val() === '') {
                                jQuery('.elaboration').slideUp();
                                jQuery('.relation, .related, textarea').val('');
                                jQuery('.related').attr('disabled', 'disabled');
                            } else {
                                jQuery('.related').removeAttr('disabled');
                                jQuery('.related').attr('required', 'required');
                            }
                        });
                        <?php if($related_ID) { ?>
                            jQuery(".elaboration").css('display', 'block');
                            jQuery('.related').removeAttr('disabled');
                        <?php } ?>
                        
                        <?php if($related_post_status == 'trash') { ?>
                            jQuery('.relation, .related, textarea').val('');
                            jQuery('.related').attr('disabled', 'disabled');
                        <?php } ?>
                        </script>
                        
                    </div>
                    
                    <div style="display:block; clear:both;"></div>

					<div class="spec attached">
						<label>
							<strong><?php _e( "Attach files", "taskrocket" ); ?></strong>
							<a href="#" class="button insert-media add_media" data-editor="content" title="Attach files"><span class="wp-media-buttons-icon"></span><?php _e( "Attach files", "taskrocket" ); ?></a>
							<?php _e( "Note: Files can only belong (be attached) to one task at any given time.", "taskrocket" ); ?>
						</label>

						<!--/ Start Attachments /-->
					    <div class="attachments">
					    <ul>
					    <?php

						// Attachments
						$attachments = get_posts( array(
							'post_type' => 'attachment',
							'posts_per_page' => -1,
							'post_parent' => $post->ID,
							'orderby' => 'title',
							'order' => 'ASC'
						) );

						if ($options['files_new_tab'] == true) {
							$newTab = ' target="_blank"';
						}

						foreach ( $attachments as $attachment ) {
							$filethumb = wp_get_attachment_thumb_url( $attachment->ID);	 // Path to the thumbnail
							$filepath = wp_get_attachment_url( $attachment->ID);			// Path to the original file
							$filename = $attachment->post_title;
							$filesize = @filesize( get_attached_file( $attachment->ID ) );
							$filesize = size_format($filesize, 2);
							$deleteAttachment = wp_nonce_url(home_url() . "/wp-admin/post.php?action=delete&amp;post=".$attachment->ID."", 'delete-post_' . $attachment->ID); ?>
								<?php if ( wp_attachment_is_image( $attachment->ID ) ) { ?>
					            <li class="file-image">
					                <a href="<?php echo $filepath; ?>?TB_iframe=true" class="<?php $options = get_option( 'taskrocket_settings' ); if ($options['use_thickbox'] == true) { echo "thickbox"; } ?>" title="<?php echo $filename; ?>"><img src="<?php echo $filethumb; ?>" /></a>
					                <a class="delete-attachment-button roundness" title="Delete this file">&#215;</a>
					                <span class="filesize"><?php echo $filesize; ?></span>
					                <em class="delete-file-confirmation"><span><strong><?php _e( "Delete?", "taskrocket" ); ?></strong> <a href="<?php echo $deleteAttachment; ?>" target="deletey" class="delete-yes roundness"><?php _e( "Yes", "taskrocket" ); ?></a> <a class="delete-no roundness"><?php _e( "No", "taskrocket" ); ?></a></span></em>
					            </li>
					           <?php } else { ?>
					           	<li class="file-other">
					                <a href="<?php echo $filepath; ?>" class="the-file-name" title="<?php echo $filename; ?>" target="_blank"><span><?php echo substr($filename, 0, 50); ?>.<?php echo get_icon_for_attachment($attachment->ID); ?></span></a>
					                <a class="delete-attachment-button roundness" title="<?php _e( "Delete this file", "taskrocket" ); ?>">&#215;</a>
					                <span class="filesize"><?php echo $filesize; ?></span>
					                <em class="delete-file-confirmation"><span><strong><?php _e( "Delete?", "taskrocket" ); ?></strong> <a href="<?php echo $deleteAttachment; ?>" target="deletey" class="delete-yes"><?php _e( "Yes", "taskrocket" ); ?></a> <a class="delete-no"><?php _e( "No", "taskrocket" ); ?></a></span></em>
					            </li>
					           <?php } ?>
						   <?php } ?>
					   </ul>
					   </div>
					   <!--/ End Attachments /-->

					<script>
						$(".delete-attachment-button").click(function () {
							$(this).closest('.attachments li').find('.delete-file-confirmation').fadeIn(250);
						});
						$(".delete-no").click(function () {
						  $(this).closest(".delete-file-confirmation").fadeOut(250);
					    });
						// Fadeout attachment list item onclick
						$(".delete-yes").click(function () {
						  $(this).closest(".attachments li").fadeOut(1000);
					    });
					</script>

					</div>

				</div>

				<iframe id="deletey" name="deletey" width="0" height="0" frameborder="0"></iframe>

			</div>


			<?php
		}

	    function save_data($post_id){
	        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
	            return;

	        if ( !wp_verify_nonce( $_POST['more_info_box_nounce'], plugin_basename( __FILE__ ) ) )
	            return;

	        // Check permissions
	        if ( 'post' == $_POST['post_type'] ){
	            if ( !current_user_can( 'edit_page', $post_id ) )
	                return;
	        }

			// Update the fields
			$data = $_POST['minfo'];
            update_post_meta($post_id, 'minfo', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $data ) ) ));

			$data = sanitize_text_field($_POST['startdate']);
			update_post_meta($post_id, 'startdate', $data, get_post_meta($post_id, 'startdate', TRUE));
			
			$data = sanitize_text_field($_POST['duedate']);
			update_post_meta($post_id, 'duedate', $data, get_post_meta($post_id, 'duedate', TRUE));

			$data = sanitize_text_field($_POST['priority']);
			update_post_meta($post_id, 'priority', $data, get_post_meta($post_id, 'priority', TRUE));
			
			$data = sanitize_text_field($_POST['tr_status']);
			update_post_meta($post_id, 'tr_status', $data, get_post_meta($post_id, 'tr_status', TRUE));
            
            $data = sanitize_text_field($_POST['relation']);
			update_post_meta($post_id, 'relation', $data, get_post_meta($post_id, 'relation', TRUE));
            
            $data = sanitize_text_field($_POST['related']);
			update_post_meta($post_id, 'related', $data, get_post_meta($post_id, 'related', TRUE));
            
            $data = sanitize_text_field($_POST['elaboration']);
			update_post_meta($post_id, 'elaboration', $data, get_post_meta($post_id, 'elaboration', TRUE));

			$data = sanitize_text_field($_POST['private']);
			update_post_meta($post_id, 'private', $data, get_post_meta($post_id, 'private', TRUE));
			
			$data = sanitize_text_field($_POST['job_number_task']);
			update_post_meta($post_id, 'job_number_task', $data, get_post_meta($post_id, 'job_number_task', TRUE));

			return $data;
	    }
	}
	$more_info_box = new more_info_box;


	// Task Rocket version number derived from style.css
	function theme_version_shortcode() {
	    $theme_name = 'taskrocket';
	    $theme_data = wp_get_theme();
	    return $theme_data->get( 'Version' );
	}
	add_shortcode('theme_version', 'theme_version_shortcode');


	// Initialize theme update checker
	require 'theme-updates/theme-update-checker.php';
	$task_rocket_update_checker = new ThemeUpdateChecker(
		'taskrocket', // Theme folder name, AKA "slug". 
		'https://taskrocket.info/files/auto-updates/task-rocket/info.json' //URL of the metadata file.
	);

	// change the title field text
	function frl_enter_title_here_filter($label, $post){
	    if($post->post_type == 'post')
	        $label = __('Enter task title here', 'taskrocket');
	    return $label;
	}
	add_filter('enter_title_here', 'frl_enter_title_here_filter', 2, 2);


	// Remove admin bar items
	function wps_admin_bar() {
	    global $wp_admin_bar;
	    $wp_admin_bar->remove_node('wp-logo');
	    $wp_admin_bar->remove_node('new-content');
	}
	add_action( 'wp_before_admin_bar_render', 'wps_admin_bar' );


	// Delete hello world post
	$post = get_page_by_path('hello-world',OBJECT,'post');
	if ($post)
	wp_delete_post($post->ID,true);

	// Delete sample page
	$post = get_page_by_path('sample-page',OBJECT,'page');
	if ($post)
	wp_delete_post($post->ID,true);

	// Delete legacy report page
	$post_report = get_page_by_path('report',OBJECT,'page');
	if ($post_report)
	wp_delete_post($post_report->ID,true);

	// Delete legacy support page
	$post_support = get_page_by_path('support',OBJECT,'page');
	if ($post_support)
	wp_delete_post($post_support->ID,true);

	// Remove dashboard icons from front-end
	add_action( 'wp_print_styles',     'my_deregister_styles', 100 );
	function my_deregister_styles()    { 
	   //wp_deregister_style( 'amethyst-dashicons-style' ); 
	   wp_deregister_style( 'dashicons' ); 
	}


	// Show the Task Rocket Dashboard widget
	function tr_dashboard_widget_function() { ?>
        <?php 
        $tweak_url = admin_url() . "admin.php?page=task-rocket-settings";
        echo "<p>";
        printf( __( 'New to Task Rocket? Get started by <a href="%s">tweaking the settings</a>.', 'taskrocket' ), $tweak_url);
        echo "</p>";
        printf( __( '<a href="%s" class="tweak">Task Rocket Settings</a>', 'taskrocket' ), $tweak_url);
        ?>
        
	<?php }
	function add_tr_dashboard_widgets() {
		wp_add_dashboard_widget('tr_dashboard_widget', 'Task Rocket', 'tr_dashboard_widget_function');
	}
	add_action('wp_dashboard_setup', 'add_tr_dashboard_widgets' );

	// Redirect to Task Rocket options page after theme activation
	global $pagenow;
	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) {
	  wp_redirect( admin_url( 'admin.php?page=task-rocket-settings' ) );
	}
    
    // Task Rocket Feed on Dashboard
    function dashboard_widget_function() {
         $rss = fetch_feed( "https://taskrocket.info/category/blog/feed/" );
      
         if ( is_wp_error($rss) ) {
              if ( is_admin() || current_user_can('manage_options') ) {
                   echo '<p>';
                   printf(__('<strong>RSS Error</strong>: %s'), $rss->get_error_message());
                   echo '</p>';
              }
         return;
    }
      
    if ( !$rss->get_item_quantity() ) {
         echo "<p>";
         echo _e( "There are no updates at this time.", "taskrocket" );
         echo "</p>";
         $rss->__destruct();
         unset($rss);
         return;
    }
      
    echo "<ul>\n";
      
    if ( !isset($items) )
         $items = 5;
      
         foreach ( $rss->get_items(0, $items) as $item ) {
              $publisher = '';
              $site_link = '';
              $link = '';
              $content = '';
              $date = '';
              $link = esc_url( strip_tags( $item->get_link() ) );
              $title = esc_html( $item->get_title() );
              $content = $item->get_content();
              $content = wp_html_excerpt($content, 200) . ' ...';
      
             echo "<li><a class='rsswidget' href='$link'>$title</a>\n<div class='rssSummary'>$content</div>\n";
    }
      
    echo "</ul>\n";
    $rss->__destruct();
    unset($rss);
    }
     
    function add_dashboard_widget() {
         wp_add_dashboard_widget('taskrocket_feed_dashboard_widget', 'News from taskrocket.info', 'dashboard_widget_function');
    }
     
    add_action('wp_dashboard_setup', 'add_dashboard_widget');

}

// Add custom fields to projects (categories) in admin.
add_action( 'category_add_form_fields', 'category_form_custom_field_add', 10 );
add_action( 'category_edit_form_fields', 'category_form_custom_field_edit', 10, 2 );

function category_form_custom_field_add( $taxonomy ) {
?>

<div class="form-field">
  <label for="tr_details"><?php _e( "More details", "taskrocket" ); ?></label>
  <textarea name="tr_details" id="tr_details" value="" rows="5" cols="50" class="tr_details"></textarea>
  <p class="description"><?php _e( "Risks, mitigation strategy, contingency plans etc.", "taskrocket" ); ?></p>
</div>

<div class="form-field">
  <label for="tr_start_date"><?php _e( "Project Start Date", "taskrocket" ); ?></label>
  <input name="tr_start_date" id="tr_start_date" type="text" value="" size="10" class="tr-dates" />
  <p class="description"><?php _e( "The date the project is expected to start.", "taskrocket" ); ?></p>
</div>

<div class="form-field">
  <label for="tr_end_date"><?php _e( "Project End Date", "taskrocket" ); ?></label>
  <input name="tr_end_date" id="tr_end_date" type="text" value="" size="10" class="tr-dates" />
  <p class="description"><?php _e( "The date the project is expected to end.", "taskrocket" ); ?></p>
</div>

<div class="form-field">
  <label for="tr_hrs_allocated"><?php _e( "Time Allocation (hrs)", "taskrocket" ); ?></label>
  <input name="tr_hrs_allocated" id="tr_hrs_allocated" type="number" value="" size="10" min="1" step="1" class="tr-time-allocated tr-dates" />
  <p class="description"><?php _e( "How many hours should be allocated to this project?", "taskrocket" ); ?></p>
</div>

<div class="form-field">
  <label for="tr_hourly_rate"><?php _e( "Hourly rate", "taskrocket" ); ?></label>
  <input name="tr_hourly_rate" id="tr_hourly_rate" type="number" size="10" min="0" step="1" class="tr-hourly-rate tr-dates" />
  <p class="description"><?php _e( "If left empty the default rate will be used.", "taskrocket" ); ?></p>
</div>


<div class="form-field">
<?php 
if($options['auto_job_numbers'] == true) { ?>
    <label for="tr_job_number"><?php _e( "Job number", "taskrocket" ); ?></label>
    <p class="description"><?php _e( "A job number will be automatically assigned when you create this project.", "taskrocket" ); ?></p>
    <input type="hidden" id="tr_job_number" name="tr_job_number" value="<?php if ($options['job_number_prefix'] == true) { echo $options['job_number_prefix']; } ?><?php echo strtoupper(date("YMd")); ?>-P" />
<?php } else { ?>
    <label for="tr_job_number"><?php _e( "Job number", "taskrocket" ); ?></label>
    <input name="tr_job_number" id="tr_job_number" type="text" value="" class="tr-job-number" />
    <p class="description"><?php _e( "What is the job number?", "taskrocket" ); ?></p>
<?php } ?>
</div>


<div class="form-field">
  <label for="tr_pm" class="pm-tip"><?php _e( "Project Manager", "taskrocket" ); ?></label>
    <select name="tr_project_manager" id="tr_project_manager">
        <option value="" id=""></option>
        <?php
            $trusers = get_users('blog_id=1&orderby=nicename');
            foreach ($trusers as $user) { ?>

                <?php // If not a client
                if ( !in_array( 'client', (array) $user->roles ) ) { ?>

                    <option <?php if ($user->ID == get_the_author_meta( 'nicename' )) echo 'selected';?> value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>" <?php if ( in_array( 'administrator', (array) $user->roles ) ) { echo ' class="is-a-pm"'; } ?>>
                    <?php if ($user->first_name !== "") {
                        echo $user->first_name . " " . $user->last_name;
                    } else {
                        echo $GLOBALS[ 'nameless' ];
                    }
                    ?> (<?php echo $user->user_email; ?>)</option>

                <?php
                    }
                ?>

            <?php
                }
            ?>
    </select>
  <p class="description"><?php _e( "Who is responsible for leading this project? (Administrators are highlighted blue).", "taskrocket" ); ?></p>
</div>

<?php
}

function category_form_custom_field_edit( $tag, $taxonomy ) {

    $tr_details_option = 'tr_details_' . $tag->term_id;
    $tr_details = get_option( stripslashes($tr_details_option ));

    $tr_sd_option = 'tr_start_date_' . $tag->term_id;
    $tr_start_date = get_option( $tr_sd_option );

    $tr_ed_option = 'tr_end_date_' . $tag->term_id;
    $tr_end_date = get_option( $tr_ed_option );

    $tr_time_option = 'tr_hrs_allocated_' . $tag->term_id;
    $tr_hrs_allocated = get_option( $tr_time_option );
    
    $tr_hr_option = 'tr_hourly_rate_' . $tag->term_id;
    $tr_hourly_rate = get_option( $tr_hr_option );
    
    $tr_job_number_option = 'tr_job_number_' . $tag->term_id;
    $tr_job_number = get_option( $tr_job_number_option );

    $tr_project_manager_option = 'tr_project_manager_' . $tag->term_id;
    $tr_project_manager = get_option( $tr_project_manager_option );
    
    $tr_archive_project_option = 'tr_project_archived_' . $tag->term_id;
    $tr_archive_project = get_option( $tr_archive_project_option );

?>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_details"><?php _e( "More details", "taskrocket" ); ?></label></th>
  <td>
    <textarea name="tr_details" id="tr_details" rows="5" cols="50" class="tr-details"><?php echo esc_attr( $tr_details ) ? esc_attr( $tr_details ) : ''; ?></textarea>
    <p class="description"><?php _e( "Risks, mitigation strategy, contingency plans etc.", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_start_date"><?php _e( "Project Start Date", "taskrocket" ); ?></label></th>
  <td>
    <input type="text" name="tr_start_date" id="tr_start_date" value="<?php echo esc_attr( $tr_start_date ) ? esc_attr( $tr_start_date ) : ''; ?>" size="10" class="tr-dates" />
    <p class="description"><?php _e( "The date the project is expected to start.", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_end_date"><?php _e( "Project End Date", "taskrocket" ); ?></label></th>
  <td>
    <input type="text" name="tr_end_date" id="tr_end_date" value="<?php echo esc_attr( $tr_end_date ) ? esc_attr( $tr_end_date ) : ''; ?>" size="10" class="tr-dates" />
    <p class="description"><?php _e( "The date the project is expected to end.", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_hrs_allocated"><?php _e( "Time Allocation (hrs)", "taskrocket" ); ?></label></th>
  <td>
    <input type="number" name="tr_hrs_allocated" id="tr_hrs_allocated" value="<?php echo esc_attr( $tr_hrs_allocated ) ? esc_attr( $tr_hrs_allocated ) : ''; ?>" min="1" step="1" size="10" class="tr-time-allocated tr-dates" />
    <p class="description"><?php _e( "How many hours should be allocated to this project?", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_hourly_rate"><?php _e( "Hourly rate", "taskrocket" ); ?></label></th>
  <td>
    <input type="number" name="tr_hourly_rate" id="tr_hourly_rate" value="<?php echo esc_attr( $tr_hourly_rate ) ? esc_attr( $tr_hourly_rate ) : ''; ?>" min="0" step="1" size="10" class="tr-hourly-rate tr-dates" />
    <p class="description"><?php _e( "If left empty the default rate will be used.", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field">
  <th scope="row" valign="top"><label for="tr_job_number"><?php _e( "Job number", "taskrocket" ); ?></label></th>
  <td>
    <input type="text" name="tr_job_number" id="tr_job_number" value="<?php echo esc_attr( $tr_job_number) ? esc_attr( $tr_job_number ) : ''; ?>" class="tr-job-number" />
    <p class="description"><?php _e( "What is the job number?", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field" id="pm_pos">
  <th scope="row" valign="top">
    <label for="tr_hrs_allocated" class="pm-tip-02"><?php _e( "Project Manager", "taskrocket" ); ?></label>
    <?php $pmID = esc_attr( $tr_project_manager ) ? esc_attr( $tr_project_manager ) : ''; ?>
 </th>
  <td>
    <select name="tr_project_manager" id="tr_project_manager">
        <option value="" id=""></option>

            <?php
                $trusers = get_users('blog_id=1&orderby=nicename');
                foreach ($trusers as $user) { ?>

                    <?php // If not a client
                    if ( !in_array( 'client', (array) $user->roles ) ) { ?>

                    <option  value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>" <?php if ( in_array( 'administrator', (array) $user->roles ) ) { echo ' class="is-a-pm"'; } ?><?php if($pmID == $user->ID) { echo " selected"; } ?>>
                    <?php if ($user->first_name !== "") {
                        echo $user->first_name . " " . $user->last_name;
                    } else {
                        echo $GLOBALS[ 'nameless' ];
                    }
                    ?> (<?php echo $user->user_email; ?>)</option>
                    <?php
                        }
                    ?>

            <?php
                }
            ?>
    </select>
    <p class="description"><?php _e( "Who is responsible for leading this project? (Administrators are highlighted blue).", "taskrocket" ); ?></p>
  </td>
</tr>

<tr class="form-field" id="pm_pos">
    <th scope="row" valign="top"><label for="tr_project_archived"><?php _e( "Archive this project", "taskrocket" ); ?></label></th>
    <td>
        <?php $trpa = esc_attr( $tr_archive_project ) ? esc_attr( $tr_archive_project ) : ''; ?>
        <input id="tr_project_archived" name="tr_project_archived" type="hidden" value="0" />
        <input id="tr_project_archived" name="tr_project_archived" type="checkbox" value="1" <?php if($trpa) { echo " checked"; } ?> />
    </td>
</tr>

<?php
}


/** Save Custom Field Of Category Form */
add_action( 'created_category', 'category_form_custom_field_save', 10, 2 );
add_action( 'edited_category', 'category_form_custom_field_save', 10, 2 );

function category_form_custom_field_save( $term_id, $tt_id ) {

    if ( isset( $_POST['tr_details'] ) ) {
        $tr_details = 'tr_details_' . $term_id;
        update_option( $tr_details, stripslashes($_POST['tr_details'] ));
    }
    if ( isset( $_POST['tr_start_date'] ) ) {
        $tr_sd_option = 'tr_start_date_' . $term_id;
        update_option( $tr_sd_option, $_POST['tr_start_date'] );
    }
    if ( isset( $_POST['tr_end_date'] ) ) {
        $tr_end_date = 'tr_end_date_' . $term_id;
        update_option( $tr_end_date, $_POST['tr_end_date'] );
    }
    if ( isset( $_POST['tr_hrs_allocated'] ) ) {
        $tr_hrs_allocated = 'tr_hrs_allocated_' . $term_id;
        update_option( $tr_hrs_allocated, $_POST['tr_hrs_allocated'] );
    }
    if ( isset( $_POST['tr_hrs_allocated'] ) ) {
        $tr_hourly_rate = 'tr_hourly_rate_' . $term_id;
        update_option( $tr_hourly_rate, $_POST['tr_hourly_rate'] );
    }
    if ( isset( $_POST['tr_job_number'] ) ) {
        
        // Get the ID of the last term, to be used in the job number.
        global $wpdb;
        $last_term_row_ID = $wpdb->get_col( "SELECT term_id FROM $wpdb->terms ORDER BY term_id DESC" );
        $project_job_row_ID = $last_term_row_ID[0];
        
        $tr_job_number = 'tr_job_number_' . $term_id;
        $options = get_option( 'taskrocket_settings' );
        if($options['auto_job_numbers'] == true) {
            update_option( $tr_job_number, $_POST['tr_job_number'] . $project_job_row_ID );
        } else {
            update_option( $tr_job_number, $_POST['tr_job_number'] );
        }
        
    }
    if ( isset( $_POST['tr_project_manager'] ) ) {
        $tr_project_manager_option = 'tr_project_manager_' . $term_id;
        update_option( $tr_project_manager_option, $_POST['tr_project_manager'] );
    }
    
    if ( isset( $_POST['tr_project_archived'] ) ) {
        $tr_archive_project = 'tr_project_archived_' . $term_id;
        update_option( $tr_archive_project, $_POST['tr_project_archived'] );
    }
}
?>