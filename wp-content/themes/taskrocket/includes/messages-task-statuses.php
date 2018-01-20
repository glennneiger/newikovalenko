<?php if($_GET['updated'] == 'yes') { ?>
    <div class="message message-restored">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'was marked complete', 'taskrocket' ); ?> 
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $_GET['task_ID']; ?>&action=incomplete&type=task&location=<?php echo $current_url; ?>" class="undo button-small"><?php _e( 'Undo', 'taskrocket' ); ?></a>
        </p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['incomplete'] == 'yes') { ?>
    <div class="message message-restored">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'was marked incomplete', 'taskrocket' ); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['deleted'] == 'yes') { ?>
    <div class="message message-deleted">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'was deleted', 'taskrocket' ); ?> 
            <a href="<?php echo get_template_directory_uri(); ?>/update-project-task.php?task_ID=<?php echo $_GET['task_ID']; ?>&action=undelete&type=task&location=<?php echo $current_url; ?>" class="undo button-small"><?php _e( 'Undo', 'taskrocket' ); ?></a>
        </p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['undeleted'] == 'yes') { ?>
    <div class="message message-restored">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'was restored', 'taskrocket' ); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['inprogress'] == 'yes') { ?>
    <div class="message message-inprogress">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'is now in progress', 'taskrocket' ); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['onhold'] == 'yes') { ?>
    <div class="message message-onhold">
        <p>'<?php echo get_the_title($_GET['task_ID']); ?>' <?php _e( 'is on hold', 'taskrocket' ); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>

<?php if($_GET['task_status'] == 'added') { ?>
    <div class="message message-restored">
        <p>'<a href="<?php echo get_the_permalink($_GET['task_ID']); ?>"><?php echo get_the_title($_GET['task_ID']); ?></a>' <?php _e( 'was added', 'taskrocket' ); ?></p>
        <span class="close"></span>
    </div>
<?php } ?>