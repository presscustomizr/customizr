<?php
/*
* Template for dislplaying the left sidebar
*/
?>
<div class="left tc-sidebar <?php tc_echo('element_class') /* the width depending on the layout see the sidebar model*/ ?>" <?php tc_echo('element_attributes') ?> >
  <div id="left" class="widget-area" role="complementary">
    <?php if ( tc_has( 'left_sidebar_social_block' ) )
      tc_render_template('modules/social_block', 'left_sidebar_social_block');
    ?>
    <?php dynamic_sidebar( 'left-sidebar' ) ?>
  </div>
</div>
