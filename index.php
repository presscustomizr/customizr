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
      czr_fn_render_template('modules/slider/slider', 'main_slider');
    }

    elseif( czr_fn_has( 'main_posts_slider' ) ) {
      czr_fn_render_template('modules/slider/slider', 'main_posts_slider');
    }

  ?>

  <?php do_action('__before_main_wrapper') ?>
    <div id="main-wrapper" class="section">
      <?php
        if ( ! czr_fn_is_home() ):
      ?>
        <div class="container-fluid">
          <?php
            if ( czr_fn_has( 'post_list_heading' ) )
              czr_fn_render_template( 'content/post-lists/headings/post_list_heading', 'post_list_heading' );
            elseif ( czr_fn_has( 'post_list_search_heading' ) )
              czr_fn_render_template( 'content/post-lists/headings/post_list_search_heading', 'post_list_search_heading' );
            elseif ( czr_fn_has('post_heading') )
              czr_fn_render_template('content/singular/post_heading', 'post_heading');
            else //fallback
              czr_fn_render_template('content/singular/page_heading', 'page_heading');

          ?>
        </div>
      <?php
        endif;
      ?>
      <?php do_action('__before_main_container'); ?>

      <?php
        /* FEATURED PAGES */
        if ( czr_fn_has( 'featured_pages' ) )
          czr_fn_render_template('modules/featured-pages/featured_pages', 'featured_pages');
      ?>

      <div class="container" role="main">

        <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

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
                      czr_fn_render_template('modules/grid/grid_wrapper', 'post_list_grid');
                    }
                    elseif ( czr_fn_has('post_list') ){
                      czr_fn_render_template('content/post-lists/post_list_alternate', 'post_list');
                    }elseif ( czr_fn_has('post_list_masonry') ) {
                      czr_fn_render_template('content/post-lists/post_list_masonry', 'post_list_masonry');
                    }elseif ( czr_fn_has('post_list_plain') ) {
                      czr_fn_render_template('content/post-lists/post_list_plain', 'post_list_plain');
                    }elseif ( czr_fn_has('post_list_plain_excerpt') ) {
                      czr_fn_render_template('content/post-lists/post_list_plain_excerpt', 'post_list_plain_excerpt');
                    } else { //fallback
                      czr_fn_render_template('content/singular/page_content', 'page_content');
                    }
                  } else {

                    if( is_single() )
                      czr_fn_render_template('content/singular/post_content', 'post_content' );
                    else
                      //fallback
                      czr_fn_render_template('content/singular/page_content', 'page_content');

                  }
                }//endwhile;
              }else {//no results
                if ( is_search() )
                  czr_fn_render_template('content/no-results/search-no-results', 'search_no_results');
                else
                  czr_fn_render_template('content/no-results/404', '404' );
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
                 czr_fn_render_template('content/authors/author_info', 'single_author_info');

              if ( czr_fn_has('related_posts') )
                czr_fn_render_template('modules/related-posts/related_posts_wrapper', 'related_posts_wrapper') ?>
            </div>
          </div>
        <?php endif ?>

        <?php if ( czr_fn_has('comments') ) : ?>
          <div class="row">
            <div class="col-xs-12">
              <?php czr_fn_render_template('content/comments/comments'); ?>
            </div>
          </div>
        <?php endif ?>

      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

    <?php
      if ( czr_fn_has('post_navigation') ) :
    ?>
      <div class="container-fluid">
        <div class="row">
        <?php
          $_post_navigation_template = $_is_post_list ? 'content/post-lists/post_navigation_posts' : 'content/singular/post_navigation_singular';
          czr_fn_render_template($_post_navigation_template, 'post_navigation');
        ?>
        </div>
      </div>
    <?php endif ?>

<?php get_footer() ?>
