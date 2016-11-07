<?php
/**
* Header actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_head' ) ) :
	class TC_head extends TC_view_base {
    static $instance;
    function __construct( $_args = array() ) {
      self::$instance =& $this;

      // Instanciates the parent class.
      parent::__construct( $_args );
    }


	  /**
		* Displays what is inside the head html tag. Includes the wp_head() hook.
		*
		*
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_render() {
			ob_start();
				?>
				<head>
				    <meta charset="<?php bloginfo( 'charset' ); ?>" />
				    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
            <?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
				      <title><?php wp_title( '|' , true, 'right' ); ?></title>
            <?php endif; ?>
				    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
				    <link rel="profile" href="http://gmpg.org/xfn/11" />
				    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

				   <!-- html5shiv for IE8 and less  -->
				    <!--[if lt IE 9]>
				      <script src="<?php echo TC_BASE_URL ?>inc/assets/js/html5.js"></script>
				    <![endif]-->
				   <!-- Icons font support for IE6-7  -->
				    <!--[if lt IE 8]>
				      <script src="<?php echo TC_BASE_URL ?>inc/assets/css/fonts/lte-ie7.js"></script>
				    <![endif]-->
				    <?php wp_head(); ?>
				    <!--Icons size hack for IE8 and less -->
				    <!--[if lt IE 9]>
				      <link href="<?php echo TC_BASE_URL ?>inc/assets/css/fonts/ie8-hacks.css" rel="stylesheet" type="text/css"/>
				    <![endif]-->
				</head>
				<?php
			$html = ob_get_contents();
		    if ($html) ob_end_clean();
		    echo apply_filters( 'tc_render_head', $html );
		}

	}//end of class
endif;