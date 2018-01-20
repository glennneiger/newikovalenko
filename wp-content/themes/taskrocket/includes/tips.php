<!--/ Start Tips /-->
<div class="tips-container">
    
    <div class="tips panel">
        <p><strong>TIP: </strong>
        <?php
        $random_tip = array(
            __( "Limit the scope of your search by using the search filter", "taskrocket" ),
            __( "Change your password and preferences in", "taskrocket" ) . " <a href='" . home_url() . "/account'>" . __( "Account Settings", "taskrocket" ) . "</a>",
            __( "Upload your photo in", "taskrocket" ) . " <a href='" . home_url() . "/account'>" . __( "Account Settings", "taskrocket" ) . "</a> " . __( "to improve your Task Rocket experience", "taskrocket" ),
            __( "Overdue items will be shown in red", "taskrocket" ),
            __( "Tasks priorities are indicated by colour. Blue = Low, Green = Normal, Orange = High and Red = Urgent", "taskrocket" ),
            __( "You can attach multiple files to tasks", "taskrocket" ),
            __( "Set start and due dates on any project", "taskrocket" ),
            __( "You can change the status of a task to Complete, Incomplete, On Hold or In Progress", "taskrocket" ),
            __( "Set start and due dates on any task", "taskrocket" ),
            __( "Add a job number to any task or project", "taskrocket" ),
            __( "Set your hourly rate (admin), and specify a time frame for any given project to keep an eye on costs", "taskrocket" ),
            __( "Disable Gravatars (Settings -> Discussion) to allow users to upload their own profile photo", "taskrocket" ),
            __( "Update your", "taskrocket" ) . " <a href='" . home_url() . "/account'>" . __( "Account Settings", "taskrocket" ) . "</a> " . __( "to share useful information with other team members", "taskrocket" ),
            __( "Keep track of time used by simply logging your time when you edit a task", "taskrocket" ),
            __( "Projects that have gone over cost will be shown in red", "taskrocket" ),
            __( "See what projects you're involved in by visiting your", "taskrocket" ) . " <a href='" . home_url() . "/account'>" . __( "Account Settings", "taskrocket" ) . "</a> " . __( "Page", "taskrocket" ),
            __( "See a complete list of", "taskrocket" ) . " <a href='" . home_url() . "/my-tasks'>" . __( "All your tasks", "taskrocket" ) . "</a>",
            __( "When viewing a report, you can email a report to anyone", "taskrocket" ),
            __( "The toolbar icons will change depending on where you are in Task Rocket", "taskrocket" ),
            __( "Administrators can make their own tasks private", "taskrocket" ),
            __( "Grab the free", "taskrocket" ) . " <a href='https://chrome.google.com/webstore/detail/task-rocket/ffoefldcgmcldohibnklhhphdgdpjdnd' target='_blank'>" . __( "Task Rocket Chrome Extension", "taskrocket" ) . "</a>"
        );
        srand(time());
        $sizeof = count($random_tip);
        $random = (rand()%$sizeof);
        print("$random_tip[$random]");
        ?>
        </p>
    </div>
    
</div>
<!--/ End Tips /-->