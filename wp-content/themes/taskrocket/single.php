<?php
wp_get_current_user();
if (is_user_logged_in()) $current_user = wp_get_current_user();

$category = get_cat_id( single_cat_title("",false) );

if( get_post_meta($post->ID, 'private', TRUE) == 'yes' && (!current_user_can( 'manage_options' ) ) ) {
	echo '<div style="padding:20px;text-align:center; font-family:arial; font-size:14px; color:#606777; background:#f3f3e7; width:400px; position: absolute; top: 50%; transform: translateY(-50%); left: calc(50% - 200px); border: solid 1px #eaeade; border-radius: 2px;">' . __( "This is a private task owned by an administrator", "taskrocket" ) . '.</div>';
	exit;
}

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	if (!$hasError) {

		$postTitle 				= trim($_POST['title']);
		$postContent 			= $_POST["minfo"];
		$startdate 				= $_POST["startdate"];
		$duedate 				= $_POST["duedate"];
		$priority 				= $_POST["priority"];
		$previousowner 			= $_POST["previousowner"];
		$previousownerID 		= $_POST["previousownerID"];
		$project_contributor 	= $_POST["project_contributor"];
		$private 				= $_POST["private"];
		$jobNumber 				= $_POST["job_number_task"];
		$tr_status 				= $_POST["tr_status"];
		$tr_relation 			= $_POST["relation"];
		$tr_related 			= $_POST["related"];


		// LOG TIME
		$logtime 		= get_post_meta($post->ID, 'logtime', TRUE);
		$addedlogtime 	= $_POST["logtime"];
		$logtotal 		= $logtime + $addedlogtime;


		$post_id = get_the_ID();
		$update_post['ID'] = $post_id;

		$template_dir = get_template_directory_uri();
		$update_post = array();
		$update_post['post_author'] 		= $project_contributor;
		$update_post['post_status'] 		= 'publish';
		$update_post['post_title'] 			= $postTitle;
		$update_post['post_content'] 		= $postContent;
		$update_post['previousowner'] 		= $previousowner;
		$update_post['previousownerID'] 	= $previousownerID;
		$update_post['logtime'] 			= $logtime;
		$update_post['filter'] 				= true;
		$update_post['post_name'] 			= str_replace(' ', '-', ''); // Need to redirect to category or updated permalink
		wp_update_post( $update_post);

		// Update the custom fields
		update_post_meta( $post_id, 'startdate', $startdate);
		update_post_meta( $post_id, 'duedate', $duedate);
		update_post_meta( $post_id, 'private', $private);
		update_post_meta( $post_id, 'priority', $priority);
		update_post_meta( $post_id, 'minfo', $postContent);
		update_post_meta( $post_id, 'previousowner', $previousowner);
		update_post_meta( $post_id, 'previousownerID', $previousownerID);
		update_post_meta( $post_id, 'logtime', $logtotal);
		update_post_meta( $post_id, 'job_number_task', $jobNumber);
		update_post_meta( $post_id, 'tr_status', $tr_status);
		update_post_meta( $post_id, 'relation', $tr_relation);
		update_post_meta( $post_id, 'related', $tr_related);
		update_post_meta( $post_id, '_updated', 'yes');

		// Update the category
		wp_set_object_terms( $post_id, intval( $_POST['categoryID'] ), 'category', false );

		// Upload file(s)
		if ( $_FILES ) {
		$files = $_FILES["tr_multiple_attachments"];
		foreach ($files['name'] as $key => $value) {
				$pid = $post_id;
				if ($files['name'][$key]) {
					$file = array(
						'name' 		=> $files['name'][$key],
						'type' 		=> $files['type'][$key],
						'tmp_name' 	=> $files['tmp_name'][$key],
						'error'		=> $files['error'][$key],
						'size' 		=> $files['size'][$key]
					);
					$_FILES = array ("tr_multiple_attachments" => $file);
					foreach ($_FILES as $file => $array) {
						$newupload = tr_handle_attachment($file,$pid);
					}
				}
			}
		}

		require_once('post-logic.php');
		wp_redirect( get_permalink( $post_id ) );exit;
	}
}
get_header();
wp_enqueue_script( 'comment-reply' );

// If the user role is 'client' and the client plug-in is not activated,
// then redirect to the client page.
if( current_user_can('client') && !is_plugin_active( 'taskrocket-clients/taskrocket-clients.php' ) ) {
	header('Location: '.home_url().'/client');
	exit();
}
$options = get_option( 'taskrocket_settings' );

global $user_ID;
$user_ID = get_current_user_id();
$categories = get_the_category();
$category_id = $categories[0]->cat_ID;
// If project manager can modify tasks
if ($options['pm_modify_tasks'] == true) {
	$project_manager = get_option( 'tr_project_manager_' . $category_id );
} else {
	$project_manager = 0;
}

$date_format = get_option('date_format');

// Convert old date format to new date format
$olddateformat = get_post_meta($post->ID, 'duedate', TRUE);
$newdateformat = new DateTime($olddateformat);
 

$datetime = strtotime( $olddateformat ); 		   // Convert to + seconds
$yesterday = strtotime("-1 days");				   // Convert today -1 day to seconds

$related_ID = get_post_meta($post->ID, 'related', TRUE);
$related_title = get_the_title($related_ID);
$related_URL = get_the_permalink($related_ID);
$elaboration = get_post_meta($post->ID, 'elaboration', TRUE);

// Get the TASK status of the related task
$related_tasks_status = get_post_meta( $related_ID, 'tr_status', TRUE ); 

// Get the POST status of the related task 
$related_post_status  = get_post_status( $related_ID );

if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to") {
	$relation = __( "Relates to", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with") { 
	$relation = __( "Has issues with", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") { 
	$relation = __( "Is blocked by", "taskrocket" );
} else if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to") { 
	$relation = __( "Is similar to", "taskrocket" );
}

?>

<div class="content task-solo">
	<div class="container">
		
		<?php if($olddateformat) {
		if ( $datetime >= $yesterday ) { ?>
		<?php } else { ?>
			<?php if(get_post_meta($post->ID, 'tr_status', TRUE) !== "complete") { ?>
				<div class="message urgent">
					<p><?php _e( "This task is overdue:", "taskrocket" ); ?> <?php echo $newdateformat->format($date_format); ?></p>
					<span class="close"></span>
				</div>
			<?php } ?>
		<?php } } ?>
		
	    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
			
		<?php if ($post_id || get_post_meta($post->ID, '_updated', TRUE) == 'yes') {
	    ?>
	    <div class="message success">
	    	<p><?php echo get_the_title(); ?> <?php _e( "was updated", "taskrocket" ); ?>.</p>
	        <span class="close"></span>
	    </div>
	    <?php }
	    	update_post_meta( $post->ID, '_updated', 'false' ) ;
	    ?>
		
		<h1>
			<?php if ( get_post_status ( $post->ID ) !== 'trash' ) {
				 echo get_the_title();
			} else {
				echo "<em>" . get_the_title() . "</em>";
			}?>
		</h1>
		<?php if($related_tasks_status !== 'complete' && $related_post_status !== 'trash') { ?>
			<?php if(get_post_meta($post->ID, 'relation', TRUE)) { ?>
				<p class="message relate">
					<?php _e( "This task", "taskrocket" ); ?> <strong class="emphasis"><?php echo $relation  . '</strong> <a href="' . $related_URL . '">' .  $related_title . '</a>'; ?>
					<?php if($elaboration) { ?>
						<em><?php echo $elaboration; ?></em>
					<?php } ?>
				</p>
			<?php } ?>
		<?php } ?>
		
		<?php if($_GET['taken'] == "yes") { ?>
			<div class="message normal">
		    	<p><?php _e( "You are now the owner of this task.", "taskrocket" ); ?></p>
		        <span class="close"></span>
		    </div>
		<?php } ?>
		
		<?php if($_GET['taken'] == "failed") { ?>
			<div class="message urgent">
		    	<p><?php _e( "Either you can't take ownership of this task, or someone took ownership of it before you.", "taskrocket" ); ?></p>
		        <span class="close"></span>
		    </div>
		<?php } ?>
		
		<?php if($_GET['following'] == "yes") { ?>
			<div class="message normal">
		    	<p><?php _e( "You are now following this task.", "taskrocket" ); ?></p>
		        <span class="close"></span>
		    </div>
		<?php } ?>
		
		<?php if($_GET['following'] == "no") { ?>
			<div class="message low">
		    	<p><?php _e( "You are no longer following this task.", "taskrocket" ); ?></p>
		        <span class="close"></span>
		    </div>
		<?php } ?>
		
		<!--/ Start Task Details Main Body /-->	    
		<div class="task-details-main-body">
			
			<?php if ($options['users_edit_tasks'] == true || $options['clients_edit_tasks'] == true || current_user_can( 'manage_options' )) { ?>
			<!--/ Start Edit Task /-->
			<div class="task-edit">
			
			    <script>
			    // Count remaining chars
			    jQuery(function($) {
			        var max = <?php echo $titlecharcount; ?>;
			        $('#title').keyup(function() {
			            if($(this).val().length > max) {
			                $(this).val($(this).val().substr(0, max));
			            }
			            $('#title-chars').html((max - $(this).val().length) + ' characters left');
			        });
			    });
			    </script>
				
			    <form action="" id="new_post" name="taskForm" method="post" enctype="multipart/form-data">
			
			        <!--/ Start Fleft /-->
			        <div class="fleft">
			
			            <!--/ Start Task Name /-->
			            <div class="section task-name <?php if ( !current_user_can( 'manage_options' ) ) { ?>not-an-admin<?php } ?>">
			                <label for="title"><?php _e( "Task name", "taskrocket" ); ?></label>
			                <em id="title-chars" class="chars"></em>
			                <input type="text" id="title" maxlength="<?php echo $titlecharcount; ?>" name="title" class="text " value="<?php if ($postTitle) echo $postTitle; else the_title(); ?>" required />
			            </div>
			            <!--/ End Task Name /-->
			
			            <!--/ Start Private /-->
			            <?php if ( current_user_can( 'manage_options' ) ) { ?>
			            <div class="section is-private">
			                <label for="private"><?php _e( "Private", "taskrocket" ); ?>
			                <input type="checkbox" id="private" name="private" value="yes" <?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { echo ' checked'; } ?> />
			                </label>
			            </div>
			            <?php } ?>
			            <!--/ End Private /-->
			

		                <?php
		                // The logic: If not a client, you can change the project...
		                if(!current_user_can( 'client' )) { ?>
							<!--/ Start Halves /-->
				            <div class="section halves">
		
			                <!--/ Start Project /-->
			                <div class="half-container">
			                    <label for="parent">Project</label>
								<select name="categoryID" id="categoryID" required>
									<option></option>
									<?php 
									$args = array(
										'orderby'            => 'name',
										'order'              => 'ASC',
										'hide_empty'		 => 0,
										'selected'           => get_category( $cat->cat_ID )
										);
										$categories = get_categories($args);
										foreach ($categories as $cat) : 
											$project = get_category( $cat->cat_ID ); 
											$project_archived = get_option( 'tr_project_archived_' . $cat->cat_ID );
											if (!$project_archived) { ?>
											<option value="<?php echo $cat->cat_ID;?>" <?php if($cat->cat_ID == $category_id) { echo 'selected="selected"'; } ?>><?php echo $cat->cat_name;?></option>
											<?php
											}
										endforeach;
									?>
								</select>
			                </div>
			                <!--/ End Project /-->
			
			                <!--/ Start Reassign /-->    
			                <div class="half-container">
			                    <?php 
			                    // If users can reassign tasks and or if an administrator
			                    if ($options['users_reassign_tasks'] == true || current_user_can( 'manage_options' ) || $user_ID == $project_manager) { ?>
									
			                    <label for="reassign" class="notify-label">
			                    <input type="checkbox" id="reassign" name="reassign" value="yes" /> <?php _e( "Reassign this task?", "taskrocket" ); ?>
			                    </label>
								
								<span class="task-owner">
									<?php 
					                if (get_the_author_meta( 'first_name') !== "" ) {
					                    echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
					                } else {
					                    echo $GLOBALS[ 'nameless' ];
					                }
					                ?>
								</span>
								
								<div class="reassign-all-users-list">
				                    <select name="project_contributor" id="project_contributor">
				                        <?php if ($options['allow_unowned_tasks'] == true) { ?>
				                        <option id="0000000" value="0000000"><?php _e( "Nobody", "taskrocket" ); ?></option>
				                        <?php } ?>
				
				                        <?php
				                        $trusers = get_users('blog_id=1&orderby=nicename');
				                        foreach ($trusers as $user) { ?>
				                        <option <?php if ($user->ID == get_the_author_meta( 'ID' )) echo 'selected';?> value="<?php echo $user->ID; ?>" id="<?php echo $user->ID; ?>">
				                        <?php if ($user->first_name !== "") {
				                        echo $user->first_name . " " . $user->last_name;
				                            } else {
				                        echo $GLOBALS[ 'nameless' ];
				                        } ?> 
										(<?php echo $user->user_email; ?>)</option>
				                        <?php
				                            }
				                        ?>
				                    </select>
								</div>
			
				                <?php } else { ?>
									<span class="task-owner">
										<?php 
						                if (get_the_author_meta( 'first_name') !== "" ) {
						                    echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
						                } else {
						                    echo $GLOBALS[ 'nameless' ];
						                }
						                ?>
									</span>
				                    <input type="hidden" name="categoryID" id="categoryID" value="<?php $categories = get_the_category(); $category_id = $categories[0]->cat_ID; echo $category_id; ?>" />
				                <?php } ?>
			
			                </div>
			                <!--/ End Reassign /--> 
							
							
						</div>
						<!--/ End Halves /-->
						<?php } // End logic. ?>
							
		
			
			            <!--/ Start Thirds /-->
			            <div class="section thirds priority-and-dates">
			
			                <div class="third-container priority">
			                    <label><?php _e( "Priority", "taskrocket" ); ?></label>
			                    <label class="radio new-task-priority-low <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'low' ) { echo "checked";}?>"><input type="radio" name="priority" value="low" id="low" <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'low' ) { echo "checked";}?>><?php _e( "Low", "taskrocket" ); ?></label>
			                    <label class="radio new-task-priority-normal <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'normal' ) { echo "checked";}?>"><input type="radio" name="priority" value="normal" id="normal" <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'normal' ) { echo "checked";}?>><?php _e( "Normal", "taskrocket" ); ?></label>
			                    <label class="radio new-task-priority-high <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'high' ) { echo "checked";}?>"><input type="radio" name="priority" value="high" id="high" <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'high' ) { echo "checked";}?>><?php _e( "High", "taskrocket" ); ?></label>
			                    <label class="radio new-task-priority-urgent <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'urgent' ) { echo "checked";}?>"><input type="radio" name="priority" value="urgent" id="urgent" <?php if(  get_post_meta($post->ID, 'priority', TRUE) == 'urgent' ) { echo "checked";}?>><?php _e( "Urgent", "taskrocket" ); ?></label>
			                </div>
			
			                <div class="third-container start-date">
			                    <label for="duedate"><?php _e( "Start date", "taskrocket" ); ?></label>
			                    <input type="text" class="text date" id="startdate" name="startdate" value="<?php if ($startdate) echo $startdate; else echo get_post_meta($post->ID, 'startdate', TRUE); ?>" /> <em class="clear-field clear-start-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
			                </div>
			
			                <div class="third-container end-date">
			                    <label for="duedate"><?php _e( "Due Date", "taskrocket" ); ?></label>
			                    <input type="text" class="text date" id="duedate" name="duedate" value="<?php if ($duedate) echo $duedate; else echo get_post_meta($post->ID, 'duedate', TRUE); ?>" /> <em class="clear-field clear-end-date-field"><?php _e( "Clear", "taskrocket" ); ?></em>
			                </div>
			
			            </div>
			            <!--/ End Thirds /-->
						
						<div class="section status">
							<?php
								// Pretty names for task status alt attributes
								if(get_post_meta($post->ID, 'tr_status', TRUE) == "incomplete") {
									$task_status =  __( "Incomplete", "taskrocket" );
								}
								if(get_post_meta($post->ID, 'tr_status', TRUE) == "complete") {
									$task_status =  __( "Complete", "taskrocket" );
								} 
								if(get_post_meta($post->ID, 'tr_status', TRUE) == "onhold") {
									$task_status =  __( "On hold", "taskrocket" );
								}
								if(get_post_meta($post->ID, 'tr_status', TRUE) == "inprogress") {
									$task_status = __( "In progress", "taskrocket" );
								} 
							?>
							<label for="status"><?php _e( "Status", "taskrocket" ); ?></label>
							
							<label class="task-radio task-status-complete <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'complete' ) { echo "checked";}?>"><input type="radio" name="tr_status" value="complete" id="complete" <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'complete' ) { echo "checked";}?>><?php _e( "Complete", "taskrocket" ); ?></label>
							
							<label class="task-radio task-status-incomplete <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'incomplete' ) { echo "checked";}?>"><input type="radio" name="tr_status" value="incomplete" id="incomplete" <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'incomplete' ) { echo "checked";}?>><?php _e( "Incomplete", "taskrocket" ); ?></label>
							
							<label class="task-radio task-status-inprogress <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'inprogress' ) { echo "checked";}?>"><input type="radio" name="tr_status" value="inprogress" id="inprogress" <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'inprogress' ) { echo "checked";}?>><?php _e( "In progress", "taskrocket" ); ?></label>
							
							<label class="task-radio task-status-onhold <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'onhold' ) { echo "checked";}?>"><input type="radio" name="tr_status" value="onhold" id="onhold" <?php if(  get_post_meta($post->ID, 'tr_status', TRUE) == 'onhold' ) { echo "checked";}?>><?php _e( "On hold", "taskrocket" ); ?></label>
							
							<div class="blocked-alert">
								<div>
									<p>
										<?php printf( __('This task is currently blocked by <a href="%1$s">%2$s</a>. Change its status and unblock anyway?', 'taskrocket' ), $related_URL, $related_title ); ?>
									</p>
									<a class="button-small delete-yes"><?php _e( "Yes", "taskrocket" ); ?></a>
									<a class="button-small delete-no"><?php _e( "No", "taskrocket" ); ?></a>
								</div>
							</div>
							
						</div>
						
						<?php // If Task Relations are not disabled
						if ($options['disable_task_relations'] == false) { ?>
							<?php if(!current_user_can( 'client' )) { // if not a client ?>
							<div class="section related-section">
								<label for="block"><?php _e( "This task", "taskrocket" ); ?></label>
								
								<select name="relation" class="relation">
									<option value=""></option>
									<option value="is_blocked_by" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Is blocked by", "taskrocket" ); ?></option>
									<option value="is_similar_to" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_similar_to" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Is similar to", "taskrocket" ); ?></option>
									<option value="has_issues_with" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "has_issues_with" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Has issues with", "taskrocket" ); ?></option>
									<option value="relates_to" <?php if(get_post_meta($post->ID, 'relation', TRUE) == "relates_to" && $related_tasks_status !== 'complete') { echo 'selected="selected"'; } ?>><?php _e( "Relates to", "taskrocket" ); ?></option>
								</select>
								
								<select name="related" class="related" disabled>
									<option value=""></option>
									<?php 
									global $post;
									$args = array( 
										'numberposts'    => -1,
										'orderby' 		 => 'title', 
										'order' 		 => 'ASC',
										'posts_per_page' => -1,
										'post_status' 	 => 'publish',
										'exclude'        => $post->ID,
										'cat'		     => $category_id,
										'meta_key'       => 'tr_status',
										'meta_value'     => array('incomplete', 'inprogress', 'onhold')
									); 
									$posts = get_posts($args); 
									foreach( $posts as $post ) : setup_postdata($post);
									$private = get_post_meta($post->ID, 'private', TRUE);
									if (current_user_can( 'manage_options' ) && $private == 'yes') {
										$view_private = __( "(Private task)", "taskrocket" );
									} else {
										$view_private = "";
									}
									?>
									<?php if (current_user_can( 'manage_options' )) { ?>
										<option value="<?php echo $post->ID; ?>" <?php if($private == 'yes') { echo 'class="is-private"'; } ?> <?php if($related_ID == $post->ID) { echo "selected"; } ?>><?php the_title(); ?> <?php echo $view_private; ?></option>
									<?php } else { ?>
										<?php if(!$private) { ?>
											<option value="<?php echo $post->ID; ?>" <?php if($related_ID == $post->ID) { echo "selected"; } ?>><?php the_title(); ?></option>
										<?php } ?>
									<?php } ?>
									<?php endforeach; wp_reset_query(); ?>
								</select>
								
								<div class="elaboration">
									<label for="block"><?php _e( "Elaboration", "taskrocket" ); ?></label>
									<textarea name="elaboration"><?php if($elaboration) { echo $elaboration; } ?></textarea>
								</div>
								
								<script>
								jQuery('.related').change(function() {
									if (jQuery(this).val() != '') {
										jQuery('.elaboration').slideDown();
									}
									if (jQuery(this).val() === '') {
										jQuery('.elaboration').slideUp();
										jQuery('.relation, .related, textarea').val('');
										jQuery('.related').attr('disabled', 'disabled');
									}
								});
								jQuery('.relation').change(function() {
									if (jQuery(this).val() === '') {
										jQuery('.elaboration').slideUp();
										jQuery('.relation, .related, textarea').val('');
										jQuery('.related').attr('disabled', 'disabled');
									} else {
										jQuery('.related').removeAttr('disabled');
										jQuery('.related').attr('required', 'required');
									}
								});
								<?php if($related_ID) { ?>
									jQuery(".elaboration").css('display', 'block');
									jQuery('.related').removeAttr('disabled');
								<?php } ?>
								
								<?php if($related_post_status == 'trash') { ?>
									jQuery('.relation, .related, textarea').val('');
									jQuery('.related').attr('disabled', 'disabled');
								<?php } ?>
								</script>
								
							</div>
						<?php } ?>
						
			            <!--/ Start Thirds /-->
			            <div class="section thirds logtime-jobnum-attachments">
			
			                <div class="third-container log-time">
			                    <label for="logtime"><?php _e( "Log time", "taskrocket" ); ?></label>
			                    <?php
			                    if (!$options['time_log_increments']) {
			                        $increment = "1";
			                    } else {
			                        $increment = $options['time_log_increments'];
			                    }?>
			                    <input type="number" min="<?php echo $increment; ?>" step="<?php echo $increment; ?>" class="text" id="logtime" name="logtime" value="" />
			                    <em class="clear-field clear-time-field"><?php _e( "Clear", "taskrocket" ); ?></em>
			                    <p class="help-topic"><?php echo $increment; ?> <?php _e( "min incs", "taskrocket" ); ?></p>
			                </div>
							
							<?php if($options['clients_create_job_numbers'] == true || !current_user_can( 'client' )) { ?>
			                <div class="half-container job-number">
			                    <label><?php _e( "Job number", "taskrocket" ); ?></label>
			                    <?php if($options['auto_job_numbers'] == true) { ?>
			                        <input type="text" id="job_number_task" name="job_number_task" value="<?php echo get_post_meta($post->ID, 'job_number_task', TRUE); ?>" />
			                    <?php } else { ?>
			                        <input type="text" class="text job-number" id="job_number_task" name="job_number_task" value="<?php echo get_post_meta($post->ID, 'job_number_task', TRUE); ?>" />
			                    <?php } ?>
			                </div>
							<?php } ?>
			
			                <div class="half-container upload-attachment">
			                    <?php
			                    if ($options['disable_file_uploads'] == false) { ?>
			                        <?php if($options['clients_attachments'] == true || current_user_can( 'manage_options' ) || current_user_can( 'editor' )) { ?>
			                            <label><?php _e( "Attach", "taskrocket" ); ?> <?php if ( $attachments ) { " " . _e( "more files", "taskrocket" ); } ?></label>
			                            <input type="file" name="tr_multiple_attachments[]"  multiple="multiple" />
			                        <?php } ?>				
			                    <?php } ?>
			                </div>
			
			            </div>
			            <!--/ End Thirds /-->
			
			        </div>
			        <!--/ End Fleft /-->
			
			
			        <!--/ Start Fright /-->
			        <div class="fright">
			            <div class="section textarea-right">
			                <label for="minfo"><?php _e( "Additional information", "taskrocket" ); ?></label>
			                <textarea name="minfo" id="minfo" class="text textarea" rows="10" cols="20"><?php echo get_post_meta($post->ID, 'minfo', TRUE); ?></textarea>
			
			                <div class="submit-button-container">
			                    <div>
			                        <input type="submit" name="submitted" class="button submit" value="<?php _e( "Update Task", "taskrocket" ); ?>" />
			                        <img src="<?php echo get_template_directory_uri(); ?>/images/loader.gif" />
			                    </div>
			                </div>
			            </div>
			        </div>
			        <!--/ End Fright /-->
					
					<?php if(get_post_meta($post->ID, 'relation', TRUE) == "is_blocked_by") { 
						$current_status = get_post_meta($post->ID, 'tr_status', TRUE);
					?>
						<script>
							jQuery('#complete, #inprogress').click(function () {
								jQuery('.blocked-alert').fadeIn();
							});
							
							jQuery('.blocked-alert .delete-yes').click(function () {
								jQuery('#complete, #inprogress').prop("checked", true);
								jQuery('.blocked-alert').fadeOut();
								jQuery('.relation, .related').val('');
							});
							jQuery('.blocked-alert .delete-no').click(function () {
								jQuery('#<?php echo $current_status; ?>').prop("checked", true);
								jQuery('.task-status-complete, .task-status-inprogress').removeClass('task-active');
								jQuery('.blocked-alert').fadeOut();
							});
						</script>
						<?php } // End if not a client ?>
					<?php } // fnd If Task Relations are not disabled ?>
					
			        <input type="hidden" name="referer" id="referer" value="new-task-single" />
			
			        <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
			
			        <?php if ($options['send_plain'] == true) { ?>
			        <input type="hidden" name="send_plain" id="send_plain" value="yes" />
			        <?php } ?>
			
			        <input type="hidden" name="previousowner" id="previousowner" value="<?php echo $current_user->user_firstname . " " . $current_user->user_lastname; ?>" />
			        <input type="hidden" name="previousownerID" id="previousownerID" value="<?php echo get_the_author_meta('ID'); ?>" />
			
			        <?php if ($options['no_emails'] == true) { ?>
			        <input type="hidden" name="no_emails" id="no_emails" value="yes" />
			        <?php } ?>
					
					<?php if( current_user_can('client')) { ?>
					<input type="hidden" name="role" id="role" value="client" />
					<?php } ?>
					
					<?php
					$pm_specified = get_option( 'tr_project_manager_' . $category_id);
					$pm_info = get_userdata($pm_specified);
					
					// If there is a project manager, use their email address.
					if($pm_specified == true) { ?>
					<input type="hidden" name="pm_email" id="pm_email" value="<?php echo $pm_info->user_email; ?>" />
					<?php // otherwise, use the default Wordpress admin email address.
					} else { ?>
					<input type="hidden" name="pm_email" id="pm_email" value="<?php echo get_option( 'admin_email' ); ?>" />
					<?php } ?>
			
			        <?php if ($options['show_gravatars'] == true) { ?>
			        <input type="hidden" name="gravatars" id="gravatars" value="yes" />
			        <?php } ?>
					
					<?php if(current_user_can( 'client' )) { ?>
					<input type="hidden" name="categoryID" id="categoryID" value="<?php foreach((get_the_category()) as $category) { echo $category->cat_ID; } ?>" />
					<?php } ?>
			
			        <input type="hidden" name="task-form-type" id="task-form-type" value="task-edit-single" />
					
					<input type="hidden" name="task-link" id="task-link" value="<?php echo get_the_permalink(); ?>" />
			
			    </form>
			</div>
			<!--/ End Edit Task /-->
			<?php } ?>		
			
			<!--/ Start Details Panel /-->	    
			<div class="project-and-task-details">
			
			    <h2><?php _e( "Task Details", "taskrocket" ); ?></h2>
			    <div>
			        <ul>
						
			            <!--/ Start Project Name /-->
			            <li class="row-0">
			                <strong class="project-name"><?php _e( "Project", "taskrocket" ); ?>: </strong> 
							<a href="<?php foreach((get_the_category()) as $category) { echo get_category_link($category->cat_ID); } ?>"><?php echo $category->cat_name; ?></a>
			            </li>
			            <!--/ End Project Name /-->
						
			
			            <!--/ Start Job Number /-->
			            <?php if(get_post_meta($post->ID, 'job_number_task', TRUE)) { ?> 
			            <li>
			                <strong class="job-number"><?php _e( "Job number", "taskrocket" ); ?>: </strong> <?php echo get_post_meta($post->ID, 'job_number_task', TRUE); ?>
			            </li>
			            <?php } else { ?>
			            <li>
			                <strong class="job-number"><?php _e( "Job number", "taskrocket" ); ?>: </strong> -
			            </li>
			            <?php } ?>
			            <!--/ End Job Number /-->
			
			            <!--/ Start priority /-->
			            <li class="priority">
			                <?php if(get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
			                <em class="priority-indicator priority-indicator-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>"></em>
			                <strong><?php _e( "Priority", "taskrocket" ); ?>: </strong>
			                <?php echo get_post_meta($post->ID, 'priority', TRUE); ?>
			            <?php } else { ?>
			                <em class="priority-indicator priority-indicator-normal"></em>
			                <strong><?php _e( "Priority", "taskrocket" ); ?>: </strong>
			                Normal
			            <?php } ?>
			            </li>
			            <!--/ End priority /-->
			
			            <!--/ Start date added /-->
			            <li>
			                <strong><?php _e( "Added", "taskrocket" ); ?>: </strong>
			                <?php echo get_the_time($date_format); ?>
			            </li>
			            <!--/ End date added /-->
			
			            <!--/ Start start date /-->
			            <li>
			                <?php if(get_post_meta($post->ID, 'startdate', TRUE) != '' ) {
			                // Convert old date format to new date format
			                $olddateformat = get_post_meta($post->ID, 'startdate', TRUE);
			                $newdateformat = new DateTime($olddateformat);
			                $date = get_post_meta($post->ID, 'startdate', TRUE); // Pull the value
			            ?>
			                <strong><?php _e( "Start date", "taskrocket" ); ?>: </strong>
			                <?php echo $newdateformat->format($date_format); ?>
			                <?php } else { ?>
			                <strong><?php _e( "Start date", "taskrocket" ); ?>: </strong> - 
			            <?php } ?>
			            </li>
			            <!--/ End start date /-->
			
			            <!--/ Start due date /-->
			            <?php if(get_post_meta($post->ID, 'duedate', TRUE) != '' ) { 
			            // Convert old date format to new date format
			            $olddateformat = get_post_meta($post->ID, 'duedate', TRUE);
			            $newdateformat = new DateTime($olddateformat);
			             
			            $date = get_post_meta($post->ID, 'duedate', TRUE); // Pull the value
			            $datetime = strtotime( $date ); 				   // Convert to + seconds
			            $yesterday = strtotime("-1 days");				   // Convert today -1 day to seconds
			            if ( $datetime >= $yesterday ) { 				   // If date value pulled is today or later, it's overdue
			            $overdue = ' class="notoverdue"';
			            } else if(get_post_meta($post->ID, 'tr_status', TRUE) !== "complete") {
			            $overdue = ' class="overdue"';
			            }
			            ?>
			            <li <?php echo( $overdue ); ?>>
			                <strong><?php _e( "Due Date", "taskrocket" ); ?>: </strong>
			                <?php echo $newdateformat->format($date_format); ?>
			            </li>
			            <?php } else { ?>
			            <li>
			                <strong><?php _e( "Due Date", "taskrocket" ); ?>: </strong> - 
			            </li>
			            <?php } ?>
			            <!--/ End due date /-->
			
			            <!--/ Start task ID /-->
			            <?php if ($options['show_ID'] == true) {
			            $showID = get_the_ID(); ?>
			            <li class="task-id">
			                <strong><?php _e( "ID", "taskrocket" ); ?>: </strong><?php echo $showID; ?>
			            </li>
			            <?php } ?>
			            <!--/ End task ID /-->
			        </ul>
			
			        <ul>
			            <!--/ Start status /-->
			            <li>
			                <strong class="complete"><?php _e( "Status", "taskrocket" ); ?>: </strong>
							<?php echo $task_status; ?>
			            </li>
			            <!--/ End status /-->
			
			
			            <!--/ Start time logged /-->
			            <?php if( !current_user_can('client')) { ?>
			                <li>
			                    <strong><?php _e( "Time spent", "taskrocket" ); ?>: </strong> 
			                    <?php if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) {
			                    // Convert minutes into human readable hours and minutes
			                    $minutes = get_post_meta($post->ID, 'logtime', TRUE);
			                    $hours = floor($minutes / 60);
			                    $min = $minutes - ($hours * 60);
			                    echo $hours . " " . __( "Hours", "taskrocket" )  . " " . $min . " " . __( "mins", "taskrocket" );
			                    ?>
			                <?php } else { ?>
			                - 
			                <?php } ?>
			                </li>
			            <?php } else { ?>
			                <?php if($options['clients_see_task_times'] == true) { ?>
			                <li>
			                    <strong><?php _e( "Time spent", "taskrocket" ); ?>: </strong> 
			                    <?php if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) { ?>
			                    <?php // Convert minutes into human readable hours and minutes
			                    $minutes = get_post_meta($post->ID, 'logtime', TRUE);
			                    $hours = floor($minutes / 60);
			                    $min = $minutes - ($hours * 60);
			                    echo $hours . " " . __( "Hours", "taskrocket" )  . " " . $min . " " . __( "mins", "taskrocket" );
			                    ?>
			                <?php } else { ?>
			                    - 
			                <?php } ?>
			                </li>
			                <?php } ?>
			            <?php } ?>
			            <!--/ End time logged /-->
			
			            <?php // If Attachments
			            $attachments = get_posts( array(
			            'post_type' => 'attachment',
			            'posts_per_page' => -1,
			            'post_parent' => $post->ID,
			            'orderby' => 'title',
			            'order' => 'ASC'
			            ) );
			            ?>
			            <li>
			                <strong><?php _e( "Attachments", "taskrocket" ); ?>: </strong>
			                <?php if ( $attachments ) { 
			                $attach = get_children(array('post_parent'=>$post->ID)); ?>
			                <?php echo $nbImg = count($attach); ?>
			            <?php } else { ?>
			                - 
			            <?php } ?>
			            </li>
			
			            <!--/ Start task cost /-->
			            <?php if(get_post_meta($post->ID, 'logtime', TRUE) >  "0" ) {
			            if($options['show_task_cost'] == true || current_user_can( 'manage_options' )) {
			                $currency_symbol = $options['currency_symbol'];
			            if ($currency_symbol == "") {
			                $currency_symbol = "$";
			            } ?>
			            <li class="task-cost">
			                <?php require($GLOBALS[ 'theme_includes' ] . 'task-cost.php'); ?>
			            </li>
			            <?php } } ?>
			            <!--/ End task cost /-->
			
			            <!--/ Start visibility /-->
			            <li class="visibility">
			                <?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
			                    <strong class="private"><?php _e( "Visibility", "taskrocket" ); ?>: </strong>
			                    <?php _e( "Private", "taskrocket" ); ?>
			                <?php } else { ?>
			                    <strong class="public"><?php _e( "Visibility", "taskrocket" ); ?>: </strong>
			                    <?php _e( "Public", "taskrocket" ); ?>
			                <?php } ?>
			            </li>
			            <!--/ End visibility /-->
			
			            <!--/ Start owner /-->
			            <li class="owner">        
			                <?php 
			                $author_id = get_the_author_meta('ID');
			                $user = get_userdata($author_id);
			                $attachment_id = $user->user_photo;
			                $image_attributes = wp_get_attachment_image_src( $attachment_id );
							$post = get_post($task_ID);
						    $task_author_ID = $post->post_author;
			                //echo $author_id;
			
			                if (get_option('show_avatars')) {
			                    echo get_avatar( $user->ID , '100');
			                } else {
			                    if( $image_attributes ) { ?>
			                        <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>">
			                    <?php } else { ?>
			                        <strong class="no-photo"></strong>
			                    <?php } ?>
			                <?php }?>
			                <strong><?php _e( "Owner", "taskrocket" ); ?>: </strong>
			                <?php 
			                if (get_the_author_meta( 'first_name') !== "" ) {
			                    echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
			                } else {
			                    echo $GLOBALS[ 'nameless' ];
			                }
			                ?>
							<?php if($task_author_ID == "0") { ?>
								<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>" class="claim button-small"><?php _e( "Take Ownership", "taskrocket" ); ?></a>
							<?php } ?>
			            </li>
			            <!--/ End owner /-->
						
						<!--/ Start previous owner /-->
						<?php 
						$previous_owner = get_post_meta($post->ID, 'previousownerID', TRUE); 
						if($previous_owner != $task_author_ID) { ?>
							<li>
								<?php require_once($GLOBALS[ 'theme_includes' ] . 'previous-owner.php'); ?>
							</li>
						<?php } ?>
						<!--/ End previous owner /-->
			        </ul>
			    </div>
			</div>
			<!--/ End Details Panel /-->	
			
			<!--/ Start Additional Info /-->
			<?php if(  get_post_meta($post->ID, 'minfo', TRUE) != '' ) { // If there is content... ?>
			<div class="additional-info">
				<h3><?php _e( "Additional Info", "taskrocket" ); ?></h3>
				<pre>
					<?php // Return string with Links, if condition is met.
					if ($options['disable_make_clickable'] == false) {
						$minfo_string = get_post_meta($post->ID, 'minfo', TRUE);
						echo make_clickable( $minfo_string );
					} else {
						echo get_post_meta($post->ID, 'minfo', TRUE);
					} ?>
				</pre>
			</div>
			<?php } ?>
			<!--/ End Additional Info /-->
			
			<?php require_once($GLOBALS[ 'theme_includes' ] . 'attachments.php'); ?>

		</div>
		<!--/ End Task Details Main Body /-->

		
		<?php endwhile; endif; ?>
				
		<?php if ($options['allow_comments'] == true) { ?>

			<?php if($options['clients_can_comment'] == true || current_user_can( 'manage_options') || current_user_can( 'editor')) { ?>
		    <!--/ Start Comments /-->
		    <div class="comment-area" id="comment-area">
		        <?php comments_template( '/includes/comments.php' ); ?> 
		    </div>
		    <!--/ End Comments /-->
		    <?php } ?>

		<?php } ?>

		</div>
	</div>

		
		<iframe id="deletey" name="deletey" width="0" height="0" frameborder="0"></iframe>
		
<?php get_footer(); ?>