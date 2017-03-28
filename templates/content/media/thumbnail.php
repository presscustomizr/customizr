<?php
/**
 * The template for displaying a thumbnail media
 *
 *
 * @package Customizr
 */

$thumbnail_item = czr_fn_get( 'thumbnail_item' );

/* Lightbox */
czr_fn_post_action( $link = $thumbnail_item[ 'lightbox_url' ], $class = 'expand-img' );
/* img */
echo $thumbnail_item[ 'img' ];
