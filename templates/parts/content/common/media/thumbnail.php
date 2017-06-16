<?php
/**
 * The template for displaying a thumbnail media
 *
 *
 * @package Customizr
 */

/* img */
czr_fn_echo( 'image' );

/* Lightbox Button */
if ( czr_fn_get_property( 'has_lightbox' ) )
    czr_fn_post_action( $link = czr_fn_get_property( 'lightbox_url' ), $class = 'expand-img' );
