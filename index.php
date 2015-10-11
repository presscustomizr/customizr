<?php
/**
 * The main template file. Includes the loop.
 *
 *
 * @package Customizr
 * @since Customizr 1.0
 */

      do_action( '__before_main_wrapper' ); ##hook of the header with get_header
        do_action( '__main_wrapper');
      do_action( '__after_main_wrapper' );##hook of the footer with get_footer
?>