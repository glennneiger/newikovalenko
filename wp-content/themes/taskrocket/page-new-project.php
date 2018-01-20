<?php
/*
Template Name: New Project
*/
// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: ' . home_url() . '/client/');
	exit();
}
require_once(ABSPATH . "wp-admin/includes/taxonomy.php");

get_header();
global $current_user;
global $wp;
$current_url = home_url(add_query_arg(array(),$wp->request));

$options = get_option( 'taskrocket_settings' );

$allow_specify_project_rate   = $options['allow_specify_project_rate'];

$standard_rate   = $options['rate'];
if($standard_rate) {
	$the_rate = $standard_rate;
} else {
	$the_rate = "0";
}
$currency_symbol = $options['currency_symbol'];
?>


<div class="content">
	<div class="container">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <h1><?php the_title(); ?></h1>
    <?php endwhile; endif; ?>

	    <?php
			if( isset( $_POST['submit'] ) ) {
			if( !empty( $_REQUEST['newcat'] ) ) {
				
				// Sanitise
				$cat_name 				= sanitize_text_field($_POST['newcat']);
				$cat_desc 				= sanitize_text_field($_POST['description']);
				$cat_parent 			= sanitize_text_field($_POST['cat']);
				$tr_details 			= sanitize_text_field($_POST['tr_details']);
				$tr_project_manager 	= sanitize_text_field($_POST['tr_project_manager']);
				$tr_job_number 			= sanitize_text_field($_POST['tr_job_number']);
				$tr_hourly_rate 		= sanitize_text_field($_POST['tr_hourly_rate']);
				$cat_slug 				= sanitize_title_with_dashes($cat_name);

				$project = array(
					'cat_name' => $cat_name,
					'category_description' 		=> $cat_desc,
					'category_nicename' 		=> $cat_slug,
					'category_parent' 			=> $cat_parent,
					'project_manager' 			=> $tr_project_manager,
					'project_details' 			=> $tr_details,
					'hourly_rate' 				=> $tr_hourly_rate,
					'project_tr_job_number' 	=> $tr_job_number,
					'project_slug' 				=> $cat_slug
				);
				
				if( $cat_ID = wp_insert_category( $project ) ) { ?>

					<div class="message success">
	                	<p><?php _e( "Project added. Go to", "taskrocket" ); ?> <a href="<?php echo home_url() . "/" . get_option( 'category_base' ) . "/" . $cat_parent; ?><?php echo $cat_slug; ?>"><?php echo stripslashes($cat_name); ?></a> <?php _e( "to start adding tasks, or add another project below.", "taskrocket" ); ?></p>
						<span class="close"></span>
	                </div>

					<?php 
						if ( !empty( $_REQUEST['group'] ) ) {
							
							$group_posts = get_posts(array(
									'posts_per_page'=> -1,
									'post_type' 	=> 'task_groups',
									'orderby'		=> 'title',
									'post_parent' 	=> $_REQUEST['group']
									)
							);
							
							foreach ($group_posts as $group_post) {
								$new_post = array(
									'post_title'     => $group_post->post_title,
									'post_status'    => 'publish',
									'post_type'      => 'post',
									'post_category'  => array($cat_ID),
									'post_author'    => $group_post->post_author
								);  
								$new_post_id = wp_insert_post( $new_post );
								
								// All post custom fields
								$post_meta = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = $group_post->ID");
								if ( count($post_meta) > 0 ) {
									$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
									foreach ($post_meta as $post_meta_field) {
										$meta_key = $post_meta_field->meta_key;
										$meta_value = addslashes($post_meta_field->meta_value);
										$sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
									}
									$sql_query .= implode(" UNION ALL ", $sql_query_sel);
									$wpdb->query($sql_query);
								}
							}
						}
						
						// Start notification that is sent to PM and team members of the project.
						if($options['no_emails'] == false) {
							// Project team email addresses
							$GLOBALS[ 'project_ID' ] = $cat_ID;
							function allProjectUsers() {
								$project_member = '';
								$cat_authors = array();
								
								
								$slug = $cat_slug;
								$cat = get_category_by_slug($slug); 
								$slug_ID = $cat->term_id;
															
								$args = array(
									'posts_per_page'     => -1,
									'category'           => $GLOBALS['project_ID'],
									'post_status'        => 'publish'
								);
								$allposts = get_posts($args);
								if ($allposts) {
									foreach($allposts as $authorpost) {
										$cat_authors[$authorpost->post_author]+=1;
									}
									arsort($cat_authors);
									foreach($cat_authors as $key => $author_post_count) {
										$user = get_userdata($key);
										// All the project members
										$project_member .= $user->user_email . ",";
									}
								}
								return array($project_member);
							}
							
							$recipient_project_members = allProjectUsers();

							// PM details
							
							$pm = get_userdata($tr_project_manager);
							$pm_email = $pm->user_email . ",";
							
							
							$recipients =  rtrim($pm_email . $recipient_project_members[0],",");
							//echo $recipients;
							
							// Current user info
							get_currentuserinfo();
							$current_user_first_name = $current_user->user_firstname;
							$current_user_last_name = $current_user->user_lastname;
							$current_user_email = $current_user->user_email;
							
							// The email
							$project_name = get_cat_name($GLOBALS['project_ID']);
							$project_url = get_category_link($GLOBALS['project_ID']);
							$icon = '<img src="' . get_template_directory_uri() . '/images/notifications/project.png" title="New Project" />';
							
							$header = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:25px;'";
							$type = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:20px; font-weight:bold;'";
							
							$view_project_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $project_url . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $project_url . '"
							style="background-color:#3b4a5b;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:120px;-webkit-text-size-adjust:none;">' . __( "View Project", "taskrocket" ) . '</a><!--[if mso]></center></v:roundrect><![endif]--></div>';
							$options = get_option( 'taskrocket_settings' );
							if($options['send_plain'] == false) {
							$body = '
							<table width="100%" border="0" cellspacing="25" cellpadding="30" style="background-color:#f3f3e7; text-align:center; width:100%; height:100%;" bgcolor="#f3f3e7">
							  <tr>
								<td valign="middle">
									<table width="450" border="0" cellspacing="0" cellpadding="30" align="center" style="text-align:center; padding:35px; background:#ffffff;" bgcolor="#ffffff">
										<tr>
											<td>' . $icon . '
												<h1' . $header . '>' . __( "A new project was created by", "taskrocket" ) . ' ' . $current_user_first_name . ' ' . $current_user_last_name . '<br />(<a href="mailto:' . $current_user_email . '" style="color:#617b96; text-decoration:none;">' . $current_user_email . '</a>)<br /><br /><span style="color:#eb8769; font-size:25px;">' . $project_name . '</span></h1>
												<p' . $type . '>You received this notification because you are involved in this project.</p>
												<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
												  <tbody>
													<tr>
													  <td align="center" valign="middle">' . $view_project_button . '</td>
													</tr>
												  </tbody>
												</table>
											</td>
										</tr>
									</table>
								</td>
							  </tr>
							</table>
							';
							} else {
								$body = __( "A new project was created by", "taskrocket" ) . ' ' . $current_user_first_name . ' ' . $current_user_last_name . ' (' . $current_user_email . ')<br /><br />' . $project_name . '<br /><br />
								You received this notification because you are involved in this project.<br />
								URL: ' . $project_url;
							}
			
							$subject = stripslashes(__( "Project added: ", "taskrocket" )) . stripslashes($cat_name);
							$headers[]= "Content-type:text/html;charset=UTF-8";
							$headers[]= __( "From", "taskrocket" ) . get_bloginfo('name') . ' <' . $current_user->user_email . '>';
							$headers[]= 'Reply-To: ' . $current_user->user_email;
							$headers[]= "MIME-Version: 1.0";
							wp_mail($recipients, $subject, $body, $headers);
						}
						// End notification that is sent to PM and team members of the project.

						
					} else { ?>

					<div class="message error">
						<p><?php printf( __( 'Sorry, but a project called %1$s already exists.', "taskrocket" ), $cat_name); ?></p>
					</div>

				<?php } ?> <?php
			}
		}
		?>


	   <?php
	   if ($options['users_create_projects'] == true || current_user_can( 'manage_options' )) { ?>

	    <div class="add-new-project">
			<script>
			// Count remaining chars
				jQuery(function($) {
					var max = <?php echo $titlecharcount; ?>;
					$('#newcat').keyup(function() {
						if($(this).val().length > max) {
							$(this).val($(this).val().substr(0, max));
						}
						$('#title-chars').html((max - $(this).val().length) + ' characters left');
					});

					var descmax = <?php echo $desccharcount; ?>;
					$('#description').keyup(function() {
						if($(this).val().length > descmax) {
							$(this).val($(this).val().substr(0, descmax));
						}
						$('#desc-chars').html((descmax - $(this).val().length) + ' characters left');
					});
				});
			</script>

	        <form action="<?php echo $current_url; ?>" name="projectForm" method="post">
				
				<!--/ Start Left /-->
				<div class="fleft">
					
					<div class="section">
						<span class="nbm">
							<label for="newcat"><?php _e( "Project name", "taskrocket" ); ?></label>
	                    	<input type="text" id="newcat" name="newcat" class="text" value="" maxlength="<?php echo $titlecharcount; ?>" required />
							<em id="title-chars" class="chars"></em>
						</span>
					</div>
					
					<div class="section">
						<span class="nbm">
	                    	<label for="description"><?php _e( "Description", "taskrocket" ); ?></label>
	                    	<input name="description" id="description" type="text" class="text" value="" maxlength="<?php echo $desccharcount; ?>" />
							<em id="desc-chars" class="chars"></em>
						</span>
					</div>
					
					<div class="section quarters">
						
						<?php if ($options['users_specify_time'] == true || current_user_can( 'manage_options' )) { ?>
						<div class="quarter-container">
							<span class="nbm">
								<label><?php _e( "Start date", "taskrocket" ); ?></label>
		                    	<input type="text" class="text date" id="tr_start_date" name="tr_start_date" />
								<em class="clear-field clear-start-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
							</span>
						</div>
			
						<div class="quarter-container">
							<span class="nbm">
			                    <label><?php _e( "End date", "taskrocket" ); ?></label>
				                <input type="text" class="text date" id="tr_end_date" name="tr_end_date" />
								<em class="clear-field clear-end-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
							</span>
						</div>

						<div class="quarter-container">
							<span class="nbm">
			                    <label><?php _e( "Time", "taskrocket" ); ?></label>
				                    <input type="number" min="1" step="1" class="text date" id="tr_hrs_allocated" name="tr_hrs_allocated" placeholder="Hrs" />
									<em class="clear-field clear-hrs-allocated-field"><?php _e( "Clear", "taskrocket" ); ?></em>
							</span>
						</div>
						<?php } ?>
						
						<div class="quarter-container job-number-container <?php if(!$allow_specify_project_rate && !current_user_can( 'manage_options' )) { echo "wider-job-number"; } ?>">
							<span class="nbm">
			                    <label><?php _e( "Job number", "taskrocket" ); ?></label>
								<?php if($options['auto_job_numbers'] == true) { ?>
									<span class="job-num-notice"><?php _e( "Auto Generated", "taskrocket" ); ?> <i class="tip master-tooltip" title="<?php _e( "A job number will be automatically assigned when you create this project.", "taskrocket" ); ?>"></i></span>
									<input type="hidden" id="tr_job_number" name="tr_job_number" value="<?php if ($options['job_number_prefix'] == true) { echo $options['job_number_prefix']; } ?><?php echo strtoupper(date("YMd")); ?>-P" />
								<?php } else { ?>
									<input type="text" class="text job-number" id="tr_job_number" name="tr_job_number" value="<?php echo $options['job_number_prefix']; ?>" />
								<?php } ?>
								<em class="clear-field clear-job-number"><?php _e( "Clear", "taskrocket" ); ?></em>
							</span>
						</div>
						
						<?php if($allow_specify_project_rate || current_user_can( 'manage_options' )) { ?>
						<div class="quarter-container">
							<span class="nbm">
			                    <label><?php echo $currency_symbol; ?><?php _e( "Rate", "taskrocket" ); ?></label>
				                    <input type="number" min="1" step="1" class="text rate" id="tr_hourly_rate" name="tr_hourly_rate" value="<?php echo $the_rate; ?>" placeholder="" />
									<em class="clear-field clear-rate-field"><?php _e( "Clear", "taskrocket" ); ?></em>
							</span>
						</div>
						<?php } ?>

					</div>
					
					
					<?php if ( is_plugin_active( 'taskrocket-task-groups/taskrocket-task-groups.php' ) ) { 
						$desc_height = "desc-hight";
					?>
						<div class="section">
							<?php require(ABSPATH . 'wp-content/plugins/taskrocket-task-groups/groups.php'); ?>
						</div>
					<?php
						}
					?>
					
					<?php
				    if ($options['allow_choose_pm'] == true || current_user_can( 'manage_options' )) { ?>
						<div class="section select-pm">
							<span class="nbm">
		                    <label><?php _e( "Select a Project Manager", "taskrocket" ); ?></label>
							<select name="tr_project_manager" id="tr_project_manager">
								<option></option>
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
							</span>
						</div>
					<?php
						}
					?>

				</div>
				<!--/ End Left /-->
	            
				
				<!--/ Start Fright /-->
				<div class="fright">
					<?php if ($options['allow_choose_pm'] == true) { $choose_pm_height = " choose-pm-height"; } ?>
					<div class="section details textarea-right">
						<span class="nbm">
							<label><?php _e( "Project details", "taskrocket" ); ?></label>
							<textarea id="tr_details" name="tr_details" class="<?php echo $desc_height . $choose_pm_height; ?>"></textarea>
						</span>
						
						<div class="submit-button-container">
							<input type="submit" name="submit" value="<?php _e( "Create Project", "taskrocket" ); ?>" class="button submit" />
							<img src="<?php echo get_template_directory_uri(); ?>/images/loader.gif" />
						</div>
						
					</div>
					

				</div>
				<!--/ End Fright /-->
			
				
	        </form>
		</div>

	    <?php } else { ?>

	        <p><?php _e( "Sorry, but the ability to create projects has not been enabled by an administrator. You'll need to ping one of these admin(s)", "taskrocket" ); ?>:</p>

	        <?php require_once($GLOBALS[ 'theme_includes' ] . "admins-list.php"); ?>

	    <?php } ?>
		
		<?php if ($options['enable_warning'] == true) { ?>
		<script>
			document.querySelector('.submit').addEventListener("click", function(){
				window.btn_clicked = true;
			});
			window.onbeforeunload = function(){
				if(!window.btn_clicked){
					return "OOPS!";
				}
			};
		</script>
		<?php } ?>

	</div>
</div>

<?php get_footer(); ?>
