<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * Includes the loop.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<?php get_header() ?>


  <?php
    // This hook is used to render the following elements(ordered by priorities) :
    // slider
    // singular thumbnail
    do_action('__before_main_wrapper')
  ?>

    <div id="main-wrapper" class="section">

            <?php
              //this was the previous implementation of the big heading.
              //The next one will be implemented with the slider module
            ?>
          <?php  if ( apply_filters( 'big_heading_enabled', false && ! czr_fn_is_real_home() && ! is_404() ) ): ?>
            <div class="container-fluid">
              <?php
                if ( czr_fn_is_registered_or_possible( 'archive_heading' ) )
                  $_heading_template = 'content/post-lists/headings/archive_heading';
                elseif ( czr_fn_is_registered_or_possible( 'search_heading' ) )
                  $_heading_template = 'content/post-lists/headings/search_heading';
                elseif ( czr_fn_is_registered_or_possible('post_heading') )
                  $_heading_template = 'content/singular/headings/post_heading';
                else //pages and fallback
                  $_heading_template = 'content/singular/headings/page_heading';

                czr_fn_render_template( $_heading_template );
              ?>
            </div>
          <?php endif ?>


          <?php
            /*
            * Featured Pages | 10
            * Breadcrumbs | 20
            */
            do_action('__before_main_container')
          ?>

          <div class="<?php czr_fn_main_container_class() ?>" role="main">

            <?php do_action('__before_content_wrapper'); ?>

            <div class="<?php czr_fn_column_content_wrapper_class() ?>">

                <?php do_action('__before_content'); ?>

                <div id="content" class="<?php czr_fn_article_container_class() ?>">

                  <?php

                    /* Archive regular headings */
                    if ( apply_filters( 'regular_heading_enabled', ! czr_fn_is_real_home() && ! is_404() ) ):

                      if ( czr_fn_is_registered_or_possible( 'archive_heading' ) )
                        czr_fn_render_template( 'content/post-lists/headings/regular_archive_heading',
                          array(
                            'model_class' => 'content/post-lists/headings/archive_heading'
                          )
                        );
                      elseif ( czr_fn_is_registered_or_possible( 'search_heading' ) )
                        czr_fn_render_template( 'content/post-lists/headings/regular_search_heading' );

                    endif;


                    do_action( '__before_loop' );

                    if ( ! czr_fn_is_home_empty() ) {
                        if ( have_posts() && ! is_404() ) {
                            //Problem to solve : we want to be able to inject any loop item ( grid-wrapper, alternate, etc ... ) in the loop model
                            //=> since it's not set yet, it has to be done now.
                            //How to do it ?

                            //How does the loop works ?
                            //The loop has its model CZR_loop_model_class
                            //This loop model might setup a custom query if passed in model args
                            //this loop model needs a loop item which looks like :
                            // Array = 'loop_item' => array(
                            //    (
                            //        [0] => modules/grid/grid_wrapper
                            //        [1] => Array
                            //            (
                            //                [model_id] => post_list_grid
                            //            )

                            //      )
                            // )
                            // A loop item will be turned into 2 properties :
                            // 1) 'loop_item_template',
                            // 2) 'loop_item_args'
                            //
                            //Then, when comes the time of rendering the loop view with the loop template ( templates/parts/loop ), we will fire :
                            //czr_fn_render_template(
                            //     czr_fn_get_property( 'loop_item_template' ),//the loop item template is set the loop model. Example : "modules/grid/grid_wrapper"
                            //     czr_fn_get_property( 'loop_item_args' ) <= typically : the model that we inject in the loop item that we want to render
                            // );

                            //Here, we inject a specific loop item, the main_content, inside the loop
                            //What is the main_content ?
                            //=> depends on the current context, @see czr_fn_get_main_content_loop_item() in core/functions-ccat.php


                            czr_fn_render_template('loop');

                        } else {//no results

                            if ( is_search() )
                              czr_fn_render_template( 'content/no-results/search_no_results' );
                            elseif ( is_404() )
                              czr_fn_render_template( 'content/no-results/404' );
                        }
                    }//not home empty

                    /*
                     * Optionally attached to this hook :
                     * - In single posts:
                     *   - Author bio | 10
                     *   - Related posts | 20
                     * - In posts and pages
                     *   - Comments | 30
                     */
                    do_action( '__after_loop' );
                  ?>
                </div>

                <?php
                  /*
                   * Optionally attached to this hook :
                   * - In single posts:
                   *   - Author bio | 10
                   *   - Related posts | 20
                   * - In posts and pages
                   *   - Comments | 30
                   */
                  do_action( '__after_content' );

                  /*
                  * SIDEBARS
                  */
                  /* By design do not display sidebars in 404 or home empty */
                  if ( ! ( czr_fn_is_home_empty() || is_404() ) ) {
                    if ( czr_fn_is_registered_or_possible('left_sidebar') )
                      get_sidebar( 'left' );

                    if ( czr_fn_is_registered_or_possible('right_sidebar') )
                      get_sidebar( 'right' );

                  }
                ?>

            </div><!-- .column-content-wrapper -->

            <?php do_action('__after_content_wrapper'); ?>


          </div><!-- .container -->

          <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

    <?php
      if ( czr_fn_is_registered_or_possible('posts_navigation') ) :
    ?>
      <div class="container-fluid">
        <?php
          if ( !is_singular() )
            czr_fn_render_template( "content/post-lists/navigation/post_list_posts_navigation" );
          else
            czr_fn_render_template( "content/singular/navigation/singular_posts_navigation" );
        ?>
      </div>
    <?php endif ?>

<?php get_footer() ?>
