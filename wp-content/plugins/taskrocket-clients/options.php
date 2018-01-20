<h3><?php _e( 'Client Settings', 'taskrocket-clients' ); ?></h3>
<?php
    $tr_active_theme  = wp_get_theme();
    $theme_version 	  = $tr_active_theme->Version;
    if($theme_version < 3) {
?>
<div class="version-warning">
    <?php _e( 'Client Control requires Task Rocket version 3 or higher, and you are running version:', 'taskrocket' ); ?> <?php echo $theme_version; ?>
    <?php
        $url = admin_url() . 'update-core.php';
        $link = sprintf( wp_kses( __( '<a href="%s">Update Now</a>', 'taskrocket' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $url ) );
        echo $link;
    ?>
</div>
<?php } ?>
<table class="form-table <?php if($theme_version < 3) { echo "disabled"; } ?>">
    <tbody>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[prevent_clients_delete_tasks]" name="taskrocket_settings[prevent_clients_delete_tasks]" type="checkbox" value="1" <?php checked( '1', $options['prevent_clients_delete_tasks'] ); ?> /><?php _e( 'Prevent clients from deleting tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[prevent_clients_delete_tasks]">
                    <?php _e( 'Clients will not be able to delete tasks they have created or that you have created for them.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_tasks]" name="taskrocket_settings[clients_see_tasks]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_tasks'] ); ?> /><?php _e( 'Let clients see other tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_tasks]">
                    <?php _e( 'Clients will be able to see all tasks in a project, even those owned by other users. Private tasks are not shown.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_add_to_cal]" name="taskrocket_settings[clients_add_to_cal]" type="checkbox" value="1" <?php checked( '1', $options['clients_add_to_cal'] ); ?> /><?php _e( 'Let clients add projects to their calendar', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_add_to_cal]">
                    <?php _e( 'Clients will be able to use the Add to Cal button to add projects to their calendar (requires the Add to Cal add-on).', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_create_tasks]" name="taskrocket_settings[clients_create_tasks]" type="checkbox" value="1" <?php checked( '1', $options['clients_create_tasks'] ); ?> /><?php _e( 'Let clients create tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_create_tasks]">
                    <?php _e( 'Clients will be able to create tasks.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_reassign_tasks]" name="taskrocket_settings[clients_reassign_tasks]" type="checkbox" value="1" <?php checked( '1', $options['clients_reassign_tasks'] ); ?> class="clients_reassign_tasks" /><?php _e( 'Let clients reassign tasks to other project members', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_reassign_tasks]">
                    <?php _e( 'Clients will be able to reassign tasks to other members of the project.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr class="new">
            <td>
                <strong><input id="taskrocket_settings[clients_assign_and_reassign_tasks_to_anyone]" name="taskrocket_settings[clients_assign_and_reassign_tasks_to_anyone]" type="checkbox" value="1" <?php checked( '1', $options['clients_assign_and_reassign_tasks_to_anyone'] ); ?> class="clients_assign_and_reassign_tasks_to_anyone" /><?php _e( 'Let clients assign and reassign tasks to absolutely anyone', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_assign_and_reassign_tasks_to_anyone]">
                    <?php _e( 'Clients will be able to assign and reassign tasks to any person, even other users and clients who are not in the same project. (requires that "Let clients reassign tasks to other project members" is enabled).', 'taskrocket-clients' ); ?>
                </label>
                <script>
                    $('.clients_assign_and_reassign_tasks_to_anyone').click(function() {
                        $('.clients_reassign_tasks').prop( "checked", true );

                        if ($('.clients_assign_and_reassign_tasks_to_anyone').is(':checked')) {
                            $('.clients_reassign_tasks').prop( "checked", true );
                        } else {
                            $('.clients_reassign_tasks').prop( "checked", false );
                        }
                    });
                </script>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_create_job_numbers]" name="taskrocket_settings[clients_create_job_numbers]" type="checkbox" value="1" <?php checked( '1', $options['clients_create_job_numbers'] ); ?> /><?php _e( 'Let clients add job numbers', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_create_job_numbers]">
                    <?php _e( 'Clients will be able to add job numbers when creating tasks (only relevant if clients are allowed to create tasks).', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_project_details]" name="taskrocket_settings[clients_see_project_details]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_project_details'] ); ?> /><?php _e( 'Let clients see project details', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_project_details]">
                    <?php _e( 'Clients will be able to view the details of the project, including the project manager, description and additional information.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_mark_own_tasks_complete]" name="taskrocket_settings[clients_mark_own_tasks_complete]" type="checkbox" value="1" <?php checked( '1', $options['clients_mark_own_tasks_complete'] ); ?> /><?php _e( 'Let clients change the status of their own tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_mark_own_tasks_complete]">
                    <?php _e( 'Clients will be able to change the status of tasks that they own.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td><strong><input id="taskrocket_settings[clients_edit_tasks]" name="taskrocket_settings[clients_edit_tasks]" type="checkbox" value="1" <?php checked( '1', $options['clients_edit_tasks'] ); ?> /> <?php _e( 'Let clients edit tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_edit_tasks]"><?php _e( 'Let clients edit their own tasks on the front-end.', 'taskrocket-clients' ); ?></label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_attachments]" name="taskrocket_settings[clients_attachments]" type="checkbox" value="1" <?php checked( '1', $options['clients_attachments'] ); ?> /><?php _e( 'Let clients attach files to tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_attachments]">
                    <?php _e( 'Clients will be able to attach files when creating new tasks.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_attachments]" name="taskrocket_settings[clients_see_attachments]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_attachments'] ); ?> /><?php _e( 'Let clients see attachments', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_attachments]">
                    <?php _e( 'Clients will be able to view any files attached to tasks.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_can_comment]" name="taskrocket_settings[clients_can_comment]" type="checkbox" value="1" <?php checked( '1', $options['clients_can_comment'] ); ?> /><?php _e( 'Let clients comment', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_can_comment]">
                    <?php _e( 'Clients will be able to create new comments and respond to comments on tasks.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_project_time_info]" name="taskrocket_settings[clients_see_project_time_info]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_project_time_info'] ); ?> /><?php _e( 'Let clients see the project time information', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_project_time_info]">
                    <?php _e( 'Clients will be able to see how much time has been allocated, spent, remaining and the time frame for the project.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_costs]" name="taskrocket_settings[clients_see_costs]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_costs'] ); ?> /><?php _e( 'Let clients see project cost', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_costs]">
                    <?php _e( 'Clients will be able to see the running cost of the project.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_task_costs]" name="taskrocket_settings[clients_see_task_costs]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_task_costs'] ); ?> /><?php _e( 'Let clients see task costs', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_task_costs]">
                    <?php _e( 'Clients will be able to see the cost of each task.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_task_times]" name="taskrocket_settings[clients_see_task_times]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_task_times'] ); ?> /><?php _e( 'Let clients see time spent on tasks', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_task_times]">
                    <?php _e( 'Clients will be able to see how much time has been spent on individual tasks.', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td>
                <strong><input id="taskrocket_settings[clients_see_team]" name="taskrocket_settings[clients_see_team]" type="checkbox" value="1" <?php checked( '1', $options['clients_see_team'] ); ?> /><?php _e( 'Show project users', 'taskrocket-clients' ); ?></strong>
                <label for="taskrocket_settings[clients_see_team]">
                    <?php _e( 'Clients will be able to see who is involved in the projects (only projects they have access to).', 'taskrocket-clients' ); ?>
                </label>
            </td>
        </tr>
        <tr>
          <td class="padder"><strong>
          <?php _e( 'Dashboard message' ); ?>
            </strong>
            <textarea id="taskrocket_settings[clients_dash_message]" name="taskrocket_settings[clients_dash_message]" cols="30" rows="5" style="width:calc(100% - 20px); height:100px;"><?php esc_attr_e( $options['clients_dash_message'] ); ?></textarea>
            <label for="taskrocket_settings[clients_dash_message]">
              <?php _e( 'Display a message on the dashboard for all clients to see.', 'taskrocket-clients' ); ?>
            </label>
            </td>
        </tr>
        <tr>
            <td>
                <div class="dash-bg">
                    <strong>
                          <?php _e( 'Dashboard message background colour', 'taskrocket-clients' ); ?>
                    </strong>
                    <input type="radio" class="dash_red" name="taskrocket_settings[clients_dash_color]" value="clients_dash_red"<?php checked( 'clients_dash_red' == $options['clients_dash_color'] ); ?> /> <?php _e( 'Red', 'taskrocket-clients' ); ?>
                    <input type="radio" class="dash_blue" name="taskrocket_settings[clients_dash_color]" value="clients_dash_blue"<?php checked( 'clients_dash_blue' == $options['clients_dash_color'] ); ?> /> <?php _e( 'Blue', 'taskrocket-clients' ); ?>
                    <input type="radio" class="dash_orange" name="taskrocket_settings[clients_dash_color]" value="clients_dash_orange"<?php checked( 'clients_dash_orange' == $options['clients_dash_color'] ); ?> /> <?php _e( 'Orange', 'taskrocket-clients' ); ?>
                    <input type="radio" class="dash_green" name="taskrocket_settings[clients_dash_color]" value="clients_dash_green"<?php checked( 'clients_dash_green' == $options['clients_dash_color'] ); ?> /> <?php _e( 'Green', 'taskrocket-clients' ); ?>
                </div>
            </td>
        </tr>
    </tbody>
</table>
