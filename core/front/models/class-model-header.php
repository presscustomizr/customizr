<?php
class TC_header_model_class extends TC_Model {
  public $has_sticky_pusher    = false;
  public $pusher_margin_top    = 103;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $element_class = apply_filters('tc_header_classes', array(
        'tc-header' ,'clearfix', 'row-fluid',
        'logo-' . esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout' ) )
    ));
    if ( true == $has_sticky_pusher = $this -> tc_has_pusher_margin_top() )
      $pusher_margin_top = apply_filters( 'tc_default_sticky_header_height', 103 );

    return array_merge( $model, compact( 'element_class', 'has_sticky_pusher', 'pusher_margin_top') );
  }


  function tc_has_pusher_margin_top() {
    //with the contx in the future the existing of the push might be an option
    //e.g. not having it in a particular page so to allow the header to overlap
    //a slider/image, hence the reason of this method existence
    return esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) )
              || TC___::$instance -> tc_is_customizing();
  }

  function tc_setup_children() {
    $children = array(
      //LOGO
      //array( 'hook' => '__header__', 'template' => 'header/logo_wrapper' ),
      array( 'hook' => false, 'template' => 'header/logo_wrapper' ),

      //array( 'hook' => '__logo_wrapper__', 'template' => 'header/logo'),
      //array( 'hook' => '__logo_wrapper__', 'id' => 'sticky_logo', 'template' => 'header/logo' , 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/logo_sticky') ),

      array( 'hook' => false, 'template' => 'header/logo'),
      array( 'hook' => false, 'id' => 'sticky_logo', 'template' => 'header/logo' , 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/logo_sticky') ),


      //TITLE
      //array( 'hook' => '__header__', 'template' => 'header/title'  ),
      array( 'hook' => false, 'template' => 'header/title'  ),

      //MOBILE TAGLINE
      //array( 'hook' => '__header__', 'template' => 'header/tagline', 'id' => 'mobile_tagline', 'priority' => 20, 'model_class' => array( 'parent' => 'header/tagline', 'name' => 'header/tagline_mobile') ),
      array( 'hook' => false, 'template' => 'header/tagline', 'id' => 'mobile_tagline', 'priority' => 20, 'model_class' => array( 'parent' => 'header/tagline', 'name' => 'header/tagline_mobile') ),

      //NAVBAR
      //array( 'hook' => '__header__', 'template' => 'header/navbar_wrapper', 'priority' => 20 ),
      array( 'hook' => false, 'template' => 'header/navbar_wrapper', 'priority' => 20 ),

      //socialblock in navbar
      //array( 'hook' => '__navbar__', 'template' => 'modules/social_block', 'priority' => is_rtl() ? 20 : 10, 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'header/header_social_block' ) ),
      array( 'hook' => false, 'template' => 'modules/social_block', 'priority' => is_rtl() ? 20 : 10, 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'header/header_social_block' ) ),

      //tagline in navbar
      //array( 'hook' => '__navbar__', 'template' => 'header/tagline', 'priority' => is_rtl() ? 10 : 20 ),
      array( 'hook' => false, 'template' => 'header/tagline', 'priority' => is_rtl() ? 10 : 20 ),

      //menu in navbar
      //array( 'hook' => '__navbar__', 'id' => 'navbar_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),
      array( 'hook' => false, 'id' => 'navbar_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),

      //secondary
      //array( 'hook' => '__navbar__', 'id' => 'navbar_secondary_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/second_menu' ) ),
      array( 'hook' => false, 'id' => 'navbar_secondary_menu', 'template' => 'header/menu', 'priority' => 30, 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/second_menu' ) ),

      //responsive menu button
      //array( 'hook' => '__navbar__', 'id' => 'mobile_menu_button', 'template' => 'header/menu_button', 'priority' => 40 ),
      array( 'hook' => false, 'id' => 'mobile_menu_button', 'template' => 'header/menu_button', 'priority' => 40 ),

      //sidenav navbar menu button
      //array( 'hook' => '__navbar__', 'id' => 'sidenav_navbar_menu_button', 'template' => 'header/menu_button', 'priority' => 25, 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),
      array( 'hook' => false, 'id' => 'sidenav_navbar_menu_button', 'template' => 'header/menu_button', 'priority' => 25, 'model_class' => array( 'parent' => 'header/menu_button', 'name' => 'header/sidenav_menu_button' ) ),

      //SIDENAV
      //array( 'hook' => '__before_page_wrapper', 'template' => 'header/sidenav' ),
      //array( 'hook' => false, 'template' => 'header/sidenav' ), <= rendered in page_wrapper template now

      );

    return $children;
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
