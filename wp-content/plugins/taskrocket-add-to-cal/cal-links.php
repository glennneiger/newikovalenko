<?php

    $pluginURl = plugins_url();
    
    if(is_page('projects')) {
        $category = $cat_ID;
    } else {
        $category = get_query_var('cat');
    }
    
    $current_cat = get_category($cat);

    $old_start_date_format = get_option( 'tr_start_date_' . $category );
    $new_start_date_format = new DateTime($old_start_date_format);

    $old_end_date_format = get_option( 'tr_end_date_' . $category );
    $new_end_date_format = new DateTime($old_end_date_format);

    $startTime = "000000";
    $endTime = "000000";

    $options = get_option( 'taskrocket_settings' );
    $caltype = $options['cal_link_type'];
    
    if(is_page('projects')) {
        $project_name = $cat->cat_name;
        $project_desc = urlencode($cat->category_description);
        $location = home_url() . "/" . $cat->category_nicename;
    } else {
        $project_name = single_cat_title("", false);;
        $project_desc_with_p = category_description( $cat_id );
        $project_desc = urlencode(preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $project_desc_with_p));
        $location = home_url(add_query_arg(array(),$wp->request));
    }
?>

<?php // If there is at least an end date
if($old_end_date_format !="" || $options['show_on_all_projects'] == true) { ?>

    <?php if($caltype == "gcal") {
        $calName = __( 'Google Calendar', 'taskrocket-add-to-cal' ); ?>

        <a href="//www.google.com/calendar/event?action=TEMPLATE&text=<?php echo $project_name; ?>&dates=<?php echo $new_start_date_format->format('Ymd'); ?>T<?php echo $startTime;?>Z/<?php echo $new_end_date_format->format('Ymd'); ?>T<?php echo $endTime;?>Z&details=<?php echo $project_desc; ?>&location=<?php echo $location; ?>" target="_blank" rel="nofollow" class="top-toggle add-to-cal-project <?php echo $caltype; ?> master-tooltip" title="<?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?>"><?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?></a>

    <?php } ?>

    <?php if($caltype == "yahoo") {
        $calName = __( 'Yahoo! Calendar', 'taskrocket-add-to-cal' ); ?>

        <a href="//calendar.yahoo.com/?v=60&TITLE=<?php echo $project_name; ?>&ST=<?php echo $new_start_date_format->format('Ymd'); ?>&ET=<?php echo $new_end_date_format->format('Ymd'); ?>&DESC=<?php echo $project_desc; ?>&URL=<?php echo $location; ?>&in_loc=<?php echo $location; ?>" target="_blank" rel="nofollow" class="top-toggle add-to-cal-project <?php echo $caltype; ?> master-tooltip" title="<?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?>"><?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?></a>

    <?php } ?>

    <?php if($caltype == "msn") {
        $calName = __( 'Microsoft Calendar', 'taskrocket-add-to-cal' ); ?>

        <a href="//calendar.live.com/calendar/calendar.aspx?rru=addevent&summary=<?php echo $project_name; ?>&dtstart=<?php echo $new_start_date_format->format('Ymd'); ?>&dtend=<?php echo $new_end_date_format->format('Ymd'); ?>&description=<?php echo $project_desc; ?>&location=<?php echo $location; ?>" target="_blank" rel="nofollow" class="top-toggle add-to-cal-project <?php echo $caltype; ?> master-tooltip" title="<?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?>"><?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?></a>

    <?php } ?>

    <?php if($caltype == "ical") {
            $calName = __( 'iCal', 'taskrocket-add-to-cal' ); ?>

        <a href="<?php echo $pluginURl; ?>/taskrocket-add-to-cal/ics.php?title=<?php echo $project_name; ?>&datestart=<?php echo $new_start_date_format->format('Ymd\THis\Z'); ?>&dateend=<?php echo $new_end_date_format->format('Ymd\THis\Z'); ?>&description=<?php echo $project_desc; ?>&filename=<?php echo $current_cat->slug; ?>&uniqid=project-<?php echo $current_cat->slug . "-" . get_cat_ID( $project_name ); ?>&uri=<?php echo home_url() . "/" . $cat->category_nicename; ?>" class="top-toggle add-to-cal-project <?php echo $caltype; ?> master-tooltip" title="(<?php _e( 'Outlook, Apple & other desktop calendars', 'taskrocket-add-to-cal' ); ?>)"><?php _e( 'Add to', 'taskrocket-add-to-cal' ); ?> <?php echo $calName; ?></a>

    <?php } ?>

    <?php if($caltype == "choose") { ?>

        <span class="top-toggle add-to-cal-project <?php echo $caltype; ?> all-rounded master-tooltip" title="<?php _e( 'Add this project to your calendar', 'taskrocket-add-to-cal' ); ?>"><?php _e( 'Add to Cal', 'taskrocket-add-to-cal' ); ?></span>
            
            <ul class="cal-choices">
                <li><a href="//www.google.com/calendar/event?action=TEMPLATE&text=<?php echo $project_name; ?>&dates=<?php echo $new_start_date_format->format('Ymd'); ?>T<?php echo $startTime;?>Z/<?php echo $new_end_date_format->format('Ymd'); ?>T<?php echo $endTime;?>Z&details=<?php echo $project_desc; ?>&location=<?php echo $location; ?>" target="_blank" rel="nofollow" class="cal-type-<?php echo $caltype; ?>"><?php _e( 'Google Calendar', 'taskrocket-add-to-cal' ); ?></a></li>

                <li><a href="//calendar.yahoo.com/?v=60&TITLE=<?php echo $project_name; ?>&ST=<?php echo $new_start_date_format->format('Ymd'); ?>&ET=<?php echo $new_end_date_format->format('Ymd'); ?>&DESC=<?php echo $project_desc; ?>&URL=<?php echo $location; ?>&in_loc=<?php echo $location; ?>" target="_blank" rel="nofollow" class="cal-type-<?php echo $caltype; ?>"><?php _e( 'Yahoo! Calendar', 'taskrocket-add-to-cal' ); ?></a></li>

                <li><a href="//calendar.live.com/calendar/calendar.aspx?rru=addevent&summary=<?php echo $project_name; ?>&dtstart=<?php echo $new_start_date_format->format('Ymd'); ?>&dtend=<?php echo $new_end_date_format->format('Ymd'); ?>&description=<?php echo $project_desc; ?>&location=<?php echo $location; ?>" target="_blank" rel="nofollow" class="cal-type-<?php echo $caltype; ?>">Microsoft Calendar</a></li>

                <li><a href="<?php echo $pluginURl; ?>/taskrocket-add-to-cal/ics.php?title=<?php echo $project_name; ?>&datestart=<?php echo $new_start_date_format->format('Ymd\THis\Z'); ?>&dateend=<?php echo $new_end_date_format->format('Ymd\THis\Z'); ?>&description=<?php echo $project_desc; ?> (<?php echo $location; ?>)&filename=<?php echo $current_cat->slug; ?>&uniqid=project-<?php echo $current_cat->slug . "-" . get_cat_ID( $project_name ); ?>&uri=<?php echo home_url() . "/" . $cat->category_nicename; ?>" class="cal-type-<?php echo $caltype; ?>" title="(Outlook, Apple &amp; other desktop calendars)"><?php _e( 'iCal compatible', 'taskrocket-add-to-cal' ); ?></a></li>
            </ul>

    <?php } ?>

<?php // End if there is at least an end date
} ?>
