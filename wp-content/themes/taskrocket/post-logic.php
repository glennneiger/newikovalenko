<?php
	ob_start();
	define('WP_USE_THEMES', false);

	if ( !function_exists( 'get_home_path' ) ) {
	require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
	}

	// Welcome to variable city!
	// "I like variables so much, I bought the company!"

	$category 				= sanitize_text_field($_POST["categoryID"]);
	$categorySlug 			= sanitize_text_field($_POST["categorySlug"]);
	$role 					= sanitize_text_field( $_POST["role"]);
	$priority 				= sanitize_text_field( $_POST["priority"]);
	$project_contributor 	= sanitize_text_field( $_POST["project_contributor"]);
	$private_task 			= sanitize_text_field( $_POST["private"]);
	$job_number_task 		= sanitize_text_field( $_POST["job_number_task"]);
	$time_spent 			= sanitize_text_field( $logtotal);
	$postTitle 				= sanitize_text_field($_POST['title']);
	$postContent 			= $_POST["minfo"];
	$startdate 				= sanitize_text_field( $_POST["startdate"]);
	$duedate 				= sanitize_text_field( $_POST["duedate"]);
	$form_type 				= sanitize_text_field( $_POST["task-form-type"]);
	$plain_emails 			= sanitize_text_field( $_POST["send_plain"]);
	$no_emails 				= sanitize_text_field( $_POST["no_emails"]);
	$reassign 				= sanitize_text_field( $_POST["reassign"]);
	$update_task 			= sanitize_text_field( $_POST["update_task"]);
	$permalink 				= sanitize_text_field( $_POST["task-link"]);
	$tr_status 				= sanitize_text_field( $_POST["tr_status"]);
	$tr_relation 			= sanitize_text_field( $_POST["relation"]);
	$tr_related 			= sanitize_text_field( $_POST["related"]);
	$tr_elaboration 			= sanitize_text_field( $_POST["elaboration"]);

	$taskSenderFirstName 	= $current_user->user_firstname;
	$taskSenderLastName 	= $current_user->user_lastname;
	$taskSenderID 			= $current_user->ID;
	
	
	// Recipients

	// Project Manager email
	$project_manager = get_option( 'tr_project_manager_' . $category);
	$project_manager_user_info = get_userdata($project_manager);
	if($project_manager) {
		$project_manager_email = $project_manager_user_info->user_email . ","; // <- PM email
	} else {
		$project_manager_email = "";
	}
	
	// Admin email
	$admin_email = sanitize_text_field( get_option( 'admin_email' )) . ","; // <- Admin email

	// Project team email(s)
	if($options['task_added_notify_project_team'] == true) {
		function allProjectUsers() {
			$project_member = '';
			$cat_authors = array();
			$args = array(
				'posts_per_page'     => -1,
				'category'           => $_POST["categoryID"],
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
			//return $project_member;
		}
	}
	
	// Other email addresses to notify
	$task_added_notify_others = $options['task_added_notify_others'];
	
	if($options['task_added_notify_pm'] == true) {
		$recipient_pm = $project_manager_email;
	}
	if($options['task_added_notify_admin'] == true) {
		$recipient_admin = $admin_email;
	}
	if($options['task_added_notify_others']) {
	    $recipient_task_added = $options['task_added_notify_others'] . ",";
	}
	if($options['task_added_notify_project_team'] == true) {
		$recipient_project_members = allProjectUsers();
	}
	if($options['task_added_notify_task_owner'] == true) {
		$task_owner = get_userdata($project_contributor);
		$task_owner_email = $task_owner->user_email . ",";
	}
	
	$recipients =  rtrim($recipient_pm . $recipient_admin . $task_owner_email . $recipient_task_added . $recipient_project_members[0],",");
	//echo $recipients; exit;


	// The message included in the email will be different depending on why the form was submitted.

	// If the form being submitted is on the 'new task' page.
	if ($form_type == "new-task") {
		$subject = stripslashes(__( "New task created by ", "taskrocket" ) . $taskSenderFirstName . '  ' . $taskSenderLastName . ':  ' . $postTitle);
		$user_info = get_userdata($project_contributor);
	}

	// If the form being submitted is the 'task solo' page.
	if ($form_type == "new-task-solo") {
		$subject = stripslashes(__( "New task created by ", "taskrocket" ) . $taskSenderFirstName . '  ' . $taskSenderLastName . ':  ' . $postTitle);

		$project_manager_email = get_option( 'tr_project_manager_' . $category);
		$user_info = get_userdata($project_manager_email);
	}

	// If the form being submitted is on the 'edit task' (single.php) page.
	if ($form_type == "task-edit-single") {
		$subject = stripslashes(__( "Task updated by ", "taskrocket" ) . $taskSenderFirstName . '  ' . $taskSenderLastName . ':  ' . $postTitle);

		$user_info = get_userdata($project_contributor);
		// If the task has been reassigned, use the email address of the person being rassigned to...
		if($reassign !=="") {
			$reassigned_recipient = $user_info->user_email;
		}
	}

	// If the form being submitted is by a client.
	if ($role == "client") {
		$subject = stripslashes(__( "New task created by ", "taskrocket" ) . $taskSenderFirstName . '  ' . $taskSenderLastName . ' (Client):  ' . $postTitle);
	}

	// Debugging
	//echo "<br />Category &#10140; " . $category . "<br />Category Slug &#10140; " . $categorySlug . "<br />Role &#10140; " . $role . "<br />Recipient &#10140; " .  $recipient . "<br />Priority &#10140; " .  $priority . "<br />PM email &#10140; " . $project_manager_email . "<br />Project contributor ID &#10140; " . $project_contributor . "<br />Project contributor First name &#10140; " . $taskSenderFirstName . "<br />Project contributor last name &#10140; " . $taskSenderLastName . "<br />Form type &#10140; " . $form_type; exit;


	// Task Priority
	if ($priority == 'low') { $prioritycolor = "43bce9"; }
	if ($priority == 'normal') { $prioritycolor = "48cfae"; }
	if ($priority == 'high') { $prioritycolor = "f9b851"; }
	if ($priority == 'urgent') { $prioritycolor = "fb6e52"; }


	$template_dir = get_template_directory_uri();
	$my_post = array();
	$my_post['post_author'] = $_POST["project_contributor"];
	$my_post['post_status'] = 'publish';
	$my_post['post_title'] = strip_tags($postTitle);
	$my_post['post_content'] = strip_tags($postContent);
	$my_post['post_category'] = array($category);
	$my_post['filter'] = true;

	// Don't insert a new post if editing a task.
	// This gets around an issue where updating the task
	// would insert a new post.
	if ($form_type !== "task-edit-single") {
		$post_id = wp_insert_post( $my_post);
	}

	//$user_info = get_userdata($recipient);
	$projectURL = get_category_link($category);
	$projectname = get_cat_name($category);
	$thepriority = get_post_meta($post->ID, 'priority', TRUE);


	// User photo condition
	if (get_option('show_avatars')) {
		$tasksendericon  = '<img src="http://www.gravatar.com/avatar/' . md5($current_user->user_email) . '?s=125" style="border-radius:100%;" />';
		//$taskreceivericon = 'http://www.gravatar.com/avatar/' . md5($recipient) . '?s=200';
	} else {
		$attachment_id = $userdata->user_photo;
		$image_attributes = wp_get_attachment_image_src( $attachment_id );
		if( $image_attributes ) {
			
			$tasksendericon = '<img src="' . $image_attributes[0] . '" width="' . $image_attributes[2] . '" height="' . $image_attributes[2] . '"  style="border-radius:100%;" />';

		} else { 
			
			$tasksendericon = '<img src="' . get_template_directory_uri() . '/images/default-user.png" width="125" height="125"  style="border-radius:100%;" />';

		}
	}
	
	// debgug 
	//echo $taskSenderID;
	//exit;

	// Get the latest post by the current author
	if ($form_type == "new-task-solo") {
		$author_selection = $project_contributor;
	} else {
		$author_selection = $current_user->ID;
	}
	$latest_post = get_posts( array(
	        'author'      => $author_selection,
	        'orderby'     => 'date',
	        'numberposts' => 1
	));
	$latest_post = $latest_post[0];
	
	// Determine what link to use for the TASK button on the email notification
	if($form_type == "task-edit-single") {
		$task_link = $permalink;
	} else {
		$task_link = $latest_post->guid;
	}


	update_post_meta( $post_id, 'startdate', $startdate);
	update_post_meta( $post_id, 'duedate', $duedate);
	update_post_meta( $post_id, 'minfo', implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $postContent ) ) ));
	update_post_meta( $post_id, 'priority', $priority);
	update_post_meta( $post_id, 'private', $private_task);
	update_post_meta( $post_id, 'tr_status', $tr_status);
	update_post_meta( $post_id, 'relation', $tr_relation);
	update_post_meta( $post_id, 'related', $tr_related);
	update_post_meta( $post_id, 'elaboration', $tr_elaboration);
	update_post_meta( $post_id, 'role', $role);

	$type = " style='font-family:Arial, Helvetica, sans-serif; color:#3b4a5b; font-size:15px; line-height:20px;'";
	$pre = " style='font-family:Arial, Helvetica, sans-serif; color:#3b4a5b; font-size:15px; line-height:20px;  display:block; width:355px; white-space: pre-line; word-wrap: break-word;'";
	$table_style = " style='background-color:#ffffff; color:#3b4a5b;margin:0 auto;text-align:left'";
	$inner_table_style = " style='font-family:Arial, Helvetica, sans-serif; font-size: 15px; color:#3b4a5b'";
	$row_style = " style='border-bottom: solid 1px #F4F4F6; padding-top:5px; padding-bottom:5px'";
	$neaten = " style='display:inline-block; width:100px;'";
	$link_colour = " style='color:#333f4f;'";
	$avatar_style = " style='border-radius:100%; width:125px; height:125px;'";
	$task_title_style = " style='font-family:Arial, Helvetica, sans-serif; display:block; background-color:#f3f3e7; border:solid 1px #e0e0d5;color:#3b4a5b; font-size:15px; line-height:22px;'";
	$strong = " style='color:#3b4a5b; text-align:right;'";

	$project_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $projectURL . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $projectURL . '"
	style="background-color:#3b4a5b;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-decoration:none;width:120px;-webkit-text-size-adjust:none;;text-align:center;">' . __( "View Project", "taskrocket" ) . '</a><!--[if mso]></center></v:roundrect><![endif]--></div>';

	$task_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $task_link . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $task_link . '"
	style="background-color:#3b4a5b;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-decoration:none;width:120px;-webkit-text-size-adjust:none;text-align:center;">' . __( "View Task", "taskrocket" ) . '</a><!--[if mso]></center></v:roundrect><![endif]--></div>';

	if($role == "client") {
		$client_advice = "<strong>" . __( "Note", "taskrocket" ) . ": </strong>" . __( "This task was submitted by a client ", "taskrocket" ) . ".";
		$plain_client_advice = __( "Note: This task was submitted by a client", "taskrocket" ) . ".";
	}
	
	// If there is a start date for the task, do this.
	$date_format = get_option('date_format');
	if($startdate !=="") {
		$olddateformat = $startdate;
		$newdateformat = new DateTime($olddateformat);
		$startdateinsert = $newdateformat->format($date_format);
		$plain_startdateinsert = $newdateformat->format($date_format);
	} else {
		$startdateinsert = __( "Any time", "taskrocket" );
		$plain_startdateinsert = __( "Any time", "taskrocket" );
	}
	
	// If there is a due date for the task, do this.
	if($duedate !=="") {
		$olddateformat = $duedate;
		$newdateformat = new DateTime($olddateformat);
		$duedateinsert = $newdateformat->format($date_format);
		$plain_duedateinsert = $newdateformat->format($date_format);
	} else {
		$duedateinsert = __( "Any time", "taskrocket" );
		$plain_duedateinsert = __( "Any time", "taskrocket" );
	}
	
	
	// Cost.
	$rate = $options['rate'];
	$task_cost = $rate * $time_spent;
	$currency_symbol = $options['currency_symbol'];
	if ($currency_symbol == "") {
		$currency_symbol = "$";
	}
	$final_cost = $currency_symbol . round($task_cost / 60 , 2);
	
	
	// Privacy
	if($private_task == "yes") {
		$privacy = "Private";
	} else {
		$privacy = "Public";
	}
	
	// Status 
	if($tr_status == "incomplete") {
		$task_status =  __( "Incomplete", "taskrocket" );
	}
	if($tr_status == "complete") {
		$task_status =  __( "Complete", "taskrocket" );
	} 
	if($tr_status == "onhold") {
		$task_status =  __( "On hold", "taskrocket" );
	}
	if($tr_status == "inprogress") {
		$task_status = __( "In progress", "taskrocket" );
	} 
	
	
	
	// If there is a job number for the task, do this.
	if($job_number_task !=="") {
		$thejobnumber = $job_number_task;
	} else {
		$thejobnumber = "";
	}
	
	// If there time spent for the task, do this.
	if($time_spent !=="") {
		$minutes = $time_spent;
		$hours = floor($minutes / 60);
		$min = $minutes - ($hours * 60);
		$thetimespent = ($hours . " " . __( "Hours", "taskrocket" ) . " " . $min . " " . __( "mins", "taskrocket" ));
		
	} else {
		$thetimespent = "";
	}
	

	// If there is more information for the task, do this.
	if($postContent !=="") {
		$taskcontent = "<strong>" . __( "More info", "taskrocket" ) . "</strong><br /><pre " . $pre . ">" . $postContent . "</pre><br />";
	} else {
		$taskcontent = "";
	}

	// If the form being submitted is on the 'edit task' (single.php) page.
	if($form_type == "task-edit-single") {
		$task_solo_text = "";
		$created_or_updated = " updated ";
	} else {
		$created_or_updated = " created ";
	}

	// If the New Task form is submitted
	if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

		// Upload file(s)
		if ( $_FILES ) {
		$files = $_FILES["tr_multiple_attachments"];
		foreach ($files['name'] as $key => $value) {
				$pid = $post_id;
				if ($files['name'][$key]) {
					$file = array(
						'name' => $files['name'][$key],
						'type' => $files['type'][$key],
						'tmp_name' => $files['tmp_name'][$key],
						'error' => $files['error'][$key],
						'size' => $files['size'][$key]
					);

					$_FILES = array (
						'tr_multiple_attachments' => $file
					);
					foreach ($_FILES as $file => $array) {
						$newupload = tr_handle_attachment($file,$pid);
					}
				}
			}
		}

		$options = get_option( 'taskrocket_settings' );

		// If email notifications are disabled
		if($no_emails == "yes") {
			// Crickets...
		} else {
			// Otherwise, email notifications are enabled so create HTML email
			// for the recipient as either HTML or plain text.
			if($plain_emails !== "yes") {
				$body = '
			<table width="100%" height="100%" border="0" cellspacing="25" cellpadding="0" style="background:#f3f3e7;padding:25px 0; text-align:center;">
			  <tr>
			    <td>
				    <table width="450" border="0" cellpadding="30" cellspacing="0"' . $table_style . ' bgcolor="#ffffff">
				        <tr>
				        	<td>
								' . $tasksendericon . '
								<div' . $type . '>
									<br /><strong>' . $taskSenderFirstName . ' ' . $taskSenderLastName . '</strong> ' . $created_or_updated . " " . __( "a task", "taskrocket" ) . ':<br /><br />
									<table width="100%" border="0" cellspacing="15" cellpadding="0"' . $task_title_style . '><tr><td><p style=" display:block; width:355px;">' . stripslashes($postTitle) . '</p></td></tr></table>
									<br />' . stripslashes($taskcontent) . '
									<table width="100%" border="0" cellspacing="0" cellpadding="0"' . $inner_table_style . '>
									  <tbody>
									    <tr>
									      <td valign="top"  width="30%"' . $row_style . '><strong' . $strong . '>' . __( "Project", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><a href="' . $projectURL . '" ' . $link_colour . '>' . $projectname . '</a></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Priority", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span style="text-transform: capitalize;">' . $thepriority . '</span> <span style="display:inline-block; width:12px; height:12px; border-radius:100%; background-color:#' . $prioritycolor . '"></span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Job", "taskrocket" ) . ' # :</strong></td>
									      <td' . $row_style . '><span>' . $thejobnumber . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Time spent", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $thetimespent . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Cost", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $final_cost . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Start date", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $startdateinsert . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Due Date", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $duedateinsert . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Visibility", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $privacy . '</span></td>
									    </tr>
									    <tr>
									      <td valign="top"' . $row_style . '><strong' . $strong . '>' . __( "Status", "taskrocket" ) . ' :</strong></td>
									      <td' . $row_style . '><span>' . $task_status . '</span></td>
									    </tr>
									  </tbody>
									</table>
									<br />
									' . $client_advice . '
									<br /><br />
									<table width="100%" border="0" cellspacing="0" cellpadding="0">
									  <tr>
									    <td width="140">' . $task_button . '</td>
										<td>' . $project_button . '</td>
									  </tr>
									</table>
								</div>
							</td>
				        </tr>
						' . $task_solo_text . '
				    </table>
			    </td>
			  </tr>
			</table>
			';
			} else {
				$body = $taskSenderFirstName . ' ' . $taskSenderLastName . ' ' . __( "created a task", "taskrocket" ) . ': ' . $postTitle . '<br /><br />' . 
				__( "Task info", "taskrocket" ) . ': <pre>' . stripslashes($postContent) . '</pre><br />' .
				__( "Project", "taskrocket" ) . ': ' . $projectname . '<br />' .
				__( "Project URL", "taskrocket" ) . ': ' . $projectURL . '<br />' .
				__( "Priority", "taskrocket" ) . ': ' . $priority . '<br /><br />' .
				__( "Job", "taskrocket" ) . ' #: ' . $thejobnumber . '<br />' .
				__( "Time spent", "taskrocket" ) . ': ' . $thetimespent . '<br />' .
				__( "Cost", "taskrocket" ) . ': ' . $final_cost . '<br />' .
				__( "Start date", "taskrocket" ) . ': ' . $plain_stardateinsert . '<br />' .
				__( "Due Date", "taskrocket" ) . ': ' . $plain_duedateinsert . '<br />' .
				__( "Visibility", "taskrocket" ) . ': ' . $privacy . '<br />' .
				__( "Status", "taskrocket" ) . ': ' . $task_status . '<br /><br />' .
				
				$plain_client_advice;
			}
			
			// Debugging
			// echo $body; exit;

			$headers[]= "Content-type:text/html;charset=UTF-8";
			$headers[]= __( "From", "taskrocket" ) . get_bloginfo('name') . ' <' . $current_user->user_email . '>';
			$headers[]= 'Reply-To: ' . $current_user->user_email;
			$headers[]= "MIME-Version: 1.0";
			wp_mail($recipients, $subject, $body, $headers);
		}
		// End If email notifications are disabled

		// Targeting the last row ID from the posts table.
		// This is used for the auto job number creation.
		global $wpdb;
		$last_row_ID = $wpdb->get_col( "SELECT ID FROM $wpdb->posts where post_type='post' ORDER BY post_date DESC" );
		$task_job_row_ID = $last_row_ID[0];
		
		if($options['auto_job_numbers'] == true) {
			if($form_type !== "task-edit-single") { // Prevent Job # inception
				update_post_meta( $post_id, 'job_number_task', $job_number_task . $task_job_row_ID);
			}
		} else {
			update_post_meta( $post_id, 'job_number_task', $job_number_task);
		}

		// Redirect logic
		if ($form_type == "new-task") {
			$clean = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $postTitle); // Clean up slashes and quotes
			header("Location: " . home_url() . "/" . get_option( 'category_base' ) . "/" . $categorySlug . "/?task_status=added&task_name=$clean&task_slug=$task_link&job_number=$job_number_task");
		}
		if ($form_type == "new-task-solo") { // 'New Task' page
			header("Location: " . home_url() . "/new-task?task_status=added&task_name=$postTitle&project=$category&task_slug=$task_link");
		}
		if ($form_type == "client") {
			header("Location: " . home_url() . "/" . get_option( 'category_base' ) . "/" . $categorySlug . "/?task_status=added&task_name=$postTitle");
		}

	}
	// End If New Task form is submitted
	ob_end_clean();
?>