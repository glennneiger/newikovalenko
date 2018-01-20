<!--/ Start Extra Nav /-->
<div class="pages-nav">
    
	<div class="nav-label">
        <?php if($options['pages_nav_label'] == false) { ?>
            <?php _e( "Pages", "taskrocket" ); ?>
        <?php } else { ?>
        <?php echo $options['pages_nav_label']; } ?>
    </div>

     <ul class="<?php if($options['child_pages_list_expanded'] == true) { echo "open-children"; } ?>">
        <?php
			// Exclude these default pages
	        $account = get_page_by_title('Account');
	        $newproject = get_page_by_title('New Project');
			$newtask = get_page_by_title('New Task');
	        $projects = get_page_by_title('Projects');
			$users = get_page_by_title('Users');
			$report = get_page_by_title('Single Report');
            $reports = get_page_by_title('Reports');
			$userprofile = get_page_by_title('User Profile');
			$client = get_page_by_title('Client');
            $singlereport = get_page_by_title('Single Report');
            $unowned = get_page_by_title('Unowned Tasks');
            $mytasks = get_page_by_title('My Tasks');

	        wp_list_pages('title_li=&sort_column=title&hierarchical=0&exclude='.

			$account->ID . "," . 
            $newproject->ID . "," . 
            $newtask->ID . "," . 
            $projects->ID . "," . 
            $users->ID . "," . 
            $report->ID . "," . 
            $reports->ID . "," . 
            $userprofile->ID . "," . 
			$client->ID . "," . 
            $singlereport->ID . "," . 
            $unowned->ID . "," . 
            $mytasks->ID
		); ?>
    </ul>
</div>
<!--/ End Extra Nav /-->