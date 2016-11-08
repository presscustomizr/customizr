<?php
/**
 * The Header for Customizr.
 *
 * Displays all of the <head> section and everything up till <div id="main-wrapper">
 *
 * @package Customizr
 * @since Customizr 1.0
 */
if ( apply_filters( 'czr_four_do', false ) ) {
  do_action( 'czr_four_template', 'header' );
  return;
}
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
		//the '__before_body' hook is used by CZR_header_main::$instance->czr_fn_head_display()
		do_action( '__before_body' );
	?>

	<body <?php body_class(); ?> <?php echo apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"') ?>>

    <?php do_action( '__before_page_wrapper' ); ?>

    <div id="tc-page-wrap" class="<?php echo implode( " ", apply_filters('tc_page_wrap_class', array() ) ) ?>">

  		<?php do_action( '__before_header' ); ?>

  	   	<header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>" role="banner">
  			<?php
  				// The '__header' hook is used with the following callback functions (ordered by priorities) :
  				//CZR_header_main::$instance->tc_logo_title_display(), CZR_header_main::$instance->czr_fn_tagline_display(), CZR_header_main::$instance->czr_fn_navbar_display()
  				do_action( '__header' );
  			?>
  		</header>
  		<?php
  		 	//This hook is used for the slider : CZR_slider::$instance->czr_fn_slider_display()
  			do_action ( '__after_header' )
  		?>