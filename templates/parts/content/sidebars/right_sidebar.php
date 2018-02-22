<?php
/*
* Template for dislplaying the right sidebar
*/
?>
<div class="right sidebar tc-sidebar <?php czr_fn_echo('element_class')?>" <?php czr_fn_echo('element_attributes') ?>>
  <div id="right" class="widget-area" role="complementary">
    <?php do_action( '__before_inner_right_sidebar' ) ?>
    <?php if ( czr_fn_is_registered_or_possible('rights_social_block') ) : ?>
      <aside class="social-block widget widget_social">
        <div class="social-links">
          <?php czr_fn_render_template( 'modules/common/social_block' ) ?>
        </div>
      </aside>
    <?php endif ?>
    <?php do_action( '__before_right_sidebar_widgets' ) ?>
    <?php dynamic_sidebar( 'right' ) ?>
    <?php do_action( '__after_right_sidebar_widets' ) ?>
    <?php do_action( '__after_inner_right_sidebar' ) ?>
  </div>
</div>