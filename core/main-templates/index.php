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
    /* SLIDERS : standard or slider of posts */
    if ( czr_fn_has('main_slider') ) {
      czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_slider') );
    }

    elseif( czr_fn_has( 'main_posts_slider' ) ) {
      czr_fn_render_template( 'modules/slider/slider', array( 'model_id' => 'main_posts_slider') );
    }

  ?>

  <?php do_action('__before_main_wrapper') ?>

    <div id="main-wrapper" class="section">
      <?php  if ( ! czr_fn_is_home() && ! is_404() ): ?>
        <div class="container-fluid">
          <?php
            if ( czr_fn_has( 'post_list_heading' ) )
              $_heading_template = 'content/headings/post_list_heading';
            elseif ( czr_fn_has( 'search_heading' ) )
              $_heading_template = 'content/headings/search_heading';
            elseif ( czr_fn_has('post_heading') )
              $_heading_template = 'content/headings/post_heading';
            else //pages and fallback
              $_heading_template = 'content/headings/page_heading';

            czr_fn_render_template( $_heading_template );
          ?>
        </div>
      <?php endif ?>


      <?php
        /* FEATURED PAGES */
        if ( czr_fn_has( 'featured_pages' ) )
          czr_fn_render_template( 'modules/featured-pages/featured_pages' );
      ?>

      <?php if ( czr_fn_has('breadcrumb') ) : ?>
        <div class="container">
          <?php czr_fn_render_template( 'modules/breadcrumb' ) ?>
        </div>
      <?php endif ?>

      <?php do_action('__before_main_container') ?>

      <div class="<?php czr_fn_main_container_class() ?>" role="main">

        <?php do_action('__before_content_wrapper'); ?>

        <div class="<?php czr_fn_column_content_wrapper_class() ?>">

          <?php do_action('__before_content'); ?>

          <div id="content" class="<?php czr_fn_article_container_class() ?>">
            <?php

              do_action( '__before_loop' );

              if ( have_posts() ) {
                while ( have_posts() ) {
                  the_post();

                  // Render list of posts based on the options
                  if ( $_is_post_list = czr_fn_is_list_of_posts() ) {
                    if ( czr_fn_has('post_list_grid') ) {
                      czr_fn_render_template( 'modules/grid/grid_wrapper', array( 'model_id' => 'post_list_grid' ) );
                    }
                    elseif ( czr_fn_has('post_list') ){
                      czr_fn_render_template( 'content/post-lists/post_list_alternate' );
                    }elseif ( czr_fn_has('post_list_masonry') ) {
                      czr_fn_render_template( 'content/post-lists/post_list_masonry' );
                    }elseif ( czr_fn_has('post_list_plain') ) {
                      czr_fn_render_template( 'content/post-lists/post_list_plain' );
                    }elseif ( czr_fn_has('post_list_plain_excerpt') ) {
                      czr_fn_render_template( 'content/post-lists/post_list_plain_excerpt', array( 'model_class' => 'content/post-lists/post_list_plain', 'model_args' => array( 'show_full_content' => 0 ) ) );
                    } else { //fallback
                      czr_fn_render_template( 'content/singular/page_content' );
                    }
                  } else {

                    if( is_single() )
                      czr_fn_render_template( 'content/singular/post_content' );
                    else
                      //fallback
                      czr_fn_render_template( 'content/singular/page_content' );

                  }
                }//endwhile;
              }else {//no results
                if ( is_search() )
                  czr_fn_render_template( 'content/no-results/search_no_results' );
                else
                  czr_fn_render_template( 'content/no-results/404' );
              }
            ?>
          </div>

          <?php do_action('__after_content'); ?>

          <?php
            /*
            * SIDEBARS
            */
            /* By design do not display sidebars in 404 */
            if ( ! is_404() ) {
              if ( czr_fn_has('left_sidebar') )
                get_sidebar( 'left' );

              if ( czr_fn_has('right_sidebar') )
                get_sidebar( 'right' );

            }
          ?>
        </div><!-- .column-content-wrapper -->

        <?php do_action('__after_content_wrapper'); ?>

        <?php if ( is_single() && ( czr_fn_has('single_author_info') || czr_fn_has('related_posts') ) ) : ?>
          <div class="row single-post-info">
            <div class="col-xs-12">
            <?php
              if ( czr_fn_has('single_author_info') )
                 czr_fn_render_template( 'content/authors/author_info' );

              if ( czr_fn_has('related_posts') )
                czr_fn_render_template( 'modules/related-posts/related_posts' )
            ?>
            </div>
          </div>
        <?php endif ?>

        <?php if ( czr_fn_has('comments') ) : ?>
          <div class="row">
            <div class="col-xs-12">
              <?php czr_fn_render_template( 'content/comments/comments' ) ?>
            </div>
          </div>
        <?php endif ?>

      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

    <?php
      if ( czr_fn_has('posts_navigation') ) :
    ?>
      <div class="container-fluid">
        <div class="row">
        <?php
          $_pn_template_prefix = $_is_post_list ? 'post_list' : 'singular';
          czr_fn_render_template( "content/navigation/{$_pn_template_prefix}_posts_navigation", array( 'model_id' => 'posts_navigation', 'model_class' => 'content/navigation/posts_navigation' ) );
        ?>
        </div>
      </div>
    <?php endif ?>

<?php get_footer() ?>
