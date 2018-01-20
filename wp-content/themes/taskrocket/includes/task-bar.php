<!--/ Start Task Bar /-->
    
    <?php if ( !is_home() ) {
        require($GLOBALS[ 'theme_includes' ] . 'previous-owner.php'); 
    } ?>

    <?php if(current_user_can( 'manage_options' )) { ?>
        <?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
            <span class="private"><?php _e( "Private", "taskrocket" ); ?></span>
        <?php } else { ?>
            <span class="private empty">-</span>
        <?php } ?>
    <?php } ?>
    
    <span class="date-created master-tooltip" title="The date this task was created">
        <strong><?php _e( "Created", "taskrocket" ); ?>:</strong> 
        <?php 
        $date_format = get_option('date_format');
        echo get_the_time($date_format); ?>
    </span>
    
        
    <?php if(  get_post_meta($post->ID, 'startdate', TRUE) != '' ) { ?>
        <span class="start-date master-tooltip" title="The start date for this task">
            <strong><?php _e( "Start date", "taskrocket" ); ?>:</strong> 
                <?php 
                    $date_format = get_option('date_format');
                    $oldstartdateformat = get_post_meta($post->ID, 'startdate', TRUE);
                    $newstartdateformat = new DateTime($oldstartdateformat);
                    echo $newstartdateformat->format($date_format);
                ?>
        </span>
    <?php } ?>
    
    
    <?php if(  get_post_meta($post->ID, 'duedate', TRUE) != '' ) { ?>

        <?php if ( get_post_status ( $post->ID ) !== 'trash' ) {
            $date = get_post_meta($post->ID, 'duedate', TRUE);	// Pull your value
            $datetime = strtotime( $date );                     // Convert to + seconds since unix epoch
            $yesterday = strtotime("-1 days");                	// Convert today -1 day to seconds since unix epoch
            if ( $datetime >= $yesterday ) {                   	// if date value pulled is today or later, we're overdue
                $overdue = ' class="notoverdue created master-tooltip" title="' . __( "This task is not overdue", "taskrocket" ) . '"';
                $overduetext = "<strong>" . __( "Due", "taskrocket" ) . ": </strong>";
            } else {
                if(get_post_meta($post->ID, 'tr_status', TRUE) !== 'complete') {
                    $overdue = ' class="overdue created master-tooltip" title="' . __( "This task is OVERDUE", "taskrocket" ) . '"';
                    $overduetext = "<strong>" . __( "Overdue", "taskrocket" ) . ": </strong>";
                } else {
                    $overdue = ' class="notoverdue master-tooltip" title="' . __( "Due date", "taskrocket" ) . '"';
                    $overduetext = "<strong>" . __( "Due", "taskrocket" ) . ": </strong>";
                }
            }
        ?>
        <span<?php echo $overdue; ?>>
            <strong><?php echo $overduetext; ?></strong> 
            <?php 
                $date_format = get_option('date_format');
                $olddateformat = get_post_meta($post->ID, 'duedate', TRUE);
                $newdateformat = new DateTime($olddateformat);
                echo $newdateformat->format($date_format);
            ?>
        </span>
        <?php } ?>
    
    <?php } else { ?>
        <span class="empty">-</span>
    <?php } ?>

    <?php require($GLOBALS[ 'theme_includes' ] . 'time-spent.php'); ?>
    <?php if(!is_home()) { require($GLOBALS[ 'theme_includes' ] . 'comment-count.php');} ?>
    
    <?php if ( get_post_status ( $post->ID ) == 'trash' ) {
        echo '<span class="master-tooltip" title="' . __( "The date this task was completed", "taskrocket" ) . '"><strong>' . __( "Completed", "taskrocket" )  . ':</strong> ';
        the_modified_date();
        echo '</span>';
    }?>
    
    <?php $attach = get_children(array('post_parent'=>$post->ID)); $attCount = count($attach);
    if($attCount > 0) {
    ?>
    <span class="attachments-count master-tooltip" title="<?php _e( "This task has", "taskrocket" ); ?> <?php echo $attCount; ?> <?php _e( "Attachment", "taskrocket" ); ?><?php if($attCount > 1) { echo "s"; } ?>">
        <strong><?php _e( "Attachment", "taskrocket" ); ?><?php if($attCount > 1) { echo "s"; } ?>: </strong><?php echo $attCount; ?>
    </span>
    <?php } ?>
    
    <?php // Unowned Task
    if ($post->post_author =="0") { ?>
        <?php //echo $post->post_author; ?>
		<span class="orphan master-tooltip" title="<?php _e( "This task doesn't have an owner", "taskrocket" ); ?>"><?php _e( "Unowned", "taskrocket" ); ?></span>
	<?php } ?>
    
    <?php // Task ID
    if ($options['show_ID'] == true) { ?>
		<span class="task-id master-tooltip" title="<?php _e( "The ID of this task", "taskrocket" ); ?>"><strong><?php _e( "ID", "taskrocket" ); ?>: </strong> <?php echo get_the_ID(); ?></span>
	<?php } ?>
    
    <?php // Job number
    if(get_post_meta($post->ID, 'job_number_task', TRUE)) { ?> 
		<span class="job-number master-tooltip" title="<?php _e( "Job number for this task", "taskrocket" ); ?>"><strong><?php _e( "Job", "taskrocket" ); ?> #: </strong><?php echo get_post_meta($post->ID, 'job_number_task', TRUE); ?></span> 
	<?php } ?>
    
    <?php require($GLOBALS[ 'theme_includes' ] . 'task-cost.php'); ?>
    
<!--/ End Task Bar /-->