<?php
class CZR_footer_push_model_class extends CZR_Model {

  function czr_fn_body_class( $_classes ) {
    //this module can be instantiated in the customizer also when the relative option is disabled
    //as it's transported via postMessage. The body class above is hence handled in the preview js
    //to allow the js to perform the push if needed.
    if ( esc_attr( czr_fn_opt( 'tc_sticky_footer') ) )
      array_push( $_classes, 'czr-sticky-footer' );
    return $_classes;
  }

}