<?php
class TC_content_wrapper_model_class extends TC_Model {
  public $content_layout;

  function tc_change_hook() {
    return '__footer__';
  }

  //fired before the model properties are parsed
  function tc_extend_params( $model = array() ) {
    //set this model's properties
    $model[ 'content_layout' ] = 'span12';
    return $model;
  }
}
