<?php
/*
Plugin Name: MapSVG Lite
Plugin URI: http://mapsvg.com
Description: Add interactive vector map to your WordPress site.
Author: Roman S. Stepanov
Author URI: http://codecanyon.net/user/RomanCode
Version: 2.3.12
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//error_reporting(E_ALL);
define('MAPSVG_DEBUG', false);

$upload_dir = wp_upload_dir();

define('MAPSVG_PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('MAPSVG_PLUGIN_DIR', realpath(plugin_dir_path( __FILE__ )));
define('MAPSVG_MAPS_DIR', realpath(MAPSVG_PLUGIN_DIR . '/maps'));
define('MAPSVG_MAPS_UPLOADS_DIR', realpath($upload_dir['basedir'] . '/mapsvg'));
define('MAPSVG_MAPS_UPLOADS_URL', $upload_dir['baseurl'] . '/mapsvg/');
define('MAPSVG_MAPS_URL', MAPSVG_PLUGIN_URL . 'maps/');
define('MAPSVG_PINS_DIR', realpath(MAPSVG_PLUGIN_DIR . '/markers'));
define('MAPSVG_PINS_URL', MAPSVG_PLUGIN_URL . 'markers/');
define('MAPSVG_VERSION', '2.3.12');
define('MAPSVG_JQUERY_VERSION', '6.2.7');

$mapsvg_inline_script = '';
$mapsvg_page = 'index';

/**
 * Add buttons to Visual Editor
 */
function mapsvg_setup_tinymce_plugin(){
// Check if the logged in WordPress User can edit Posts or Pages
    // If not, don't register our TinyMCE plugin
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }

    // Check if the logged in WordPress User has the Visual Editor enabled
    // If not, don't register our TinyMCE plugin
    if ( get_user_option( 'rich_editing' ) !== 'true' ) {
        return;
    }

    wp_register_style('mapsvg-tinymce', MAPSVG_PLUGIN_URL . "css/mapsvg-tinymce.css");
    wp_enqueue_style('mapsvg-tinymce');

    // Setup some filters
    add_filter('mce_external_plugins', 'mapsvg_add_tinymce_plugin');
    add_filter('mce_buttons', 'mapsvg_add_tinymce_button');
    add_action('wp_footer', 'add_thickbox');

}

if ( is_admin() ) {
    add_action( 'init', 'mapsvg_setup_tinymce_plugin' );
}

/**
 * Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
 *
 * @param array $plugin_array Array of registered TinyMCE Plugins
 * @return array Modified array of registered TinyMCE Plugins
 */
function mapsvg_add_tinymce_plugin( $plugin_array ) {

    $plugin_array['mapsvg'] = MAPSVG_PLUGIN_URL . 'js/tinymce-mapsvg.js';
    return $plugin_array;

}

/**
 * Adds a button to the TinyMCE / Visual Editor which the user can click
 * to insert a custom CSS class.
 *
 * @param array $buttons Array of registered TinyMCE Buttons
 * @return array Modified array of registered TinyMCE Buttons
 */
function mapsvg_add_tinymce_button($buttons){
    array_push( $buttons, 'mapsvg' );
    return $buttons;
}


/**
 * Add common JS & CSS
 */
function mapsvg_add_jscss_common(){

    wp_register_style('mapsvg', MAPSVG_PLUGIN_URL . 'css/mapsvg.css');
    wp_enqueue_style('mapsvg');        

    wp_register_script('jquery.mousewheel', MAPSVG_PLUGIN_URL . 'js/jquery.mousewheel.min.js',array('jquery'), '3.0.6');
    wp_enqueue_script('jquery.mousewheel', null, '3.0.6');

    if(MAPSVG_DEBUG)        
        wp_register_script('mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg.js', array('jquery'), rand());
    else
        wp_register_script('mapsvg', MAPSVG_PLUGIN_URL . 'js/mapsvg.min.js', array('jquery'), MAPSVG_JQUERY_VERSION);
    
    wp_enqueue_script('mapsvg');

}
add_action('wp_enqueue_scripts', 'mapsvg_add_jscss_common');


/**
 * Add admin's JS & CSS
 */
function mapsvg_add_jscss_admin($hook_suffix){

    global $mapsvg_settings_page, $wp_version;

    // Load scripts only if we on mapSVG admin page
    if ( $mapsvg_settings_page != $hook_suffix )
        return;

    mapsvg_add_jscss_common();

    if(isset($_GET['page']) && $_GET['page']=='mapsvg-config'){

        wp_register_script('mapsvg.admin', MAPSVG_PLUGIN_URL . 'js/admin.js', array('jquery'), '3.0');
        wp_enqueue_script('mapsvg.admin');
        
        wp_register_script('bootstrap', MAPSVG_PLUGIN_URL . "js/bootstrap.min.js", null, '3.3.6');
        wp_enqueue_script('bootstrap');
    	wp_register_style('bootstrap', MAPSVG_PLUGIN_URL . "css/bootstrap.min.css", null, '3.3.6');
    	wp_enqueue_style('bootstrap');
    	wp_register_style('fontawesome', MAPSVG_PLUGIN_URL . "css/font-awesome.min.css", null, '4.4.0');
    	wp_enqueue_style('fontawesome');   
        
        wp_register_script('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'js/bootstrap-colorpicker.min.js');
        wp_enqueue_script('bootstrap-colorpicker');
    	wp_register_style('bootstrap-colorpicker', MAPSVG_PLUGIN_URL . 'css/bootstrap-colorpicker.min.css');
        wp_enqueue_style('bootstrap-colorpicker');

    	wp_register_style('main.css', MAPSVG_PLUGIN_URL . 'css/main.css');
    	wp_enqueue_style('main.css');

        wp_enqueue_script('select2', MAPSVG_PLUGIN_URL . 'js/select2.min.js', array('jquery'), '4.0',true);
        wp_register_style('select2', MAPSVG_PLUGIN_URL . 'css/select2.min.css');
    	wp_enqueue_style('select2');

        wp_enqueue_script('growl', MAPSVG_PLUGIN_URL . 'js/jquery.growl.js', array('jquery'), '4.0',true);
        wp_register_style('growl', MAPSVG_PLUGIN_URL . 'css/jquery.growl.css');
        wp_enqueue_style('growl');


        wp_register_script('handlebars', MAPSVG_PLUGIN_URL . 'js/handlebars.js', null, '4.0.2');
        wp_enqueue_script('handlebars');

        wp_register_script('nanoscroller', MAPSVG_PLUGIN_URL . 'js/jquery.nanoscroller.min.js', null, '0.8.7');
        wp_enqueue_script('nanoscroller');
        wp_register_style('nanoscroller', MAPSVG_PLUGIN_URL . 'css/nanoscroller.css');
        wp_enqueue_style('nanoscroller');

        wp_register_script('typeahead', MAPSVG_PLUGIN_URL . 'js/typeahead.bundle.min.js', null, '1.0');
        wp_enqueue_script('typeahead');

        if(version_compare($wp_version, "3.8", '>=')){
            wp_register_style('mapsvg-grey', MAPSVG_PLUGIN_URL . 'css/grey.css');
            wp_enqueue_style('mapsvg-grey');
        }
    }
     
}


/**
 * Add submenu element to Plugins
 */
$mapsvg_settings_page = '';

function mapsvg_config_page() {
    global $mapsvg_settings_page;

	if ( function_exists('add_menu_page') )
		$mapsvg_settings_page = add_menu_page('MapSVG', 'MapSVG', 'edit_posts', 'mapsvg-config', 'mapsvg_conf', '', 66);


    add_action('admin_enqueue_scripts', 'mapsvg_add_jscss_admin',0);
}

add_action( 'admin_menu', 'mapsvg_config_page' );


/**
 * Register [mapsvg] shortcode
 */
function mapsvg_print( $atts ){
  global $mapsvg_inline_script;

  $post = mapsvg_get_map($atts['id']);

  if (empty($post->ID))
    return 'Map not found, please check "id" parameter in your shortcode.';

  $data  = '<div id="mapsvg-'.$post->ID.'" class="mapsvg"></div>';
  $script = '<script type="text/javascript">';

  if(!empty($atts['selected'])){
      $country = str_replace(' ','_', $atts['selected']);
      $script .= '
      var mapsvg_options = '.$post->post_content.';
      jQuery.extend( true, mapsvg_options, {regions: {"'.$country.'": {selected: true}}} );
      jQuery("#mapsvg-'.$post->ID.'").mapSvg(mapsvg_options);</script>';
  }else{
      $script .= 'jQuery("#mapsvg-'.$post->ID.'").mapSvg('.$post->post_content.');</script>';
  }
  $mapsvg_inline_script[] = $script;
  
  //wp_footer('script');
  add_action('wp_footer', 'script', 9999);

  //return //wp_specialchars_decode($data);
  return $data;
}
add_shortcode( 'mapsvg', 'mapsvg_print' );


function script(){
    global $mapsvg_inline_script;
    foreach($mapsvg_inline_script as $m){
        echo $m;
    }
}

function mapsvg_so_handle_038($content) {
    $content = str_replace(array("&#038;","&amp;"), "&", $content); // or $url = $original_url
    return $content;
}
add_filter('the_content', 'mapsvg_so_handle_038', 199, 1);

/**
 * Save map settings as custom type post (post_type = mapsvg)
 */
function mapsvg_save( $data ){
    global $wpdb;

    // Check nonce
    check_ajax_referer('ajax_mapsvg_save-'.$_POST['data']['map_id']);
    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $data_js   = stripslashes($data['mapsvg_data']);

    $postarr = array(
    	'post_type'    => 'mapsvg',
    	'post_status'  => 'publish'
    );

    if(isset($data['title'])){
        $postarr['post_title'] = strip_tags(stripslashes($data['title']));
    }else{
        $postarr['post_title'] = "New Map";
    }

      $postarr['post_content'] = $data_js;

    if(isset($data['map_id']) && $data['map_id']!='new'){
        $postarr['ID'] = (int)$data['map_id'];
        // PREPARE STATEMENT AND PUT INTO DB
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $postarr['ID']))
        );
        update_post_meta($postarr['ID'], 'mapsvg_version', MAPSVG_VERSION);
        $post_id = $postarr['ID'];
    }else{
        $post_id = wp_insert_post( $postarr );
        // PREPARE STATEMENT AND PUT INTO DB
        $wpdb->query(
            $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID = %d", array($postarr['post_title'], $postarr['post_content'], $post_id))
        );
        add_post_meta($post_id, 'mapsvg_version', MAPSVG_VERSION);
    }

    return $post_id;
}

function mapsvg_delete($id, $ajax){

    $id = (int)$_POST['id'];
    // Check nonce
    check_ajax_referer( 'ajax_mapsvg_delete-'.$id);
    // Check user rights
    if(!current_user_can('delete_posts'))
        die();


    wp_delete_post($id);
    delete_post_meta($id, 'mapsvg_version');
    if(!$ajax)
        wp_redirect(admin_url('plugins.php?page=mapsvg-config'));
}

function mapsvg_copy($id, $new_title){
    global $wpdb;

    // Check nonce
    check_ajax_referer( 'ajax_mapsvg_copy-'.$_POST['id']);
    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $post = &mapsvg_get_map($id);

    $copy_post = array(
    	'post_type'    => 'mapsvg',
    	'post_status'  => 'publish'
    );

    $new_title = stripslashes(strip_tags($new_title));
    $post_content = $post->post_content;

    $new_id = wp_insert_post($copy_post);

    $wpdb->query(
        $wpdb->prepare("update $wpdb->posts set post_title=%s, post_content=%s WHERE ID=%d", array($new_title, $post_content, $new_id))
    );

    $version = get_post_meta($id, 'mapsvg_version', true);
    add_post_meta($new_id, 'mapsvg_version', $version);
    return $new_id;
}


/**
 * Remove empty elements from an array
 */
function mapsvg_remove_empty($arr){
    foreach ($arr as $id=>$a){
        if(is_array($a)){
            $arr[$id] = mapsvg_remove_empty($a);
            if(count($arr[$id])==0) unset($arr[$id]);
        }else{
            if($arr[$id] == '') unset($arr[$id]);
        }
    }
    return $arr;
}

/**
 * Read JS map settings from DB
 */
function mapsvg_get_map($id, $format = 'object'){
    global $wpdb;

    $res = $wpdb->get_results(
        $wpdb->prepare("select * from $wpdb->posts WHERE ID = %d", (int)$id)
    );
    $res = $res[0] ? $res[0] : array();
    return $format == 'object' ? $res : json_encode($res);
}

/**
 * Settings page in Admin Panel
 */
function mapsvg_conf(){
    global $mapsvg_page;

    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $file       = null;
    $map_chosen = false;
    $svg_file_url = "";
    if (isset($_GET['map']))
        $svg_file_url = $_GET['map'];

    if(isset($_POST['upload_svg']) && $_FILES['svg_file']['tmp_name']){
        check_admin_referer( 'upload_map' );

        if(!file_exists(MAPSVG_MAPS_UPLOADS_DIR)){
            if(!wp_mkdir_p(MAPSVG_MAPS_UPLOADS_DIR))
                $mapsvg_error = "Unable to create directory ".MAPSVG_MAPS_UPLOADS_DIR.". Is its parent directory writable by the server?";
        }else{
            if(!wp_is_writable(MAPSVG_MAPS_UPLOADS_DIR))
                $mapsvg_error = MAPSVG_MAPS_UPLOADS_DIR." is not writable. Please change folder permissions to 777.";
        }


        $filename = sanitize_file_name(basename($_FILES["svg_file"]["name"]));
        $target_file = MAPSVG_MAPS_UPLOADS_DIR . "/".$filename;
        
        $file_parts = pathinfo($_FILES['svg_file']['name']);
                
        if(strtolower($file_parts['extension'])!='svg'){
            $mapsvg_error = 'Wrong file format ('.$file_parts['extension'].'). Only SVG files are compatible with the plugin.';
        }else{   
            if (@move_uploaded_file($_FILES["svg_file"]["tmp_name"], $target_file)) {
                    $mapsvg_notice = "The file ". $filename. " has been uploaded.";
            
                    $svg_file_url = MAPSVG_MAPS_UPLOADS_URL.$filename;
            } else {
                    $mapsvg_error = "An error occured during upload of your file. Please check that ".MAPSVG_MAPS_UPLOADS_DIR." folder exists and it has full permissions (777).";
            }            
        }     
    }
    
    // If $_GET['map_id'] is set then we should get map's settings and from DB
    $map_id = isset($_GET['map_id']) ? $_GET['map_id'] : 'new';

    $js_mapsvg_options = "";
    if($map_id && $map_id!='new'){
        $post = mapsvg_get_map($map_id);
        $js_mapsvg_options = $post->post_content;
        $mapsvg_version = get_post_meta((int)$map_id, 'mapsvg_version');
    }


    $title = "";
    if($svg_file_url || ($map_id && $map_id!='new')){

        $mapsvg_page = 'edit';

        $title = isset($post) && $post->post_title ? $post->post_title : "New map";

        if ($js_mapsvg_options == "" && $svg_file_url!="")
            $js_mapsvg_options = json_encode(array('source' => $svg_file_url));

        // Load pin images
        $pin_files = @scandir(MAPSVG_PINS_DIR);
        if($pin_files){
            array_shift($pin_files);
            array_shift($pin_files);
        }

        $safeMarkerImagesURL = safeURL(MAPSVG_PINS_URL);
        $markerImages = array();
        $allowed =  array('gif','png' ,'jpg','svg','jpeg');
        foreach($pin_files as $p){
            $ext = pathinfo($p, PATHINFO_EXTENSION);
            if(in_array($ext,$allowed) )
                $markerImages[] = array("url"=>$safeMarkerImagesURL.$p, "file"=>$p);
        }
    }else{
        $mapsvg_page = 'index';
        // Load list of available maps from MAPSVG_MAPS_DIR

        $maps = array();
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_DIR)) as $filename)
        {
            if(strpos($filename,'.svg')!==false){
                $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_DIR,'',$filename)),'/');
                $maps[] = array(
                    "url" => MAPSVG_MAPS_URL . $path_s,
                    "path" => $path_s
                );
            }
        }
        if(is_dir(MAPSVG_MAPS_UPLOADS_DIR)){
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(MAPSVG_MAPS_UPLOADS_DIR)) as $filename)
            {
                if(strpos($filename,'.svg')!==false){
                    $path_s = ltrim(str_replace('\\','/',str_replace(MAPSVG_MAPS_UPLOADS_DIR,'',$filename)),'/');

                    $maps[] = array(
                        "url" => MAPSVG_MAPS_UPLOADS_URL.$path_s,
                        "path" => 'user-uploads/'.$path_s
                    );
                }
            }
        }

        if(isset($_GET['mapsvg_rollback'])){
            rollBack();
        }

        $generated_maps = get_posts(array('numberposts'=>999, 'post_type'=>'mapsvg'));

        $outdated_maps = getOutdated();
        $num = count($outdated_maps);
        if($num>0){
            // do update
            $num_updated = updateOutdatedMaps($outdated_maps);
            if ($num == 1 && $num_updated = 1)
                $mapsvg_notice = "There was 1 outdated map created in old version of MapSVG. The map was successfully updated.";
            elseif ($num == $num_updated)
                $mapsvg_notice = "There were ".$num." outdated maps created in old versions of MapSVG. All maps were successfully updated.";
            elseif ($num_updated == 0)
                $mapsvg_notice = "An error occured during update of your maps created in previous versions of MapSVG plugin. Please contact MapSVG support to get help.";
            elseif ($num != $num_updated)
                $mapsvg_notice = "There were ".$num." outdated maps created in old versions of MapSVG - and ".$num_updated." were successfully updated.";

        }

    }

    $template = 'template_'.$mapsvg_page.'.inc';

    include(MAPSVG_PLUGIN_DIR.'/header.inc');
    include(MAPSVG_PLUGIN_DIR.'/'.$template);
    if($template = '/template_edit.inc')
        include (MAPSVG_PLUGIN_DIR.'/template_handlebars.hbs');
    include(MAPSVG_PLUGIN_DIR.'/footer.inc');

    return true;
}


function ajax_mapsvg_save() {
    if(isset($_POST['data']))
        echo $post_id = mapsvg_save($_POST['data']);
	die();
}
add_action('wp_ajax_mapsvg_save', 'ajax_mapsvg_save');

function ajax_mapsvg_delete() {
    if(isset($_POST['id']))
        mapsvg_delete($_POST['id'], true);
	die();
}
add_action('wp_ajax_mapsvg_delete', 'ajax_mapsvg_delete');

function ajax_mapsvg_copy() {
    if(!empty($_POST['id']) && !empty($_POST['new_name']))
        echo mapsvg_copy($_POST['id'], $_POST['new_name']);
	die();
}
add_action('wp_ajax_mapsvg_copy', 'ajax_mapsvg_copy');

function mapsvg_get() {
    if(!current_user_can('read_posts'))
        die();

    if(isset($_POST['id'])){
        $post = mapsvg_get_map($_POST['id']);
        if ($post->post_type!='mapsvg'){
            echo 'Post type must be "mapsvg"';
            die();
        }
        
        $mapsvg_options = $post->post_content;
    }
        echo $mapsvg_options;

	die();
}
add_action('wp_ajax_mapsvg_get', 'mapsvg_get');
add_action( 'wp_ajax_nopriv_mapsvg_get', 'mapsvg_get' ); 


/**
 *  Register mapSVG post type
 */
function reg_mapsvg_post_type(){
    $post_args = array(
        'labels' => array(
            'name' => 'MapSVG',
            'singular_name' => 'mapSVG map'),
        'description' => 'Allows you to insert a map to any page of your website',
        'public' => false,
        'show_ui' => false,
        'exclude_from_search' => true,
        'can_export' => true
    );

    register_post_type('mapsvg', $post_args);
}
add_action('init','reg_mapsvg_post_type');

function cleanArray($arr){
    foreach($arr as $k=>$v) {
        if(is_array($v))
            $arr[$k] = cleanArray($v);
        else
            $arr[$k] = trim(htmlspecialchars(strip_tags($v)));
    }
    return $arr;
}


function mapsvg_ajaxurl() {
    ?>
        <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
        </script>
    <?php
}
add_action('wp_head','mapsvg_ajaxurl');

function mapsvg_get_maps () {
//    $data = get_posts(array('numberposts'=>999, 'post_type'=>'mapsvg');
//    echo json_encode($data);
    $args = array( 'post_type' => 'mapsvg');
    $loop = new WP_Query( $args );
    $array = array();

    while ( $loop->have_posts() ) : $loop->the_post();

        $array[] = array(
            'id' => get_the_ID(),
            'title' => get_the_title()
        );

    endwhile;

    wp_reset_query();
    ob_clean();
    echo json_encode($array);
    die();
}

add_action ( 'wp_ajax_mapsvg_get_maps', 'mapsvg_get_maps' );

function safeURL($url){
    if(strpos("http://",$url) == 0 || strpos("https://",$url) == 0)
        $url = "//".array_pop(explode("://", $url));
    return $url;
}

function getOldOptions(){
    global $wpdb;

    $r = $wpdb->get_results("
        SELECT meta_value FROM ".$wpdb->postmeta." WHERE meta_key = 'mapsvg_options'
    ");
    foreach ( $r as $other_version ){
        ?>
            <script type="text/javascript">
                console.log(<?php json_encode($other_version)?>);
            </script>
        <?php
    }


}

function getOutdated(){
    global $wpdb;

    $r = $wpdb->get_results("
        SELECT t.pid as id, t.ver as version FROM (SELECT p.ID as pid, pm.meta_value as ver FROM ".$wpdb->posts." p
        LEFT JOIN ".$wpdb->postmeta." pm ON pm.post_id = p.ID AND pm.meta_key = 'mapsvg_version'
        WHERE p.post_type='mapsvg') t WHERE t.ver != '".MAPSVG_VERSION."' OR t.ver IS NULL
    ");

    $maps_outdated = array();

    if($r)
        foreach ( $r as $other_version ){
            if($other_version->version == null || version_compare($other_version->version, '2.0.0', '<')){
                $maps_outdated[$other_version->id] = $other_version->version ? $other_version->version : '1.6.4' ;
            }
        }


    return $maps_outdated;
}

function updateOutdatedMaps($maps){
    $i = 0;
    if($maps)
        foreach($maps as $id=>$version){
            if($version == null || version_compare($version,'2.0.0','<'))
                if(updateMapTo2($id))
                    $i++;
        }
    return $i;
}

function updateMapTo2($id){
    $d = get_post_meta($id,'mapsvg_options');
    if($d && isset($d[0]['m']))
        $data = $d[0]['m'];
    else
        return false;

    $events = array();
    if(isset($d[0]['events']))
        foreach($d[0]['events'] as $key=>$val)
            if(!empty($val))
                $events[$key] = $val;


    if(isset($data['pan'])){
        // do
        $data['scroll'] = array('on'=>($data['pan']=="1"));
        unset($data['pan']);
    }


    if(isset($data['zoom'])){
        $data['zoom'] = array('on'=>($data['zoom']=="1"));
    }else{
        $data['zoom'] = array();
    }

    if(isset($data['zoomButtons'])){
        $data['zoom']['buttons'] = array('location'=>$data['zoomButtons']['location']);
        unset($data['zoomButtons']);
    }
    if(isset($data['zoomLimit'])){
        $data['zoom']['limit'] = $data['zoomLimit'];
        unset($data['zoomLimit']);
    }
    if(isset($data['zoomDelta'])){
        unset($data['zoomDelta']);
    }
    if(isset($data['popover'])){
        unset($data['popover']);
    }

    if(isset($data['tooltipsMode'])){
        $data['tooltips'] = array('mode'=>($data['tooltipsMode']=='names'?'id':'off'));
        unset($data['tooltipsMode']);
    }

    if(isset($data['regions'])){
        if(count($data['regions'])>0){
            foreach($data['regions'] as &$r){
                if(isset($r['attr'])){
                    foreach($r['attr'] as $key=>$value){
                        if(!empty($value))
                            $r[$key] = $value;
                    }
                    unset($r['attr']);
                }
            }
        }
    }

    if(isset($data['marks'])){
        if(count($data['marks'])>0){
            $data['markers'] = $data['marks'];
            $inc = 0;
            foreach($data['markers'] as &$m){
                $m['id'] = 'marker_'.$inc;
                $inc++;
                if(isset($m['attrs'])){
                    foreach($m['attrs'] as $key=>$value){
                        if(!empty($value))
                            $m[$key] = $value;
                    }
                    unset($m['attrs']);
                }
            }
        }
        unset($data['marks']);
    }

    $data = json_encode($data);
    // We should add events to options separately as they
    // shouldn't be enclosed with quotes by json_encode
    $str = array();
    if(!empty($events)){
        foreach($events as $e=>$func)
            $str[] = $e.':'.stripslashes_deep($func);
        $events = implode(',',$str);

        $data = substr($data,0,-1).','.$events.'}';
    }

//        $data = str_replace("'","\'",$data);
    $data = addslashes($data);

//    delete_post_meta($id, 'mapsvg_options');
    mapsvg_save(array('map_id'=>$id, 'mapsvg_data'=>$data));

    return true;
}

function rollBack(){
    global $wpdb;

    // Check user rights
    if(!current_user_can('edit_posts'))
        die();

    $res = $wpdb->get_results("
        SELECT post_id, meta_value FROM ".$wpdb->postmeta." WHERE meta_key = 'mapsvg_options'
    ");
    foreach ( $res as $r ){
        delete_post_meta($r->post_id, 'mapsvg_version');
    }
}

?>