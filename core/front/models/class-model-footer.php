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

}