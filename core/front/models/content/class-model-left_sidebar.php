<?php
class TC_left_sidebar_model_class extends TC_widget_area_wrapper_model_class {
  public $position = 'left';
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model                = parent::tc_extend_params( $model );   
    $global_layout        = apply_filters( 'tc_global_layout' , TC_init::$instance -> global_layout );
    $screen_layout        = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar'  );
    $sidebar_layout       = $global_layout[$screen_layout];

    //defines the sidebar wrapper class
    $model['wrapper_class'] = apply_filters( 'tc_left_sidebar_class', array( $sidebar_layout['sidebar'], $this -> position, 'tc-sidebar') );

    $model['inner_class']             = array('widget-area');
    $model['action_hook_suffix']      = '_'. $this -> position;
    $model['inner_id']                = $this -> position;
    
    return $model;
  }
}
