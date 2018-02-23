<?php
/*
* Template for displaying the left sidebar
*/
?>
<div class="left sidebar tc-sidebar <?php czr_fn_echo('element_class')?>" <?php czr_fn_echo('element_attributes') ?>>
  <div id="left" class="widget-area" role="complementary">
    <?php do_action( '__before_inner_left_sidebar' ) ?>
    <?php if ( czr_fn_is_registered_or_possible('lefts_social_block') ) : ?>
      <aside class="social-block widget widget_social">
        <div class="social-links">
          <?php czr_fn_render_template( 'modules/common/social_block' ) ?>
        </div>
      </aside>
    <?php endif ?>
    <?php do_action( '__before_left_sidebar_widgets' ) ?>
    <?php dynamic_sidebar( 'left' ) ?>
    <?php do_action( '__after_left_sidebar_widets' ) ?>
    <?php do_action( '__after_inner_left_sidebar' ) ?>
  </div>
</div>