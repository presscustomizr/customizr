<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * By design it has no sidebars
 */
?>
<?php get_header() ?>

  <?php do_action('__before_main_wrapper'); ?>
    <div id="main-wrapper" class="section bg">

      <?php do_action('__before_main_container'); ?>

      <div class="container" role="main">

        <?php if ( czr_fn_has('breadcrumb') ) { czr_fn_render_template('modules/breadcrumb'); } ?>

        <div class="<?php czr_fn_column_content_wrapper_class() ?>">

          <?php do_action('__before_content'); ?>

          <div id="content" class="col-xs-12 col-md-8 push-md-2 article-container">
              <?php czr_fn_render_template('content/no-results/404', '404') ?>
          </div>

          <?php do_action('__after_content'); ?>

        </div><!-- .column-content-wrapper -->
      </div><!-- .container -->

      <?php do_action('__after_main_container'); ?>

    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
