<?php
	ob_start();
	
	define('WP_USE_THEMES', false);
	if ( !function_exists( 'get_home_path' ) ) {
		require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
	}
	
	// Get Project info from query string.
	$project_id 	= $_GET['projectid'];
	$project_name 	= $_GET['project_name'];
	
	// Get Task info from query string.
	$task_id 		= $_GET['taskid'];
	$task_name 		= $_GET['task_name'];
	
	// Get referer from query string.
	$referer    	= $_GET['referer'];
	
	
	// Start get the category posts (specific to project)
	$cat_posts = get_posts(array(
		'category' => $project_id,
		'numberposts' => -1
	));
	
	$parent_ids = array_map('get_post_ids', $cat_posts);
	function get_post_ids($post_obj) {
		return isset($post_obj->ID) ? $post_obj->ID : false;
	}
	// End get the category posts (specific to project)
	
	//////////////////////////////////////////////////////	
	// If downloading the project files
	if($referer == "project") {
		$download_file_name = $project_name;
		
		// Attachments as array
		$the_attachments = get_posts(array(
			'post_parent__in' => $parent_ids,
			'numberposts' => -1,
			'post_type' => 'attachment'
		));
	}
	// End if downloading the project files
	
	
	//////////////////////////////////////////////////////	
	// If downloading the task files
	if($referer == "task") {
		$download_file_name = $task_name;
		
		// Get post attachments
		$the_attachments = get_posts( array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_parent' => $task_id
		) );
	} // End if downloading the task files
	if ($the_attachments) {
		// Ignore user abort in order to delete the zip file even if the user cancels the download
		ignore_user_abort(true);
		$upload_dir = wp_upload_dir();
		$taskrocket_dir = $upload_dir['basedir'] . '/';
		$zipname = $download_file_name . '.zip';
		$zippath = $taskrocket_dir . $zipname;
		$zip = new ZipArchive;
		$zip->open($zippath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
		foreach ($the_attachments as $attachment) {
			$file = get_attached_file($attachment->ID);
			$zip->addFile($file, basename($file));
		}
		$zip->close();
		
		ob_end_clean();
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$zipname);
		header('Content-Length: ' . filesize($zippath));
		readfile($zippath);
		unlink($zippath);
	} else {
		header("Location: " . $_SERVER['HTTP_REFERER'] . "?downloads=0");
	}
?>