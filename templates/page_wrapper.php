<div id="tc-page-wrap" <?php tc_echo('element_attributes') ?>>
  <?php tc_render_template('header/header'); ?>

  <?php do_action('__page_wrapper__'); ?>

  <?php tc_render_template('content/main_wrapper'); ?>

  <?php tc_render_template('footer/footer'); ?>
</div>
