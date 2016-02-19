<?php
class TC_header_model_class extends TC_Model {
  public $classes;
 
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'classes' ] = implode( " ", apply_filters('tc_header_classes', $this -> tc_get_header_classes ( array('tc-header' ,'clearfix', 'row-fluid') ) ) );
    return $model;
  }

  /**
  * 
  * @package Customizr
  * @since Customizr 3.5.0
  * @since Customizr 3.2.0 as TC_header::tc_set_header_classes()
  */
  function tc_get_header_classes( $_classes ) {
    $_show_tagline       = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_tagline') );
    $_show_title_logo    = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') );
    $_use_sticky_logo 	 = $this -> tc_use_sticky_logo();
    $_shrink_title_logo  = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_shrink_title_logo') );

    $_show_menu          = 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_menu') );
    
    $_header_layout      = "logo-" . esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout' ) );
    $_add_classes 			= array(
        $_show_tagline ? 'tc-tagline-on' : 'tc-tagline-off',
        $_show_title_logo ? 'tc-title-logo-on' : 'tc-title-logo-off',
        $_use_sticky_logo ? 'tc-sticky-logo-on' : '',
        $_shrink_title_logo ? 'tc-shrink-on' : 'tc-shrink-off',
        $_show_menu ? 'tc-menu-on' : 'tc-menu-off',
        $_header_layout
	);
    
    return array_merge( $_classes , $_add_classes ); 
  }

  //THE FOLLOWING METHOD IS USED ALSO BY THE STICKY LOGO CONTROLLER
  /**
  * Returns a boolean wheter we're using or not a specific sticky logo
  *
  * @package Customizr
  * @since Customizr 3.2.9
  */
  function tc_use_sticky_logo(){
    if ( ! esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_logo_upload") ) )
      return false;
    if ( ! ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") ) &&
      esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') )
    ) )
      return false;
    return true;
  }
}//end of class
