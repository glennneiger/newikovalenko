<?php
$cat_name = get_cat_name($cat_id);

$project_job_num = get_option( 'tr_job_number_' . $cat_id);
$pm_specified = get_option( 'tr_project_manager_' . $cat_id);
$user_info = get_userdata($pm_specified);

$date_format = get_option('date_format');
$old_start_date_format = get_option( 'tr_start_date_' . $cat_id );
$new_start_date_format = new DateTime($old_start_date_format);
$old_end_date_format = get_option( 'tr_end_date_' . $cat_id );
$new_end_date_format = new DateTime($old_end_date_format);

if (!empty($cat_name)) { ?>
	
	<div class="task border-soft <?php echo "row-" . ($i++ % 2); ?> no-image is-a-project <?php if($project_job_num || $pm_specified || $old_start_date_format || $old_end_date_format) { echo "more-padding"; } ?>">
		<?php 
			if ($pm_specified) { 
				if (get_option('show_avatars')) { ?>
				    <?php echo get_avatar( $user_info, 64 ); ?>
				<?php } else { ?>
				    <?php $attachment_id = $user_info->user_photo;
				    $image_attributes = wp_get_attachment_image_src( $attachment_id );
				    if( $image_attributes ) { ?> 
				    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[2]; ?>" height="<?php echo $image_attributes[2]; ?>" class="avatar" />
				    <?php } else { ?>
				        <span class="no-photo avatar"></span>
				    <?php } ?>
				<?php }
			}
		?>
		<h2><a href="<?php echo get_term_link( (int) $cat_id, 'category');?>"><?php echo $cat_name;?></a></h2>
		<p class="project-result"><?php _e( "Project", "taskrocket" ); ?></p>

		<?php if($project_job_num || $pm_specified || $old_start_date_format || $old_end_date_format) { ?>
		
		<p class="task-details">

			<?php 
			if($project_job_num) { ?>
			<span class="job-number master-tooltip" title="Job Number">
				<?php echo get_option( 'tr_job_number_' . $cat_id); ?>
			</span>
			<?php } ?>

			<?php 
				if ($pm_specified) { 
			?>
			<span class="master-tooltip" title="<?php _e( "Project Manager", "taskrocket" ); ?>">
				<?php /* translators: PM is short for Project Manager */ 
				_e( "PM", "taskrocket" ); ?>: <?php echo $user_info->user_firstname . " " . $user_info->user_lastname; ?>
			</span>
			<?php } ?>
			
			<?php 
				if ($old_start_date_format || $old_end_date_format) { 
			?>
			<span class="time-frame master-tooltip" title="<?php _e( "The time frame for this project", "taskrocket" ); ?>">
				 <?php echo $new_start_date_format->format($date_format); ?> &#10140; <?php echo $new_end_date_format->format($date_format); ?>
			</span>
			<?php } ?>
			
		</p>
		
		<?php } ?>
		
	</div>
	<?php
} ?>