<?php
// Send an email notification (only used when a task status is changed).

// Project Manager email
$project_manager = get_option( 'tr_project_manager_' . $GLOBALS['project_ID']);
$project_manager_user_info = get_userdata($project_manager);
if($project_manager) {
    $project_manager_email = $project_manager_user_info->user_email . ","; // <- PM email
} else {
    $project_manager_email = "";
}

// Admin email and project contributor (task owner)
$admin_email = sanitize_text_field( get_option( 'admin_email' )) . ","; // <- Admin email
$project_contributor = sanitize_text_field( $_POST["project_contributor"]) . ",";  // <- Task owner email

// Project team email
if($options['task_changes_status_notify_project_team'] == true) {
    function allProjectUsers() {
        $project_member = '';
        $cat_authors = array();
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
        //return $project_member;
    }
}

// Recipients
if($options['task_changes_status_notify_pm'] == true) {
    $recipient_pm = $project_manager_email;
}
if($options['task_changes_status_notify_admin'] == true) {
    $recipient_admin = $admin_email;
}
if($options['task_changes_notify_others']) {
    $recipient_task_changes = $options['task_changes_notify_others'] . ",";
}
if($options['task_changes_status_notify_project_team'] == true) {
    $recipient_project_members = allProjectUsers();
}
if($options['task_changes_status_notify_task_owner'] == true) {
    $task_owner = get_userdata($_GET['owner_ID']);
    $task_owner_email = $task_owner->user_email . ",";
}

$recipients =  rtrim($recipient_pm . $recipient_admin . $task_owner_email . $recipient_task_changes . $recipient_project_members[0],",");
//echo $recipients; exit;

// Pretty names for task status alt attributes
if($action == "complete") {
    $task_status = __( "Complete", "taskrocket" );
    $icon = '<img src="' . get_template_directory_uri() . '/images/notifications/complete.png" title="Complete" />';
}
if($action == "incomplete") {
    $task_status = __( "Incomplete", "taskrocket" );
    $icon = '<img src="' . get_template_directory_uri() . '/images/notifications/incomplete.png" title="Incomplete" />';
}
if($action == "onhold") {
    $task_status = __( "On hold", "taskrocket" );
    $icon = '<img src="' . get_template_directory_uri() . '/images/notifications/onhold.png" title="On hold" />';
}
if($action == "inprogress") {
    $task_status = __( "In progress", "taskrocket" );
    $icon = '<img src="' . get_template_directory_uri() . '/images/notifications/inprogress.png" title="In progress" />';
}

$project_name = get_cat_name($GLOBALS['project_ID']);
$project_url = get_category_link($GLOBALS['project_ID']);
$task_url = get_permalink($task_ID);
$task_title = get_the_title($task_ID);

$header = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:25px;'";
$type = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:20px; font-weight:bold;'";

$view_task_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $task_url . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $task_url . '"
style="background-color:#3b4a5b;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:120px;-webkit-text-size-adjust:none;">' . __( "View Task", "taskrocket" ) . '</a><!--[if mso]></center></v:roundrect><![endif]--></div>';

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
                <td>
                	<h1' . $header . '>' . __( "The task", "taskrocket" ) . ' <br /><span style="display:block; font-size:25px; color:#3b4a5b; margin-top: 10px;">' . $task_title . '</span><br />' . __( "changed status", "taskrocket" ) . '</h1>
                    <p>' . $icon . '</p>
                    <h2' . $type . '>' . $task_status . '</h2>
                    <p' . $type . '>(<a href="' . $project_url . '" style="text-decoration:none; color:#617b96;">' . $project_name . '</a>)</p>
                    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                      <tbody>
                        <tr>
                          <td align="center" valign="middle">' . $view_task_button . '</td>
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
    $body = __( "The task", "taskrocket" ) . '<br /><br /> - ' . $task_title . '<br /><br />' . __( "changed status", "taskrocket" ) . ':<br /><br /><b>' . $task_status . '</b>
    <br /><br />
    Task: ' . $task_url . '<br />Project: ' . $project_url;
}
//echo $body; exit;
$subject = stripslashes(__( "Task status changed:", "taskrocket" ) . ' ' . $task_status . ' (' . get_the_title($task_ID) . ')');
$headers[]= "Content-type:text/html;charset=UTF-8";
$headers[]= __( "From", "taskrocket" ) . get_bloginfo('name') . ' <' . $current_user->user_email . '>';
$headers[]= 'Reply-To: ' . $current_user->user_email;
$headers[]= "MIME-Version: 1.0";
wp_mail($recipients, $subject, $body, $headers);
?>