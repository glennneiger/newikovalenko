<?php
get_header();
?>
	<div class="content">
		<div class="container error-404">
		<h1><?php _e( "404: Don't Freak Out", "taskrocket" ); ?></h1>
		<p><?php _e( "The page may have existed at some time, but it doesn't now.", "taskrocket" ); ?></p>
		<p><?php _e( "If you were expecting a page or a task, you may have accidentally deleted it. If so just restore it from the trash.", "taskrocket" ); ?></p>
		<p><?php _e( "Otherwise the search may be your best bet to find what you're looking for.", "taskrocket" ); ?></p>
		</div>
	</div>

<?php get_footer(); ?>