<?php
if($options['no_emails'] == false) {
    global $post;
    // Send an email notification when a comment is added to the database.

    $options = get_option( 'taskrocket_settings' );
    $category_base = get_option( 'category_base' );
    
    $the_ID = $comment_ID; // Comment ID
    $get_the_comment_ID = get_comment($the_ID); 
    $comment_post_ID = $get_the_comment_ID->comment_post_ID;
    $comment_author = get_comment_author($the_ID);
    $comment_author_email = get_comment_author_email($the_ID);

    $project_url = get_the_category($comment_post_ID);
    $the_project_url = home_url() . "/" . $category_base . "/" . esc_html( $project_url[0]->slug );
    $the_project_ID = esc_html( $project_url[0]->term_id );

    $project_name = get_cat_name($comment_post_ID);
    $the_project_name = esc_html( $project_url[0]->name );

    $task_url = get_permalink($comment_post_ID);
    $task_title = get_the_title($comment_post_ID);
    $task_owner_id = get_post_field('post_author', $comment_post_ID);
    $task_owner_email = get_the_author_meta('user_email', $task_owner_id); // $comment_post_ID should be user id

    $comment_owner_email = get_comment_author_email($the_ID);
    $the_comment = get_comment($the_ID);
    $comment_content = $the_comment->comment_content;
    $comment_url = $task_url . '#comment-' . $the_ID;

    $icon = '<img src="' . get_template_directory_uri() . '/images/notifications/comment.png" title="Comment" />';


    // Project Manager email
    $project_manager = get_option( 'tr_project_manager_' . $the_project_ID);
    $project_manager_user_info = get_userdata($project_manager);
    if($project_manager) {
        $project_manager_email = $project_manager_user_info->user_email . ","; // <- PM email
    } else {
        $project_manager_email = "";
    }
    
    // Required for categiry ID to work inside a function
    $GLOBALS['the_project_ID'] = $the_project_ID;

    // Admin email and project contributor (task owner)
    $admin_email = sanitize_text_field( get_option( 'admin_email' )) . ","; // <- Admin email
    $project_contributor = sanitize_text_field( $_POST["project_contributor"]) . ",";  // <- Task owner email

    // Project team email
    if($options['comment_is_made_notify_project_team'] == true) {
        function allProjectUsers() {
            $project_member = '';
            $cat_authors = array();
            $args = array(
                'posts_per_page'     => -1,
                'category'           => $GLOBALS['the_project_ID'],
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
    if($options['comment_is_made_notify_pm'] == true) {
        $recipient_pm = $project_manager_email;
    }
    if($options['comment_is_made_notify_admin'] == true) {
        $recipient_admin = $admin_email;
    }
    if($options['comment_is_made_notify_others']) {
        $recipient_others = $options['comment_is_made_notify_others'] . ",";
    }
    if($options['comment_is_made_notify_project_team'] == true) {
        $recipient_project_team = allProjectUsers();
    }
    if($options['comment_is_made_notify_task_owner'] == true) {
        $recipient_task_owner = $task_owner_email . ",";
    }

    $comment_notify_recipients =  rtrim($recipient_pm . $recipient_admin . $recipient_others . $recipient_task_owner . $recipient_project_team[0],",");

    //echo $comment_notify_recipients; exit;


    $header = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:25px;'";
    $type = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:20px; font-weight:bold;'";
    $type_comment = " style='font-family:Arial, Helvetica, sans-serif; color:#617b96; font-size:15px; line-height:20px;'";

    $view_comment_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $comment_url . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $comment_url . '"
    style="background-color:#3b4a5b;border-radius:4px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:40px;text-align:center;text-decoration:none;width:120px;-webkit-text-size-adjust:none;">' . __( "View Comment", "taskrocket" ) . '</a><!--[if mso]></center></v:roundrect><![endif]--></div>';

    $view_project_button = '<div><!--[if mso]><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="' . $the_project_url . '" style="height:40px;v-text-anchor:middle;width:120px;" arcsize="10%" stroke="f" fillcolor="#3b4a5b"><w:anchorlock/><center><![endif]--><a href="' . $the_project_url . '"
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
                        <p>' . $icon . '</p>
                    	<h1' . $header . '>' . __( "The task", "taskrocket" ) . ' <br /><span style="display:block; font-size:25px; color:#3b4a5b; margin-top: 5px;">' . $task_title . '</span><br />' . __( "has a new comment by", "taskrocket" ) . ' ' . $comment_author . ' (' . $comment_author_email . ')</h1>
                        <p' . $type . '>(<a href="' . $the_project_url . '" style="text-decoration:none; color:#617b96;">' . $the_project_name . '</a>)</p>
                        <p' . $type_comment . '>"' . $comment_content . '"</p>
                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                          <tbody>
                            <tr>
                              <td align="center" valign="middle">' . $view_comment_button . '</td>
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
        $body = __( "The task", "taskrocket" ) . '<br /> -- ' . $task_title . '<br />' . __( "has a new comment", "taskrocket" ) . ':<br /><br />"' . $comment_content . '"<br /><br />' . '
        Comment: ' . $comment_url . '<br />Project: ' . $the_project_url;
    }
    //echo $body; exit;
    $subject = stripslashes(__( "New comment on:", "taskrocket" ) . ' ' . $task_status . ' (' . get_the_title($comment_post_ID) . ')');
    $headers[]= "Content-type:text/html;charset=UTF-8";
    $headers[]= __( "From", "taskrocket" ) . get_bloginfo('name') . ' <' . $current_user->user_email . '>';
    $headers[]= 'Reply-To: ' . $current_user->user_email;
    $headers[]= "MIME-Version: 1.0";
    wp_mail($comment_notify_recipients, $subject, $body, $headers);
}
?>