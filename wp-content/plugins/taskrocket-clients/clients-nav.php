<?php
    // Code for list of projects for currently logged in user.
    if(current_user_can( 'client' )) {
    $userID = get_current_user_id();
    $cat_base = get_option( 'category_base' );
    $client_project = get_user_meta( $userID, 'client_project', true );
    if (is_array($client_project)) {
        foreach ($client_project as $project_ID) : 
            $project = get_category( $project_ID );
            $project_archived = get_option( 'tr_project_archived_' . $project_ID );
            if (!$project_archived) {  ?>
            <li><a href="<?php echo home_url() . '/' . $cat_base . '/' . $project->slug;?>"><?php echo $project->name;?></a></li>
            <?php
            }
        endforeach;
        }
    }
?>