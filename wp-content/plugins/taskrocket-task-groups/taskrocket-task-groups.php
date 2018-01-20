<?php
/*
Plugin Name: Task Rocket Task Groups
Plugin URI: https://taskrocket.info/task-groups/
Description: Create pre-set groups of tasks that can be applied to new projects (requires at least Task Rocket 3)
Version: 3.4.4
Author: Michael Ott
Author Email: hello@michaelott.id.au
Text Domain: taskrocket-task-groups
Domain Path: /languages/
*/

// Look for translation file
function load_task_groups_textdomain() {
    load_plugin_textdomain( 'taskrocket-task-groups', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'load_task_groups_textdomain' );

// Update checker
require 'plugin-updates/plugin-update-checker.php';
$TRUpdateChecker = PucFactory::buildUpdateChecker(
    'https://taskrocket.info/files/auto-updates/task-groups/info.json',
    __FILE__,
    'task-rocket-task-groups'
);

class TaskRocketTaskGroups {

	const name = 'Task Rocket Task Groups';
	const slug = 'task_rocket_task_groups';

	function __construct() {
		// Register activation hook
		register_activation_hook( __FILE__, array( &$this, 'install_task_rocket_task_groups' ) );

		// Hook up to the init action
		add_action( 'init', array( &$this, 'init_task_rocket_task_groups' ) );
	}

	// Runs when the plugin is activated
	function install_task_rocket_task_groups() {
	}

}

// Add custom CSS to admin
function task_groups_admin_style() {
	$plugin_directory = plugins_url('css/', __FILE__ );
    wp_enqueue_style('task-groups-style', $plugin_directory . 'task-groups.css');
}
add_action('admin_enqueue_scripts', 'task_groups_admin_style');

// Add custom JS to admin
if ( is_admin() ) {
	$plugin_directory = plugins_url('js/', __FILE__ );
	wp_register_script('tr-group-js', $plugin_directory . 'min/task-groups.min.js');
	wp_enqueue_script('tr-group-js');
}


// Custom post type for Task Groups
function create_posttype() {

	$plugin_directory = plugins_url('images/', __FILE__ );
	register_post_type( 'task_groups',

		array(
			'labels' => array(
				'singular_name'     => __( 'Group Item', 'taskrocket-task-groups'),
				'name' 				=> __( 'Task Groups', 'taskrocket-task-groups' ),
				'add_new'           => __( 'Add Group Item', 'taskrocket-task-groups'),
				'add_new_item'      => __( 'Add Group or Group Item', 'taskrocket-task-groups'),
				'edit_item'         => __( 'Edit Group Item', 'taskrocket-task-groups'),
				'new_item'          => __( 'New Group Item', 'taskrocket-task-groups'),
				'search_items'      => __( 'Search Task Group Items', 'taskrocket-task-groups'),
				'not_found'  		=> __( 'No Task Group Items found', 'taskrocket-task-groups'),
				'not_found_in_trash'=> __( 'No Task Group Items found in Trash.', 'taskrocket-task-groups'),
				'all_items'     	=> __( 'All Task Group Items', 'taskrocket-task-groups')
			),
			'public'			 	=> false, // Hides the permalink
			'has_archive' 			=> false,
			'rewrite'				=> array('slug' => 'group'),
			'publicly_queryable'  	=> false,
			'hierarchical'        	=> true,
			'show_ui' 				=> true,
			'exclude_from_search'	=> true,
			'query_var'				=> true,
			'menu_position'			=> 5,
			'can_export'          	=> true,
			'menu_icon'         	=> $plugin_directory . 'task-group.png',
			'supports'  			=> array('title', 'revisions', 'page-attributes', 'author'),
		)
	);
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

// Change messages for custom post type
add_filter( 'post_updated_messages', 'tr_group_updated_messages' );
function tr_group_updated_messages( $tr_group_messages ) {

	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );
	
	$tr_group_messages['task_groups'] = array(
		0  => '',
		1  => __( 'Task Group item updated.', 'taskrocket-task-groups'),
		2  => __( 'Task Group updated.', 'taskrocket-task-groups'),
		3  => __( 'Task Group deleted.', 'taskrocket-task-groups'),
		4  => __( 'Task Group item updated.', 'taskrocket-task-groups'),
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Task Group item restored to revision from %s', 'taskrocket-task-groups'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Task Group item published.', 'taskrocket-task-groups'),
		7  => __( 'Task Group item saved.', 'taskrocket-task-groups'),
		8  => __( 'Task Group item submitted.', 'taskrocket-task-groups'),
		9  => sprintf(
			__( 'Task Group item scheduled for: %1$s.', 'taskrocket-task-groups' ),
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Task Group item draft updated.', 'taskrocket-task-groups' )
	);

	return $tr_group_messages;
}

// Change the title-prompt-text
add_filter( 'enter_title_here', 'custom_tg_title' );
function custom_tg_title( $input ) {
    global $post_type;
    if ( is_admin() && 'task_groups' == $post_type )
        return __( 'Task group or task name', 'taskrocket-task-groups' );
    return $input;
}

// Get plugin version
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugin_data = get_plugin_data( __FILE__ );
$tr_task_groups_version = $plugin_data['Version'];


// Remove author metabox from task_groups post type
function my_remove_meta_boxes() {
		remove_meta_box( 'authordiv', 'task_groups', 'normal' );
}
add_action( 'admin_menu', 'my_remove_meta_boxes' );


// Add metabox for Task Owner
class tr_tg_otmeta {

	var $plugin_dir;
	var $plugin_url;

	function  __construct() {

		add_action( 'add_meta_boxes', array( $this, 'tr_tg_meta_box' ) );
		add_action( 'save_post', array($this, 'save_data') );
	}

	// Add the meta box to the POSTS sidebar
	function tr_tg_meta_box(){
		add_meta_box(
				'object_type'
			,'Task Owner'
			,array( &$this, 'meta_box_content' )
			,'task_groups'
			,'side'
			,'default'
		);
	}

	function meta_box_content(){
		global $post;
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'tr_tg__nounce' ); ?>

		<?php
			$ot  	= get_post_meta($post->ID, "tr_tg", TRUE);
			$dog 	= get_post_meta($post->ID, "disable_open_graph", TRUE);
			$current_screen = get_current_screen();

			if ($current_screen ->id === 'post') {
				$post_type_label = 'post';
			} else if ($current_screen ->id === 'page') {
				$post_type_label = 'page';
			} else {
				$post_type_label = '';
			}
		
		
		global $post;
		// $users = get_users(array('role'=>'editor'));
		$users = get_users(array(
			'meta_key' 	=> 'first_name',
			'orderby' 	=> 'meta_value',
			'order' 	=> 'ASC'
		));
		?>
		<select id='tr_post_author_override' name='post_author_override' class=''>
			<?php if($task_owner_ID == "0") { ?>
				<option value="0000000" selected="selected"><?php _e( 'Nobody (no owner)', 'taskrocket-task-groups' ); ?></option>
			<?php } else { ?>
				<option value="0000000"><?php _e( 'Nobody (no owner)', 'taskrocket-task-groups' ); ?></option>
			<?php } ?>
			<?php foreach($users as $user) { 
				$the_user_ID = $user->id;
				$task_owner_ID = $post->post_author;
			?>
			<option value='<?php echo $user->id; ?>'<?php if($the_user_ID == $task_owner_ID) { echo "selected"; } ?>><?php echo $user->user_firstname; ?> <?php echo $user->user_lastname; ?> (<?php echo $user->user_email; ?>)</option>
			<?php } ?>
		</select>

		<?php 
		}

	function save_data($post_id) {
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !wp_verify_nonce( $_POST['tr_tg__nounce'], plugin_basename( __FILE__ ) ) )
			return;

		// Check permissions
		if ( 'page' == $_POST['post_type'] ){
			if ( !current_user_can( 'edit_page', $post_id ) )
				return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return;
		}
		
		$tr_tg_field_data = $_POST['tr_tg_field'];
		update_post_meta($post_id, 'tr_tg', $tr_tg_field_data, $ot);
		//return $tr_tg_field_data;

		$disable_open_graph_data = $_POST['disable_open_graph'];
		update_post_meta($post_id, 'disable_open_graph', $disable_open_graph_data, $dog);
		//return $disable_open_graph_data;
	}

}
$tr_tg_otmeta = new tr_tg_otmeta;