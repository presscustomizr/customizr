<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="colophon" <?php tc_echo('element_attributes') ?>>
  <div class="container">
    <div class="row-fluid">
      <div class="span3 pull-left"><?php
        if ( is_rtl() ) {
          if ( tc_has('footer_btt') )
            tc_render_template('footer/footer_btt');
        } else
          if ( tc_has('footer_social_block') )
            tc_render_template('modules/social_block', 'footer_social_block');
    ?></div>
      <div class="span6"><?php
        if ( tc_has('footer_credits') )
         tc_render_template('footer/footer_credits');
    ?></div>
      <div class="span3"><?php
        if ( is_rtl() ) {
          if ( tc_has('footer_social_block') )
            tc_render_template('modules/social_block', 'footer_social_block');
        } else
          if ( tc_has('footer_btt') )
            tc_render_template('footer/footer_btt');
    ?></div>
    </div><!-- .row-fluid -->
  </div><!-- .container -->
</div>
