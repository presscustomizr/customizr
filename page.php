<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>


  <?php //tc_render_template('content', 'main_content'); ?>


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
                  //if ( tc_has('main_loop') ) { tc_render_template('content/loop', 'main_loop'); }
                  ?>


                  <?php
                    if ( have_posts() ) {
                      while ( have_posts() ) {
                        the_post();
                        ?>
                        <article <?php tc_echo( 'article_selectors', 'singular_article' ) ?> <?php tc_echo( 'element_attributes', 'singular_article' ) ?>>
                          <?php
                            do_action( '__before_inner_singular_article' );

                            if ( tc_has('singular_headings') ) {
                              tc_render_template('content/singles/singular_headings', 'singular_headings');
                            }

                              tc_render_template('content/singles/page_content');

                            do_action( '__after_inner_singular_article' );
                            //do_action( '__article__' ) ?>
                        </article>
                        <?php
                      }//endwhile;
                    }//endif;
                  ?>



                <?php
                  if ( tc_has('comments') ) { tc_render_template('content/comments/comments'); }
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( tc_has('right_sidebar') ) { tc_render_template('content/sidebars/right_sidebar', 'right_sidebar'); }
            //tc_render_template('content/main_container');
            //do_action( '__main_container__')
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>
      <?php if ( tc_has('footer_push') ) { tc_render_template('footer/footer_push', 'footer_push'); } ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>


<?php get_footer() ?>
