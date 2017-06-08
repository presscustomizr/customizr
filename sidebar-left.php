<?php
/**
 * The left sidebar template
 *
 *
 * @package Customizr
 * @since Customizr 3.1.0
 */
if ( apply_filters( 'czr_modern_style', false ) ) {
  do_action( 'czr_modern_style_tmpl', 'sidebar-left' );
  return;
}
dynamic_sidebar( 'left' );