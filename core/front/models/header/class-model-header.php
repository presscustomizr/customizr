<?php
class TC_header_model_class extends TC_Model {

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ] = apply_filters('tc_header_classes', array(
        'tc-header' ,'clearfix', 'row-fluid',
        'logo-' . esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout' ) )
    ));
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> element_class ) )  
      $model -> element_class = join( ' ', array_unique( $model -> element_class ) );    
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


  function tc_body_class( $_classes/*array*/ ) {
    //STICKY HEADER
    if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) ) {
      array_push( $_classes, 'tc-sticky-header', 'sticky-disabled' );
      
      //STICKY TRANSPARENT ON SCROLL
      if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_transparent_on_scroll' ) ) )
        array_push( $_classes, 'tc-transparent-on-scroll' );
      else
        array_push( $_classes, 'tc-solid-color-on-scroll' );
    }
    else
      array_push( $_classes, 'tc-no-sticky-header' );
    
    return $_classes;
  }
}//end of class
