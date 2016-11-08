<?php
/**
 * The right sidebar template
 *
 *
 * @package Customizr
 * @since Customizr 3.1.0
 */
if ( apply_filters( 'czr_four_do', false ) ) {
  do_action( 'czr_four_template', 'sidebar-right' );
  return;
}
dynamic_sidebar( 'right' );