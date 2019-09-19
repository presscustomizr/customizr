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
  <?php czr_fn_require_once( CZR_MAIN_TEMPLATES_PATH . 'head-no-model.php' ) ?>

  <body <?php body_class(); ?>>
    <?php
    // see https://github.com/presscustomizr/customizr/issues/1722
    if ( function_exists( 'wp_body_open' ) ) {
      wp_body_open();
    } else {
      do_action( 'wp_body_open' );
    }

    if ( apply_filters( 'czr_skip_link', true ) ) :
    ?>
      <a class="screen-reader-text skip-link" href="<?php echo apply_filters( 'czr_skip_link_anchor', '#content' ); ?>"><?php esc_html_e( 'Skip to content', 'customizr' ) ?></a>
    <?php
    endif;

    if ( czr_fn_is_registered_or_possible('sidenav') && czr_fn_is_registered_or_possible('header') ) {
      czr_fn_render_template( 'header/parts/sidenav' );
    }
    ?>

    <?php do_action('__before_page_wrapper'); ?>

    <div id="tc-page-wrap" class="<?php czr_fn_page_wrapper_class() ?>">

      <?php
        //will fire do_action( '__before_header' )

        //as of the 13th of July 2017, the header model associated with the header template has been registered already on wp.
        //Unlike most of the other models, that are registered on the fly ( or on the flight like Rocco can say .. AH AH AH)
        //in this case the following function prints the template located in templates/part/header.php
        czr_fn_render_template( 'header' );

        //will fire do_action( '__after_header' )
      ?>
