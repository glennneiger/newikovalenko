<?php $options = get_option( 'taskrocket_settings' ); ?>
<?php // If user is a client
	if( !current_user_can('client')) { ?>

<div class="clear"></div>

</div>
<!--/ End Container /-->

<?php // If user is a client
} ?>

<?php
if ($options['custom_js'] !== "") { ?>
<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/<?php echo $options['custom_js']; ?>'></script>
<?php } ?>

<div class="mask"></div>

<?php wp_footer(); ?>

<?php // If user is not a client
	if( !current_user_can('client')) { ?>
	<span class="toggle-menu">
	    <span class="bar-01"></span>
	    <span class="bar-02"></span>
	    <span class="bar-03"></span>
	</span>
<?php } ?>


<?php if($options['disable_loading_animation'] == false) { ?>
<script>
$(document).ready(function() {
	//Preloader
	$(window).load(function() {
		preloaderFadeOutTime = 500;
		function hidePreloader() {
		var preloader = $('.spinner-wrapper');
		preloader.fadeOut(preloaderFadeOutTime);
	}
	hidePreloader();
	});
});
</script>
<?php } ?>

<?php if($options['disable_fancy_tooltips'] == false) { ?>
	<?php if(is_category() || is_page('new-task') || is_page('new-project') || is_single() || is_page('single-report') || is_page('projects') || is_page('my-tasks') || is_home() || is_page('reports') || is_page('users') || ($_GET['s']) ) { ?>
	<script>
	// Tooltips
	if($(window).width() > 960) {
		$(document).ready(function() {
			// Tooltip only Text
			$('.master-tooltip').hover(function(){
			        // Hover over code
			        var title = $(this).attr('title');
			        $(this).data('tipText', title).removeAttr('title');
			        $('<p class="tooltip"></p>')
			        .text(title)
			        .appendTo('body')
			        .fadeIn('slow');
			}, function() {
			        // Hover out code
			        $(this).attr('title', $(this).data('tipText'));
			        $('.tooltip').remove();
			}).mousemove(function(e) {
			        var mousex = e.pageX + -75; //Get X coordinates
			        var mousey = e.pageY + 20; //Get Y coordinates
			        $('.tooltip')
			        .css({ top: mousey, left: mousex })
			});
		});
	}
	</script>
	<?php } ?>
<?php } ?>

</body>
</html>
