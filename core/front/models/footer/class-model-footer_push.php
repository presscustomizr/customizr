<?php
class CZR_footer_push_model_class extends CZR_Model {

  function czr_fn_body_class( $_classes ) {
    //this module can be instantiated in the customizer also when the relative option is disabled
    //as it's transported via postMessage. The body class above is hence handled in the preview js
    //to allow the js to perform the push if needed.
    if ( esc_attr( czr_fn_opt( 'tc_sticky_footer') ) )
      $_classes[] = 'czr-sticky-footer';
    return $_classes;
  }

  /*
  * Callback of czr_fn_user_options_style hook
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.3.27
  */
  function czr_fn_user_options_style_cb( $_css ){
    $_css = sprintf("%s\n%s",
      $_css,
        "#czr-push-footer { display: none; visibility: hidden; }
        .czr-sticky-footer #czr-push-footer.sticky-footer-enabled { display: block; }
        "
      );
    return $_css;
  }

}