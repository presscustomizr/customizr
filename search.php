<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * Used to display either your home displaying latest posts or your blog page or your empty front
 *
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
    <div id="main-wrapper" class="section bg">

      <?php do_action('__before_main_container'); ?>

      <div class="container" role="main">

        <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

        <div class="<?php czr_fn_column_content_wrapper_class() ?>">

          <?php do_action('__before_content'); ?>

          <div id="content" class="<?php czr_fn_article_container_class() ?>">
            <?php
              if ( is_home() && ! is_front_page() )//blog page title
                if ( czr_fn_has('posts_list_headings') ) { czr_fn_render_template('content/post-lists/posts_list_headings', 'posts_list_headings'); }
              if ( have_posts() ) {
                while ( have_posts() ) {
                  the_post();

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
                    }

                  }
                }//endwhile;
              }else {//no results
                czr_fn_render_template('content/no-results/search-no-results', 'search_no_results');
              }
            ?>
          </div>

          <?php do_action('__after_content'); ?>

          <?php
            if ( czr_fn_has('left_sidebar') ) {
              czr_fn_render_template('content/sidebars/left_sidebar', 'left_sidebar');
            }
          ?>
          <?php
            if ( czr_fn_has('right_sidebar') ) {
              czr_fn_render_template('content/sidebars/right_sidebar', 'right_sidebar');
            }
          ?>
        </div><!-- .column-content-wrapper -->

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
          czr_fn_render_template( 'content/post-lists/post_navigation_posts', 'post_navigation');
        ?>
        </div>
      </div>
    <?php endif ?>

<?php get_footer() ?>
