<?php
/**
* Loads front end CSS
* Inline front end skin
*
*
* @package            Customizr
*/
if ( ! class_exists( 'CZR_resources_styles' ) ) :
   class CZR_resources_styles {
         //Access any method or var of the class with classname::$instance -> var or method():
         static $instance;

         private $_minify_css;
         private $_resources_version;

         function __construct () {

               self::$instance =& $this;

               add_action( 'after_setup_theme'                   , array( $this, 'czr_fn_setup_properties' ), 20 );

               add_action( 'wp_enqueue_scripts'                  , array( $this , 'czr_fn_enqueue_front_styles' ) );

               add_filter( 'czr_user_options_style'              , array( $this , 'czr_fn_write_custom_css') , apply_filters( 'czr_custom_css_priority', 9999 ) );

               add_filter( 'czr_user_options_style'              , array( $this , 'czr_fn_maybe_write_skin_inline_css') );

         }


         //hook: after_setup_theme
         function czr_fn_setup_properties() {

               $this->_resouces_version        = CZR_DEBUG_MODE || CZR_DEV_MODE ? CUSTOMIZR_VER . time() : CUSTOMIZR_VER;

               $this->_minify_css              = CZR_DEBUG_MODE || CZR_DEV_MODE ? false : true ;
               $this->_minify_css              = esc_attr( czr_fn_opt( 'tc_minified_skin' ) ) ? $this->_minify_css : false;

         }



         /**
         * Registers and enqueues Customizr stylesheets
         * @package Customizr
         * @since Customizr 1.1
         */
         function czr_fn_enqueue_front_styles() {

               $_path       = CZR_ASSETS_PREFIX . 'front/css/';

               $_ver        = $this->_resouces_version;

               $_ext        = $this->_minify_css ? '.min.css' : '.css';

               wp_enqueue_style( 'customizr-bs'             , czr_fn_get_theme_file_url( "{$_path}custom-bs/custom-bootstrap{$_ext}" ) , array(), $_ver, 'all' );

               wp_enqueue_style( 'customizr-flickity'       , czr_fn_get_theme_file_url( "{$_path}flickity{$_ext}" ), array(), $_ver, 'all' );

               wp_enqueue_style( 'customizr-magnific'       , czr_fn_get_theme_file_url( "{$_path}magnific-popup{$_ext}" ), array(), $_ver, 'all' );

               wp_enqueue_style( 'customizr-scrollbar'      , czr_fn_get_theme_file_url( "{$_path}jquery.mCustomScrollbar.min.css" ), array(), $_ver, 'all' );

               //Customizr main stylesheet
               wp_enqueue_style( 'customizr-common'         , czr_fn_get_theme_file_url( "{$_path}style{$_ext}"), array(), $_ver, 'all' );


               //Customizer user defined style options : the custom CSS is written with a high priority here
               wp_add_inline_style( 'customizr-common'      , apply_filters( 'czr_user_options_style' , '' ) );

               //Customizr stylesheet (style.css)
               wp_enqueue_style( 'customizr-style'          , czr_fn_get_theme_file_url( "style{$_ext}"), array(), $_ver, 'all' );

         }

         /**
         * Writes the sanitized custom CSS from options array into the custom user stylesheet, at the very end (priority 9999)
         * hook : czr_user_options_style
         * @package Customizr
         * @since Customizr 2.0.7
         */
         function czr_fn_write_custom_css( $_css = null ) {

               $_css                     = isset( $_css ) ? $_css : '';
               $_moved_opts              = czr_fn_opt(  '__moved_opts' );

               /*
               * Do not print old custom css if moved in the WP Custom CSS
               */
               if ( !empty( $_moved_opts ) && is_array( $_moved_opts ) && in_array( 'custom_css', $_moved_opts) ) {
                     return $_css;
               }

               $tc_custom_css            = czr_fn_opt( 'tc_custom_css' );
               $esc_tc_custom_css        = esc_html( $tc_custom_css );

               if ( ! isset( $esc_tc_custom_css ) || empty( $esc_tc_custom_css ) )
                     return $_css;

               return apply_filters( 'czr_write_custom_css',
                     $_css . "\n" . html_entity_decode( $esc_tc_custom_css ),
                     $_css,
                     $tc_custom_css
               );

         }//end of function




         /* See: https://github.com/presscustomizr/customizr/issues/605 */
         function czr_fn_apply_media_upload_front_patch( $_css ) {

               global $wp_version;
               if ( version_compare( '4.5', $wp_version, '<=' ) )
                     $_css = sprintf("%s%s", $_css, 'table { border-collapse: separate; } body table { border-collapse: collapse; }');

               return $_css;

         }


         function czr_fn_maybe_write_skin_inline_css( $_css ) {

               //retrieve the current option
               $skin_color                     = czr_fn_opt( 'tc_skin_color' );

               //retrieve the default color
               $defaults                       = czr_fn_get_default_options();

               $def_skin_color                 = isset( $defaults['tc_skin_color'] ) ? $defaults['tc_skin_color'] : false;

               if ( in_array( $def_skin_color, array( $skin_color, strtoupper( $skin_color) ) ) )
                     return;

               $skin_dark_color                = czr_fn_darken_hex( $skin_color, '12%' );
               $skin_light_color               = czr_fn_lighten_hex( $skin_color, '15%' );
               $skin_lightest_color            = czr_fn_lighten_hex( $skin_color, '20%' );

               //shaded
               $skin_lightest_color_shade_high = czr_fn_hex2rgba( $skin_lightest_color, 0.2, $array = false, $make_prop_value = true );
               $skin_dark_color_shade_high     = czr_fn_hex2rgba( $skin_dark_color, 0.2, $array = false, $make_prop_value = true );
               $skin_dark_color_shade_low      = czr_fn_hex2rgba( $skin_dark_color, 0.8, $array = false, $make_prop_value = true);

               //LET'S DANCE
               //start computing style
               $skin                           = array();
               $glue                           = $this->_minify_css || esc_attr( czr_fn_opt( 'tc_minified_skin' ) ) ? '' : "\n";

               $skin_style_map                 = array(

                     'skin_color' => array(
                           'color'  => $skin_color,
                           'rules'  => array(
                                 //prop => selectors
                                 'color'  => array(
                                       'a',
                                       '.btn-skin:active',
                                       '.btn-skin:focus',
                                       '.btn-skin:hover',
                                       '.btn-skin.inverted',
                                       '.grid-container__classic .post-type__icon',
                                       '.post-type__icon:hover .icn-format',
                                       '.grid-container__classic .post-type__icon:hover .icn-format',
                                       "[class*='grid-container__'] .entry-title a.czr-title:hover",
                                 ),
                                 'border-color' => array(
                                       '.czr-slider-loader-wrapper .czr-css-loader > div ',
                                       '.btn-skin',
                                       '.btn-skin:active',
                                       '.btn-skin:focus',
                                       '.btn-skin:hover',
                                       '.btn-skin-h-dark',
                                       '.btn-skin-h-dark.inverted:active',
                                       '.btn-skin-h-dark.inverted:focus',
                                       '.btn-skin-h-dark.inverted:hover',
                                       '.tc-header.border-top',
                                 ),
                                 'background-color' => array(
                                       "[class*='grid-container__'] .entry-title a:hover::after",
                                       '.grid-container__classic .post-type__icon',
                                       '.btn-skin',
                                       '.btn-skin.inverted:active',
                                       '.btn-skin.inverted:focus',
                                       '.btn-skin.inverted:hover',
                                       '.btn-skin-h-dark',
                                       '.btn-skin-h-dark.inverted:active',
                                       '.btn-skin-h-dark.inverted:focus',
                                       '.btn-skin-h-dark.inverted:hover',
                                 )
                           )
                     ),

                     'skin_light_color' => array(
                           'color'  => $skin_light_color,
                           'rules'  => array(
                                 //prop => selectors
                                 'color'  => array(
                                       '.btn-skin-light:active',
                                       '.btn-skin-light:focus',
                                       '.btn-skin-light:hover',
                                       '.btn-skin-light.inverted',
                                 ),

                                 'border-color' => array(
                                       '.btn-skin-light',
                                       '.btn-skin-light.inverted',
                                       '.btn-skin-light:active',
                                       '.btn-skin-light:focus',
                                       '.btn-skin-light:hover',
                                       '.btn-skin-light.inverted:active',
                                       '.btn-skin-light.inverted:focus',
                                       '.btn-skin-light.inverted:hover',
                                 ),

                                 'background-color' => array(
                                       '.btn-skin-light',
                                       '.btn-skin-light.inverted:active',
                                       '.btn-skin-light.inverted:focus',
                                       '.btn-skin-light.inverted:hover',
                                 ),
                           )
                     ),

                     'skin_lightest_color' => array(
                           'color'  => $skin_lightest_color,
                           'rules'  => array(
                                 //prop => selectors
                                 'color'  => array(
                                       '.btn-skin-lightest:active',
                                       '.btn-skin-lightest:focus',
                                       '.btn-skin-lightest:hover',
                                       '.btn-skin-lightest.inverted',
                                 ),

                                 'border-color' => array(
                                       '.btn-skin-lightest',
                                       '.btn-skin-lightest.inverted',
                                       '.btn-skin-lightest:active',
                                       '.btn-skin-lightest:focus',
                                       '.btn-skin-lightest:hover',
                                       '.btn-skin-lightest.inverted:active',
                                       '.btn-skin-lightest.inverted:focus',
                                       '.btn-skin-lightest.inverted:hover',
                                 ),

                                 'background-color' => array(
                                       '.btn-skin-lightest',
                                       '.btn-skin-lightest.inverted:active',
                                       '.btn-skin-lightest.inverted:focus',
                                       '.btn-skin-lightest.inverted:hover',
                                 ),
                           )
                     ),

                     'skin_lightest_color_shade_high' => array(
                           'color'  => $skin_lightest_color_shade_high,
                           'rules'  => array(
                                 'background-color' => array(
                                       '.post-navigation',
                                 )
                           )
                     ),


                     'skin_dark_color' => array(
                           'color'  => $skin_dark_color,
                           'rules'  => array(
                                 //prop => selectors
                                 'color'  => array(
                                       '.pagination',
                                       'a:hover',
                                       'a:focus',
                                       'a:active',
                                       '.btn-skin-dark:active',
                                       '.btn-skin-dark:focus',
                                       '.btn-skin-dark:hover',
                                       '.btn-skin-dark.inverted',
                                       '.btn-skin-dark-oh:active',
                                       '.btn-skin-dark-oh:focus',
                                       '.btn-skin-dark-oh:hover',
                                       '.post-info a:not(.btn):hover',
                                       '.grid-container__classic .post-type__icon .icn-format',
                                       "[class*='grid-container__'] .hover .entry-title a",
                                       '.widget-area a:not(.btn):hover',
                                       'a.czr-format-link:hover',
                                       '.format-link.hover a.czr-format-link',
                                       'input[type=submit]:hover',
                                       '.tabs .nav-link:hover',
                                       '.tabs .nav-link.active',
                                       '.tabs .nav-link.active:hover',
                                       '.tabs .nav-link.active:focus'
                                 ),

                                 'border-color' => array(
                                       '.grid-container__classic.tc-grid-border .grid__item',
                                       '.btn-skin-dark',
                                       '.btn-skin-dark.inverted',
                                       'input[type=submit]',
                                       '.btn-skin-dark:active',
                                       '.btn-skin-dark:focus',
                                       '.btn-skin-dark:hover',
                                       '.btn-skin-dark.inverted:active',
                                       '.btn-skin-dark.inverted:focus',
                                       '.btn-skin-dark.inverted:hover',
                                       '.btn-skin-h-dark:active',
                                       '.btn-skin-h-dark:focus',
                                       '.btn-skin-h-dark:hover',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-dark-oh:active',
                                       '.btn-skin-dark-oh:focus',
                                       '.btn-skin-dark-oh:hover',
                                       '.btn-skin-dark-oh.inverted:active',
                                       '.btn-skin-dark-oh.inverted:focus',
                                       '.btn-skin-dark-oh.inverted:hover',
                                       'input[type=submit]:hover',

                                 ),

                                 'background-color' => array(
                                       '.btn-skin-dark',
                                       '.btn-skin-dark.inverted:active',
                                       '.btn-skin-dark.inverted:focus',
                                       '.btn-skin-dark.inverted:hover',
                                       '.btn-skin-h-dark:active',
                                       '.btn-skin-h-dark:focus',
                                       '.btn-skin-h-dark:hover',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-h-dark.inverted',
                                       '.btn-skin-dark-oh.inverted:active',
                                       '.btn-skin-dark-oh.inverted:focus',
                                       '.btn-skin-dark-oh.inverted:hover',
                                       '.grid-container__classic .post-type__icon:hover',
                                       'input[type=submit]',
                                       '.widget-area .widget:not(.widget_shopping_cart) a:not(.btn):before',
                                       "[class*='grid-container__'] .hover .entry-title a::after",
                                       'a.czr-format-link::before',
                                       '.comment-author a::before',
                                       '.comment-link::before',
                                       '.tabs .nav-link.active::before'
                                 ),
                           )
                     ),

                     'skin_dark_color_shade_high' => array(
                           'color'  => $skin_dark_color_shade_high,
                           'rules'  => array(
                                 //prop => selectors
                                 'background-color' => array(
                                       '.btn-skin-dark-shaded:active',
                                       '.btn-skin-dark-shaded:focus',
                                       '.btn-skin-dark-shaded:hover',
                                       '.btn-skin-dark-shaded.inverted',
                                 )
                           )
                     ),

                     'skin_dark_color_shade_low' => array(
                           'color'  => $skin_dark_color_shade_low,
                           'rules'  => array(
                                 //prop => selectors
                                 'background-color' => array(
                                       '.btn-skin-dark-shaded',
                                       '.btn-skin-dark-shaded.inverted:active',
                                       '.btn-skin-dark-shaded.inverted:focus',
                                       '.btn-skin-dark-shaded.inverted:hover',
                                 )
                           )
                     )

               );

               //these break the style, let's separate them from the others;
               $skin[]  = '::-moz-selection{background-color:'. $skin_color .'}';
               $skin[]  = '::selection{background-color:'. $skin_color .'}';

               //Builder
               foreach ( $skin_style_map as $skin_color => $params ) {

                     foreach ( $params['rules'] as $prop => $selectors ) {

                           $_selectors = implode( ",{$glue}", apply_filters( "czr_dynamic_{$skin_color}_{$prop}_prop_selectors", $selectors ) );

                           if ( $_selectors ) {
                                 $skin[] = sprintf( '%1$s{%2$s:%3$s}',
                                       $_selectors,
                                       $prop, //property
                                       $params['color'] //color
                                 );
                           }//end if $_selectors

                     }//end foreach $param['rules'] as ...

               }//end foreach $skinner as ...

               // end computing
               if ( empty ( $skin ) ) {
                     return;
               }

               //LET's GET IT ON
               return implode( "{$glue}{$glue}", $skin );

         }

   }//end of CZR_resources_styles
endif;
