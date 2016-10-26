<?php
class CZR_logo_sticky_model_class extends CZR_logo_model_class{
  public $logo_type   = 'sticky';

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function czr_fn_maybe_filter_views_model() {
    parent::czr_fn_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header', array( $this, 'pre_rendering_view_header_cb' ) );
  }

  /**
  * parse header model before rendering to add sticky logo
  */
  function pre_rendering_view_header_cb( $header_model ) {
    if ( ! is_array( $header_model -> element_class ) )
      $header_model -> element_class = explode( " ", $header_model -> element_class );

    array_push( $header_model -> element_class, 'tc-sticky-logo-on' );
  }

  function czr_fn_user_options_style_cb( $_css ) {
    $_css = sprintf( "%s%s",
        parent::czr_fn_user_options_style_cb( $_css ),
        "
        .navbar-logo img.sticky {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .navbar-logo img {
            display: none;
         }
        .sticky-enabled .tc-sticky-logo-on .navbar-logo img.sticky {
            display: inline-block;
        }"
    );

    return $_css;
  }//end sticky-logo css
}//end class