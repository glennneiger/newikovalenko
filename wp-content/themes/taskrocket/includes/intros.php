<?php
$logo = $options['branding'];
$part = pathinfo($logo);
$dir_path =  $part['dirname'];
$file_extension =  $part['extension'];
$just_file_name = $part['filename'];
$chrome_extension_link = "https://chrome.google.com/webstore/detail/task-rocket/ffoefldcgmcldohibnklhhphdgdpjdnd";
?>

<?php // Intro for administrators
if(current_user_can( 'manage_options' )) {
if(!isset( $_COOKIE['tr_admin_intro']  ) ) {
	$adminintro = $_COOKIE['tr_admin_intro'];
	if($adminintro !="watched") { ?>
	<div class="intro admin-intro">
		<div class="start">
			<?php if ( !empty( $logo ) ) { ?>
				<img src="<?php echo $dir_path . '/' . $just_file_name . '-150x150.' . $file_extension; ?>" />
			<?php } else { ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/system/favicon-large.png" />
			<?php } ?>
			<h3><?php _e( "Howdy!", "taskrocket" ); ?></h3>
			<p><?php printf( __( "If you haven't already done it yet, go tweak some settings to get things running the way you want. After that feel free to grab the official <a href='%s' target='_blank'>Chrome Extension</a>.", "taskrocket" ), $chrome_extension_link); ?></p>
			<a href="<?php echo admin_url(); ?>admin.php?page=task-rocket-settings" class="button"><?php _e( "Roger that", "taskrocket" ); ?></a>
			<div><span class="dismiss"><?php _e( "Maybe Later", "taskrocket" ); ?></span></div>
		</div>
	</div>
<?php } } } ?>

<?php // Intro for users
if(current_user_can( 'editor' )) {
if(!isset( $_COOKIE['tr_user_intro']  ) ) {
	$userintro = $_COOKIE['tr_user_intro'];
	if($userintro !="watched") { ?>
	<div class="intro user-intro">
		<div class="start">
			<?php if ( !empty( $logo ) ) { ?>
				<img src="<?php echo $dir_path . '/' . $just_file_name . '-150x150.' . $file_extension; ?>" />
			<?php } else { ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/system/favicon-large.png" alt="Branding" />
			<?php } ?>
			<h3><?php _e( "Howdy!", "taskrocket" ); ?></h3>
			<p><?php printf( __( "If you haven't already done so, update your profile with as much information as you can and set some user preferences. This will provide a better experience for you and everyone else. After that feel free to grab the official <a href='%s' target='_blank'>Chrome Extension</a>.", "taskrocket" ), $chrome_extension_link); ?></p>
			<a href="<?php echo home_url(); ?>/account/" class="button"><?php _e( "Roger that", "taskrocket" ); ?></a>
			<div><span class="dismiss"><?php _e( "Maybe Later", "taskrocket" ); ?></span></div>
		</div>
	</div>
<?php } } } ?>

<?php // Intro for clients
if ( is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
if(current_user_can( 'client' )) {
if(!isset( $_COOKIE['tr_client_intro']  ) ) {
	$userintro = $_COOKIE['tr_user_intro'];
	if($userintro !="watched") { ?>
	<div class="intro user-intro">
		<div class="start">
			<?php if ( !empty( $logo ) ) { ?>
				<img src="<?php echo $dir_path . '/' . $just_file_name . '-150x150.' . $file_extension; ?>" />
			<?php } else { ?>
				<img src="<?php echo get_template_directory_uri(); ?>/images/system/favicon-large.png" alt="Branding" />
			<?php } ?>
			<h3><?php _e( "Howdy!", "taskrocket" ); ?></h3>
			<p><?php _e( "If you haven't already done so, update your profile with as much information as you can. This will provide a better experience for you and everyone else.", "taskrocket" ); ?></p>
			<a href="<?php echo home_url(); ?>/account/" class="button"><?php _e( "Roger that", "taskrocket" ); ?></a>
			<div><span class="dismiss"><?php _e( "Maybe Later", "taskrocket" ); ?></span></div>
		</div>
	</div>
<?php } } } } ?>