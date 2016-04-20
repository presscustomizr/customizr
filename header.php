<?php
/**
 * The template for displaying the site header
 *
 * Displays all of the head element and everything up until the header.tc-header div.
 *
 * @package WordPress
 * @subpackage Customizr
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
<html <?php language_attributes(); ?> >
<!--<![endif]-->
  <?php tc_render_template('header/head'); ?>

  <body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
    <?php if ( tc_has('sidenav') && tc_has('header') ){ tc_render_template('header/sidenav'); }; ?>

    <?php do_action('__before_page_wrapper'); ?>

    <div id="tc-page-wrap">

      <?php tc_render_template('header'); ?>
