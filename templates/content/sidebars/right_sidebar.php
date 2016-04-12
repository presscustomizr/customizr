<?php
/*
* Template for dislplaying the right sidebar
*/
?>
<div class="right tc-sidebar <?php tc_echo('element_class') /* the width depending on the layout see the sidebar model*/ ?>" <?php tc_echo('element_attributes') ?> >
  <div id="right" class="widget-area" role="complementary">
    <?php if ( tc_has( 'right_sidebar_social_block' ) ) {
      tc_render_template('modules/social_block', 'right_sidebar_social_block');
    }
    ?>
    <?php dynamic_sidebar( 'right-sidebar' ) ?>
  </div>
</div>
