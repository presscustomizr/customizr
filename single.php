<?php
/**
 * The template for displaying all single posts
 *
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
    <?php /* thumbnail in single post */
      if ( tc_has('post_thumbnail') && '__before_main_wrapper' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ) { tc_render_template('content/singles/thumbnail_single', 'post_thumbnail'); }
    ?>
    <div id="main-wrapper" class="container">

      <?php if ( tc_has('breadcrumb') ) { tc_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <?php
      /* FEATURED PAGES */
      if ( tc_has( 'featured_pages' ) )
        tc_render_template('modules/featured-pages/featured_pages', 'featured_pages');
      ?>
      <div class="container" role="main">
        <div class="<?php tc_column_content_wrapper_class() ?>">
          <?php
            if ( tc_has('left_sidebar') ) { tc_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php tc_article_container_class() ?>">
                <?php
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();
                      tc_render_template('content/singles/post_content');
                    }//endwhile;
                  }//endif;
                ?>
                <?php
                  if ( tc_has('comments') ) tc_render_template('content/comments/comments');
                  if ( tc_has('post_navigation_singular') ) tc_render_template('content/singles/post_navigation_singular', 'post_navigation_singular');
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
