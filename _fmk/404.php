<?php
/**
 *
 * The template for displaying 404 pages (not found)
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
                <?php czr_fn_render_template('content/singles/404', '404') ?>
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
