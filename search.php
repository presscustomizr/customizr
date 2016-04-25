<?php
/**
 * The template for displaying search results pages
 *
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>
  <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container">

      <?php if ( czr_has('breadcrumb') ) { czr_render_template('modules/breadcrumb'); } ?>

       <div class="container" role="main">
        <div class="<?php tc_column_content_wrapper_class() ?>">
          <?php
            if ( czr_has('left_sidebar') ) { czr_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php tc_article_container_class() ?>">
                <?php
                  if ( have_posts() ) {
                    if ( czr_has('posts_list_headings') ) { czr_render_template('content/post-lists/posts_list_headings', 'posts_list_headings'); }
                    while ( have_posts() ) {
                      the_post();

                      if ( czr_has('post_list_grid') ) {
                        czr_render_template('modules/grid/grid_wrapper', 'post_list_grid');
                      }
                      elseif ( czr_has('post_list') ){
                        czr_render_template('content/post-lists/post_list_wrapper', 'post_list');
                      }
                    }//endwhile;
                  }//endif;
                  else {//no results
                    czr_render_template('content/singles/no_results', 'no_results');
                  }
                ?>
                <?php
                    if ( czr_has('post_navigation_posts') )
                      czr_render_template('content/post-lists/post_navigation_posts', 'post_navigation_posts');
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
