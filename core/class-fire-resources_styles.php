<?php
/**
* Inline front end skin
*
*
* @package      Customizr
*/
if ( ! class_exists( 'CZR_resources_styles' ) ) :
    class CZR_resources_styles {
        //Access any method or var of the class with classname::$instance -> var or method():
        static $instance;

        function __construct () {
            self::$instance =& $this;

          add_filter( 'czr_user_options_style'        , array( $this , 'czr_fn_maybe_write_skin_inline_css') );
        }


        function czr_fn_maybe_write_skin_inline_css( $_css ) {
            //retrieve the current option
            $skin_color     = czr_fn_get_opt( 'tc_skin_color' );

            //retrieve the default color
            $defaults       = czr_fn_get_default_options();

            $def_skin_color = isset( $defaults['tc_skin_color'] ) ? $defaults['tc_skin_color'] : false;

            if ( $skin_color == $def_skin_color )
                return;

            $skin_dark_color      = czr_fn_sass_darken( $skin_color, '15' );
            $skin_darkest_color   = czr_fn_sass_darken( $skin_color, '25' );
            $skin_light_color     = czr_fn_sass_lighten( $skin_color, '15' );
            $skin_lightest_color  = czr_fn_sass_lighten( $skin_color, '25' );

            //LET'S DANCE
            //start computing style
            $styles    = array();
            $glue      = esc_attr( czr_fn_get_opt('tc_minified_skin') ) ? '' : "\n";

            $skinner   = array(
                'skin_color' => array(
                    'color'  => $skin_color,
                    'rules'  => array(
                        //prop => selectors
                        'color'  => array(
                            'a',
                            '.btn-skin:hover',
                            '.btn-skin.inverted',
                            '.post-type__icon:hover .icn-format',
                            '[class*="grid-container__"] .hover .entry-title a'
                        ),
                        'border-color' => array(
                            '.btn-skin',
                            '.btn-skin:hover',
                        ),
                        'background-color' => array(
                            '.btn-skin',
                            '.btn-skin.inverted:hover'
                        )
                    )
                ),

                'skin_lightest_color_shade_high' => array(
                    'color'  => czr_fn_hex2rgba( $skin_lightest_color, 0.2, $array=false, $make_prop_value=true),
                    'rules'  => array(
                        'background-color' => array(
                            '.post-navigation',
                        )
                    )
                ),


                'skin_dark_color' => array(
                    'color'  => $skin_darkest_color,
                    'rules'  => array(
                        //prop => selectors
                        'background-color' => array(
                            '.flickity-page-dots .dot'
                        )
                    )
                ),

                'skin_darkest_color' => array(
                    'color'  => $skin_darkest_color,
                    'rules'  => array(
                        //prop => selectors
                        'color'  => array(
                            '.pagination',
                            '.btn-skin-darkest:hover',
                            '.btn-skin-darkest.inverted',
                            'a:hover',
                            'a:focus',
                            'a:active',
                            '.entry-meta a:not(.btn):hover',
                            '.grid-container__classic .post-type__icon .icn-format',
                            '[class*="grid-container__"] .entry-title a:hover',
                            '.widget-area a:not(.btn):hover',
                        ),
                        'border-color' => array(
                            '.btn-skin-darkest',
                            '.btn-skin-darkest.inverted',
                            'input[type=submit]',
                            '.btn-skin-darkest:hover',
                            '.btn-skin-darkest.inverted:hover',
                            'input[type=submit]:hover',
                        ),
                        'background-color' => array(
                            '.btn-skin-darkest',
                            '.btn-skin-darkest.inverted:hover',
                            'input[type=submit]',
                            '.widget-area .widget:not(.widget_shopping_cart) a:not(.btn):before',
                        )
                    )
                ),

                'skin_darkest_color_shade_high' => array(
                    'color'  => czr_fn_hex2rgba($skin_darkest_color, 0.2, $array=false, $make_prop_value=true),
                    'rules'  => array(
                        //prop => selectors
                        'background-color' => array(
                            '.btn-skin-darkest-shaded:hover',
                            '.btn-skin-darkest-shaded.inverted',
                            '.slider-nav .slider-control',
                            '.mfp-gallery .slider-control',
                            '.tc-gallery-nav .slider-control',
                        )
                    )
                ),

                'skin_darkest_color_shade_low' => array(
                    'color'  => czr_fn_hex2rgba($skin_darkest_color, 0.7, $array=false, $make_prop_value=true),
                    'rules'  => array(
                        //prop => selectors
                        'background-color' => array(
                            '.btn-skin-darkest-shaded',
                            '.btn-skin-darkest-shaded.inverted:hover',
                            '.slider-nav .slider-control:hover',
                            '.mfp-gallery .slider-control:hover',
                            '.tc-gallery-nav .slider-control:hover',
                        )
                    )
                )
            );

            //these break the style, let's separate them from the others;
            $styles[]  = '::-moz-selection { background-color:'. $skin_color .'}';
            $styles[]  = '::selection { background-color:'. $skin_color .'}';

            foreach ( $skinner as $skin_color => $params ) {
                foreach ( $params['rules'] as $prop => $selectors ) {
                    $_selectors = implode( ",{$glue}",
                        apply_filters( "czr_dynamic_{$skin_color}_{$prop}_prop_selectors", $selectors ) );

                    if ( $_selectors ) {
                        $styles[] = sprintf( '%1$s{%2$s:%3$s}',
                                                $_selectors,
                                                $prop, //property
                                                $params['color'] //color
                                    );
                    }

                }
            }

            // $styles[]  = '::selection, ::moz-selection { background-color: '. $skin_color .' }';

            // //Skin color => color Property
            // $_skin_color_color_prop_selectors = array(
            //     'a',
            //     '.btn-skin:hover',
            //     '.btn-skin.inverted',
            //     '.post-type__icon:hover .icn-format',
            //     '[class*="grid-container__"] .hover .entry-header .entry-title a'
            // );

            // $_skin_color_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_color_color_prop_selectors', $_skin_color_color_prop_selectors ) );

            // $styles[] = $_skin_color_color_prop_selectors ? $_skin_color_color_prop_selectors . '{ color: '. $skin_color .' }' : '';

            // // Skin color => border-color Property
            // $_skin_color_border_color_prop_selectors = array(
            //     '.btn-skin',
            //     '.btn-skin:hover',
            // );

            // $_skin_color_border_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_color_border_color_prop_selectors', $_skin_color_border_color_prop_selectors ) );

            // $styles[] = $_skin_color_border_color_prop_selectors ? $_skin_color_border_color_prop_selectors . '{ border-color: '. $skin_color .' }' : '';


            // // Skin color => background-color Property
            // $_skin_color_background_color_prop_selectors = array(
            //     '.btn-skin',
            //     '.btn-skin.inverted:hover'
            // );

            // $_skin_color_background_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_color_background_color_prop_selectors', $_skin_color_background_color_prop_selectors ) );

            // $styles[] = $_skin_color_background_color_prop_selectors ? $_skin_color_background_color_prop_selectors . '{ background-color: '. $skin_color .' }' : '';


            // // Skin darkest color => color Property
            // $_skin_darkest_color_color_prop_selectors = array(
            //     '.pagination',
            //     '.btn-skin-darkest:hover',
            //     '.btn-skin-darkest.inverted',
            //     'a:hover',
            //     'a:focus',
            //     'a:active',
            //     '.entry-meta a:not(.btn):hover',
            //     '.grid-container__classic .post-type__icon .icn-format',
            //     '[class*="grid-container__"] .entry-header .entry-title a:hover',
            //     '.widget-area a:not(.btn):hover',

            // );

            // $_skin_darkest_color_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_darkest_color_color_prop_selectors', $_skin_darkest_color_color_prop_selectors ) );

            // $styles[] = $_skin_darkest_color_color_prop_selectors ? $_skin_darkest_color_color_prop_selectors . '{ color: '. $skin_darkest_color .'}' : '';


            // // Skin darkest color => border-color Property
            // $_skin_darkest_color_border_color_prop_selectors = array(
            //     '.btn-skin-darkest',
            //     '.btn-skin-darkest.inverted',
            //     'input[type=submit]',
            //     '.btn-skin-darkest:hover',
            //     '.btn-skin-darkest.inverted:hover',
            //     'input[type=submit]:hover',
            // );

            // $_skin_darkest_color_border_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_darkest_color_border_color_prop_selectors', $_skin_darkest_color_border_color_prop_selectors ) );

            // $styles[] = $_skin_darkest_color_border_color_prop_selectors ? $_skin_darkest_color_border_color_prop_selectors . '{ border-color: '. $skin_darkest_color .' }' : '';


            // // Skin darkest color => background-color Property
            // $_skin_darkest_color_background_color_prop_selectors = array(
            //     '.btn-skin-darkest',
            //     '.btn-skin-darkest.inverted:hover',
            //     'input[type=submit]',
            //     '.widget-area .widget:not(.widget_shopping_cart) a:not(.btn):before',
            // );

            // $_skin_darkest_color_background_color_prop_selectors = implode( ",{$glue}", apply_filters( 'czr_dynamic_skin_darkest_color_background_color_prop_selectors', $_skin_darkest_color_background_color_prop_selectors ) );

            // $styles[] = $_skin_darkest_color_background_color_prop_selectors ? $_skin_darkest_color_background_color_prop_selectors . '{ background-color: '. $skin_darkest_color .' }' : '';

            // end computing
            if ( empty ( $styles ) )
              return;




            //LET's GET IT ON
            // start output
            $_p_styles   = array();
            // '<style type="text/css">' );
            // $_p_styles[] = '/* Dynamic CSS: For no styles in head, copy and put the css below in your child theme\'s style.css, disable dynamic styles */';
            $_p_styles[] = implode( "{$glue}", $styles );
            //$_p_styles[] = '</style>'."\n";
            //end output;
            //print
            return implode( "{$glue}", $_p_styles );
        }
  }//end of CZR_resources_styles
endif;
