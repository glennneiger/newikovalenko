<?php
/*
Template Name: New Task
*/
if (is_user_logged_in()) $current_user = wp_get_current_user();
get_header();
$category = get_cat_id( single_cat_title("",false) );
require_once("wp-admin/includes/taxonomy.php");

$category = $_GET['project'];
$project_slug = get_category($category);

global $userdata;
wp_get_current_user();
get_header();

$options = get_option( 'taskrocket_settings' );
if(current_user_can( 'client' ) && $options['clients_create_tasks'] == false ) {
	exit;
}
?>

	<div class="content">
		<div class="container">
			
			<?php if($_GET['task_status'] == 'added') { // Show this message when task is added
			$taskName = $_GET['task_name'];
			?>
			<div class="message success">
				<p>
					<?php _e( "The task", "taskrocket" ); ?> 
					<a href="<?php echo $_GET['task_slug']; ?>">
						<?php $str = stripslashes($taskName); echo(str_replace("/", '',$str)); ?>
					</a> 
					<?php _e( "was added", "taskrocket" ); ?>  
					<?php if(!current_user_can( 'client' )) { ?> 
						<?php _e( "to", "taskrocket" ); ?>   
						<a href="<?php echo home_url() . "/" . get_option( 'category_base' ) . "/" . $project_slug->slug; ?>"><?php echo get_cat_name($category); ?></a> 
					<?php } ?>  
				</p>
				<span class="close"></span>
			</div>
			<?php } ?>

			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		    <h1><?php the_title(); ?></h1>
		    <?php endwhile; endif; ?>

			<?php if ($options['users_create_tasks'] == true || current_user_can( 'manage_options' )) {
		        require_once($GLOBALS[ 'theme_includes' ] . "add-task-form.php");
		    } else { ?>

			<p><?php _e( "Sorry, but the ability to create tasks has not been enabled by an administrator. You'll need to ping one of these admin(s)", "taskrocket" ); ?>: </p>
			<?php require($GLOBALS[ 'theme_includes' ] . 'admins-list.php'); ?>

			<?php } ?>
			
			<?php if ($options['enable_warning'] == true) { ?>
			<script>
				document.querySelector('.submit').addEventListener("click", function(){
				    window.btn_clicked = true;
				});
				window.onbeforeunload = function(){
				    if(!window.btn_clicked){
				        return __( "Oops!", "taskrocket" );
				    }
				};
			</script>
			<?php } ?>

		</div>
	</div>

<?php get_footer(); ?>
