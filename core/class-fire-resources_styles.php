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

         if ( in_array( $def_skin_color, array( $skin_color, strtoupper( $skin_color) ) ) )
            return;

         $skin_dark_color      = czr_fn_darken_hex( $skin_color, '12%' );
         $skin_darkest_color   = czr_fn_darken_hex( $skin_color, '20%' );
         $skin_light_color     = czr_fn_lighten_hex( $skin_color, '12%' );
         $skin_lightest_color  = czr_fn_lighten_hex( $skin_color, '20%' );

         //LET'S DANCE
         //start computing style
         $styles    = array();
         $glue      = esc_attr( czr_fn_get_opt( 'tc_minified_skin' ) ) ? '' : "\n";

         $skin_style_map = array(
            'skin_color' => array(
               'color'  => $skin_color,
               'rules'  => array(
                  //prop => selectors
                  'color'  => array(
                     'a',
                     '.btn-skin:hover',
                     '.btn-skin.inverted',
                     '.grid-container__classic .post-type__icon',
                     '.post-type__icon:hover .icn-format',
                     '.grid-container__classic .post-type__icon:hover .icn-format',
                     '[class*="grid-container__"] .hover .entry-title a'
                  ),
                  'border-color' => array(
                     '.czr-slider-loader-wrapper .czr-css-loader > div ',
                     '.btn-skin',
                     '.btn-skin:hover',
                  ),
                  'background-color' => array(
                     '.grid-container__classic .post-type__icon',
                     '.btn-skin',
                     '.btn-skin.inverted:hover'
                  )
               )
            ),

            'skin_light_color' => array(
               'color'  => $skin_light_color,
               'rules'  => array(
                  //prop => selectors
                  'color'  => array(
                     '.btn-skin-light:hover',
                     '.btn-skin-light.inverted',
                  ),

                  'border-color' => array(
                     '.btn-skin-light',
                     '.btn-skin-light.inverted',
                     '.btn-skin-light:hover',
                     '.btn-skin-light.inverted:hover',
                  ),

                  'background-color' => array(
                     '.btn-skin-light',
                     '.btn-skin-light.inverted:hover',
                  ),
               )
            ),

            'skin_lightest_color' => array(
               'color'  => $skin_lightest_color,
               'rules'  => array(
                  //prop => selectors
                  'color'  => array(
                     '.btn-skin-lightest:hover',
                     '.btn-skin-lightest.inverted',
                  ),

                  'border-color' => array(
                     '.btn-skin-lightest',
                     '.btn-skin-lightest.inverted',
                     '.btn-skin-lightest:hover',
                     '.btn-skin-lightest.inverted:hover',
                  ),

                  'background-color' => array(
                     '.btn-skin-lightest',
                     '.btn-skin-lightest.inverted:hover',
                  ),
               )
            ),

            'skin_light_color_shade_high' => array(
               'color'  => czr_fn_hex2rgba( $skin_light_color, 0.4, $array=false, $make_prop_value=true),
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
                     '.btn-skin-dark:hover',
                     '.btn-skin-dark.inverted',
                  ),

                  'border-color' => array(
                     '.grid-container__classic.tc-grid-border .grid__item',
                     '.btn-skin-dark',
                     '.btn-skin-dark.inverted',
                     '.btn-skin-dark:hover',
                     '.btn-skin-dark.inverted:hover',
                  ),

                  'background-color' => array(
                     '.btn-skin-dark',
                     '.btn-skin-dark.inverted:hover',
                     '.flickity-page-dots .dot',
                  ),
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
                     '.grid-container__classic .post-type__icon:hover',
                     '.btn-skin-darkest',
                     '.btn-skin-darkest.inverted:hover',
                     'input[type=submit]',
                     '.widget-area .widget:not(.widget_shopping_cart) a:not(.btn):before',
                  )
               )
            ),

            'skin_darkest_color_shade_high' => array(
               'color'  => czr_fn_hex2rgba($skin_darkest_color, 0.4, $array=false, $make_prop_value=true),
               'rules'  => array(
                  //prop => selectors
                  'background-color' => array(
                     '.btn-skin-darkest-shaded:hover',
                     '.btn-skin-darkest-shaded.inverted',
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
                  )
               )
            )

         );

         //these break the style, let's separate them from the others;
         $styles[]  = '::-moz-selection{background-color:'. $skin_color .'}';
         $styles[]  = '::selection{background-color:'. $skin_color .'}';

         //Builder
         foreach ( $skin_style_map as $skin_color => $params ) {
            foreach ( $params['rules'] as $prop => $selectors ) {
               $_selectors = implode( ",{$glue}", apply_filters( "czr_dynamic_{$skin_color}_{$prop}_prop_selectors", $selectors ) );

               if ( $_selectors ) {
                  $styles[] = sprintf( '%1$s{%2$s:%3$s}',
                                       $_selectors,
                                       $prop, //property
                                       $params['color'] //color
                  );
               }//end if $_selectors
            }//end foreach $param['rules'] as ...
         }//end foreach $skinner as ...

         // end computing
         if ( empty ( $styles ) )
            return;

         //LET's GET IT ON
         return implode( "{$glue}{$glue}", $styles );
      }
   }//end of CZR_resources_styles
endif;
