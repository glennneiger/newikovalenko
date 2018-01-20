<?php
/*
Template Name: Account
*/
$wpdb->hide_errors(); nocache_headers();

global $userdata; wp_get_current_user();
$options = get_option( 'taskrocket_settings' );


if(!empty($_POST['action'])){

	require_once(ABSPATH . 'wp-admin/includes/user.php');
	require_once(ABSPATH . WPINC . '/registration.php');

	check_admin_referer('update-profile_' . $user_ID);

	$errors = edit_user($user_ID);
	
	//$new_post = wp_insert_post($post_array);
	if (!function_exists('wp_generate_attachment_metadata')){
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    }
	
    if($_FILES['user_photo']['name']!=='') {
        foreach ($_FILES as $file => $array) {
            if ($_FILES[$file]['error'] !== UPLOAD_ERR_OK) {
                return "upload error : " . $_FILES[$file]['error'];
            }
            $attach_id = media_handle_upload( $file );
        }   
    }
    if ($attach_id > 0){
		$meta_key = "user_photo";
		$meta_value = $attach_id;
		$prev_value = "";
		update_user_meta($user_ID, $meta_key, $meta_value, $prev_value);
    }


	if ( is_wp_error( $errors ) ) {
		foreach( $errors->get_error_messages() as $message )
			$errmsg = "$message";
	}

	if($errmsg == '') {
		do_action('personal_options_update',$user_ID);
		$d_url = $_POST['dashboard_url'];
		wp_redirect(home_url() . '?page_id='.$post->ID.'&updated=true' );
	} else {
		$errmsg = '' . $errmsg . '';
	}
}


get_header();
wp_get_current_user();

?>

        <div class="content account">
			<div class="container">
				
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				<h1>
					<?php echo $userdata->first_name ?> <?php echo $userdata->last_name ?> 
					<span>
						/ <?php $user_roles = $current_user->roles; $user_role = array_shift($user_roles); echo $user_role; ?>
					</span>
				</h1>
				<?php endwhile; endif; ?>
				
				<?php if ( isset($_GET['updated']) ): $d_url = $_GET['d'];?>
				<div class="message success">
					<p><?php _e( "Your details were successfully updated.", "taskrocket" ); ?></p>
					<span class="close"></span>
				</div>
				<?php elseif($errmsg!=""): ?>
				<div class="message error">
					<p><?php echo $errmsg;?></p>
					<span class="close"></span>
				</div>
				<?php endif;?>
			
            <form name="accountForm" action="" method="post" id="form" enctype="multipart/form-data">

	            <?php wp_nonce_field('update-profile_' . $user_ID) ?>
	            <input type="hidden" name="from" value="profile" />
	            <input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
	            <input type="hidden" name="dashboard_url" value="<?php echo get_option("dashboard_url"); ?>" />
	            <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />

				<input type="hidden" name="web" id="web" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="github" id="github" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="twitter" id="twitter" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="skype" id="skype" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="googleplus" id="googleplus" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="facebook" id="facebook" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="other" id="other" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="phone" id="phone" value="<?php echo $user_id; ?>" />
				<input type="hidden" name="description" id="description" value="<?php echo $user_id; ?>" />

				<div class="avatar-info panel">
					
					<?php if (get_option('show_avatars')) { ?>
						
						<?php echo get_avatar( $current_user->ID, 200 ); ?>
						<p><strong><?php _e( "Gravatars are in use.", "taskrocket" ); ?></strong>
							<?php 
							$discussion_settings_url = get_admin_url() . "options-discussion.php";
							$gravatar_url = "https://en.gravatar.com/";
							if(current_user_can( 'manage_options' )) {
								printf( __( 'If you would rather upload a photo, disable "Show Avatars" in the WordPress <a href="%1$s" target="_blank">discussion settings</a>.<br />Otherwise, change your photo by logging in (or creating an account) with your email address at <a href="%2$s" target="_blank">gravatar.com</a>.', "taskrocket" ), $discussion_settings_url, $gravatar_url); 
							} else {
									printf( __( 'If you would rather upload a photo, ask an administrator to disable "Show Avatars" in the WordPress discussion settings.<br />Otherwise, change your photo by logging in (or creating an account) with your email address at <a href="%2$s" target="_blank">gravatar.com</a>.', "taskrocket" ), $discussion_settings_url, $gravatar_url); 
							}?>
						</p>
							
					<?php } else { ?>
						
						<?php $attachment_id = $userdata->user_photo;
						$image_attributes = wp_get_attachment_image_src( $attachment_id );
						if( $image_attributes ) { ?> 
						<div class="image-overflow">
							<img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" id="output" />
						</div>
						<?php } else { ?>
							<div class="image-overflow">
								<img src="<?php echo get_template_directory_uri(); ?>/images/default-user.png" class="default-avatar" id="output" />
							</div>
						<?php } ?>
						<p>
							<strong><?php _e( "Upload a photo", "taskrocket" ); ?></strong>
							<input type="file" name="user_photo" id="user_photo" accept="image/*" onchange="loadFile(event)" />
						</p>
			
					<?php } ?>
					
					<script>
					var loadFile = function(event) {
						var output = document.getElementById('output');
						output.src = URL.createObjectURL(event.target.files[0]);
					};
					</script>
							
				</div>

				<div class="section">
                    <fieldset>

						<span class="nbm">
							<label><?php _e( "First name", "taskrocket" ); ?></label>
                        	<input type="text" name="first_name" class="text" id="first_name" value="<?php echo $userdata->first_name ?>" required />
						</span>

                        <span class="nbm">
							<label><?php _e( "Last name", "taskrocket" ); ?></label>
                        	<input type="text" name="last_name" class="text" id="last_name" value="<?php echo $userdata->last_name ?>" />
						</span>
						
						<span class="nbm">
							<label><?php _e( "Nickname", "taskrocket" ); ?></label>
                        	<input type="text" name="nickname" class="text" id="nickname" value="<?php echo $userdata->nickname ?>" required />
						</span>

                        <span class="nbm">
							<label><?php _e( "Email", "taskrocket" ); ?></label>
                        	<input type="text" name="email" class="text full-width" id="email" value="<?php echo $userdata->user_email ?>" required />
						</span>

                 	</fieldset>
				</div>
				
				<div class="section half-panel">
                    <fieldset>
						<span class="nbm">
                        	<label><?php _e( "New password", "taskrocket" ); ?></label>
                        	<input type="password" name="pass1" class="text" id="pass1" />
						</span>
						
						<span class="nbm">
                        	<label><?php _e( "Confirm new password", "taskrocket" ); ?></label>
                        	<input type="password" name="pass2" class="text" id="pass2" />
						</span>
                 	</fieldset>
				</div>
				
				<div class="section half-panel">
                    <fieldset>
						<span class="nbm">
							<label><?php _e( "Phone", "taskrocket" ); ?></label>
                        	<input type="text" name="phone" class="text" id="phone" value="<?php echo $userdata->phone ?>" />
						</span>

						<span class="nbm">
							<label><?php _e( "Skype", "taskrocket" ); ?></label>
                        	<input type="text" name="skype" class="text full-width" id="skype" value="<?php echo $userdata->skype ?>" />
						</span>
                 	</fieldset>
				</div>

				<?php if( !current_user_can('client')) { ?>
				<div class="section bio">
					<label><?php _e( "Enter a brief bio", "taskrocket" ); ?></label>
					<input type="text" name="description" class="text" id="description" value="<?php echo $userdata->description; ?>" />
				</div>
				
				<div class="section preferences-dash">
					<label><?php _e( "Dashboard preferences", "taskrocket" ); ?></label>
					
					<?php $number_recent_tasks = $userdata->number_recent_tasks; ?>
					<span class="number-recent-tasks">
						<select name="number_recent_tasks">
							<option value="0"<?php if($number_recent_tasks ==0) { echo " selected"; } ?>>0</option>
							<option value="3"<?php if($number_recent_tasks ==3) { echo " selected"; } ?>>3</option>
							<option value="5"<?php if($number_recent_tasks ==5) { echo " selected"; } ?>>5</option>
							<option value="10"<?php if($number_recent_tasks ==10) { echo " selected"; } ?>>10</option>
							<option value="15"<?php if($number_recent_tasks ==15) { echo " selected"; } ?>>15</option>
							<option value="20"<?php if($number_recent_tasks ==20) { echo " selected"; } ?>>20</option>
							<option value="25"<?php if($number_recent_tasks ==25) { echo " selected"; } ?>>25</option>
						</select><?php _e( "Recent tasks", "taskrocket" ); ?>
					</span>
					
					<?php $number_recent_comments = $userdata->number_recent_comments; ?>
					<span class="number-recent-comments">
						<select name="number_recent_comments">
							<option value="0"<?php if($number_recent_comments ==0) { echo " selected"; } ?>>0</option>
							<option value="3"<?php if($number_recent_comments ==3) { echo " selected"; } ?>>3</option>
							<option value="5"<?php if($number_recent_comments ==5) { echo " selected"; } ?>>5</option>
							<option value="10"<?php if($number_recent_comments ==10) { echo " selected"; } ?>>10</option>
							<option value="15"<?php if($number_recent_comments ==15) { echo " selected"; } ?>>15</option>
							<option value="20"<?php if($number_recent_comments ==20) { echo " selected"; } ?>>20</option>
							<option value="25"<?php if($number_recent_comments ==25) { echo " selected"; } ?>>25</option>
						</select><?php _e( "Recent comments", "taskrocket" ); ?>
					</span>
					
					<?php $number_recent_pages = $userdata->number_recent_pages; ?>
					<span class="number-recent-pages">
						<select name="number_recent_pages">
							<option value="0"<?php if($number_recent_pages ==0) { echo " selected"; } ?>>0</option>
							<option value="3"<?php if($number_recent_pages ==3) { echo " selected"; } ?>>3</option>
							<option value="5"<?php if($number_recent_pages ==5) { echo " selected"; } ?>>5</option>
							<option value="10"<?php if($number_recent_pages ==10) { echo " selected"; } ?>>10</option>
							<option value="15"<?php if($number_recent_pages ==15) { echo " selected"; } ?>>15</option>
							<option value="20"<?php if($number_recent_pages ==20) { echo " selected"; } ?>>20</option>
							<option value="25"<?php if($number_recent_pages ==25) { echo " selected"; } ?>>25</option>
						</select><?php _e( "Recent pages", "taskrocket" ); ?>
					</span>
					
					<?php $my_projects_dash = $userdata->my_projects_dash; ?>
					<span class="dash-projects">
						<input type="hidden" name="my_projects_dash" value="no" />
						<input type="checkbox" name="my_projects_dash" value="yes"<?php if ($my_projects_dash == "yes") { echo " checked"; } ?> /> Show my projects
					</span>
					
					<?php $show_tips = $userdata->show_tips; ?>
					<span class="tips">
						<input type="hidden" name="show_tips" value="no" />
						<input type="checkbox" name="show_tips" value="yes"<?php if ($show_tips == "yes") { echo " checked"; } ?> /> Show Tips
					</span>
					
				</div>
				
				<div class="section tabs-project-page">
					<label><?php _e( "Tabs Order on project pages", "taskrocket" ); ?></label>
					<?php 
						$tab_1 = $userdata->tab_1;
						$tab_2 = $userdata->tab_2;
						$tab_3 = $userdata->tab_3;
						$tab_4 = $userdata->tab_4;
					?>
					
					<span class="tabs-order tabs-order-1">
						<select name="tab_1">
							<option value=""></option>
							<option value="mat"<?php if($tab_1 =="mat") { echo " selected"; } ?>>My Active Tasks</option>
							<option value="mct"<?php if($tab_1 =="mct") { echo " selected"; } ?>>My Completed Tasks</option>
							<?php if($options['users_only_see_own_tasks'] == false || current_user_can( 'manage_options' )) { ?>
							<option value="aat"<?php if($tab_1 =="aat") { echo " selected"; } ?>>All Active Tasks</option>
							<option value="act"<?php if($tab_1 =="act") { echo " selected"; } ?>>All Completed Tasks</option>
							<?php } ?>
						</select> <?php _e( "First tab", "taskrocket" ); ?>
					</span>
					
					<span class="tabs-order tabs-order-2">
						<select name="tab_2">
							<option value=""></option>
							<option value="mat"<?php if($tab_2 =="mat") { echo " selected"; } ?>>My Active Tasks</option>
							<option value="mct"<?php if($tab_2 =="mct") { echo " selected"; } ?>>My Completed Tasks</option>
							<?php if($options['users_only_see_own_tasks'] == false || current_user_can( 'manage_options' )) { ?>
							<option value="aat"<?php if($tab_2 =="aat") { echo " selected"; } ?>>All Active Tasks</option>
							<option value="act"<?php if($tab_2 =="act") { echo " selected"; } ?>>All Completed Tasks</option>
							<?php } ?>
						</select> <?php _e( "Second tab", "taskrocket" ); ?>
					</span>
					
					<?php if($options['users_only_see_own_tasks'] == false || current_user_can( 'manage_options' )) { ?>
					<span class="tabs-order tabs-order-3">
						<select name="tab_3">
							<option value=""></option>
							<option value="mat"<?php if($tab_3 =="mat") { echo " selected"; } ?>>My Active Tasks</option>
							<option value="mct"<?php if($tab_3 =="mct") { echo " selected"; } ?>>My Completed Tasks</option>
							<option value="aat"<?php if($tab_3 =="aat") { echo " selected"; } ?>>All Active Tasks</option>
							<option value="act"<?php if($tab_3 =="act") { echo " selected"; } ?>>All Completed Tasks</option>
						</select> <?php _e( "Third tab", "taskrocket" ); ?>
					</span>
					
					<span class="tabs-order tabs-order-4">
						<select name="tab_4">
							<option value=""></option>
							<option value="mat"<?php if($tab_4 =="mat") { echo " selected"; } ?>>My Active Tasks</option>
							<option value="mct"<?php if($tab_4 =="mct") { echo " selected"; } ?>>My Completed Tasks</option>
							<option value="aat"<?php if($tab_4 =="aat") { echo " selected"; } ?>>All Active Tasks</option>
							<option value="act"<?php if($tab_4 =="act") { echo " selected"; } ?>>All Completed Tasks</option>
						</select> <?php _e( "Fourth tab", "taskrocket" ); ?>
					</span>
					<?php } ?>
										
				</div>
				
				<div class="section you-online">
					<span>
						<label><?php _e( "Website", "taskrocket" ); ?></label>
						<input type="text" name="web" class="text" id="web" value="<?php echo $userdata->web ?>" />
					</span>

					<span>
						<label><?php _e( "Twitter", "taskrocket" ); ?></label>
						<input type="text" name="twitter" class="text" id="twitter" value="<?php echo $userdata->twitter ?>" />
					</span>

					<span>
						<label><?php _e( "Google+", "taskrocket" ); ?></label>
						<input type="text" name="googleplus" class="text full-width" id="googleplus" value="<?php echo $userdata->googleplus ?>" />
					</span>
					
					<span class="nbm">
						<label><?php _e( "Facebook", "taskrocket" ); ?></label>
						<input type="text" name="facebook" class="text" id="facebook" value="<?php echo $userdata->facebook ?>" />
					</span>
					
					<span class="nbm">
						<label><?php _e( "Other", "taskrocket" ); ?></label>
						<input type="text" name="other" class="text full-width" id="other" value="<?php echo $userdata->other ?>" />
					</span>
				</div>

				<?php } ?>
			
			<span class="submit-button-container">
				<br /><br />
				<input type="submit" value="<?php _e( "Update", "taskrocket" ); ?>" class="button submit" />
				<img src="<?php echo get_template_directory_uri(); ?>/images/loader.gif" />
			</span>
			<input type="hidden" name="action" value="update" />

            </form>

        	</div>
		</div>

<?php get_footer(); ?>
