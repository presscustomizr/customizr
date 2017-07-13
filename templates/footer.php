<?php
/**
 * The template for displaying the site footer
 *
 * Contains the closing of the #tc-page-wrap div and all content after
 */

      if ( czr_fn_is_registered_or_possible('footer') ) {
        //will fire do_action( '__before_footer' )
        czr_fn_render_template( 'footer' );
        //will fire do_action( '__after_footer' )
      }
    ?>
    </div><!-- end #tc-page-wrap -->

    <?php
      do_action('__after_page_wrapper');

      if ( czr_fn_is_registered_or_possible('search_full_page') )
        czr_fn_render_template( 'modules/search/search_full_page' );

      if ( czr_fn_is_registered_or_possible('btt_arrow') )
        czr_fn_render_template( 'footer/btt_arrow' );

      wp_footer();

      do_action( '__after_wp_footer' );
    ?>
  </body>
  <?php do_action( '__after_body' ) ?>
</html>
