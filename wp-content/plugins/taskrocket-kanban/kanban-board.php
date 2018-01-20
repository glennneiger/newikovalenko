<?php
	define('WP_USE_THEMES', false);

	if ( !function_exists( 'get_home_path' ) ) {
		require_once( dirname(__FILE__) . '/../../../wp-blog-header.php' );
	}
	
	$kanbanID = $_GET['kanbanID'];
	
	// Todo tasks count
	$todo_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $kanbanID,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('incomplete')
	);
	$todo_posts = new WP_Query($todo_args);
	$todo = $todo_posts->post_count;
	
	// In progress tasks count
	$inprogress_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $kanbanID,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('inprogress')
	);
	$inprogress_posts = new WP_Query($inprogress_args);
	$inprogress = $inprogress_posts->post_count;
	
	// On hold tasks count
	$onhold_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $kanbanID,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('onhold')
	);
	$onhold_posts = new WP_Query($onhold_args);
	$onhold = $onhold_posts->post_count;
	
	// Done tasks count
	$done_args = array(
		'posts_per_page' 	=> -1,
		'post_type' 		=> 'post',
		'post_status'		=> 'publish',
		'cat' 				=> $kanbanID,
		'meta_key'          => 'tr_status',
		'meta_value'        => array('complete')
	);
	$done_posts = new WP_Query($done_args);
	$done = $done_posts->post_count;
    ?>

	<div class="kanban-nav">
		<span class="k-nav close-kanban" title="<?php _e( "Close", "taskrocket-kanban" ); ?>"><?php _e( "Close", "taskrocket-kanban" ); ?></span>
		<span class="k-nav reload-kanban" title="<?php _e( "Reload", "taskrocket-kanban" ); ?>"><?php _e( "Reload", "taskrocket-kanban" ); ?></span>
		<span class="k-nav alt-view-kanban" title="<?php _e( "Simple view", "taskrocket-kanban" ); ?>"><?php _e( "Simple view", "taskrocket-kanban" ); ?></span>
		<span class="k-nav filter show-low active" title="<?php _e( "Low prioriry", "taskrocket-kanban" ); ?>"><?php _e( "Low prioriry", "taskrocket-kanban" ); ?></span>
		<span class="k-nav filter show-normal active" title="<?php _e( "Normal Priority", "taskrocket-kanban" ); ?>"><?php _e( "Normal Priority", "taskrocket-kanban" ); ?></span>
		<span class="k-nav filter show-high active" title="<?php _e( "High Priority", "taskrocket-kanban" ); ?>"><?php _e( "High Priority", "taskrocket-kanban" ); ?></span>
		<span class="k-nav filter show-urgent active" title="<?php _e( "Urgent Priority", "taskrocket-kanban" ); ?>"><?php _e( "Urgent Priority", "taskrocket-kanban" ); ?></span>
	</div>
	
	<script>
	$(document).ready(function() {
		$(".k-nav").click(function() {
			$(this).toggleClass("active");
		});
		$(".close-kanban").click(function() {
			$(this).removeClass("active");
		});
		$(".reload-kanban").click(function() {
			$(this).addClass("rotate");
		});
		$(".alt-view-kanban").click(function () {
		    if($.cookie('alt-view-kanban-state') == 'hidden') {
				$(".kanban-item").toggleClass("alt-view-kanban-item");
		        $.cookie('alt-view-kanban-state', 'visible', { expires: 9999 });
		    } else {
				$(".kanban-item").toggleClass("alt-view-kanban-item");
		        $.cookie('alt-view-kanban-state', 'hidden'); 
		    };
		});
		if($.cookie('alt-view-kanban-state') == 'hidden') {
	        $('.kanban-item').addClass('alt-view-kanban-item');
	        $('.alt-view-kanban').addClass('active');
	    }
		
		$(".kanban-nav .show-low").click(function() {
			$('this').removeClass("active");
			$('.kanban-item-low').fadeToggle(250);
		});
		$(".kanban-nav .show-normal").click(function() {
			$('this').removeClass("active");
			$('.kanban-item-normal').fadeToggle(250);
		});
		$(".kanban-nav .show-high").click(function() {
			$('this').removeClass("active");
			$('.kanban-item-high').fadeToggle(250);
		});
		$(".kanban-nav .show-urgent").click(function() {
			$('this').removeClass("active");
			$('.kanban-item-urgent').fadeToggle(250);
		});
		
	});
	</script>

    <!--/ Start Todo /-->
    <div class="kanban-panel todo">
        
        <div class="kanban-header">
            <?php _e( "Todo", "taskrocket-kanban" ); ?> <span><?php if ($todo > 0) { echo $todo; } else { echo "0"; } ?></span>
        </div>
        
		<div class="item-container">
	        <?php 
	            $todo_tasks = get_posts(array(
	            	'posts_per_page' 	=> -1,
	            	'post_type' 		=> 'post',
	            	'category' 			=> $kanbanID,
	            	'post_status'		=> 'publish',
	            	'meta_key'          => 'tr_status',
					'orderby' 			=> $_COOKIE['OrderBy'],
					'order' 			=> $_COOKIE['Order'],
	            	'meta_value'        => array('incomplete')
	            )
	        );
	        $todo_tasks_posts = new WP_Query($todo_tasks);
	        $all_active_todo = $todo_tasks_posts->post_count;
	        ?>

	    	<?php
	    	if($all_active_todo > 0) {
	    	$i = 0;
	    	foreach($todo_tasks as $post) :
	    	setup_postdata($post);
	    	if ($options['show_ID'] == true) {
	    		$showID = '<span title="' . __( "Task ID", "taskrocket-kanban" ) . '">' . get_the_ID() . '</span>';
	    	}
			include plugin_dir_path( __FILE__ ) . 'includes/relation-vars.php';
	    	?>
	        <!--/ Start Todo Item /-->
	    	<div class="kanban-item <?php if( get_post_meta($post->ID, 'priority', TRUE) != "" ) { echo " kanban-item-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " kanban-item-normal"; } ?>">

			<?php // If you are an administrator....
			if (current_user_can( 'manage_options' ) ) { ?>

				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				
				<?php include plugin_dir_path( __FILE__ ) . 'includes/relation.php'; ?>

				<?php // ... otherwise you must be a project contributor.
				} else { ?>

				<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
				<a class="title"><?php _e( "Private Task", "taskrocket-kanban" ); ?></a>
				<?php } else { ?>
				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				<?php } ?>

			<?php } ?>


			<span class="kanban-priority">
				<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php echo get_post_meta($post->ID, 'priority', TRUE); ?> 
				<?php } else { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php _e( "Normal", "taskrocket-kanban" ); ?>
				<?php } ?>
			</span>
	    
	    
	    	<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
			
			<span class="owner-details">
				<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>
	    	<?php 
	    		$author = get_the_author();
	    		if($author !=="") {
	    			if (get_the_author_meta( 'first_name') !== "" ) {
	    				echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
	    			} else {
	    				echo $GLOBALS[ 'nameless' ];
	    			}
	    		} else {
	    			echo $GLOBALS[ 'nameless' ]; ?>
	    			<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>" class="unowned" title="<?php _e( "Take ownership of this task", "taskrocket-kanban" ); ?>"><?php _e( "Take", "taskrocket-kanban" ); ?></a>
	    		<?php }
	    	?>
	    	</span>

	    	</div>
	    	<?php endforeach; ?>
	        
	    	<?php wp_reset_postdata();
	            } 
	        ?>
		</div>

    </div>
    <!--/ End Todo /-->
    
    <!--/ Start In Progress /-->
    <div class="kanban-panel inprogress">
        
        <div class="kanban-header">
            <?php _e( "In Progress", "taskrocket-kanban" ); ?> <span><?php if ($inprogress > 0) { echo $inprogress; } else { echo "0"; } ?></span>
        </div>
		
		<div class="item-container">
			
			<?php 
	            $inprogress_tasks = get_posts(array(
	            	'posts_per_page' 	=> -1,
	            	'post_type' 		=> 'post',
	            	'category' 			=> $kanbanID,
	            	'post_status'		=> 'publish',
					'orderby' 			=> $_COOKIE['OrderBy'],
					'order' 			=> $_COOKIE['Order'],
	            	'meta_key'          => 'tr_status',
	            	'meta_value'        => array('inprogress')
	            )
	        );
	        $inprogress_tasks_posts = new WP_Query($inprogress_tasks);
	        $all_active_inprogress = $inprogress_tasks_posts->post_count;
	        ?>

	    	<?php
	    	if($all_active_inprogress > 0) {
	    	$i = 0;
	    	foreach($inprogress_tasks as $post) :
	    	setup_postdata($post);
	    	if ($options['show_ID'] == true) {
	    		$showID = '<span title="' . __( "Task ID", "taskrocket-kanban" ) . '">' . get_the_ID() . '</span>';
	    	}
			include plugin_dir_path( __FILE__ ) . 'includes/relation-vars.php';
	    	?>
	        <!--/ Start Todo Item /-->
	    	<div class="kanban-item <?php if( get_post_meta($post->ID, 'priority', TRUE) != "" ) { echo " kanban-item-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " kanban-item-normal"; } ?>">

			<?php // If you are an administrator....
			if (current_user_can( 'manage_options' ) ) { ?>

				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				
				<?php include plugin_dir_path( __FILE__ ) . 'includes/relation.php'; ?>

				<?php // ... otherwise you must be a project contributor.
				} else { ?>

				<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
				<a class="title"><?php _e( "Private Task", "taskrocket-kanban" ); ?></a>
				<?php } else { ?>
				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				<?php } ?>

			<?php } ?>

			<span class="kanban-priority">
				<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php echo get_post_meta($post->ID, 'priority', TRUE); ?> 
				<?php } else { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php _e( "Normal", "taskrocket-kanban" ); ?>
				<?php } ?>
			</span>
	    
	    
	    	<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
			
			<span class="owner-details">
				<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>
	    	<?php 
	    		$author = get_the_author();
	    		if($author !=="") {
	    			if (get_the_author_meta( 'first_name') !== "" ) {
	    				echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
	    			} else {
	    				echo $GLOBALS[ 'nameless' ];
	    			}
	    		} else {
	    			echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket-kanban" ) . ')'; ?>
	    			<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>"><?php _e( "Take Ownership", "taskrocket-kanban" ); ?></a>
	    		<?php }
	    	?>
	    	</span>

	    	</div>
	        <!--/ End Todo Item /-->
	    	<?php endforeach; ?>
	        
	    	<?php wp_reset_postdata();
	            } 
	        ?>
		
		</div>
        
    </div>
	<!--/ End In Progress /-->
    
	<!--/ Start On Hold /-->
    <div class="kanban-panel onhold">
        
        <div class="kanban-header">
            <?php _e( "On Hold", "taskrocket-kanban" ); ?> <span><?php if ($onhold > 0) { echo $onhold; } else { echo "0"; } ?></span>
        </div>
		
		<div class="item-container">
			
			<?php 
	            $onhold_tasks = get_posts(array(
	            	'posts_per_page' 	=> -1,
	            	'post_type' 		=> 'post',
	            	'category' 			=> $kanbanID,
	            	'post_status'		=> 'publish',
					'orderby' 			=> $_COOKIE['OrderBy'],
					'order' 			=> $_COOKIE['Order'],
	            	'meta_key'          => 'tr_status',
	            	'meta_value'        => array('onhold')
	            )
	        );
	        $onhold_tasks_posts = new WP_Query($onhold_tasks);
	        $all_active_onhold = $onhold_tasks_posts->post_count;
	        ?>

	    	<?php
	    	if($all_active_onhold > 0) {
	    	$i = 0;
	    	foreach($onhold_tasks as $post) :
	    	setup_postdata($post);
	    	if ($options['show_ID'] == true) {
	    		$showID = '<span title="' . __( "Task ID", "taskrocket-kanban" ) . '">' . get_the_ID() . '</span>';
	    	}
			include plugin_dir_path( __FILE__ ) . 'includes/relation-vars.php';
	    	?>
	        <!--/ Start Todo Item /-->
	    	<div class="kanban-item <?php if( get_post_meta($post->ID, 'priority', TRUE) != "" ) { echo " kanban-item-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " kanban-item-normal"; } ?>">

			<?php // If you are an administrator....
			if (current_user_can( 'manage_options' ) ) { ?>

				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				
				<?php include plugin_dir_path( __FILE__ ) . 'includes/relation.php'; ?>

				<?php // ... otherwise you must be a project contributor.
				} else { ?>

				<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
				<a class="title"><?php _e( "Private Task", "taskrocket-kanban" ); ?></a>
				<?php } else { ?>
				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				<?php } ?>

			<?php } ?>

			<span class="kanban-priority">
				<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php echo get_post_meta($post->ID, 'priority', TRUE); ?> 
				<?php } else { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php _e( "Normal", "taskrocket-kanban" ); ?>
				<?php } ?>
			</span>
	    
	    
	    	<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
			
			<span class="owner-details">
				<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>
	    	<?php 
	    		$author = get_the_author();
	    		if($author !=="") {
	    			if (get_the_author_meta( 'first_name') !== "" ) {
	    				echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
	    			} else {
	    				echo $GLOBALS[ 'nameless' ];
	    			}
	    		} else {
	    			echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket-kanban" ) . ')'; ?>
	    			<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>"><?php _e( "Take Ownership", "taskrocket-kanban" ); ?></a>
	    		<?php }
	    	?>
	    	</span>

	    	</div>
	        <!--/ End Todo Item /-->
	    	<?php endforeach; ?>
	        
	    	<?php wp_reset_postdata();
	            } 
	        ?>
			
		</div>
        
    </div>
	<!--/ Emd On Hold /-->
    
	<!--/ Start Done /-->
    <div class="kanban-panel done">
        
        <div class="kanban-header">
            <?php _e( "Done", "taskrocket-kanban" ); ?> <span><?php if ($done > 0) { echo $done; } else { echo "0"; } ?></span>
        </div>
		
		<div class="item-container">
			<?php 
	            $done_tasks = get_posts(array(
	            	'posts_per_page' 	=> -1,
	            	'post_type' 		=> 'post',
	            	'category' 			=> $kanbanID,
	            	'post_status'		=> 'publish',
					'orderby' 			=> $_COOKIE['OrderBy'],
					'order' 			=> $_COOKIE['Order'],
	            	'meta_key'          => 'tr_status',
	            	'meta_value'        => array('complete')
	            )
	        );
	        $done_tasks_posts = new WP_Query($done_tasks);
	        $all_active_done = $done_tasks_posts->post_count;
	        ?>

	    	<?php
	    	if($all_active_done > 0) {
	    	$i = 0;
	    	foreach($done_tasks as $post) :
	    	setup_postdata($post);
	    	if ($options['show_ID'] == true) {
	    		$showID = '<span title="' . __( "Task ID", "taskrocket-kanban" ) . '">' . get_the_ID() . '</span>';
	    	}
	    	?>
	        <!--/ Start Todo Item /-->
	    	<div class="kanban-item <?php if( get_post_meta($post->ID, 'priority', TRUE) != "" ) { echo " kanban-item-" . get_post_meta($post->ID, 'priority', TRUE); } else { echo " kanban-item-normal"; } ?>">

			<?php // If you are an administrator....
			if (current_user_can( 'manage_options' ) ) { ?>

				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>

				<?php // ... otherwise you must be a project contributor.
				} else { ?>

				<?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
				<a class="title"><?php _e( "Private Task", "taskrocket-kanban" ); ?></a>
				<?php } else { ?>
				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
				<?php } ?>

			<?php } ?>

			<span class="kanban-priority">
				<?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php echo get_post_meta($post->ID, 'priority', TRUE); ?> 
				<?php } else { ?>
					<strong><?php _e( "Priority", "taskrocket-kanban" ); ?>:</strong> <?php _e( "Normal", "taskrocket-kanban" ); ?>
				<?php } ?>
			</span>
	    
	    
	    	<?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
			
			<span class="owner-details">
				<?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>
	    	<?php 
	    		$author = get_the_author();
	    		if($author !=="") {
	    			if (get_the_author_meta( 'first_name') !== "" ) {
	    				echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
	    			} else {
	    				echo $GLOBALS[ 'nameless' ];
	    			}
	    		} else {
	    			echo $GLOBALS[ 'nameless' ] . ' (' . __( "unowned task", "taskrocket-kanban" ) . ')'; ?>
	    			<a href="<?php echo get_template_directory_uri(); ?>/includes/take-ownership.php?task_ID=<?php echo $post->ID; ?>&task_URL=<?php the_permalink(); ?>"><?php _e( "Take Ownership", "taskrocket-kanban" ); ?></a>
	    		<?php }
	    	?>
	    	</span>
		</div>

        <!--/ End Todo Item /-->
    	<?php endforeach; ?>
        
    	<?php wp_reset_postdata();
            } 
        ?>
		</div>
        
    </div>
	<!--/ End Done /-->