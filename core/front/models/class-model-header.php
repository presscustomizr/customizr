<?php
class CZR_cl_header_model_class extends CZR_cl_Model {
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $_model = array(
      'element_class' => apply_filters('czr_header_container_classes', array(
          'overlap' == esc_attr( czr_fn_get_opt( 'tc_sticky_header_type' ) ) ? 'navbar-fixed-top' : '',
          'logo-' . esc_attr( czr_fn_get_opt( 'tc_header_layout' ) )
      ))
    );

    if ( esc_attr( czr_fn_get_opt( "tc_sticky_header") || CZR() -> czr_fn_is_customizing() ) )
      array_push( $_model['element_class'],
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_shrink_title_logo') ) ? ' tc-shrink-on' : ' tc-shrink-off',
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_title_logo') ) ? 'tc-title-logo-on' : 'tc-title-logo-off'
      );

    return array_merge( $model, $_model );
  }

  function czr_fn_setup_children() {
    $children = array(
      //* Registered as child here as they need to filter the header class and add custom style css */
      array( 'model_class' => 'header/logo', 'id' => 'logo' ),
      array( 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/logo_sticky'), 'id' => 'sticky_logo' ),

      //here because it acts on the header class
      array( 'id' => 'tagline', 'model_class' => 'header/tagline' ),
    );

    return $children;
  }

  /**
  * Adds a specific style to handle the header top border and z-index
  * hook : czr_fn_user_options_style
  *
  * @package Customizr
  */
  function czr_fn_user_options_style_cb( $_css ) {
        //HEADER Z-INDEX
        if ( 100 != esc_attr( czr_fn_get_opt( 'tc_sticky_z_index') ) ) {
          $_custom_z_index = esc_attr( czr_fn_get_opt( 'tc_sticky_z_index') );
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
  
  function czr_fn_body_class( $_classes/*array*/ ) {
        //STICKY HEADER
        if ( 1 == esc_attr( czr_fn_get_opt( 'tc_sticky_header' ) ) ) {
          array_push( $_classes, 'tc-sticky-header', 'sticky-disabled', 'navbar-sticky-' . esc_attr( czr_fn_get_opt( 'tc_sticky_header_type' ) ) );

          //STICKY TRANSPARENT ON SCROLL
          if ( 1 == esc_attr( czr_fn_get_opt( 'tc_sticky_transparent_on_scroll' ) ) )
            array_push( $_classes, 'tc-transparent-on-scroll' );
          else
            array_push( $_classes, 'tc-solid-color-on-scroll' );
        }
        else
          array_push( $_classes, 'tc-no-sticky-header' );
        return $_classes;
  }
}//end of class