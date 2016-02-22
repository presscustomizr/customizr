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
    if ( ! (bool) TC_utils::$inst->tc_opt('tc_hide_all_menus') ) {
      //adds the second menu state
      $_secondary_menu     = TC_Utils::$inst -> tc_is_secondary_menu_enabled() ? 'tc-second-menu-on' : '';
      $_secondary_menu    .= ' tc-second-menu-' . esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_resp_setting' ) ) . '-when-mobile';
    }

    $_add_classes 			= array(
        $_show_tagline ? 'tc-tagline-on' : 'tc-tagline-off',
        $_show_title_logo ? 'tc-title-logo-on' : 'tc-title-logo-off',
        $_use_sticky_logo ? 'tc-sticky-logo-on' : '',
        $_shrink_title_logo ? 'tc-shrink-on' : 'tc-shrink-off',
        $_show_menu ? 'tc-menu-on' : 'tc-menu-off',
        isset( $_secondary_menu ) ? $_secondary_menu : '',
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

  /**
  * Adds a specific style to handle the header top border and z-index
  * hook : tc_user_options_style
  *
  * @package Customizr
  */
  function tc_user_options_style_cb( $_css ) {
    //TOP BORDER
    if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_top_border') ) ) {
      $_css = sprintf("%s%s",
        $_css,
        "
        header.tc-header {border-top: none;}
        "
      );
    }
    //HEADER Z-INDEX
    if ( 100 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_z_index') ) ) {
      $_custom_z_index = esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_z_index') );
      $_css = sprintf("%s%s",
        $_css,
        "
        .tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header {
          z-index:{$_custom_z_index}
        }"
      );
    }
    return $_css;
  }


  function tc_body_class( $_classes ) {
    //STICKY HEADER
    if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) ) {
      $_classes = array_merge( $_classes, array('tc-sticky-header', 'sticky-disabled') );
      
      //STICKY TRANSPARENT ON SCROLL
      if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_transparent_on_scroll' ) ) )
        $_classes = array_merge( $_classes, array('tc-transparent-on-scroll') );
      else
        $_classes = array_merge( $_classes, array('tc-solid-color-on-scroll') );
    }
    else {
      $_classes = array_merge( $_classes, array('tc-no-sticky-header') );
    }
    return $_classes;
  }
}//end of class
