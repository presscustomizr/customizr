<?php
 /**
 * The template for displaying the footer.
 *
 *
 * @package Customizr
 * @since Customizr 3.0
 */
    	do_action( '__before_footer' );
    		do_action( '__footer_main');
      ?>
    </div><!-- //#tc-page-wrapper -->
    <?php
    do_action( '__after_page_wrap' );
    wp_footer(); //do not remove, used by the theme and many plugins
    do_action( '__after_footer' ); ?>
  </body>
  <?php do_action( '__after_body' ); ?>
</html>