<h3><?php _e( 'Add To Cal Settings', 'taskrocket-add-to-cal' ); ?></h3>
<?php
    $tr_active_theme  = wp_get_theme();
    $theme_version 	  = $tr_active_theme->Version;
    if($theme_version < 3) {
?>
<div class="version-warning">
    <?php _e( 'Add To Cal requires Task Rocket version 3 or higher, and you are running version:', 'taskrocket-add-to-cal' ); ?> <?php echo $theme_version; ?>
    <?php
        $url = admin_url() . 'update-core.php';
        $link = sprintf( wp_kses( __( '<a href="%s">Update Now</a>', 'taskrocket-add-to-cal' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
        echo $link;
    ?>
</div>
<?php } ?>
<table class="form-table <?php if($theme_version < 3) { echo "disabled"; } ?>">
    <tbody>
        <tr>
            <td class="padder">
                <strong><?php _e( 'Prefered calendar application', 'taskrocket-add-to-cal' ); ?></strong>
                <select name="taskrocket_settings[cal_link_type]">
                    <option></option>
                    <option value="gcal" <?php if ( $options['cal_link_type'] == "gcal" ) echo 'selected="selected"'; ?>><?php _e( 'Google Calendar (web)', 'taskrocket-add-to-cal' ); ?></option>
                    <option value="yahoo" <?php if ( $options['cal_link_type'] == "yahoo" ) echo 'selected="selected"'; ?>><?php _e( 'Yahoo Calendar (web)', 'taskrocket-add-to-cal' ); ?></option>
                    <option value="msn" <?php if ( $options['cal_link_type'] == "msn" ) echo 'selected="selected"'; ?>><?php _e( 'Microsoft Calendar (web)', 'taskrocket-add-to-cal' ); ?></option>
                    <option value="ical" <?php if ( $options['cal_link_type'] == "ical" ) echo 'selected="selected"'; ?>><?php _e( 'iCal compatible desktop calendars (Outlook, Apple & others)', 'taskrocket-add-to-cal' ); ?></option>
                    <option value="choose" <?php if ( $options['cal_link_type'] == "choose" ) echo 'selected="selected"'; ?>><?php _e( 'Let me choose on the front-end', 'taskrocket-add-to-cal' ); ?></option>
                </select>
                <label for="taskrocket_settings[use_for_google]">
                    <?php _e( 'Which calendar application would you like the "Add to" button to use on the front-end', 'taskrocket-add-to-cal' ); ?> 
                    (<a href="https://en.wikipedia.org/wiki/List_of_applications_with_iCalendar_support" target="_blank">
                        <?php _e( 'Help: Apps with iCal support', 'taskrocket-add-to-cal' ); ?>
                    </a>)
                </label>
            </td>
        </tr>

        <tr>
            <td><strong>
                <input id="taskrocket_settings[show_on_all_projects]" name="taskrocket_settings[show_on_all_projects]" type="checkbox" value="1" <?php checked( '1', $options['show_on_all_projects'] ); ?> />
                <?php _e( 'Show on all projects', 'taskrocket-add-to-cal'); ?>
                </strong>
                <label for="taskrocket_settings[show_on_all_projects]">
                    <?php _e( 'Show the button on all projects. If disabled, the button will only show on projects that have a due date.', 'taskrocket-add-to-cal' ); ?>
                </label>
            </td>
        </tr>

    </tbody>
</table>