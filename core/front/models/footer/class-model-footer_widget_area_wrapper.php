<?php
class TC_footer_widget_area_wrapper_model_class extends TC_Model {
  public $key;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $n_footer_widgets = count( apply_filters( 'tc_footer_widgets', TC_init::$instance -> footer_widgets ) );
    $model['element_class'] = $n_footer_widgets ? array('span' . 12/$n_footer_widgets) : array();

    $model[ 'key' ] = isset( $model['id'] ) ? $model['id'] : 'footer_one';
    return $model;
  }
}
