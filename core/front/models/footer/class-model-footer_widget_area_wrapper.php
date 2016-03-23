<?php
class TC_footer_widget_area_wrapper_model_class extends TC_Model {
   
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $n_footer_widgets = count( apply_filters( 'tc_footer_widgets', TC_init::$instance -> footer_widgets ) );
    $model['element_class'] = $n_footer_widgets ? array('span' . 12/$n_footer_widgets) : array();
    
    $element_id_pos         = strrpos( $model['id'] , '_');
    if ( FALSE == $element_id_pos || 1 + $element_id_pos == strlen( $model['id'] ) )
      return;
    $model['element_id']    = substr( $model['id'] , $element_id_pos );
    
    return $model;
  }
}
