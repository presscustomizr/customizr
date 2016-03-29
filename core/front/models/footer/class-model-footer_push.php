<?php
class TC_footer_push_model_class extends TC_Model {

  function tc_body_class( $_classes ) {
    //this module can be instantiated in the customizer also when the relative option is disabled
    //as it's transported via postMessage. The body class above is hence handled in the preview js
    //to allow the js to perform the push if needed.
    if ( esc_attr( TC_utils::$inst -> tc_opt( 'tc_sticky_footer') ) )
      array_push( $_classes, 'tc-sticky-footer' );
    return $_classes;
  }

  /*
  * Callback of tc_user_options_style hook
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.3.27
  */
  function tc_user_options_style_cb( $_css ){
    $_css = sprintf("%s\n%s",
      $_css,
        "#tc-push-footer { display: none; visibility: hidden; }
        .tc-sticky-footer #tc-push-footer.sticky-footer-enabled { display: block; }
        "
      );
    return $_css;
  }
}
