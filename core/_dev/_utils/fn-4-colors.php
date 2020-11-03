<?php
/*  Darken hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hex' ) ) {
   function czr_fn_darken_hex( $hex, $percent, $make_prop_value = true ) {

      $hsl      = czr_fn_hex2hsl( $hex );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $dark_hsl, $make_prop_value );
   }
}

/*  Lighten hex color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hex' ) ) {

   function czr_fn_lighten_hex($hex, $percent, $make_prop_value = true) {

      $hsl       = czr_fn_hex2hsl( $hex );

      $light_hsl   = czr_fn_lighten_hsl( $hsl, $percent );

      return czr_fn_hsl2hex( $light_hsl, $make_prop_value );
   }
}

/*  Darken rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_rgb' ) ) {
   function czr_fn_darken_rgb( $rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $dark_hsl   = czr_fn_darken_hsl( $hsl, $percent );

      return czr_fn_hsl2rgb( $dark_hsl, $array, $make_prop_value );
   }
}

/*  Lighten rgb color
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_rgb' ) ) {

   function czr_fn_lighten_rgb($rgb, $percent, $array = false, $make_prop_value = false ) {

      $hsl      = czr_fn_rgb2hsl( $rgb, true );

      $light_hsl = czr_fn_lighten_hsl( $light_hsl, $percent );

      return czr_fn_hsl2rgb( $light_hsl, $array, $make_prop_value );

   }
}



/* Darken/Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken_hsl' ) ) {
   function czr_fn_darken_hsl( $hsl, $percentage, $array = true ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) - $percentage;
      $hsl[2] = ( $hsl[2] < 0 ) ? 0: $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/* Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten_hsl' ) ) {
   function czr_fn_lighten_hsl( $hsl, $percentage, $array = true  ) {

      $percentage = trim( $percentage, '% ' );

      $hsl[2] = ( $hsl[2] * 100 ) + $percentage;
      $hsl[2] = ( $hsl[2] > 100 ) ? 1 : $hsl[2]/100;

      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}



/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgb' ) ) {
   function czr_fn_hex2rgb( $hex, $array = false, $make_prop_value = false ) {

      //$hex = trim( $hex, '# ' );
      // Nov 2020 => fixes https://github.com/presscustomizr/customizr/issues/1866
      $hex = preg_replace("/[^A-Za-z0-9]/","",$hex);

      if ( 3 == strlen( $hex ) ) {

         $r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
         $g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
         $b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );

      }
      else {

         $r = hexdec( substr( $hex, 0, 2 ) );
         $g = hexdec( substr( $hex, 2, 2 ) );
         $b = hexdec( substr( $hex, 4, 2 ) );

      }

      $rgb = array( $r, $g, $b );

      if ( !$array ) {

         $rgb = implode( ",", $rgb );
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;

      }

      return $rgb;
  }
}

/*  Convert hexadecimal to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgba' ) ) {
   function czr_fn_hex2rgba( $hex, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb = $rgba = czr_fn_hex2rgb( $hex, $_array = true );

      $rgba[]     = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*  Convert rgb to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2rgba' ) ) {
   function czr_fn_rgb2rgba( $rgb, $alpha = 0.7, $array = false, $make_prop_value = false ) {

      $rgb   = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb   = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb   = $rgba = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $rgba[] = $alpha;

      if ( !$array ) {

         $rgba = implode( ",", $rgba );
         $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;

      }

      return $rgba;
  }
}

/*
* Following heavily based on
* https://github.com/mexitek/phpColors
* MIT License
*/
/*  Convert  rgb to hexadecimal
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hex' ) ) {
   function czr_fn_rgb2hex( $rgb, $make_prop_value = false ) {

      $rgb = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      // Convert RGB to HEX
      $hex[0] = str_pad( dechex( $rgb[0] ), 2, '0', STR_PAD_LEFT );
      $hex[1] = str_pad( dechex( $rgb[1] ), 2, '0', STR_PAD_LEFT );
      $hex[2] = str_pad( dechex( $rgb[2] ), 2, '0', STR_PAD_LEFT );

      $hex = implode( '', $hex );

      return $make_prop_value ? "#{$hex}" : $hex;
   }
}

/*
* heavily based on
* phpColors
*/

/*  Convert rgb to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hsl' ) ) {
   function czr_fn_rgb2hsl( $rgb, $array = false ) {

      $rgb       = is_array( $rgb ) ? $rgb : explode( ',', $rgb );
      $rgb       = is_array( $rgb) ? $rgb : array( $rgb );
      $rgb       = count( $rgb ) < 3 ? array_pad( $rgb, 3, 255 ) : $rgb;

      $deltas    = array();

      $RGB       = array(
         'R'   => ( $rgb[0] / 255 ),
         'G'   => ( $rgb[1] / 255 ),
         'B'   => ( $rgb[2] / 255 )
      );


      $min       = min( array_values( $RGB ) );
      $max       = max( array_values( $RGB ) );
      $span      = $max - $min;

      $H = $S    = 0;
      $L         = ($max + $min)/2;

      if ( 0 != $span ) {

         if ( $L < 0.5 ) {
            $S = $span / ( $max + $min );
         }
         else {
            $S = $span / ( 2 - $max - $min );
         }

         foreach ( array( 'R', 'G', 'B' ) as $var ) {
            $deltas[$var] = ( ( ( $max - $RGB[$var] ) / 6 ) + ( $span / 2 ) ) / $span;
         }

         if ( $max == $RGB['R'] ) {
            $H = $deltas['B'] - $deltas['G'];
         }
         else if ( $max == $RGB['G'] ) {
            $H = ( 1 / 3 ) + $deltas['R'] - $deltas['B'];
         }
         else if ( $max == $RGB['B'] ) {
            $H = ( 2 / 3 ) + $deltas['G'] - $deltas['R'];
          }

         if ($H<0) {
            $H++;
         }

         if ($H>1) {
            $H--;
         }
      }

      $hsl = array( $H*360, $S, $L );


      if ( !$array ) {
         $hsl = implode( ",", $hsl );
      }

      return $hsl;
   }
}

/*  Convert hsl to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2rgb' ) ) {

   function czr_fn_hsl2rgb( $hsl, $array=false, $make_prop_value = false ) {

      list($H,$S,$L) = array( $hsl[0]/360, $hsl[1], $hsl[2] );

      $rgb           = array_fill( 0, 3, $L * 255 );

      if ( 0 !=$S ) {

         if ($L < 0.5 ) {

            $var_2 = $L * ( 1 + $S );

         } else {

            $var_2 = ( $L + $S ) - ( $S * $L );

         }

         $var_1  = 2 * $L - $var_2;

         $rgb[0] = czr_fn_hue2rgbtone( $var_1, $var_2, $H + ( 1/3 ) );
         $rgb[1] = czr_fn_hue2rgbtone( $var_1, $var_2, $H );
         $rgb[2] = czr_fn_hue2rgbtone( $var_1, $var_2, $H - ( 1/3 ) );
      }

      if ( !$array ) {
         $rgb = implode(",", $rgb);
         $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;
      }

      return $rgb;
   }
}

/* Convert hsl to hex
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2hex' ) ) {
   function czr_fn_hsl2hex( $hsl = array(), $make_prop_value = false ) {
      $rgb = czr_fn_hsl2rgb( $hsl, $array = true );

      return czr_fn_rgb2hex( $rgb, $make_prop_value );
   }
}

/* Convert hex to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2hsl' ) ) {
   function czr_fn_hex2hsl( $hex ) {
      $rgb = czr_fn_hex2rgb( $hex, true );

      return czr_fn_rgb2hsl( $rgb, true );
   }
}

/* Convert hue to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hue2rgbtone' ) ) {
   function czr_fn_hue2rgbtone( $v1, $v2, $vH ) {
      $_to_return = $v1;

      if( $vH < 0 ) {
         $vH += 1;
      }
      if( $vH > 1 ) {
         $vH -= 1;
      }

      if( (6*$vH) < 1 ) {
         $_to_return = ($v1 + ($v2 - $v1) * 6 * $vH);
      }
      elseif( (2*$vH) < 1 ) {
         $_to_return = $v2;
      }
      elseif( (3*$vH) < 2 ) {
         $_to_return = ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
      }

      return round( 255 * $_to_return );
   }
}


/* Returns the complementary hsl color
/* ------------------------------------ */
function czr_fn_rgb_invert( $rgb )  {
   // Adjust Hue 180 degrees
   //$hsl[0] += ($hsl[0]>180) ? -180:180;
   $rgb_inverted =  array(
      255 - $rgb[0],
      255 - $rgb[1],
      255 - $rgb[2]
   );

   return $rgb_inverted;
}

/* Returns the complementary hsl color
/* ------------------------------------ */
function czr_fn_hex_invert( $hex, $make_prop_value = true )  {
   $rgb           = czr_fn_hex2rgb( $hex, $array = true );
   $rgb_inverted  = czr_fn_rgb_invert( $rgb );

   return czr_fn_rgb2hex( $rgb_inverted, $make_prop_value );
}

?>