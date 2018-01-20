<?php 
	ob_start();
	define('WP_USE_THEMES', false);

	if ( !function_exists( 'get_home_path' ) ) {
		require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
	}

	$options 		= get_option( 'taskrocket_settings' );
	$post_status 	= "'publish'";
	$date_format 	= get_option('date_format');
	

	// Field values
	$project_name 			= $_POST["project_name"];
	$project_id 			= $_POST["project_id"];
	$project_slug			= $_POST["project_slug"];
	$report_recipient 		= $_POST["report_recipient"];
	$total_tasks 			= $_POST["total_tasks"];
	$outstanding_tasks 		= $_POST["outstanding_tasks"];
	$completed_tasks		= $_POST["completed_tasks"];
	$project_manager 		= $_POST["project_manager"];
	$job_number				= $_POST["job_number"];
	$budget 				= $_POST["budget"];
	$start_date 			= $_POST["start_date"];
	$due_date 				= $_POST["due_date"];
	$time_allocated 		= $_POST["time_allocated"];
	$time_used 				= $_POST["time_used"];
	$time_remaining 		= $_POST["time_remaining"];
	$cost 					= $_POST["cost"];
	$percent_complete 		= $_POST["percent_complete"];
	$the_rate 				= $_POST["the_rate"];
	$currency_symbol 	    = $options['currency_symbol'];
	
	$sender_first_name 		= $current_user->user_firstname;
	$sender_last_name 		= $current_user->user_lastname;
	$sender_email			= $current_user->user_email;
	$sender_id 				= $current_user->ID;
	
	// Project URL
	$project_url = home_url() . "/" . get_option( 'category_base' ) . "/" . $project_slug;
	
	// Author image
	$authorID = $sender_id;
	$user = get_userdata($authorID);
	$attachment_id = $user->user_photo;
	$image_attributes = wp_get_attachment_image_src( $attachment_id );
	$photosize = "40";
	
	if (get_option('show_avatars')) {
		$photo =  get_avatar( $user->ID , $photosize);
	} else {
		if( $image_attributes ) {
			$photo = '<img src="' . $image_attributes[0] . '" width="' . $photosize . '" height="' . $photosize . '" style="border-radius: 100%;" />';
		} else {
			$photo = '<img src="' . get_template_directory_uri() . '/images/default-user.png" width="' . $photosize . '" height="' . $photosize . '" style="border-radius: 100%;" />';
		} 
	}
	
	// Styles
	$main_type 		= 'font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #495777; line-height: 20px;';
	$heading1 		= 'font-family: Arial, Helvetica, sans-serif, sans-serif; font-size: 20px; color: #495777; line-height: 24px;';
	$heading2 		= 'font-family: Arial, Helvetica, sans-serif, sans-serif; font-size: 17px; color: #495777; line-height: 18px; padding-top: 25px; padding-bottom: 0';
	$row_style 		= "border-bottom: solid 1px #f3f3e7; line-height: 25px; font-family: Arial, Helvetica, sans-serif, sans-serif; font-size: 13px; color: #495777;";
	$row_bg 		= "background-color: #fafafb;";
	$border_right 	= "border-right: solid 1px #f3f3e7";
	$border_left 	= "border-left: solid 1px #f3f3e7";
	$anchor_color 	= "color: #495777; text-decoration: none;";
	
	$button = '<div><!--[if mso]>
  <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $project_url . '" style="height:53px;v-text-anchor:middle;width:200px;" arcsize="8%" stroke="f" fillcolor="#49a9ce">
    <w:anchorlock/>
    <center>
  <![endif]-->
      <a href="' . $project_url . '"
style="background-color:#49a9ce;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:53px;text-align:center;text-decoration:none;width:200px;-webkit-text-size-adjust:none;">' . __( "Go to Project", "taskrocket" ) . '</a>
  <!--[if mso]>
    </center>
  </v:roundrect>
<![endif]--></div>';
	
	// Main tasks query
	query_posts( array ( 
		'category_name'         => $project_name, 
		'posts_per_page'        => -1,
		'post_status' 		    => array($post_status), // Condition based on setting.
		'orderby'               => 'title',
		'order'                 => 'ASC',
	) );
	
	$body[] = '
	<h1 style="' . $heading1 . '">Report: ' . $project_name . ' <span style="color: #4ec0ea;"> - ' . $percent_complete . '</span></h1>
	<table style="border: solid 1px #f3f3e7; background-color: #fafafb;" cellspacing="15">
		<tbody>
			<tr>
				<td><p style="' . $main_type . '">' . __( "This report was sent by", "taskrocket" ) . ' ' . $sender_first_name . ' ' . $sender_last_name . ' (' . $sender_email . ') <br />to ' . $report_recipient . '.</p></td>
				<td style="width: 40px; vertical-align: middle;" width="40">' . $photo . '</td>
				<td style="width: 40px; vertical-align: middle;">' . $button . '</td>
			</tr>
		</tbody>
	</table>
	';
	
	$body[] = '
	<h2 style="' . $heading2 . '">' . __( "Project Details", "taskrocket" ) . '</h2>
	<table border="0" cellspacing="0" cellpadding="5" style="width: 100%; border-top:solid 1px #f3f3e7;">
	    <tbody>
	        <tr style="' . $main_type . $row_bg . '">
	            <td style="' . $row_style . $border_left . '; width: 20%;"><strong>' . __( "Total tasks", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '; width: 30%;">' . $total_tasks . '</td>
	            <td style="' . $row_style . '; width: 20%;"><strong>' . __( "Start date", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '; width: 30%;">' . $start_date . '</td>
	        </tr>
	        <tr style="' . $main_type . '">
	            <td style="' . $row_style . $border_left . '"><strong>' . __( "Outstanding tasks", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $outstanding_tasks . '</td>
	            <td style="' . $row_style . '"><strong>' . __( "Due Date", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $due_date . '</td>
	        </tr>
	        <tr style="' . $main_type . $row_bg . '">
	            <td style="' . $row_style . $border_left . '"><strong>' . __( "Completed tasks", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' .$completed_tasks . '</td>
	            <td style="' . $row_style . '"><strong>' . __( "Time allocated", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $time_allocated . '</td>
	        </tr>
	        <tr style="' . $main_type . '">
	            <td style="' . $row_style . $border_left . '"><strong>' . __( "Project Manager", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $project_manager . '</td>
	            <td style="' . $row_style . '"><strong>' . __( "Time used", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $time_used . '</td>
	        </tr>
	        <tr style="' . $main_type . $row_bg . '">
	            <td style="' . $row_style . $border_left . '"><strong>' . __( "Job number", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' .$job_number . '</td>
	            <td style="' . $row_style . '"><strong>' . __( "Time remaining", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $time_remaining . '</td>
	        </tr>
	        <tr style="' . $main_type . '">
	            <td style="' . $row_style . $border_left . '"><strong>' . __( "Budget", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $budget . '</td>
	            <td style="' . $row_style . '"><strong>' . __( "Cost", "taskrocket" ) . ': </strong></td>
	            <td style="' . $row_style . $border_right . '">' . $cost . ' (' . $currency_symbol . $the_rate . '/' . __( "hr", "taskrocket" ) . ')</td>
	        </tr>
	    </tbody>
	</table>
	';
	
	$body[] = '
	<h2 style="' . $heading2 . '">' . __( "Project Tasks", "taskrocket" ) . '</h2>
	<p style="' . $main_type . '"><strong>'. __( "Status Legend", "taskrocket" ) .': </strong>	 <img src="' . get_template_directory_uri() . '/images/notifications/incomplete.png" width="12" height="12" style="display:inline-block; margin-left:15px;" /> '. __( "Incomplete", "taskrocket" ) . '<img src="' . get_template_directory_uri() . '/images/notifications/complete.png" width="12" height="12" style="display:inline-block; margin-left:15px;" /> '. __( "Complete", "taskrocket" ) . '<img src="' . get_template_directory_uri() . '/images/notifications/inprogress.png" width="12" height="12" style="display:inline-block; margin-left:15px;" /> '. __( "In Progress", "taskrocket" ) . '	<img src="' . get_template_directory_uri() . '/images/notifications/onhold.png" width="12" height="12" style="display:inline-block;margin-left:15px;" /> '. __( "On Hold", "taskrocket" ) . '</p>
	<table border="0" cellspacing="0" cellpadding="5" style="width: 100%; border-top:solid 1px #f3f3e7;">
	<thead style="background-color: #efeff0;">
		<tr style="' . $main_type . '">
			<th style="' . $row_style . ' " align="left">' . __( "Task", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Date Added", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Due Date", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Completed", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="center">' . __( "Priority", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="center">' . __( "Status", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Time", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Cost", "taskrocket" ) . '</th>
			<th style="' . $row_style . ' " align="left">' . __( "Owner", "taskrocket" ) . '</th>
		</tr>
	</thead>';
	
	$c = true;
	while ( have_posts() ) : the_post();

	$old_due_date = get_post_meta($post->ID, 'duedate', TRUE);
	$new_due_date = new DateTime($old_due_date);
	$status       = get_post_meta($post->ID, 'tr_status', TRUE);
	
	if($status == "incomplete") {
		$task_status = __( "Incomplete", "taskrocket" );
	}
	if($status == "complete") {
		$task_status = __( "Complete", "taskrocket" );
	} 
	if($status == "onhold") {
		$task_status = __( "On hold", "taskrocket" );
	}
	if($status == "inprogress") {
		$task_status = __( "In progress", "taskrocket" );
	}
	
	if($old_due_date == TRUE) { 
		$the_date =  $new_due_date->format($date_format);
	} else { 
		$the_date = "-";
	}
	
	$priority = get_post_meta($post->ID, 'priority', TRUE);
	if($priority == "") {
		$dot = '<img src="' . get_template_directory_uri() . '/images/email-report/dot-normal.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($priority == "low") {
		$dot = '<img src="' . get_template_directory_uri() . '/images/email-report/dot-low.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($priority == "normal") {
		$dot = '<img src="' . get_template_directory_uri() . '/images/email-report/dot-normal.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($priority == "high") {
		$dot = '<img src="' . get_template_directory_uri() . '/images/email-report/dot-high.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($priority == "urgent") {
		$dot = '<img src="' . get_template_directory_uri() . '/images/email-report/dot-urgent.png" width="12" height="12" style="display:inline-block;" />';
	}
	
	if($status == "") {
		$status_dot = '<img src="' . get_template_directory_uri() . '/images/notifications/inprogress.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($status == "inprogress") {
		$status_dot = '<img src="' . get_template_directory_uri() . '/images/notifications/inprogress.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($status == "incomplete") {
		$status_dot = '<img src="' . get_template_directory_uri() . '/images/notifications/incomplete.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($status == "complete") {
		$status_dot = '<img src="' . get_template_directory_uri() . '/images/notifications/complete.png" width="12" height="12" style="display:inline-block;" />';
	}
	if($status == "onhold") {
		$status_dot = '<img src="' . get_template_directory_uri() . '/images/notifications/onhold.png" width="12" height="12" style="display:inline-block;" />';
	}
	
	$date_completed = get_the_modified_date($date_format);
	if($status == "complete") {
		$date_completed = $date_completed;
		$del_start = "<del>";
		$del_end = "</del>";
		$light_row = "color: #acb0bf !important; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px;";
	} else {
		$date_completed = "-";
		$del_start = "";
		$del_end = "";
		$line_through = "";
		$light_row = "";
	}
	
	//$the_status = $post->post_status;
	if($status == "complete") {
		$line_through = "text-decoration: line-through;";
	}
	
	
	// Cost
	$minutes = get_post_meta($post->ID, 'logtime', TRUE);
	if ($minutes > 0 ) {
		
		$standard_rate = $options['rate'];
		$project_rate = get_option( 'tr_hourly_rate_' . $project_id );
		
		if($project_rate) { 
			$the_rate = $project_rate;
		} else if($standard_rate) {
			$the_rate = $standard_rate;
		} else {
			$the_rate = 0;
		}
		
	    $task_cost = $the_rate * $minutes;
	    if ($currency_symbol == "") {
	        $currency_symbol = "$";
	    }
	    $the_task_cost = $currency_symbol . round($task_cost / 60 , 2);
	} else {
		$the_task_cost = "-";
	}
	
	// Task time
	if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) { 
		$minutes = get_post_meta($post->ID, 'logtime', TRUE);
		$hours = floor($minutes / 60);
		$min = $minutes - ($hours * 60);
		$the_time = $hours . " hrs " . $min . " mins";
	} else {
		$the_time = "-";
	}

	
	// Task owner
	$the_task_owner = get_the_author_meta( 'user_firstname' , $post->post_author ) . " " . get_the_author_meta( 'user_lastname' , $post->post_author );
	
	
	$body[] = '
	<tr style="' . $main_type . '; ' . (($c = !$c)?' background-color: #fafafb':'') . ';">
		<td style="' . $row_style . $light_row . $main_type . '"><a href="' . get_the_permalink() . '" style="' . $anchor_color . $light_row . $main_type . '">' . $del_start . get_the_title() . $del_end .  '</a></td>
		<td style="' . $row_style . '">' . get_the_time($date_format) . '</td>
		<td style="' . $row_style . '">' . $the_date . '</td>
		<td style="' . $row_style . '">' . $date_completed . '</td>
		<td style="' . $row_style . '; text-align:center;" align="center">' . $dot . '</td>
		<td style="' . $row_style . '; text-align:center;" align="center">' . $status_dot . '</td>
		<td style="' . $row_style . '">' . $the_time . '</td>
		<td style="' . $row_style . '">' . $the_task_cost . '</td>
		<td style="' . $row_style . '">' . $the_task_owner . '</td>
	</tr>
	';

	endwhile; 
	wp_reset_query();
	
	$body[] = '</table>'; ?>

	<?php 
	// Email details
	$subject = stripslashes('' . __( "Project Report", "taskrocket" ) . ': ' . $project_name . " - " . $percent_complete);
	
	$email_body = join("\r\n",$body);
	
	// Debug
	// echo $email_body;
	// exit;

	// Prepare headers
	$headers[]= "Content-type:text/html;charset=UTF-8";
	$headers[]= 'From:' . $sender_email;
	$headers[]= 'Reply-To: ' . $sender_email;
	$headers[]= "MIME-Version: 1.0";
	wp_mail($report_recipient, $subject, $email_body, $headers);
	ob_end_clean();
	header("Location: " . home_url() . "/single-report?projectid=" . $project_id . "&sent=yes&recipient=" . $report_recipient);
?>