<?php
class CZR_cl_footer_widgets_model_class extends CZR_cl_Model {

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    //hack to render white color icons if skin is grey or black
    $model['element_class'] = ( in_array( czr_fn_get_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';

    return $model;
  }
}
