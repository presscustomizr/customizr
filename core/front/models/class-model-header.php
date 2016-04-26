<?php
class CZR_cl_header_model_class extends CZR_cl_Model {
  public $has_sticky_pusher    = false;
  public $pusher_margin_top    = 103;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $element_class = apply_filters('czr_header_classes', array(
        'tc-header' ,'clearfix', 'row-fluid',
        'logo-' . esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_header_layout' ) )
    ));
    if ( true == $has_sticky_pusher = $this -> czr_fn_has_pusher_margin_top() )
      $pusher_margin_top = apply_filters( 'czr_default_sticky_header_height', 103 );

    return array_merge( $model, compact( 'element_class', 'has_sticky_pusher', 'pusher_margin_top') );
  }


  function czr_fn_has_pusher_margin_top() {
    //with the contx in the future the existing of the push might be an option
    //e.g. not having it in a particular page so to allow the header to overlap
    //a slider/image, hence the reason of this method existence
    return esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_sticky_header' ) )
              || CZR___::$instance -> czr_fn_is_customizing();
  }

  function czr_fn_setup_children() {
    $children = array(
      //LOGO
      /* Registered as child here as it needs to filter the header class and add custom style css */
      array( 'model_class' => 'header/logo_wrapper', 'logo_wrapper' ),

      array( 'model_class' => 'header/logo', 'id' => 'logo' ),
      array( 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/logo_sticky'), 'id' => 'sticky_logo' ),


      //TITLE
      /* Registered as child here as it needs to filter the header class and add custom style css */
      array( 'model_class' => 'header/title', 'id' => 'title'  ),

      //MOBILE TAGLINE
      array(  'id' => 'mobile_tagline',  'model_class' => array( 'parent' => 'header/tagline', 'name' => 'header/tagline_mobile') ),

      //NAVBAR
      /* Registered as child here as it needs to filter the body class (the header itself is registered in init)
      * and its id is used to add further CSS classe by other elements
      */
      array( 'model_class' => 'header/navbar_wrapper', 'id' => 'navbar_wrapper' ),

      //socialblock in navbar
      array( 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'header/header_social_block' ), 'id' => 'header_social_block', 'controller' => 'social_block' ),

      //tagline in navbar
      //here because it acts on the header class
      array( 'id' => 'tagline', 'model_class' => 'header/tagline' ),

      //menu in navbar
      array( 'id' => 'navbar_menu', 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),

      //secondary
      array( 'id' => 'navbar_secondary_menu', 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/second_menu' ) ),


      //sidenav navbar menu button
      array( 'id' => 'sidenav_navbar_menu_button', 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),


      /* Registered as child here as it needs to filter the body class class and the logos to add custom styles */
      array( 'id' => 'sidenav', 'model_class' => 'header/sidenav' ), //<= rendered in page_wrapper template now

      //second_menu help block
      array(
        'hook'        => '__after_sidenav_navbar_menu_button',
        'template'    => 'modules/help_block',
        'id'          => 'second_menu_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/second_menu_help_block'),
        'priority'    => 40
      ),
      //main menu help block
      array(
        'hook'        => '__after_mobile_menu_button',
        'template'    => 'modules/help_block',
        'id'          => 'main_menu_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/main_menu_help_block'),
        'priority'    => 40
      )
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
    //TOP BORDER
    if ( 1 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_top_border') ) ) {
      $_css = sprintf("%s%s",
        $_css,
        "
        header.tc-header {border-top: none;}
        "
      );
    }
    //HEADER Z-INDEX
    if ( 100 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_sticky_z_index') ) ) {
      $_custom_z_index = esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_sticky_z_index') );
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
    if ( 1 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_sticky_header' ) ) ) {
      array_push( $_classes, 'tc-sticky-header', 'sticky-disabled' );

      //STICKY TRANSPARENT ON SCROLL
      if ( 1 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_sticky_transparent_on_scroll' ) ) )
        array_push( $_classes, 'tc-transparent-on-scroll' );
      else
        array_push( $_classes, 'tc-solid-color-on-scroll' );
    }
    else
      array_push( $_classes, 'tc-no-sticky-header' );

    return $_classes;
  }
}//end of class
