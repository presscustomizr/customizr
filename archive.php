<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
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
      <?php
        if ( czr_fn_has('post_list_page_header') ):
      ?>
        <div class="container-fluid">
          <?php czr_fn_render_template('content/post-lists/post_list_page_header', 'post_list_page_header');?>
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
              if ( have_posts() ) {
                while ( have_posts() ) {
                  the_post();

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

                }//endwhile;
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
