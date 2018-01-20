<?php
/*
Template Name: Search
*/
get_header(); ?>

<div class="content search-results all-tasks-list">
    <div class="container">

		<?php
        // Extend WordPress search to include custom fields
        function cf_search_join( $join ) {
            global $wpdb;
            if ( is_search() ) {    
                $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
            }
            
            return $join;
        }
        add_filter('posts_join', 'cf_search_join' );
        /**
         * Modify the search query with posts_where
         */
        function cf_search_where( $where ) {
            global $pagenow, $wpdb;
           
            if ( is_search() ) {
                $where = preg_replace(
                    "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                    "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_key = 'job_number_task' AND ".$wpdb->postmeta.".meta_value LIKE $1)", $where );
            }
            return $where;
        }
        add_filter( 'posts_where', 'cf_search_where' );
        /**
         * Prevent duplicates
         */
        function cf_search_distinct( $where ) {
            global $wpdb;
            if ( is_search() ) {
                return "DISTINCT";
            }
            return $where;
        }
        add_filter( 'posts_distinct', 'cf_search_distinct' );
        
		$cat_results_count = 0;
		if (empty($_GET['cat'])) {
			global $wpdb;
			$category_job_number_results = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'tr_job_number_%' AND option_value = '$s'", OBJECT );

			$category_results = $wpdb->get_col( "SELECT a.term_id FROM $wpdb->terms a JOIN $wpdb->term_taxonomy b ON a.term_id = b.term_id WHERE b.taxonomy = 'category' AND a.name LIKE '%$s%'" );

			if (sizeof($category_job_number_results) > 0) {
				foreach ( $category_job_number_results as $result ) {
					$cat_id = str_replace('tr_job_number_', '', $result->option_name);
					if (!in_array($cat_id, $category_results)) $category_results[] = $cat_id;
				}
			}
			$cat_results_count = sizeof($category_results);
		}
		?>

		<?php if (have_posts() || $cat_results_count > 0) : ?>
			<?php
			$allsearch = new WP_Query();
			$the_project = $_GET['cat'];
			if($the_project == 0) {
				$categoryID = 0;
			}  else {
				$categoryID = $_GET['cat'];
			}
			$project = get_the_category_by_ID( $categoryID );
			$key = esc_html($s, 1); ?>
			<h1><?php _e( "Your search for", "taskrocket" ); ?> <?php if($the_project == "0") { echo ' <span class="term">' . __( "Attachments", "taskrocket" ) . '</span> ' . __( "with the term", "taskrocket" ) . ' '; } ?>
				<?php $count = $allsearch->post_count;
                echo '<span class="term">' . $key . '</span>'; ?> <?php if($categoryID !="") { echo ' in <span class="term">' .  $project . '</span>'; } ?> 
				
				<?php if($the_project !== "0") { ?>
					<?php _e( "found", "taskrocket" ); ?> <span class="term"><?php $total_results = $wp_query->found_posts; echo $total_results + $cat_results_count; ?></span> <?php _e( "result", "taskrocket" ); ?><?php if($total_results > 1) { echo "s"; } ?>
				<?php } ?>
			</h1>

			<div id="task-list" class="search-results">

				<?php 
				$i = 0;
                if($the_project !=="0") {
    				if ($cat_results_count > 0) {
    					foreach ( $category_results as $cat_id ) {
    						require($GLOBALS[ 'theme_includes' ] . 'search-result-project.php');
    					}
    				}
                }

				while (have_posts()) : the_post(); ?>

				<?php if($the_project == "0") { ?> 
					<?php if(get_post_type( $post_id ) == "attachment") { ?>  
						<?php require($GLOBALS[ 'theme_includes' ] . 'search-result.php'); ?>   
					<?php } ?> 
				<?php } else { ?>
					<?php require($GLOBALS[ 'theme_includes' ] . 'search-result.php'); ?>    
				<?php } ?>

				<?php endwhile; ?>

			</div>

		<?php else : ?>

			<h1><?php _e( "Nothing found", "taskrocket" ); ?></h1>
			<h2 class="no-search-results"><?php _e( "I couldn't find anything related to", "taskrocket" ); ?> <?php /* Search Count */ $allsearch = new WP_Query("s=$s&showposts=-1"); $key = esc_html($s, 1); $count = $allsearch->post_count; 
            echo '<span class="term">' . $key . '</span>'; ?>.</h2>
		<?php endif; ?>
    
	</div>
</div>

<?php get_footer(); ?>