<?php
/*
Template Name: Users
*/
// If the user role is 'client' redirect to the client page.
if( current_user_can('client')) {
	header('Location: '.home_url().'/client');
	exit();
}
global $wpdb;
$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users" );
get_header();
$options = get_option( 'taskrocket_settings' );
?>

<div class="content users">
	<div class="container">

	    <h1><?php echo $user_count; ?> <?php _e( "User", "taskrocket" ); ?><?php if($user_count > 1) { echo "s"; } ?></h1>


		<?php require($GLOBALS[ 'theme_includes' ] . 'users.php'); ?>

		<div id="profile_pane" class="profile-pane"></div>

		<script type="text/javascript">
		jQuery(function ($) {
	        $(".profile-link").on("click", function(){
	            $("#profile_pane").load($(this).attr("page"));
	            return false;
	        });
	    });
	</script>

	</div>
</div>

<?php get_footer(); ?>
