<?php
class CZR_header_model_class extends CZR_Model {

  public $primary_nbwrapper_class;
  public $topbar_nbwrapper_class;
  public $mobile_nbwrapper_class;

  public $navbar_template;


  function __construct( $model = array() ) {
    parent::__construct( $model );

    $children = array(
      //* Registered as children here as they need to filter the header class and add custom style css */
      array( 'id' => 'logo', 'model_class' => 'header/parts/logo',  ),
      array( 'model_class' => 'header/parts/title', 'id' => 'title' ),


      //primary menu in the navbar
      array(
        'id' => 'navbar_primary_menu',
        'model_class' => 'header/parts/menu',
        'args' => array(
          'czr_menu_location' => 'primary_navbar'
        )
      ),

      //secondary menu in the navbar
      array(
        'id' => 'navbar_secondary_menu',
        'model_class' => 'header/parts/menu',
        'args' => array(
          'czr_menu_location' => 'secondary_navbar'
        ),
      ),

      //topbar menu
      array(
        'id' => 'topbar_menu',
        'model_class' => 'header/parts/menu',
        'args' => array(
          'czr_menu_location' => 'topbar'
        ),
      ),

      //sidenav menu
      array(
        'id' => 'sidenav_menu',
        'model_class' => 'header/parts/menu',
        'args' => array(
          'czr_menu_location' => 'sidenav'
        ),
      ),

      //mobile menu
      array(
        'id' => 'mobile_menu',
        'model_class' => 'header/parts/menu',
        'args' => array(
          'czr_menu_location' => 'mobile'
        ),
      ),

    );

    //register a single woocommerce cart model in the header
    //needs to be registered early to use 'woocommerce_add_to_cart_fragments'
    if ( apply_filters( 'tc_woocommerce_options_enabled', false )  ) {

        if ( 'none' != esc_attr( czr_fn_opt( 'tc_header_desktop_wc_cart' ) ) || esc_attr( czr_fn_opt( 'tc_header_mobile_wc_cart' ) ) ) {
            $children[] =  array(
              'model_class' => 'header/parts/woocommerce_cart',
              'id'          => 'woocommerce_cart',
          );
        }
    }

    foreach ( $children as $child_model ) {
        CZR() -> collection -> czr_fn_register( $child_model );
    }//foreach
  }//_construct


  /**
  * @override
  * fired before the model properties are parsed in the constructor
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
    $header_layouts = esc_attr( czr_fn_opt( 'tc_header_layout' ) );

    switch ( $header_layouts ) {

      case 'right'    : $navbar_template = 'navbar_wrapper';
                        $element_class   = 'sl-logo_right';
                        break;
      case 'centered' : $navbar_template = 'navbar_wrapper';
                        $element_class   = 'logo_centered';
                        break;
     /*  maybe pro
      case 'v-left'   : $navbar_template = 'vertical_navbar';
                        $element_class   = 'v-logo_left';
                        break;
      case 'v-right'  : $navbar_template = 'vertical_navbar';
                        $element_class   = 'v-logo_right';
                        break;
      */
      default         : $navbar_template = 'navbar_wrapper';
                        $element_class   = 'sl-logo_left';
    }

    $element_class            = array( $element_class );


    /* Is the header absolute ? add absolute and header-transparent classes
    * The header is absolute when:
    * a) not 404
    *
    * b) the header_type option is 'absolute'
    * or
    * c) we display a full heading (with background) AND
    * c.1) not in front page
    */
    //if ( !is_404() && ( 'absolute' == esc_attr( czr_fn_opt( 'tc_header_type' ) ) /*||
    //    ( 'full' == esc_attr( czr_fn_opt( 'tc_heading' ) ) && ! czr_fn_is_real_home() ) */ ) )
    //  array_push( $element_class, 'header-absolute', 'header-transparent' );


    /* Sticky header treatment */
    //Classes added here to the header will be the used in CSS and JS to obtain the desired style/effect
    array_push( $element_class,
        0 != esc_attr( czr_fn_opt( 'tc_sticky_shrink_title_logo') ) ? 'sticky-brand-shrink-on' : '',
        0 != esc_attr( czr_fn_opt( 'tc_sticky_transparent_on_scroll') ) ? 'sticky-transparent' : ''
    );

    /*
    * Set the desktop and mobile navbar classes (bp visibility and stickiness )
    * TODO: allow breakpoint changes
    */
    $_desktop_primary_navbar_class  = array( 'hidden-md-down' );
    $_desktop_topbar_navbar_class   = array( 'hidden-md-down' );
    $_mobile_navbar_class           = array( 'hidden-lg-up' );

    /*
    * Informations about the current active blocks in the primary header navbar :
    * => do we have the tagline ? is there a secondary horizontal menu, do we have a primary sidebar menu ? etc.
    */
    if ( czr_fn_is_registered_or_possible( 'navbar_primary_menu' ) && has_nav_menu( 'main') ) {
        $_desktop_primary_navbar_class[] = 'has-horizontal-menu';
    }
    if ( has_nav_menu( 'secondary') && czr_fn_is_registered_or_possible( 'navbar_secondary_menu' ) ) {
        $_desktop_primary_navbar_class[] = 'has-horizontal-menu';
    }
    if ( czr_fn_is_registered_or_possible( 'branding_tagline' ) && 'brand_next' == czr_fn_opt( 'tc_header_desktop_tagline' ) ) {
        $_desktop_primary_navbar_class[] = 'has-tagline-aside';
    }
    /*
    * Desktop sticky header
    */
    if ( 'no_stick' != esc_attr( czr_fn_opt( 'tc_header_desktop_sticky' ) ) ) {
      if (  'topbar' == esc_attr( czr_fn_opt( 'tc_header_desktop_to_stick' ) ) )
        $_desktop_topbar_navbar_class[] = 'desktop-sticky';
      else
        $_desktop_primary_navbar_class[] = 'desktop-sticky';
    }

    /*
    * Mobile sticky header
    */
    if ( 'no_stick' != esc_attr( czr_fn_opt( 'tc_header_mobile_sticky' ) ) ) {
      $_mobile_navbar_class[] = 'mobile-sticky';
    }



    /* TOP BORDER */
    if ( 1 == esc_attr( czr_fn_opt( 'tc_top_border') ) ) {
      $element_class[] = 'border-top';
    }

    /* Submenus effect */
    if ( ! wp_is_mobile() && 0 != esc_attr( czr_fn_opt( 'tc_menu_submenu_fade_effect') ) ) {
      $element_class[] = 'czr-submenu-fade';
    }

    if ( 0 != esc_attr( czr_fn_opt( 'tc_menu_submenu_item_move_effect') ) ) {
      $element_class[] = 'czr-submenu-move';
    }

    return array_merge( $model, array(
        'element_class'                 => array_filter( apply_filters( 'czr_header_class', $element_class ) ),
        'navbar_template'               => $navbar_template,
        'primary_nbwrapper_class'       => $_desktop_primary_navbar_class,
        'topbar_nbwrapper_class'        => $_desktop_topbar_navbar_class,
        'mobile_nbwrapper_class'        => $_mobile_navbar_class
    ) );
  }



  /**
  * Adds a specific style to handle the header top border and z-index
  * hook : czr_fn_user_options_style
  *
  * @package Customizr
  */
  function czr_fn_user_options_style_cb( $_css ) {
    //TOP BORDER
    if ( 1 == esc_attr( czr_fn_opt( 'tc_top_border') ) ) {
      $_css = sprintf("%s\n%s",
          $_css,
            ".tc-header.border-top { border-top-width: 5px; border-top-style: solid }"
      );
    }

    //HEADER Z-INDEX
    if ( 100 != esc_attr( czr_fn_opt( 'tc_sticky_z_index') ) ) {
      $_custom_z_index = esc_attr( czr_fn_opt( 'tc_sticky_z_index') );
      $_css = sprintf("%s%s",
        $_css,
        "
        .tc-header{
          z-index:{$_custom_z_index}
        }"
      );
    }
    return $_css;
  }

}//end of class