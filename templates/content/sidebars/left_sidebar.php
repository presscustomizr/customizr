<?php
/*
* Template for displaying the left sidebar
*/
?>
<div class="left tc-sidebar <?php czr_echo('element_class') /* the width depends on the layout see the sidebar model*/ ?>" <?php czr_echo('element_attributes') ?> >
  <div id="left" class="widget-area" role="complementary">
    <?php if ( czr_has( 'left_sidebar_social_block' ) )
      czr_render_template('modules/social_block', 'left_sidebar_social_block');
    ?>
    <?php do_action( '__before_inner_left_sidebar' ) ?>
    <?php dynamic_sidebar( 'left' ) ?>
    <?php do_action( '__after_inner_left_sidebar' ) ?>
  </div>
</div>
