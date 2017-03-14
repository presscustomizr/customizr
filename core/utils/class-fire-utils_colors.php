<?php
/* 
https://gist.github.com/jegtnes/5720178
*/
if ( ! function_exists( 'czr_fn_sass_lighten' ) ) {
    function czr_fn_sass_darken($hex, $percent) {
        preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
            str_replace('%', '', $percent);
            $color = "#";
            for($i = 1; $i <= 3; $i++) {
                    $primary_colors[$i] = hexdec($primary_colors[$i]);
                    $primary_colors[$i] = round($primary_colors[$i] * (100-($percent*2))/100);
                    $color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
            }
            return $color;
    }
}

if ( ! function_exists( 'czr_fn_sass_lighten' ) ) {
    function czr_fn_sass_lighten($hex, $percent) {
        preg_match('/^#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $primary_colors);
        str_replace('%', '', $percent);
        $color = "#";
        for($i = 1; $i <= 3; $i++) {
            $primary_colors[$i] = hexdec($primary_colors[$i]);
            $primary_colors[$i] = round($primary_colors[$i] * (100+($percent*2))/100);
            $color .= str_pad(dechex($primary_colors[$i]), 2, '0', STR_PAD_LEFT);
        }
        return $color;
    }
}

/*  Convert hexadecimal to rgb
/* ------------------------------------ */
if ( ! function_exists( 'czr_fn_hex2rgb' ) ) {
    function czr_fn_hex2rgb( $hex, $array=false ) {
        $hex = str_replace("#", "", $hex);
        if ( strlen($hex) == 3 ) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array( $r, $g, $b );
        if ( !$array ) { $rgb = implode(",", $rgb); }
        return $rgb;
  }
}