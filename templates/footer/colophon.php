<?php
/**
 * The template for displaying the standard colophon
 *
 * @package Customizr
 * @since Customizr 3.5.0
 */
?>
<div class="colophon" <?php czr_echo('element_attributes') ?>>
  <div class="container">
    <div class="row-fluid">
      <div class="span3 pull-left"><?php
        if ( is_rtl() ) {
          if ( czr_has('footer_btt') )
            czr_render_template('footer/footer_btt');
        } else
          if ( czr_has('footer_social_block') )
            czr_render_template('modules/social_block', 'footer_social_block');
    ?></div>
      <div class="span6"><?php
        if ( czr_has('footer_credits') )
         czr_render_template('footer/footer_credits');
    ?></div>
      <div class="span3"><?php
        if ( is_rtl() ) {
          if ( czr_has('footer_social_block') )
            czr_render_template('modules/social_block', 'footer_social_block');
        } else
          if ( czr_has('footer_btt') )
            czr_render_template('footer/footer_btt');
    ?></div>
    </div><!-- .row-fluid -->
  </div><!-- .container -->
</div>
