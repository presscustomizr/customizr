<?php
/**
 * The template for displaying pages
 * (used also to display a static front page)
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>

  <?php

    /* SLIDERS : standard or slider of posts */
    if ( czr_has('main_slider') ) {
      czr_render_template('modules/slider/slider', 'main_slider');
    }
    if( czr_has( 'main_posts_slider' ) ) {
      czr_render_template('modules/slider/slider', 'main_posts_slider');
    }

  ?>

  <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container">

      <?php if ( czr_has('breadcrumb') ) { czr_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <?php
      /* FEATURED PAGES */
      if ( czr_has( 'featured_pages' ) )
        czr_render_template('modules/featured-pages/featured_pages', 'featured_pages');
      ?>
      <div class="container" role="main">
        <div class="<?php tc_column_content_wrapper_class() ?>">
          <?php
            if ( czr_has('left_sidebar') ) { czr_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php tc_article_container_class() ?>">
                <?php
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();
                      czr_render_template('content/singles/page_content');
                    }//endwhile;
                  }//endif;
                ?>
                <?php
                  if ( czr_has('comments') ) czr_render_template('content/comments/comments');
                  if ( czr_has('post_navigation_singular') ) czr_render_template('content/singles/post_navigation_singular', 'post_navigation_singular');
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( czr_has('right_sidebar') ) { czr_render_template('content/sidebars/right_sidebar', 'right_sidebar'); }
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>
      <?php if ( czr_has('footer_push') ) { czr_render_template('footer/footer_push', 'footer_push'); } ?>

    </div><!-- #main-wrapper -->

  <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
