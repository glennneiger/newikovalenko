<!--/ Start Clients Nav /-->
<div class="pages-nav">
    
	<div class="nav-label">
        <?php if ($options['pages_nav_label'] == FALSE) { ?>
            <?php _e( "Pages", "taskrocket" ); ?>
        <?php } else { ?>
        <?php echo $options['pages_nav_label']; } ?>
    </div>

     <ul>
        <?php
        $args = array(
            'post_type'           => 'page',
            'post_status'         => 'publish',
            'posts_per_page'      => -1,
            'caller_get_posts'    => 1,
            'orderby'             => 'title',
            'order'               => 'ASC'
        );
        $ca_query = new WP_Query($args);
        if( $ca_query->have_posts() ) {
          while ($ca_query->have_posts()) : $ca_query->the_post(); 
          
          $ca_value = "client_access_let_clients_view_this_page";
          $client_page_access = get_post_meta( $post->ID, $ca_value, true );
          
            if($client_page_access) { ?>
                <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></li>
            <?php
            } endwhile;
        }
        wp_reset_query();
		?>
    </ul>
</div>
<!--/ End Clients Nav /-->