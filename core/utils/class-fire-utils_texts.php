<?php
/**
* Posts thumbnails actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.5.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>, Rocco ALIBERTI <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME, Rocco ALIBERTI
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_utils_texts' ) ) :
  class CZR_utils_texts {
    static $instance;
    static $post_formats_with_no_heading = array( 'aside' , 'status' , 'link' , 'quote' );

    function __construct () {
      self::$instance =& $this;
    }

    function czr_fn_post_has_headings() {
      return ! ( in_array( get_post_format(), apply_filters( 'czr_post_formats_with_no_heading', self::$post_formats_with_no_heading ) ) );
    }
  }
endif;

/* Exposed methods */
if ( ! function_exists( 'czr_fn_post_has_title' ) ):
  function czr_fn_post_has_title() {
    return CZR_utils_texts::$instance -> czr_fn_post_has_headings();
  }
endif;
