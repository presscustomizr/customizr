<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * Used to display either your home displaying latest posts or your blog page or your empty front
 *
 * @package Customizr
 * @since Customizr 3.5
 */
?>
<?php get_header() ?>
  <?php do_action('__before_main_wrapper'); ?>
    <div id="main-wrapper" class="section">

    <?php do_action('__before_main_container'); ?>
      <div class="container" role="main">
        <div class="<?php czr_fn_column_content_wrapper_class() ?>">
          <!-- left sidebar -->
              <?php do_action('__before_content'); ?>

              <div id="content" class="col-md-12 <?php czr_fn_article_container_class() ?>">
                <?php
                   $page = get_post(1);
                   $content = $page->post_content;
                   $content = apply_filters('the_content', $content);
                   $content = str_replace(']]>', ']]>', $content);
                   echo $content;
                ?>
              </div>

              <?php do_action('__after_content'); ?>

          <!-- right sidebar -->
        </div>
      </div><!-- .container -->
    </div><!-- #main-wrapper -->

    <?php do_action('__after_main_wrapper'); ?>

<?php get_footer() ?>
