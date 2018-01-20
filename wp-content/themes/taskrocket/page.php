<?php
// If client can view this page
if( current_user_can('client')) {
	$ca_value = "client_access_let_clients_view_this_page";
	$client_page_access = get_post_meta( $post->ID, $ca_value, true );
	if(!$client_page_access) {
		header('Location: '.home_url().'/client');
		exit();
	}
}

get_header();
wp_enqueue_script( 'comment-reply' );
$options = get_option( 'taskrocket_settings' );
?>

        <div class="content user-content<?php if ( has_post_thumbnail() ) { echo " user-content-padding"; } ?>">
			<div class="container">
	        	<?php if ( has_post_thumbnail() ) { ?>
						<div class="header-image">
	                        <h1><?php the_title(); ?></h1>
	                        <?php the_post_thumbnail("large"); ?>
						</div>
				<?php
					}
				?>

				<?php if ( !has_post_thumbnail() ) { ?>
					<h1 class="page-title"><?php the_title(); ?></h1>
				<?php } ?>

				<?php
				if( !empty( $post->post_content) ) { ?>
	            <div class="main-content">
					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	                <?php the_content(); ?>
	                <?php endwhile; endif; ?>
	            </div>
				<?php } ?>

				<?php
				if ($options['allow_comments'] == true) { ?>

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

<?php get_footer(); ?>