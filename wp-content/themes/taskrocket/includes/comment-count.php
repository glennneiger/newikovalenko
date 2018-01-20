<?php 
if (get_comments_number() > 0) {
$options = get_option( 'taskrocket_settings' ); 
if ($options['allow_comments'] == true) { ?>
	<span class="comment-count master-tooltip" title="<?php _e( "This task has", "taskrocket" ); ?> <?php echo comments_number( '0', '1', '%' ); ?> <?php _e( "Comments", "taskrocket" ); ?>">
	<?php $comment_link = get_permalink(); ?>
	<a href="<?php echo $comment_link; ?>#comment-area" class="comment-num"><strong>Comment<?php if (get_comments_number() > 1) { echo "s"; } ?>: </strong><?php echo comments_number( '0', '1', '%' ); ?></a>
	</span>
<?php } else { ?>
	<span class="comment-count">-</span>
<?php } } ?>
