<h3><?php _e( "Gantt Settings", "taskrocket" ); ?></h3>
<?php
    $tr_active_theme  = wp_get_theme();
    $theme_version 	  = $tr_active_theme->Version;
    if($theme_version < 4) {
?>
<div class="version-warning">
    <?php _e( 'Gantt requires Task Rocket version 4 or higher, and you are running version:', 'taskrocket-gantt' ); ?> <?php echo $theme_version; ?>
    <?php
        $url = admin_url() . 'update-core.php';
        $link = sprintf( wp_kses( __( '<a href="%s">Update Now</a>', 'taskrocket-gantt' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
        echo $link;
    ?>
</div>
<?php } ?>
<table class="form-table <?php if($theme_version < 4) { echo "disabled"; } ?>">
    <tbody>
        <tr>
            <td><strong>
                <?php _e( 'Show the Gantt chart', 'taskrocket-gantt' ); ?>
                </strong>
                <label>
                <?php _e( 'Show the Gantt chart at these locations. (Note: Items only show in Gantt charts if they have both start and end dates.)', 'taskrocket-gantt' ); ?>
                </label>
                <span class="checky first-checky">
                    <input id="taskrocket_settings[show_gantt_dashboard]" name="taskrocket_settings[show_gantt_dashboard]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_dashboard'] ); ?> /> <?php _e( 'Dashboard', 'taskrocket-gantt' ); ?>
                </span>
                
                <span class="checky">
                    <input id="taskrocket_settings[show_gantt_all_projects]" name="taskrocket_settings[show_gantt_all_projects]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_all_projects'] ); ?> /> <?php _e( 'All Active Projects', 'taskrocket-gantt' ); ?>
                </span>
                
                <span class="checky">
                    <input id="taskrocket_settings[show_gantt_project_pages]" name="taskrocket_settings[show_gantt_project_pages]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_project_pages'] ); ?> /> <?php _e( 'Project (individual)', 'taskrocket-gantt' ); ?>
                </span>
                
                <span class="checky">
                    <input id="taskrocket_settings[show_gantt_report]" name="taskrocket_settings[show_gantt_report]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_report'] ); ?> /> <?php _e( 'Reports', 'taskrocket-gantt' ); ?>
                </span>
                
                <span class="checky">
                    <input id="taskrocket_settings[show_gantt_report_individual]" name="taskrocket_settings[show_gantt_report_individual]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_report_individual'] ); ?> /> <?php _e( 'Report (individual)', 'taskrocket-gantt' ); ?>
                </span>
                
                <span class="checky">
                    <input id="taskrocket_settings[show_gantt_clients_project_pages]" name="taskrocket_settings[show_gantt_clients_project_pages]" type="checkbox" value="1" <?php checked( '1', $options['show_gantt_clients_project_pages'] ); ?> /> <?php _e( 'Client projects (only for projects they have access to)', 'taskrocket-gantt' ); ?>
                </span>
            </td>
        </tr>
        <tr class="gantt-radios">
            <td>
                <strong>
                    <?php _e( 'All projects view scale', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[gantt_scale_all_projects]">
                <?php _e( 'The initial overview scale when all projects are shown in the Gantt chart.', 'taskrocket-gantt' ); ?>
                </label>
                <div>
                    <input type="radio" name="taskrocket_settings[gantt_scale_all_projects]" value="hrs"<?php checked( 'hrs' == $options['gantt_scale_all_projects'] ); ?> /> <?php _e( 'Hours', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_all_projects]" value="days"<?php checked( 'days' == $options['gantt_scale_all_projects'] ); ?> /> <?php _e( 'Days', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_all_projects]" value="weeks"<?php checked( 'weeks' == $options['gantt_scale_all_projects'] ); ?> /> <?php _e( 'Weeks', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_all_projects]" value="months"<?php checked( 'months' == $options['gantt_scale_all_projects'] ); ?> /> <?php _e( 'Months', 'taskrocket-gantt' ); ?>
                </div>
            </td>
        </tr>
        <tr class="gantt-radios">
            <td>
                <strong>
                    <?php _e( 'Individual projects view scale', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[gantt_scale_individual_projects]">
                <?php _e( 'The initial overview scale when viewing an individual project in the Gantt chart.', 'taskrocket-gantt' ); ?>
                </label>
                <div>
                    <input type="radio" name="taskrocket_settings[gantt_scale_individual_projects]" value="hrs"<?php checked( 'hrs' == $options['gantt_scale_individual_projects'] ); ?> /> <?php _e( 'Hours', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_individual_projects]" value="days"<?php checked( 'days' == $options['gantt_scale_individual_projects'] ); ?> /> <?php _e( 'Days', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_individual_projects]" value="weeks"<?php checked( 'weeks' == $options['gantt_scale_individual_projects'] ); ?> /> <?php _e( 'Weeks', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_scale_individual_projects]" value="months"<?php checked( 'months' == $options['gantt_scale_individual_projects'] ); ?> /> <?php _e( 'Months', 'taskrocket-gantt' ); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>
                <input id="taskrocket_settings[hide_completed_tasks_in_gantt]" name="taskrocket_settings[hide_completed_tasks_in_gantt]" type="checkbox" value="1" <?php checked( '1', $options['hide_completed_tasks_in_gantt'] ); ?> />
                <?php _e( 'Hide completed tasks', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[hide_completed_tasks_in_gantt]">
                <?php _e( 'Do not show completed tasks in the Gantt chart.', 'taskrocket-gantt' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong>
                    <?php _e( 'Pagination', 'taskrocket-gantt' ); ?>
                </strong>
                <input class="gantt_pagination" id="taskrocket_settings[gantt_pagination]" name="taskrocket_settings[gantt_pagination]" type="number" min="0" max="100" value="<?php esc_attr_e( $options['gantt_pagination'] ); ?>" />
                <label for="taskrocket_settings[gantt_pagination]">
                <?php _e( 'How many items to show per vertical pagination.', 'taskrocket-gantt'); ?>
                </label>
            </td>
        </tr>
        <tr class="gantt-radios">
            <td>
                <strong>
                    <?php _e( 'Navigation', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[gantt_nav]">
                <?php _e( 'What type of navigation to use for the Gantt chart.', 'taskrocket-gantt' ); ?>
                </label>
                <div>
                    <input type="radio" name="taskrocket_settings[gantt_nav]" value="buttons"<?php checked( 'buttons' == $options['gantt_nav'] ); ?> /> <?php _e( 'Buttons', 'taskrocket-gantt' ); ?>
                    <input type="radio" name="taskrocket_settings[gantt_nav]" value="slider"<?php checked( 'slider' == $options['gantt_nav'] ); ?> /> <?php _e( 'Slider', 'taskrocket-gantt' ); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td><strong>
                <input id="taskrocket_settings[scroll_today]" name="taskrocket_settings[scroll_today]" type="checkbox" value="1" <?php checked( '1', $options['scroll_today'] ); ?> />
                <?php _e( 'Scroll to today', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[scroll_today]">
                <?php _e( 'Automatically scroll the Gantt chart to the current day.', 'taskrocket-gantt' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td><strong>
                <input id="taskrocket_settings[show_project_description]" name="taskrocket_settings[show_project_description]" type="checkbox" value="1" <?php checked( '1', $options['show_project_description'] ); ?> />
                <?php _e( 'Show project description', 'taskrocket-gantt' ); ?>
                </strong>
                <label for="taskrocket_settings[show_project_description]">
                <?php _e( 'When hovering over a Gantt item, show the project description if it has one.', 'taskrocket-gantt' ); ?>
                </label>
            </td>
        </tr>
    </tbody>
</table>