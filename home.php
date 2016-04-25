<?php
/**
 * The template for displaying the blog
 * (either your home displaying latest posts or your blog page or your empty front)
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>

  <?php

    /* SLIDERS : standard or slider of posts */
    if ( tc_has('main_slider') ) {
      tc_render_template('modules/slider/slider', 'main_slider');
    }
    if( tc_has( 'main_posts_slider' ) ) {
      tc_render_template('modules/slider/slider', 'main_posts_slider');
    }
  ?>

  <?php do_action('__before_main_wrapper'); ?>
    <?php
      /* FEATURED PAGES */
      if ( tc_has( 'featured_pages' ) )
        tc_render_template('modules/featured-pages/featured_pages', 'featured_pages');
    ?>
    <div id="main-wrapper" class="container" <?php tc_echo('element_attributes', 'main_content') ?>>

      <?php if ( tc_has('breadcrumb') ) { tc_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <div class="container" role="main">
        <div class="<?php tc_echo ('column_content_class', 'main_content' ) ?>">
          <?php
            if ( tc_has('left_sidebar') ) { tc_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php tc_echo( 'article_wrapper_class', 'main_content' ) ?>">
                <?php
                  if ( is_home() && ! is_front_page() )//blog page title
                    if ( tc_has('posts_list_headings') ) { tc_render_template('content/post-lists/post_list_headings', 'posts_list_headings'); }
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();

                      if ( tc_has('post_list_grid') ) {
                        tc_render_template('modules/grid/grid_wrapper', 'post_list_grid');
                      }
                      elseif ( tc_has('post_list') ){
                        tc_render_template('content/post-lists/post_list_wrapper', 'post_list');
                      }
                    }//endwhile;
                  }
                ?>
                <?php
                    if ( tc_has('post_navigation_posts') )
                      tc_render_template('content/post-lists/post_navigation_posts', 'post_navigation_posts');
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( tc_has('right_sidebar') ) { tc_render_template('content/sidebars/right_sidebar', 'right_sidebar'); }
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>
      <?php if ( tc_has('footer_push') ) { tc_render_template('footer/footer_push', 'footer_push'); } ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
