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
	<?php do_action( '__head' ); ?>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

	   	<header class="tc-header clearfix" role="banner">
			
			<?php do_action( 'before_menu' ); ?>

	      	<div class="navbar-wrapper clearfix row-fluid">
          	
          	<!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
	            <?php 
	            	do_action( '__logo_title' );
					
					do_action( '__tagline' );
	            
					do_action ( '__menu' );
				?>

        	</div><!-- /.navbar-wrapper -->

		</header>

	  		<?php do_action ( '__slider' ) ?>

		<div id="main-wrapper" class="container">
