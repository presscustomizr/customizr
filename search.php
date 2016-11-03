<?php
/**
 * The search template file
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
    <div id="main-wrapper" class="section">
      <?php
        if ( czr_fn_has('post_list_search_header') ):
      ?>
        <div class="container-fluid">
          <?php czr_fn_render_template('content/post-lists/post_list_search_header', 'post_list_search_header');?>
        </div>
      <?php
        endif;
      ?>
      <?php do_action('__before_main_container'); ?>

      <div class="container" role="main">

        <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

        <div class="<?php czr_fn_column_content_wrapper_class() ?>">

          <?php do_action('__before_content'); ?>

          <div id="content" class="<?php czr_fn_article_container_class() ?>">
            <?php
              if ( have_posts() && czr_fn_is_list_of_posts() ) {
                while ( have_posts() ) {
                  the_post();
                  // Render list of posts based on the options
                  czr_fn_render_list_of_posts();
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
