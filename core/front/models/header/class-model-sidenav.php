<?php
class TC_sidenav_model_class extends TC_Model {
  public $class;
  public $inner_class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'class' ]         = implode(' ', apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) );
    $model[ 'inner_class' ]   = implode(' ', apply_filters('tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) );  
    
    return $model;
  }
}
