<?php

/** 
 * The game adding template
 */

get_header();
?>
<?php
function link_scr($script_url) {

         //if( strpos( $script_url, '.js' ) ) {
            echo "<script type='text/javascript' src = ". $script_url. "></script>";
        //}
}


function link_files() {
    global $files_uploaded;
    $files_attached = get_post_meta(get_the_ID(), 'files_atached_existing', true);
    delete_post_meta(get_the_ID(), 'files_atached_existing', true);
    $files_uploaded = get_post_meta(get_the_ID(), 'files_uploaded', true);
    delete_post_meta(get_the_ID(), 'files_uploaded', true);
    
   if(!empty($files_attached)) {
        $file_num = $files_attached;
        for ($i=0; $i < $file_num ; $i++) {
            $single_scr = get_post_meta(get_the_ID(), 'attached_ex_file' . $i, true);
            link_scr($single_scr);
           delete_post_meta(get_the_ID(), 'attached_ex_file' . $i, true);

        }
    } elseif (!empty($files_uploaded)) {
        $file_num = $files_uploaded;
            for ($i=0; $i < $file_num ; $i++) {
                 $single_scr = get_post_meta(get_the_ID(), 'attached_file' . $i, true);
                 link_scr($single_scr);
                 delete_post_meta(get_the_ID(), 'attached_file' . $i, true);
        }
            }
            else {
            $file_num = 0;
            return $file_num;
    }
}

 ?>
<?php if ( have_posts() ) { while ( have_posts() ) : the_post(); ?>
    <?php
        $post_layout = get_post_meta( get_the_ID(), '_deliver_page_settings_post_view', true );
        $is_full = ( $post_layout == 'full' && deliver_has_post_thumbnail() );
    ?>
    <?php if ( $is_full ) :
    ?>
        <div class="post-image full">
            <div class="post-media parallax skrollable skrollable-between no-parallax" data-bottom-top="top: -50%;" data-top-bottom="top: 0%;">
                <?php echo deliver_post_thumbnail( get_the_ID(), false, 'full' ); ?>
            </div>
            <div class="post-figure">
                <div class="post-header">
                    <h1 class="entry-title" itemprop="headline"><?php the_title(); ?></h1>
                    <h5 class="post-meta">
                        <?php echo deliver_get_post_meta( 'basic' ); ?>
                    </h5>
                </div>
                <h6 class="post-action">
                    <?php if ( deliver_get_option( 'blog_post_show_post_share_button', true ) ) : ?>
                        <span class="post-share">
                            <a href="javascript:void(0)" class="button"><i class="linea-icon-basic-share"></i>Share</a>
                            <?php echo deliver_display_share_buttons(); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( deliver_get_option( 'blog_post_show_post_like_button', true ) ) : ?>
                        <?php echo deliver_post_like( 'button' ); ?>
                    <?php endif; ?>

                    <?php if ( comments_open() ) : ?>
                        <a href="<?php echo esc_url( get_comments_link() ); ?>" class="button post-comment"><i class="linea-icon-basic-message"></i><?php echo get_comments_number(); ?></a>
                    <?php endif; ?>
                </h6>
            </div>
        </div>
    <?php endif; ?>
    <div id="content">
        <div class="<?php echo deliver_get_content_classes( false, 'content-wrapper' ); ?>">
            <div id="main" role="main">
                <?php 
                deliver_get_template( 'content-single', $post_layout );
                $sp = get_post_meta(get_the_ID(),'att');
                var_dump($sp);
                echo "<br>";
                //echo $sp;
                echo '<canvas id = "canvas" width="640" height="480"></canvas>';
                echo "<script src='https://code.createjs.com/createjs-2015.11.26.min.js'></script>";
                link_files();
                echo '<script>preload();</script>';
				?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php endwhile; } ?>


<?php get_footer(); ?>