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
    </div><!-- end #tc-page-wrap -->

    <?php do_action('__after_page_wrapper'); ?>
    <?php if ( czr_fn_has('saerch_full_page') ){ czr_fn_render_template('modules/search_full_page'); }; ?>
    <?php if ( czr_fn_has('btt_arrow') ){ czr_fn_render_template('footer/btt_arrow'); }; ?>
    <?php wp_footer() ?>
  </body>
</html>
