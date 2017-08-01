<?php
/**
 * The right sidebar template
 *
 *
 * @package Customizr
 * @since Customizr 3.1.0
 */
if ( apply_filters( 'czr_ms', false ) ) {
  do_action( 'czr_ms_tmpl', 'sidebar-right' );
  return;
}
dynamic_sidebar( 'right' );