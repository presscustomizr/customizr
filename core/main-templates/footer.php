<?php
/**
 * The template for displaying the site footer
 *
 * Contains the closing of the #tc-page-wrap div and all content after
 */

      do_action( '__before_footer' );
      if ( czr_fn_has('footer') )
        czr_fn_render_template( 'footer' );
    ?>
    </div><!-- end #tc-page-wrap -->

    <?php
      do_action('__after_page_wrapper');

      if ( czr_fn_has('saerch_full_page') )
        czr_fn_render_template( 'modules/search_full_page' );

      if ( czr_fn_has('btt_arrow') )
        czr_fn_render_template( 'footer/btt_arrow' );

      wp_footer();

      do_action( '__after_footer' );
    ?>
  </body>
  <?php do_action( '__after_body' ) ?>
</html>
