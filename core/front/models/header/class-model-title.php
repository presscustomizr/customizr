<?php
class CZR_title_model_class extends CZR_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class' ]       = apply_filters( 'czr_logo_class', $this -> get_title_wrapper_class(), $model );

    return $model;
  }

  function get_title_wrapper_class() {
    return czr_fn_has( 'tagline' ) ? 'has_tagline' : '';
  }
}