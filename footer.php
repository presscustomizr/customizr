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
      <?php tc_render_template('footer'); ?>
    </div>

    <?php do_action('__after_page_wrapper'); ?>
    <?php if ( tc_has('btt_arrow') ){ tc_render_template('footer/btt_arrow'); }; ?>
    <?php wp_footer() ?>
  </body>
</html>
