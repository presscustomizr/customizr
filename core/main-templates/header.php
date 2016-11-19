<?php
/**
 * The template for displaying the site header
 *
 * Displays all of the head element and everything up until the header.tc-header div.
 *
 * @package Customizr
 * @since Customizr 3.5
 */
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?> class="no-js">
<!--<![endif]-->
  <?php czr_fn_render_template( 'header/head' ) ?>

  <body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <?php if ( czr_fn_has('sidenav') && czr_fn_has('header') ) czr_fn_render_template( 'header/sidenav' ) ?>

    <?php do_action('__before_page_wrapper'); ?>

    <div id="tc-page-wrap">

      <?php czr_fn_render_template( 'header' ); ?>
