<?php
class CZR_tagline_model_class extends CZR_Model {

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_attributes' ] = ( czr_fn_is_customizing() && 0 == esc_attr( czr_fn_opt( 'tc_show_tagline') ) ) ? 'style="display:none;"' : '';
    return $model;
  }

}