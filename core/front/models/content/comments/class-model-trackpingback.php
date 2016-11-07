<?php
class CZR_trackpingback_model_class extends CZR_Model {

  //bools
  public $ping_number = 0;

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