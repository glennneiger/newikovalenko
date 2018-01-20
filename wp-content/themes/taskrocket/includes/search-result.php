<div class="task border-soft <?php if(  get_post_meta($post->ID, 'priority', TRUE) != '' ) { ?> task-priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?><?php } else { ?> task-priority-normal<?php } ?> <?php echo "row-" . ($i++ % 2); ?> <?php if ( wp_attachment_is_image( $post_id ) ) { echo "has-image"; } else echo "no-image"; ?> <?php if(get_post_type( $post_id ) == "attachment") { echo "is-attachment"; } else { echo "is-a-task"; } ?>">

    
    <?php //If you are an administrator....
    if (current_user_can( 'manage_options' ) ) { ?>
        
        <?php if(get_post_type( $post_id ) == "attachment") { 
            $filethumb = wp_get_attachment_thumb_url( $attachment->ID);	 // Path to the thumbnail
            $filepath = wp_get_attachment_url( $attachment->ID);		 // Path to the original file
            $filename = $attachment->post_title;
            ?>

                <h2><a href="<?php echo wp_get_attachment_url( $post_id ); ?>" class="download-icon" download><?php the_title(); ?></a></h2>
                
                <?php if ( wp_attachment_is_image( $post_id ) ) { ?>
                    <a href="<?php echo $filepath; ?>?TB_iframe=true" rel="task-<?php echo get_the_ID(); ?>-images" class="search-attachment-image image-anchor <?php $options = get_option( 'taskrocket_settings' ); if ($options['use_thickbox'] == true) { echo "thickbox"; } ?>" title="<?php echo $filename; ?>">
                    <img src="<?php echo $filethumb; ?>" /></a>
                <?php } else { ?>
                    <a href="<?php echo wp_get_attachment_url( $post_id ); ?>" class="search-attachment-file download-icon" title="<?php _e( "Download", "taskrocket" ); ?>"><span><?php echo get_post_mime_type( $post_id ); ?></span></a>
                <?php } ?>
        <?php } else { ?>
            <h2><a href="<?php the_permalink(); ?>?source=search"><?php the_title(); ?></a></h2>
        <?php } ?>

    <?php // ... otherwise you must be a project contributor.
    } else { ?>

        <?php if( get_post_meta($post->ID, 'private', TRUE) == 'yes' ) { ?>
            <h2><?php _e( "Private Task", "taskrocket" ); ?></h2>
        <?php } else { ?>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <?php } ?>

    <?php } ?>
    
    <p class="project-result">
    <?php if(get_post_type( $post_id ) !== "attachment") { ?>
        <a href="<?php echo home_url(); ?>/<?php echo get_option( 'category_base' ); ?>/<?php $category = get_the_category(); echo $category[0]->category_nicename; ?>">
            
        
            <?php 
                if (get_the_author_meta( 'first_name') !== "" ) {
                    echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
                } else {
                    echo $GLOBALS[ 'nameless' ];
                }
            ?> &#10140;  
            
            <?php $category = get_the_category(); echo $category[0]->cat_name; ?>
        </a>
    
    <?php } else { ?>
        <?php 
            if (get_the_author_meta( 'first_name') !== "" ) {
                echo get_the_author_meta( 'first_name' ) . " " . get_the_author_meta( 'last_name' );
            } else {
                echo $GLOBALS[ 'nameless' ];
            }
        ?>
    <?php } ?>
    </p>

    <p class="task-details">
    <?php require($GLOBALS[ 'theme_includes' ] . 'task-bar.php'); ?>
    </p>
    <?php require($GLOBALS[ 'theme_includes' ] . 'task-author.php'); ?>
    

    <em class="priority-<?php echo get_post_meta($post->ID, 'priority', TRUE); ?>"></em>

</div>