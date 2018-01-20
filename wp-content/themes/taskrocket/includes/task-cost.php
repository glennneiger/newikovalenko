<?php 
    $minutes        = get_post_meta($post->ID, 'logtime', TRUE);
    $referer        = $_GET['referer'];
    $project_ID     = $_GET['projectid']
?>

<?php if ($minutes > 0 ) { ?>

    <?php if($options['show_task_cost'] == true && $options['clients_see_task_costs'] || current_user_can( 'manage_options' ) || $options['show_task_cost'] == true && current_user_can( 'editor' )) { ?>
        
        <span class="task-cost master-tooltip" title="<?php _e( "How much this task has cost so far", "taskrocket" ); ?>">
        <strong><?php _e( "Cost", "taskrocket" ); ?>: </strong> 
        
        <?php 
            if($referer == "project") {
                $the_cat_id = $project_ID; // report single
            } else {
                $the_cat_id = $cat_id;     // project page
            }
        
            $standard_rate = $options['rate'];
            $project_rate = get_option( 'tr_hourly_rate_' . $the_cat_id );
            
            if($project_rate) { 
            	$the_rate = $project_rate;
            } else if($standard_rate) {
            	$the_rate = $standard_rate;
            } else {
            	$the_rate = 0;
            }
            
            $task_cost = $the_rate * $minutes;
            $currency_symbol = $options['currency_symbol'];
            if ($currency_symbol == "") {
                $currency_symbol = "$";
            }
            echo $currency_symbol . round($task_cost / 60 , 2);            
        ?>
        </span>
    <?php } ?>

<?php } else { ?>
    <span class="task-cost empty">-</span>
<?php } ?>




