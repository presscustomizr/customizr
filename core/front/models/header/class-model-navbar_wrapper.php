<?php
class TC_navbar_wrapper_model_class extends TC_Model {
  public $class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    add_filter( 'body_class', array( $this, 'tc_add_body_classes' ) );

    $model[ 'class' ] = $this -> get_navbar_classes();
    
    return $model;
  }

  /**
  * parse this model properties
  */
  function pre_rendering_my_view_cb( $model ) {
    $model -> class = join( ' ', array_unique( $model -> class ) );
  }

  function get_navbar_classes() {
    $_classes = array('navbar-wrapper', 'clearfix', 'span9');

    $_classes = ( ! wp_is_mobile() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_fade_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-fade' ) ) : $_classes;
    
    $_classes = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_submenu_item_move_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-move' ) ) : $_classes;
    
    $_classes = ( ! wp_is_mobile() && 'hover' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_type' ) ) ) ? array_merge( $_classes, array( 'tc-open-on-hover' ) ) : array_merge( $_classes, array( 'tc-open-on-click' ) );
  //Navbar menus positions (not sidenav)
  //CASE 1 : regular menu (sidenav not enabled), controled by option 'tc_menu_position'
  //CASE 2 : second menu ( is_secondary_menu_enabled ?), controled by option 'tc_second_menu_position'
   /* if ( ! apply_filters( 'tc_is_sidenav_enabled', 'aside' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_style' ) ) ) )
       array_push( $_classes , esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );*/
/*    if ( apply_filters( 'tc_is_second_menu_enabled', (bool)esc_attr( TC_utils::$inst->tc_opt( 'tc_display_second_menu' ) ) ) )
        array_push( $_classes , esc_attr( TC_utils::$inst->tc_opt( 'tc_second_menu_position') ) );*/
    return $_classes;    
  }

   /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_body_classes($_classes) {
    //No navbar box
    if ( 1 != esc_attr( TC_utils::$inst->tc_opt( 'tc_display_boxed_navbar') ) )
      $_classes = array_merge( $_classes , array('no-navbar' ) );
    return $_classes;
  }
}
