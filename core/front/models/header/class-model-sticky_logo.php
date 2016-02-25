<?php
if ( tc_fw_require_once('models/header/class-model-logo.php') ) :
class TC_sticky_logo_model_class extends TC_logo_model_class{
  public $logo_type   = 'sticky';

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */ 
  function tc_maybe_filter_views_model() {
    parent::tc_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header', array( $this, 'pre_rendering_view_header_cb' ) );
  }

  /**
  * parse header model before rendering to add sticky logo
  */ 
  function pre_rendering_view_header_cb( $header_model ) {
    if ( ! is_array( $header_model -> class ) )
      $header_model -> class = explode( " ", $header_model -> class );

    array_push( $header_model -> class, 'tc-sticky-logo-on' );
  } 
  
  function tc_user_options_style_cb( $_css ) {
    $_css = sprintf( "%s%s",
        $_css,
        "
        .site-logo img.sticky {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .site-logo img {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .site-logo img.sticky {
            display: inline-block;
        }"
    );

    return $_css;  
  }//end sticky-logo css
}//end class
endif;
