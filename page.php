<?php
/**
 * The template for displaying pages
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
    if ( tc_has('main_slider') ) {
      tc_render_template('modules/slider/slider', 'main_slider');
    }
    if( tc_has( 'main_posts_slider' ) ) {
      tc_render_template('modules/slider/slider', 'main_posts_slider');
    }

  ?>

  <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container" <?php tc_echo('element_attributes', 'main_content') ?>>

      <?php if ( tc_has('breadcrumb') ) { tc_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <?php
      /* FEATURED PAGES */
      if ( tc_has( 'featured_pages' ) )
        tc_render_template('modules/featured-pages/featured_pages', 'featured_pages');
      ?>
      <div class="container" role="main">
        <div class="<?php echo implode(' ', tc_get( 'column_content_class', 'main_content' ) );  ?>">
          <?php
            if ( tc_has('left_sidebar') ) { tc_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php echo implode(' ', tc_get( 'article_wrapper_class', 'main_content' ) ); ?>">
                <?php
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();
                      tc_render_template('content/singles/page_content');
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