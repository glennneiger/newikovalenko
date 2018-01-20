<!--/ Start Dash Pages /-->
<div class="dash-pages">
     <ul class="<?php if($options['child_pages_list_expanded'] == true) { echo "open-children"; } ?>">
         <li class="header"><?php _e( "Recently Added Pages", "taskrocket" ); ?></li>
        <?php
            // Exclude these pages
            // https://codex.wordpress.org/Function_Reference/get_pages
            $account = get_page_by_title('Account');
	        $newproject = get_page_by_title('New Project');
			$newtask = get_page_by_title('New Task');
	        $projects = get_page_by_title('Projects');
	        $support = get_page_by_title('Support');
			$users = get_page_by_title('Users');
			$report = get_page_by_title('Report');
            $reports = get_page_by_title('Reports');
			$userprofile = get_page_by_title('User Profile');
			$client = get_page_by_title('Client');
            $singlereport = get_page_by_title('Single Report');
            $mytasks = get_page_by_title('My Tasks');
            $unownedtasks = get_page_by_title('Unowned Tasks');
            
            $excluded = $excludedPaged = array(
                $account->ID,
    			$client->ID,
    			$users->ID,
    			$report->ID,
                $reports->ID,
    			$userprofile->ID,
    			$newproject->ID,
    			$newtask->ID,
    			$projects->ID,
    			$support->ID,
                $singlereport->ID,
                $mytasks->ID,
                $unownedtasks->ID
            );
            
        	$dashpages = get_pages( array( 
                'sort_column' => 'post_date', 
                'sort_order' => 'DESC',
                'exclude' => $excluded,
                'number' => $dash_pages,
                'post_type' => 'page',
	            'post_status' => 'publish'
            ) );
            
            $date_format = get_option('date_format');
            $i = 0;
        	foreach( $dashpages as $page ) {
                
                $author = get_the_author();
                $authorID = get_the_author_id();
                $user = get_userdata($authorID);
                
        	?>
    		<li class="<?php echo "row-" . ($i++ % 2); ?>">
                <a href="<?php echo get_page_link( $page->ID ); ?>">
                    <?php echo $page->post_title; ?>
                </a>
                <span class="date"><?php echo get_the_time($date_format, $page->ID); ?></span>
            </li>
        <?php
        	}
        ?>
</ul>
</div>
<!--/ End Dash Pages /-->