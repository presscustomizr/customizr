<?php
if ( ! function_exists( 'czr_fn_sass_lighten' ) ) {
    function czr_fn_sass_darken($hex, $percent, $make_prop_value = true) {
        return $make_prop_value ? '#' . czr_fn_darken( czr_fn_rgb2hsl(czr_fn_hex2rgb($hex, true ), true ), $percent ) :
        czr_fn_darken( czr_fn_rgb2hsl(czr_fn_hex2rgb($hex, true ), true ), $percent );
    }
}

if ( ! function_exists( 'czr_fn_sass_lighten' ) ) {
    function czr_fn_sass_lighten($hex, $percent, $make_prop_value = true) {
        return $make_prop_value ? '#' . czr_fn_lighten( czr_fn_rgb2hsl(czr_fn_hex2rgb($hex, true ), true ), $percent ) :
        czr_fn_lighten( czr_fn_rgb2hsl(czr_fn_hex2rgb($hex, true ), true ), $percent );
    }
}


/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgb' ) ) {
    function czr_fn_hex2rgb( $hex, $array = false, $make_prop_value = false ) {
        $hex = str_replace("#", "", $hex);

        if ( strlen($hex) == 3 ) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        }
        else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }

        $rgb = array( $r, $g, $b );

        if ( !$array ) {
            $rgb = implode(",", $rgb);
            $rgb = $make_prop_value ? "rgb($rgb)" : $rgb;
        }

        return $rgb;
  }
}

/*  Convert hexadecimal to rgba
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgba' ) ) {
    function czr_fn_hex2rgba( $hex, $alpha = 0.7, $array = false, $make_prop_value = false ) {
        $rgb = $rgba = czr_fn_hex2rgb($hex, $_array = true);
        $rgba[]      = $alpha;

        if ( !$array ) {
            $rgba = implode(",", $rgba);
            $rgba = $make_prop_value ? "rgba($rgba)" : $rgba;
        }

        return $rgba;
  }
}

/*  Convert  rgb to hexadecimal
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hex' ) ) {
    function czr_fn_rgb2hex( $rgb, $make_prop_value = false ) {

        $rgb = is_array( $rgb ) ? $rgb : explode(',', $rgb);
        $rgb = is_array( $rgb) ? $rgb : array( $rgb );
        $rgb = count($rgb) < 3 ? array_pad( $rgb, 3, 255) : $rgb;

        // Convert RGB to HEX
        $hex[0] = str_pad(dechex($rgb[0]), 2, '0', STR_PAD_LEFT);
        $hex[1] = str_pad(dechex($rgb[1]), 2, '0', STR_PAD_LEFT);
        $hex[2] = str_pad(dechex($rgb[2]), 2, '0', STR_PAD_LEFT);

        $hex = implode( '', $hex );

        return $make_prop_value ? $hex : "#{$hex}";
    }
}

/*
* heavily based on
* phpColors
*/

/*  Convert rgb to hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_rgb2hsl' ) ) {
    function czr_fn_rgb2hsl( $rgb, $array=false, $make_prop_value = false ) {
        $HSL = array();

        $R = ($rgb[0] / 255);
        $G = ($rgb[1] / 255);
        $B = ($rgb[2] / 255);

        $min = min($R, $G, $B);
        $max = max($R, $G, $B);
        $del_max = $max - $min;

        $L = ($max + $min)/2;

        if ($del_max == 0)
        {
            $H = 0;
            $S = 0;
        }
        else
        {
            if ( $L < 0.5 ) $S = $del_max / ( $max + $min );
            else            $S = $del_max / ( 2 - $max - $min );
            $del_R = ( ( ( $max - $R ) / 6 ) + ( $del_max / 2 ) ) / $del_max;
            $del_G = ( ( ( $max - $G ) / 6 ) + ( $del_max / 2 ) ) / $del_max;
            $del_B = ( ( ( $max - $B ) / 6 ) + ( $del_max / 2 ) ) / $del_max;
            if      ($R == $max) $H = $del_B - $del_G;
            else if ($G == $max) $H = ( 1 / 3 ) + $del_R - $del_B;
            else if ($B == $max) $H = ( 2 / 3 ) + $del_G - $del_R;
            if ($H<0) $H++;
            if ($H>1) $H--;
        }
        $HSL['H'] = ($H*360);
        $HSL['S'] = $S;
        $HSL['L'] = $L;

        if ( !$array ) {
            $HSL = implode(",", $HSL);
            $HSL = $make_prop_value ? "rgba($rgba)" : $HSL;
        }

        return $HSL;
    }
}

/* Convert hsl to hex
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hsl2hex' ) ) {
    function czr_fn_hsl2hex( $hsl = array() ){

        list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );
        if( $S == 0 ) {
            $r = $L * 255;
            $g = $L * 255;
            $b = $L * 255;
        } else {
            if($L<0.5) {
                $var_2 = $L*(1+$S);
            } else {
                $var_2 = ($L+$S) - ($S*$L);
            }
            $var_1 = 2 * $L - $var_2;
            $r = round(255 * czr_fn_hue2rgb( $var_1, $var_2, $H + (1/3) ));
            $g = round(255 * czr_fn_hue2rgb( $var_1, $var_2, $H ));
            $b = round(255 * czr_fn_hue2rgb( $var_1, $var_2, $H - (1/3) ));
        }
        // Convert to hex
        $r = dechex($r);
        $g = dechex($g);
        $b = dechex($b);

        // Make sure we get 2 digits for decimals
        $r = (strlen("".$r)===1) ? "0".$r:$r;
        $g = (strlen("".$g)===1) ? "0".$g:$g;
        $b = (strlen("".$b)===1) ? "0".$b:$b;
        return $r.$g.$b;

    }
}

/* Convert hue to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hue2rgb' ) ) {
    function czr_fn_hue2rgb( $v1,$v2,$vH ) {
        if( $vH < 0 ) {
            $vH += 1;
        }
        if( $vH > 1 ) {
            $vH -= 1;
        }
        if( (6*$vH) < 1 ) {
               return ($v1 + ($v2 - $v1) * 6 * $vH);
        }
        if( (2*$vH) < 1 ) {
            return $v2;
        }
        if( (3*$vH) < 2 ) {
            return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
        }
        return $v1;
    }
}


/* Darken hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_darken' ) ) {
    function czr_fn_darken( $hsl, $amount ){
        $hsl['L'] = ($hsl['L'] * 100) - $amount;
        $hsl['L'] = ($hsl['L'] < 0) ? 0:$hsl['L']/100;

        return czr_fn_hsl2hex($hsl);
    }
}

/* Lighten hsl
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_lighten' ) ) {
    function czr_fn_lighten( $hsl, $amount ) {
        $hsl['L'] = ($hsl['L'] * 100) + $amount;
        $hsl['L'] = ($hsl['L'] > 100) ? 1:$hsl['L']/100;

        return czr_fn_hsl2hex($hsl);
    }
}