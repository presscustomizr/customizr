<?php
/**
 * The Header for Customizr.
 *
 * Displays all of the <head> section and everything up till <div id="main-wrapper">
 *
 * @package Customizr
 * @since Customizr 1.0
 */
if ( apply_filters( 'czr_ms', false ) ) {
    //in core init => add_action( 'czr_ms_tmpl', array( $this , 'czr_fn_load_modern_template_with_no_model' ), 10 , 1 );
    //function czr_fn_load_modern_template_with_no_model( $template = null ) {
    //     $template = $template ? $template : 'index';
    //     $this -> czr_fn_require_once( CZR_MAIN_TEMPLATES_PATH . $template . '.php' );
    // }
    do_action( 'czr_ms_tmpl', 'header' );
    return;
}
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7 no-js" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8 no-js" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html class="no-js" <?php language_attributes(); ?>>
<!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
        <?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
			<title><?php wp_title( '|' , true, 'right' ); ?></title>
        <?php endif; ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="profile"  href="https://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<!-- html5shiv for IE8 and less  -->
		<!--[if lt IE 9]>
			<script src="<?php echo CZR_FRONT_ASSETS_URL ?>js/libs/html5.js"></script>
		<![endif]-->
		<?php wp_head(); ?>
	</head>
	<?php
		do_action( '__before_body' );
	?>

	<body <?php body_class(); ?> <?php echo apply_filters('tc_body_attributes' , '') ?>>
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
    ?>
    <?php do_action( '__before_page_wrapper' ); ?>

    <div id="tc-page-wrap" class="<?php echo implode( " ", apply_filters('tc_page_wrap_class', array() ) ) ?>">

  		<?php do_action( '__before_header' ); ?>

  	   	<header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>">
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