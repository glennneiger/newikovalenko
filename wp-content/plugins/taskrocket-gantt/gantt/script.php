<script src="<?php echo home_url() . '/wp-content/plugins/taskrocket-gantt/gantt/js/jquery.fn.gantt.min.js'; ?>"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/latest/js/bootstrap.min.js"></script>
<script>
<?php 
if(is_category() || is_page('single-report')) {
    $gantt_scale_individual_projects = $options['gantt_scale_individual_projects'];
    if ($gantt_scale_individual_projects) {
        $init_gantt_scale = $gantt_scale_individual_projects;
    } else {
        $init_gantt_scale = "weeks";
    }
} else {
    $gantt_scale_all_projects = $options['gantt_scale_all_projects'];
    if ($gantt_scale_all_projects) {
        $init_gantt_scale = $gantt_scale_all_projects;
    } else {
        $init_gantt_scale = "weeks";
    }
}

$gantt_pagination = $options['gantt_pagination'];
if ($gantt_pagination) {
    $init_gantt_pagination = $gantt_pagination;
} else {
    $init_gantt_pagination = 10;
}

$gantt_nav = $options['gantt_nav'];
if ($gantt_nav == "buttons") {
    $the_gantt_nav = "buttons";
} else {
    $the_gantt_nav = "scroll";
}

$gantt_today = $options['scroll_today'];
if ($gantt_today) {
    $scroll_to_today = "true";
} else {
    $scroll_to_today = "false";
}

$hide_completed_gantt = $options['hide_completed_tasks_in_gantt'];
if ($hide_completed_gantt == 1) {
    $completed_in_gantt = array('incomplete', 'inprogress', 'onhold');
} else {
    $completed_in_gantt = array('incomplete', 'inprogress', 'onhold', 'complete');
}

// Dashboard
if(is_home() || is_page('projects') || is_page('reports')) { ?>
jQuery(function ($) {
    "use strict";
    $(".gantt").gantt({
        source: [
            <?php $categories = get_categories(array(
                'hide_empty'       => 0,
                'taxonomy'         => 'category',
                'orderby'          => 'title',
                'depth' 		   => 0
            ));
            
            $i = 1;
            $date_format = get_option('date_format');
            foreach ($categories as $category) {
                $start_date = get_option( 'tr_start_date_' . $category->cat_ID );
                $milliseconds_start = 1000 * strtotime($start_date);
                $the_new_start_date_format = new DateTime($start_date);
                $the_new_start_date = $the_new_start_date_format->format($date_format);
                
                $end_date = get_option( 'tr_end_date_' . $category->cat_ID );
                $milliseconds_end = 1000 * strtotime($end_date);
                $new_end_date_format = new DateTime($end_date);
                $new_end_date = $new_end_date_format->format($date_format);
                
                $job_number = get_option( 'tr_job_number_' . $category->cat_ID );
                $hours_allocated = get_option( 'tr_hrs_allocated_' . $category->cat_ID );
                
                $pm = get_option( 'tr_project_manager_' . $category->cat_ID );
                $pm_info = get_userdata($pm);
                $pm_name = $pm_info->user_firstname . " " . $pm_info->user_lastname;
                
                $project_link = home_url() . "/projects/" . $category->slug;
                
                $project_description = strip_tags(category_description( $category->cat_ID ));
                $show_project_description = $options['show_project_description'];
                if ($show_project_description) {
                    $project_description_clean = str_replace("\n", '', $project_description);
                }
                
                $project_archived = get_option( 'tr_project_archived_' . $category->cat_ID );
                
                $args = array(
                    'numberposts' 	=> -1,
                    'offset' 	    => 0,
                    'post_status' 	=> 'publish',
                    'category' 	 	=> $category->cat_ID
                );
                $alltimes = get_posts( $args );

                $total = 0;
                foreach( $alltimes as $logtimeID ) {
                    $single = get_post_meta( $logtimeID->ID, 'logtime', true );
                    $total += $single;
                }

                $minutes = $total;
                $hours = floor($minutes / 60);
                $min = $minutes - ($hours * 60);
                
                // If these conditions
                if($category->cat_ID !== 1) {
                    if($start_date && $end_date) {
                        if(!$project_archived) {
            ?>
                
                {
                name: "<a href='<?php echo $project_link; ?>'><?php echo $category->cat_name; ?></a>",
                desc: "",
                values: [{
                    from: "/Date(<?php echo $milliseconds_start; ?>)/",
                    to: "/Date(<?php echo $milliseconds_end; ?>)/",
                    label: "<?php echo get_cat_name( $category->cat_ID ) ?>",
                    desc: "<?php echo $project_description_clean; ?>",
                    customClass: "<?php echo 'bar-' .  $i++; ?>",
                    dataObj: {
                        title: "<?php echo $category->cat_name; ?>", 
                        content: "<ul><?php if($pm) { ?><li><strong><?php _e('Project manager', 'taskrocket-gantt'); ?>: </strong><?php echo $pm_name; ?></li><?php } ?><li><strong><?php _e('Start date', 'taskrocket-gantt'); ?>: </strong><?php echo $the_new_start_date; ?></li><li><strong><?php _e('Due date', 'taskrocket-gantt'); ?>: </strong><?php echo $new_end_date; ?></li><?php if($hours_allocated) { ?><li><strong><?php _e('Time allocated', 'taskrocket-gantt'); ?>: </strong><?php echo $hours_allocated; ?>hrs</li><?php } ?><?php if($total) { ?><li><strong><?php _e('Time spent', 'taskrocket-gantt'); ?>: </strong><?php echo $hours; ?><?php _e('hrs', 'taskrocket-gantt'); ?>, <?php echo $min; ?><?php _e('mins', 'taskrocket-gantt'); ?></li><?php } ?><?php if($job_number) { ?><li><strong><?php _e('Job #', 'taskrocket-gantt'); ?>: </strong><?php echo $job_number; ?></li><?php } ?></ul>"
                    }
                }]},

            <?php     } 
                    }
                }
            }
            // End if these conditions
        ?>
        ],
        navigate: "<?php echo $the_gantt_nav; ?>",
        scale: "<?php echo $init_gantt_scale; ?>",
        maxScale: "months",
        minScale: "hours",
        itemsPerPage: <?php echo $init_gantt_pagination; ?>,
        useCookie: false,
        scrollToToday: <?php echo $scroll_to_today; ?>,
        onItemClick: function(data) {
            //alert("I've been clicked");
        },
        onAddClick: function(dt, rowId) {
            //alert("Empty space clicked");
        },
        onRender: function() {
            if (window.console && typeof console.log === "function") {
                console.log("chart rendered");
            }
        }
    });

    $(".gantt").popover(
        {
            selector: ".bar",
            title: function() {
				return $(this).data('dataObj').title;
			},
            content: function() {
				return $(this).data('dataObj').content;
			},
            placement: 'top',
            trigger: "hover",
            container: 'body',
            html: true
        }
    );
    
});
<?php } 

// Project and Report Pages

if(is_category()) {
    $cat_ID = get_query_var('cat'); // Project page
}
if(is_page('single-report')) {
    $cat_ID = $_GET['projectid'];   // Report page
}

if(is_category() || is_page('single-report')) { ?>
jQuery(function ($) {
    "use strict";
    $(".gantt").gantt({
        source: [
            <?php $all_active_tasks_gantt = get_posts(array(
    			'posts_per_page' 	=> -1,
    			'post_type' 		=> 'post',
    			'category' 			=> $cat_ID,
    			'post_status'		=> 'publish',
    			'meta_key'          => 'tr_status',
                'orderby'           => $the_gantt_order,
                'order'             => 'ASC',
    			'meta_value'        => $completed_in_gantt
    		)
    	);
            
            $i = 1;
            $date_format = get_option('date_format');
            foreach($all_active_tasks_gantt as $post) {
                $task_title = get_the_title();
                
                $start_date_gantt = get_post_meta($post->ID, 'startdate', TRUE);
                $milliseconds_start = 1000 * strtotime($start_date_gantt);
                $the_new_start_date_format = new DateTime($start_date_gantt);
                $the_new_start_date = $the_new_start_date_format->format($date_format);

                $end_date_gantt = get_post_meta($post->ID, 'duedate', TRUE);
                $milliseconds_end = 1000 * strtotime($end_date_gantt);
                $new_end_date_format = new DateTime($end_date_gantt);
                $new_end_date = $new_end_date_format->format($date_format);
                
                $job_number = get_post_meta($post->ID, 'job_number_task', TRUE);
                $priority = get_post_meta($post->ID, 'priority', TRUE);
                $comment_count = wp_count_comments($post->ID);
                
                $task_link = get_permalink($post->ID);
                
                $task_owner = get_post($post->ID);
                $task_owner_id = $task_owner->post_author;
                $task_owner_name = get_the_author_meta('user_firstname',$task_owner_id) . " " . get_the_author_meta('user_lastname',$task_owner_id);
                
                
                if(get_post_meta($post->ID, 'tr_status', TRUE) == "incomplete") {
                    $task_status = __( "Incomplete", "taskrocket" );
                }
                if(get_post_meta($post->ID, 'tr_status', TRUE) == "onhold") {
                    $task_status = __( "On hold", "taskrocket" );
                }
                if(get_post_meta($post->ID, 'tr_status', TRUE) == "inprogress") {
                    $task_status = __( "In progress", "taskrocket" );
                }
                if(get_post_meta($post->ID, 'tr_status', TRUE) == "complete") {
                    $task_status = __( "Complete", "taskrocket" );
                }
                
                if($start_date_gantt && $end_date_gantt) {
            ?>
                
                {
                name: "<a href='<?php echo $task_link; ?>'><?php echo $task_title; ?></a>",
                desc: "",
                values: [{
                    from: "/Date(<?php echo $milliseconds_start; ?>)/",
                    to: "/Date(<?php echo $milliseconds_end; ?>)/",
                    label: "<?php echo $task_title; ?>",
                    desc: "",
                    customClass: "<?php echo 'bar-' .  $i++; ?> bar-task <?php echo 'bar-' .  get_post_meta($post->ID, 'tr_status', TRUE); ?>",
                    dataObj: {
                        title: "<?php echo get_the_title(); ?>", 
                        content: "<ul><li><strong><?php _e('Owner', 'taskrocket-gantt'); ?>: </strong><?php echo $task_owner_name; ?></li><li><strong><?php _e('Start date', 'taskrocket-gantt'); ?>: </strong><?php echo $the_new_start_date; ?></li><li><strong><?php _e('End date', 'taskrocket-gantt'); ?>: </strong><?php echo $new_end_date; ?></li><li><strong><?php _e('Priority', 'taskrocket-gantt'); ?>: </strong><?php if($priority) { echo $priority;} else { echo "<?php _e('Normal', 'taskrocket-gantt'); ?>"; } ?></li><li><strong><?php _e('Status', 'taskrocket-gantt'); ?>: </strong><?php echo $task_status; ?></li><li><strong><?php _e('Comments', 'taskrocket-gantt'); ?>: </strong><?php echo $comment_count->total_comments; ?></li><?php if($job_number) { ?><li><strong><?php _e('Job #', 'taskrocket-gantt'); ?>: </strong><?php echo $job_number; ?></li><?php } ?></ul>"
                    }
                }]},

            <?php } 
            }
            ?>
        ],
        navigate: "<?php echo $the_gantt_nav; ?>",
        scale: "<?php echo $init_gantt_scale; ?>",
        maxScale: "months",
        minScale: "hours",
        itemsPerPage: <?php echo $init_gantt_pagination; ?>,
        useCookie: false,
        waitText: "<?php _e('Please wait', 'taskrocket-gantt'); ?>",
        scrollToToday: <?php echo $scroll_to_today; ?>,
        onItemClick: function(data) {
            //alert("Item clicked - show some details");
        },
        onAddClick: function(dt, rowId) {
            //alert("Empty space clicked - add an item!");
        },
        onRender: function() {
            if (window.console && typeof console.log === "function") {
                console.log("chart rendered");
            }
        }
    });
    
    $(".gantt").popover(
        {
            selector: ".bar",
            title: function() {
				return $(this).data('dataObj').title;
			},
            content: function() {
				return $(this).data('dataObj').content;
			},
            placement: 'top',
            trigger: "hover",
            container: 'body',
            html: true
        }
    );
    
});
<?php } ?>
</script>