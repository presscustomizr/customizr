<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * Used to display either your home displaying latest posts or your blog page or your empty front
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>
  <?php

    /* SLIDERS : standard or slider of posts */
    if ( czr_fn_has('main_slider') ) {
      czr_fn_render_template('modules/slider/slider', 'main_slider');
    }
    if( czr_fn_has( 'main_posts_slider' ) ) {
      czr_fn_render_template('modules/slider/slider', 'main_posts_slider');
    }
  ?>

  <?php do_action('__before_main_wrapper'); ?>
    <?php
      /* FEATURED PAGES */
      if ( czr_fn_has( 'featured_pages' ) )
        czr_fn_render_template('modules/featured-pages/featured_pages', 'featured_pages');
    ?>
    <div id="main-wrapper" class="container">

      <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <div class="container" role="main">
        <div class="<?php czr_fn_column_content_wrapper_class() ?>">
          <?php
            if ( czr_fn_has('left_sidebar') ) { czr_fn_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php czr_fn_article_container_class() ?>">
                <?php
                  if ( is_home() && ! is_front_page() )//blog page title
                    if ( czr_fn_has('posts_list_headings') ) { czr_fn_render_template('content/post-lists/posts_list_headings', 'posts_list_headings'); }
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();

                      if ( czr_fn_has('post_list_grid') ) {
                        czr_fn_render_template('modules/grid/grid_wrapper', 'post_list_grid');
                      }
                      elseif ( czr_fn_has('post_list') ){
                        czr_fn_render_template('content/post-lists/post_list_wrapper', 'post_list');
                      }else {
                        czr_fn_render_template('content/singles/page_content', 'page');
                      }
                    }//endwhile;
                  }
                ?>
                <?php
                    if ( czr_fn_has('comments') ) { czr_fn_render_template('content/comments/comments'); }

                    if ( is_singular() && czr_fn_has('post_navigation_singular') )
                      czr_fn_render_template('content/singles/post_navigation_singular', 'post_navigation_singular');
                    elseif ( is_home() && ! is_front_page() && czr_fn_has('post_navigation_posts') )
                    if ( czr_fn_has('post_navigation_posts') )
                      czr_fn_render_template('content/post-lists/post_navigation_posts', 'post_navigation_posts');
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( czr_fn_has('right_sidebar') ) { czr_fn_render_template('content/sidebars/right_sidebar', 'right_sidebar'); }
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>
      <?php if ( czr_fn_has('footer_push') ) { czr_fn_render_template('footer/footer_push', 'footer_push'); } ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
