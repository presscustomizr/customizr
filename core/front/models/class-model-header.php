<?php
class CZR_header_model_class extends CZR_Model {
  public $elements_container_class;
  public $navbar_template;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    /*
    * New layouts
    * 1) left           => sl-logo_left   //sl stays for single-line : backward compatibility
    * 2) right          => sl-logo_right  //sl stays for single-line : backward compatibility
    * 3) centered       => logo_centered  //backward compatibility
    * 4) v-left         => v-logo_left    //v stays for "vertical"
    * 5) v-right        => v-logo_right   //v stays for "vertical"
    */
    $header_layouts = esc_attr( czr_fn_get_opt( 'tc_header_layout' ) );

    switch ( $header_layouts ) {

      case 'right'    : $navbar_template = 'single_line';
                        $element_class   = 'sl-logo_right';
                        break;
      case 'centered' : $navbar_template = 'logo_centered';
                        $element_class   = 'logo_centered';
                        break;
      case 'v-left'   : $navbar_template = 'vertical';
                        $element_class   = 'v-logo_left';
                        break;
      case 'v-right'  : $navbar_template = 'vertical';
                        $element_class   = 'v-logo_right';
                        break;

      default         : $navbar_template = 'single_line';
                        $element_class   = 'sl-logo_left';
    }

    $element_class            = array( $element_class );
    $elements_container_class = array();


    /* Is the header absolute ? add absolute and header-transparent classes
    * The header is absolute when:
    * a) the header_type option is 'absolute'
    * or
    * b) we display a full heading (with background) AND
    * b.1) not in front page
    */
    if ( 'absolute' == esc_attr( czr_fn_get_opt( 'tc_header_type' ) ) ||
        ( 'full' == esc_attr( czr_fn_get_opt( 'tc_heading' ) ) && ! czr_fn_is_home() ) )
      array_push( $element_class, 'header-absolute', 'header-transparent' );

    //No navbar box
    if ( 1 != esc_attr( czr_fn_get_opt( 'tc_display_boxed_navbar') ) )
      array_push( $element_class, 'no-navbar' );

    //regular menu
    if ( 'side' != esc_attr( czr_fn_get_opt( 'tc_menu_style') ) )
      array_push( $element_class, 'czr-regular-menu' );


    //header class for the secondary menu
    if ( czr_fn_is_secondary_menu_enabled() )
      array_push(  $element_class,
        'czr-second-menu-on',
        'czr-second-menu-' . esc_attr( czr_fn_get_opt( 'tc_second_menu_resp_setting' ) ) . '-when-mobile'
      );


    /* Sticky header treatment */
    $_sticky_header  = esc_attr( czr_fn_get_opt( "tc_sticky_header") ) || czr_fn_is_customizing();

    if ( $_sticky_header ) {
      array_push( $element_class,
        0 != esc_attr( czr_fn_get_opt( 'tc_woocommerce_header_cart_sticky' ) ) ? 'czr-wccart-on' : 'czr-wccart-off',
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_tagline') ) ? 'czr-tagline-on' : 'czr-tagline-off',
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_menu') ) ? 'czr-menu-on' : 'czr-menu-off',
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_shrink_title_logo') ) ? 'czr-shrink-on' : 'czr-shrink-off',
        0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_title_logo') ) ? 'czr-title-logo-on' : 'czr-title-logo-off'
      );
      array_push( $elements_container_class, 'navbar-to-stick' );
    }


    return array_merge( $model, array(
      'element_class'            => array_filter( apply_filters( 'czr_header_class', $element_class ) ),
      'elements_container_class' => array_filter( apply_filters( 'czr_header_elements_container_class', $elements_container_class ) ),
      'navbar_template'          => $navbar_template
     ) );
  }


  function czr_fn_setup_children() {
    $children = array(
      //* Registered as children here as they need to filter the header class and add custom style css */
      array( 'id' => 'logo', 'model_class' => 'header/logo',  ),
      array( 'id' => 'sticky_logo', 'model_class' => array( 'parent' => 'header/logo', 'name' => 'header/logo_sticky') ),

      //secondary and primary menu registered here because of the extending
      array( 'id' => 'navbar_menu', 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/regular_menu' ) ),

      array( 'id' => 'secondary_menu', 'model_class' => array( 'parent' => 'header/menu', 'name' => 'header/second_menu' ) ),

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
        .navbar-sticky, .header-absolute .topnav_navbars__wrapper {
          z-index:{$_custom_z_index}
        }"
      );
    }
    return $_css;
  }


  function czr_fn_body_class( $_classes/*array*/ ) {
    //STICKY HEADER
    if ( 1 == esc_attr( czr_fn_get_opt( 'tc_sticky_header' ) ) ) {

      /* WHICH OPTIONS SHOULD BE KEPT HERE ???? */
      //STICKY TRANSPARENT ON SCROLL
      if ( 1 == esc_attr( czr_fn_get_opt( 'tc_sticky_transparent_on_scroll' ) ) )
        array_push( $_classes, 'tc-transparent-on-scroll' );
      else
        array_push( $_classes, 'tc-solid-color-on-scroll' );
    }

    return $_classes;
  }
}//end of class