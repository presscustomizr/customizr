<?php
if ( tc_has('sidenav') && tc_has('header') )
  tc_render_template('header/sidenav');
?>
<div id="tc-page-wrap" <?php tc_echo('element_attributes') ?>>
  <?php
    tc_render_template('header/header');
    tc_render_template('content/main_wrapper');
    tc_render_template('footer/footer');
  ?>
</div>
