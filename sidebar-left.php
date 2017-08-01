<?php
/**
 * The left sidebar template
 *
 *
 * @package Customizr
 * @since Customizr 3.1.0
 */
if ( apply_filters( 'czr_ms', false ) ) {
  do_action( 'czr_ms_tmpl', 'sidebar-left' );
  return;
}
dynamic_sidebar( 'left' );