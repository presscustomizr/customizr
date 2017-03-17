<?php
/*
* Template for displaying the left sidebar
*/
?>
<div class="left sidebar tc-sidebar <?php czr_fn_echo('element_class')?>" <?php czr_fn_echo('element_attributes') ?>>
  <div id="left" class="widget-area" role="complementary">
    <?php do_action( '__before_inner_left_sidebar' ) ?>
    <?php dynamic_sidebar( 'left' ) ?>
    <?php do_action( '__after_inner_left_sidebar' ) ?>
  </div>
</div>