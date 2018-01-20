<!--/ Start Mini Bar /-->
<ul class="mini-bar">
	<li title="<?php _e( 'Edit my details', 'taskrocket-clients' ); ?>">
		<a href="<?php echo home_url(); ?>/account" class="edit-account">
			<?php require($GLOBALS[ 'theme_includes' ] . "avatar.php"); ?>
		</a>
	</li>
	<li title="<?php _e( 'My tasks', 'taskrocket-clients' ); ?>" class="no-hover"><a href="<?php echo home_url(); ?>/my-tasks/" class="task-count"><span><?php echo count_user_posts( $user_ID ); ?></span></a></li>
	<li title="<?php _e( 'Dashboard', 'taskrocket-clients' ); ?>"><a href="<?php echo home_url(); ?>/" class="dashboard"><?php _e( 'Home', 'taskrocket-clients' ); ?></a></li>
	<?php 
	// If clients can create tasks and the setting to allow 
	// tasks to be created on the front-end are both enabled, then
    // it's OK to show the 'create new task' button.
	if($options['clients_create_tasks'] == true) { ?>
	<li title="<?php _e( 'Create a new task', 'taskrocket-clients' ); ?>"><a href="<?php echo home_url(); ?>/new-task/" class="client-new-task"><?php _e( 'New task', 'taskrocket-clients' ); ?></a></li>
	<?php } ?>
	<li title="<?php _e( "Pages", "taskrocket-clients" ); ?>"><a class="pages"><?php _e( "Pages", "taskrocket-clients" ); ?></a></li>
	<li title="<?php _e( 'Logout', 'taskrocket-clients' ); ?>"><a href="<?php echo wp_logout_url(); ?>" class="logout"><?php _e( 'Logout', 'taskrocket-clients' ); ?></a></li>
</ul>
<!--/ End Mini Bar /-->

<nav>
	<span class="label"><?php echo get_bloginfo('name'); ?></span>
	<ul>
		<?php require_once(plugin_dir_path( __FILE__ ) . '/clients-nav.php'); ?>
	</ul>
</nav>

<?php require_once(plugin_dir_path( __FILE__ ) . '/pages-nav.php'); ?>