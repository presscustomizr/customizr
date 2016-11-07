<?php
/**
 * The Header for Customizr.
 *
 * Displays all of the <head> section and everything up till <div id="main-wrapper">
 *
 * @package Customizr
 * @since Customizr 1.0
 */
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
	<?php
		//the '__before_body' hook is used by TC_header_main::$instance->tc_head_display()
		do_action( '__before_body' );
	?>

	<body <?php body_class(); ?> <?php echo apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"') ?>>

    <?php do_action( '__before_page_wrapper' ); ?>

    <div id="tc-page-wrap" class="<?php echo implode( " ", apply_filters('tc_page_wrap_class', array() ) ) ?>">
      <?php
      do_action( '__before_header' );
  		  do_action( '__header_main');
      do_action ( '__after_header' ); //This hook is used for the slider
      ?>
