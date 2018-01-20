<?php
/*
Template Name: User Profile
*/
$options = get_option( 'taskrocket_settings' );
$userID = $_GET['userID'];
$user_info = get_userdata($userID);
?>

<div class="user-profile">

	<div class="pic">
		<?php if (get_option('show_avatars')) { ?>
			<?php echo get_avatar( $user_info->user_email, 300 ); ?>
			<?php 
				$role = $user_info->roles[0];
				if($role == "administrator") { ?>
				<span class="admin-icon"></span>
			<?php } ?>
		<?php } else { ?>
			<?php 
			$attachment_id = $user_info->user_photo;
			$image_attributes = wp_get_attachment_image_src( $attachment_id, $size = 'medium', $icon = false );
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

	<div class="user-content">

		<h1>
			<?php if ($user->user_firstname !== "" ) {
				echo $user_info->first_name . " " . $user_info->last_name;
			} else {
				echo $GLOBALS[ 'nameless' ];
			}
			?>
		</h1>

		<p class="role">
			<span>Role</span>
			<?php global $current_user;
			if($user_info->roles[0] =="administrator") {
				if ($options['admin_title'] !== "") {
					echo $options['admin_title'];
				} else {
					echo "Administrator";
				}
			}
			if($user_info->roles[0] =="editor") {
				echo "Team Member";
			}
			if($user_info->roles[0] =="client") {
				echo "Client";
			}
			?>
		</p>

		<p><span>Joined</span>
			<?php 
				$olddateformat = $user_info->user_registered;
				$newdateformat = new DateTime($olddateformat);
				$date_format = get_option('date_format');
				echo $newdateformat->format($date_format);
			?>
		</p>

		<p>
			<span><?php _e( "Email", "taskrocket" ); ?></span>
			<a href="mailto:<?php echo $user_info->user_email; ?>"><?php echo $user_info->user_email; ?></a>
		</p>

		<?php if($user_info->phone !="") { ?>
		<p>
			<span><?php _e( "Phone", "taskrocket" ); ?></span>
			<a href="tel:<?php echo $user_info->phone; ?>"><?php echo $user_info->phone; ?></a>
		</p>
		<?php } ?>

		<?php if($user_info->skype !="") { ?>
		<p>
			<span><?php _e( "Skype", "taskrocket" ); ?></span>
			<a href="skype:<?php echo $user_info->skype; ?>?call" rel="nofollow"><?php echo $user_info->skype; ?></a></p>
		</p>
		<?php } ?>
		
		<a href="<?php echo home_url(); ?>/my-tasks/?user=<?php echo $userID; ?>" class="button-cta"><?php _e( "View", "taskrocket" ); ?> <?php if ($user->user_firstname !== "" ) {
			echo $user_info->first_name . " " . $user_info->last_name;
		} else {
			echo $GLOBALS[ 'nameless' ];
		}
		?>'s <?php _e( "Tasks", "taskrocket" ); ?></a>

		<?php if($user_info->description !="") { ?>
		<p class="bio">
			<?php echo $user_info->description; ?>
		</p>
		<?php } ?>

		<?php if($user_info->web !="") { ?>
			<a href="<?php echo $user_info->web; ?>" class="button-small web"><?php _e( "Website", "taskrocket" ); ?></a>
		<?php } ?>
		
		<?php if($user_info->googleplus !="") { ?>
			<a href="<?php echo $user_info->googleplus; ?>" class="button-small googleplus"><?php _e( "Google+", "taskrocket" ); ?></a>
		<?php } ?>

		<?php if($user_info->twitter !="") { ?>
			<a href="<?php echo $user_info->twitter; ?>" class="button-small twitter"><?php _e( "Twitter", "taskrocket" ); ?></a>
		<?php } ?>

		<?php if($user_info->facebook !="") { ?>
			<a href="<?php echo $user_info->facebook; ?>" class="button-small facebook"><?php _e( "Facebook", "taskrocket" ); ?></a>
		<?php } ?>

		<?php if($user_info->other !="") { ?>
			<a href="<?php echo $user_info->other; ?>" class="button-small other"><?php _e( "Other", "taskrocket" ); ?></a>
		<?php } ?>

		<div class="close-profile-info-icon">x</div>

	</div>

</div>