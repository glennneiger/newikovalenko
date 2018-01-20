<span class="description"><?php _e( "Allow", "taskrocket" ); ?>
    <?php
    $userID = $_GET['user_id'];
    $user_info = get_userdata($userID);
    $client_project = get_user_meta( $userID, 'client_project', true );
    echo "<strong>" . $user_info->user_firstname . " " . $user_info->user_lastname . "</strong> (" . $user_info->user_login . ")";
    ?>
    <?php _e( "access to these projects", "taskrocket" ); ?>:
</span>
    
<p style="margin: 10px 0 0 0;"><input type="checkbox" name="selectAll" id="toggle_all_projects" /> Toggle all</p>

<table class="form-table client-access-table">
    <tr>
        <td class="client-projects">
            <?php
            foreach (get_categories('sort_order=asc&hide_empty=0') as $category) { 
                $project = get_category( $cat->cat_ID ); 
                $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID ); 
                if (!$project_archived) { ?>
                <label>
                    <input type="checkbox" value="<?php echo $category->cat_ID; ?>" name="client_project[]" <?php if (is_array($client_project) && in_array($category->cat_ID, $client_project)) echo 'checked="checked"';?>><?php echo $category->name; ?>
                </label>
            <?php } } ?>
        </td>
    </tr>
</table>
<script src="//code.jquery.com/jquery-latest.min.js"></script>
<script>
$(document).ready(function(){
    $('#toggle_all_projects').click (function () {
         var checkedStatus = this.checked;
        $('.client-projects').find('label :checkbox').each(function () {
            $(this).prop('checked', checkedStatus);
         });
    });
});
</script>