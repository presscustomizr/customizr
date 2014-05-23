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
<?php tc__f('rec' , __FILE__ , __FUNCTION__ ); ?>

	<?php do_action( '__before_body' ); ?>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">
		
		<?php do_action( '__before_header' ); ?>

	   	<header class="tc-header clearfix row-fluid" role="banner">
			
			<?php do_action( '__header' ); ?>

		</header>

		<?php 
		 //This hook is filtered with the slider
		do_action ( '__after_header' )
		?>
