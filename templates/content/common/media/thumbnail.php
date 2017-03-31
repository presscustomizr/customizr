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
czr_fn_post_action( $link = czr_fn_get( 'lightbox_url' ), $class = 'expand-img' );
