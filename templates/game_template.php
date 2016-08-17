<?php

/** 
 * The game adding template
 */

get_header();
?>
<?php
function link_scr($arr) {

         if($arr){
             foreach ($arr as $script_url) {

                echo "<script type='text/javascript' src = ". $script_url. "></script>"; 

        }
     }
}

function sort_link($files) {
       if(!empty($files)) {
        $file_num = $files;
        $config = [];
        $objects = [];
        $abs_obj = [];
        $scenes = [];
        $core = [];
        $lib = [];
        $other = [];
        foreach($files as $file){
            if(isset($file["url"])) {
            $single_scr = $file['url'];
            if(pathinfo($single_scr, PATHINFO_EXTENSION) == 'js') {
            if(strpos($single_scr, 'config')){
                $config[] = $single_scr;
            } elseif (strpos($single_scr, 'objects')) {

                if(strpos($single_scr, 'sprite_')){
                    $abs_obj[] = $single_scr;
                } else {
                $objects[] = $single_scr;                    
                }

            } elseif (strpos($single_scr, 'scenes')) {
                $scenes[] = $single_scr;
            } elseif (strpos($single_scr, 'core')) {
                $core[] = $single_scr;
            } elseif (strpos($single_scr, '.min.')) { //put lib name here
                $lib[] = $single_scr;
            } else {
                $other[] = $single_scr;
            }
        }

        }
    }
           link_scr($lib);
           link_scr($config);
           link_scr($abs_obj);
           link_scr($objects);
           link_scr($scenes);
           link_scr($core);
           link_scr($other);
     } 
}


function link_files() {

    $files_attached = get_post_meta(get_the_ID(), 'attached_files', true);
    delete_post_meta(get_the_ID(), 'attached_files', true);
    $files_uploaded = get_post_meta(get_the_ID(), 'uploaded_files', true);
    delete_post_meta(get_the_ID(), 'uploaded_files', true);

    sort_link($files_attached);
    sort_link($files_uploaded);
}

 ?>
 
 <script type='text/javascript'>

     var loc = <?php echo json_encode(get_post_meta(get_the_ID(), 'upload_dir', true)["url"]); ?>;
     document.write(loc);
     var data = ".container {width: 100%;}canvas {border: 1px solid black;border-radius: 5px;margin: 0 auto;display: block;background-color:white;}@font-face {font-family: 'Interstate Regular';src: url('"+loc+"/INTRCM_0.ttf');}@font-face {font-family: 'Interstate Bold';src: url('"+loc+"/INTBDCM_0.ttf');}";
     var css = document.createElement('style');
     css.innerHTML = data;
      document.head.appendChild(css);

 </script>

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
                echo '<canvas id = "canvas" width="1024" height="800"></canvas>';
                echo "<script src='https://code.createjs.com/createjs-2015.11.26.min.js'></script>";
                link_files();
				?>
                <span class="jq"></span>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
<?php endwhile; } ?>


<?php get_footer(); ?>