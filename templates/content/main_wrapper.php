<?php
/**
 * The template for displaying the main wrapper
 *
 * @package WordPress
 * @subpackage Customizr
 * @since Customizr 3.5.0
 */
?>
<div id="main-wrapper" class="container" <?php tc_echo('element_attributes') ?>>
  <?php if ( tc_has('breadcrumb') ) { tc_render_template('modules/breadcrumb'); } ?>
  <?php do_action('__before_main_container'); ?>
  <div class="container" role="main">
    <div class="<?php tc_echo( 'column_content_class' ) ?>">
      <?php
        if ( tc_has('left_sidebar') )
          tc_render_template('modules/widget_area_wrapper', 'left_sidebar');

          if ( tc_has('content_wrapper') )
            tc_render_template('content/content_wrapper');

        if ( tc_has('right_sidebar') )
          tc_render_template('modules/widget_area_wrapper', 'right_sidebar');
        //tc_render_template('content/main_container');
        //do_action( '__main_container__')
      ?>
    </div>
  </div>
  <?php do_action('__after_main_container'); ?>
</div>
