<?php
/**
 * The template for displaying the site footer
 *
 * Contains the closing of the #tc-page-wrap div and all content after
 *
 * @package Customizr
 * @since Customizr 3.5
 */
?>
      <?php czr_fn_render_template('footer'); ?>
    </div>

    <?php do_action('__after_page_wrapper'); ?>
    <?php if ( czr_fn_has('btt_arrow') ){ czr_fn_render_template('footer/btt_arrow'); }; ?>
    <?php wp_footer() ?>
  </body>
</html>
