<?php
class CZR_cl_trackpingback_model_class extends CZR_cl_Model {

  //bools
  public $has_edit_button;
  public $ping_number;


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'has_edit_button' ]         = ! CZR() -> czr_fn_is_customizing();
    $model[ 'ping_number' ]             = 0;

    return $model;
  }

  /*
  * Prepare template for comments
  *
  */
  function czr_fn_setup_late_properties() {
    global $comment;

    $_pn = $this->ping_number;
    $_pn++;

    $this -> czr_fn_set_property( 'ping_number', $_pn );

  }
}