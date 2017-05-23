<?php
class CZR_footer_model_class extends CZR_Model {

  function czr_fn_setup_children() {
    $children = array(

      /* Needs to access the body class */
      /*********************************************
      * FOOTER PUSH
      *********************************************/
      array(
        'id'          => 'footer_push',
        'model_class' => 'footer/footer_push',
      ),
    );

    return $children;
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