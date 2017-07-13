<?php
class CZR_footer_model_class extends CZR_Model {

  function __construct( $model = array() ) {
    parent::__construct( $model );

    CZR() -> collection -> czr_fn_register( array(
        'id'          => 'footer_push',
        'template'    => 'footer/footer_push',
        'hook'        => '__after_main_container'
      ) );
  }//_construct

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