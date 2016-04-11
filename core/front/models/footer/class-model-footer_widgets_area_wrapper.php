<?php
class TC_footer_widgets_area_wrapper_model_class extends TC_Model {

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    //hack to render white color icons if skin is grey or black
    $model['element_class'] = ( in_array( TC_utils::$inst->tc_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';

    return $model;
  }
}
