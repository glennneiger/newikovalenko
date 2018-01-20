<tr>
    <td>
        <strong><?php _e( 'When a comment is posted send email notification to:', 'taskrocket-comment-notify' ); ?></strong>
        
        <span class="checky first-checky">
            <input id="taskrocket_settings[comment_is_made_notify_pm]" name="taskrocket_settings[comment_is_made_notify_pm]" type="checkbox" value="1" <?php checked( '1', $options['comment_is_made_notify_pm'] ); ?> /><?php _e( 'The project manager for the project', 'taskrocket-comment-notify' ); ?>
        </span>
        
        <span class="checky">
            <input id="taskrocket_settings[comment_is_made_notify_admin]" name="taskrocket_settings[comment_is_made_notify_admin]" type="checkbox" value="1" <?php checked( '1', $options['comment_is_made_notify_admin'] ); ?> /><?php _e( 'The website administrator', 'taskrocket-comment-notify'); ?> (<?php echo get_option( 'admin_email' ); ?>)
        </span>
        
        <span class="checky">
            <input id="taskrocket_settings[comment_is_made_notify_project_team]" name="taskrocket_settings[comment_is_made_notify_project_team]" type="checkbox" value="1" <?php checked( '1', $options['comment_is_made_notify_project_team'] ); ?> /><?php _e( 'The entire project team', 'taskrocket-comment-notify' ); ?>
        </span>
        
        <span class="checky">
            <input id="taskrocket_settings[comment_is_made_notify_task_owner]" name="taskrocket_settings[comment_is_made_notify_task_owner]" type="checkbox" value="1" <?php checked( '1', $options['comment_is_made_notify_task_owner'] ); ?> /><?php _e( 'The task owner', 'taskrocket-comment-notify' ); ?>
        </span>
        
        
        <br /><br />
        <strong><?php _e( 'Also send notification to:', 'taskrocket-comment-notify'); ?></strong>
        <input id="taskrocket_settings[comment_is_made_notify_others]" name="taskrocket_settings[comment_is_made_notify_others]" type="text" value="<?php esc_attr_e( $options['comment_is_made_notify_others'] ); ?>" placeholder="someone@domain.com" class="notify-others" />
        <label for="taskrocket_settings[comment_is_made_notify_others]">
        <?php _e( 'To specify more than one email address, separate with a comma (without spaces). Eg: someguy@domain.com,somegirl@domain.com', 'taskrocket-comment-notify' ); ?>
        </label>
    </td>
</tr>