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
 *
 * @package Customizr
 * @since Twenty Customizr 3.5
 */
?>
<?php get_header() ?>
  <?php do_action('__before_main_wrapper'); ?>

    <div id="main-wrapper" class="container">

      <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

      <?php do_action('__before_main_container'); ?>
      <div class="container" role="main">
        <div class="<?php czr_fn_column_content_wrapper_class() ?>">
          <?php
            if ( czr_fn_has('left_sidebar') ) { czr_fn_render_template('content/sidebars/left_sidebar', 'left_sidebar'); }
          ?>

              <?php do_action('__before_content'); ?>

              <div id="content" class="<?php czr_fn_article_container_class() ?>">
                <?php
                  if ( czr_fn_has('posts_list_headings') ) { czr_fn_render_template('content/post-lists/posts_list_headings', 'posts_list_headings'); }
                  if ( have_posts() ) {
                    while ( have_posts() ) {
                      the_post();

                      if ( czr_fn_has('post_list_grid') ) {
                        czr_fn_render_template('modules/grid/grid_wrapper', 'post_list_grid');
                      }
                      elseif ( czr_fn_has('post_list') ){
                        czr_fn_render_template('content/post-lists/post_list_wrapper', 'post_list');
                      }
                    }//endwhile;
                  }
                ?>
                <?php
                    if ( czr_fn_has('post_navigation_posts') )
                      czr_fn_render_template('content/post-lists/post_navigation_posts', 'post_navigation_posts');
                ?>
              </div>

              <?php do_action('__after_content'); ?>

            <?php
            if ( czr_fn_has('right_sidebar') ) { czr_fn_render_template('content/sidebars/right_sidebar', 'right_sidebar'); }
          ?>
        </div>
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>
      <?php if ( czr_fn_has('footer_push') ) { czr_fn_render_template('footer/footer_push', 'footer_push'); } ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
