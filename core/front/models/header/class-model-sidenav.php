<?php
class TC_sidenav_model_class extends TC_Model {

  function __construct( $model = array() ) {
    parent::__construct( $model );
    add_filter( 'tc_menu_open_on_click', array( $this, 'tc_disable_dropdown_on_click' ), 10, 3 );
  }


  /**
  * @override
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  }


  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class($_classes) {
    array_push( $_classes, 'tc-side-menu' );

    //sidenav where
    $_where = str_replace( 'pull-menu-', '', esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_position') ) );
    array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );

    return $_classes;
  }

  /**
  * This hooks is fired in the Walker_Page extensions, by the start_el() methods.
  * It only concerns the main menu, when the sidenav is enabled.
  * @since Customizr 3.4+
  *
  * hook :tc_menu_open_on_click
  */
  function tc_disable_dropdown_on_click( $replace, $search, $_location = null ) {
    return 'main' == $_location ? $search : $replace ;
  }

  /**
  * Adds a specific style for the sidenav
  * hook : tc_user_options_style
  *
  * @package Customizr
  * @since Customizr 3.2.11
  */
  function tc_user_options_style_cb( $_css ) {
    $sidenav_width = apply_filters( 'tc_sidenav_width', 330 );

    $_sidenav_mobile_css = '
        #tc-sn { width: %1$spx;}
        nav#tc-sn { z-index: 999; }
        [class*=sn-left].sn-close #tc-sn, [class*=sn-left] #tc-sn{
          -webkit-transform: translate3d( -100%%, 0, 0 );
          -moz-transform: translate3d( -100%%, 0, 0 );
          transform: translate3d(-100%%, 0, 0 );
        }
        [class*=sn-right].sn-close #tc-sn,[class*=sn-right] #tc-sn {
          -webkit-transform: translate3d( 100%%, 0, 0 );
          -moz-transform: translate3d( 100%%, 0, 0 );
          transform: translate3d( 100%%, 0, 0 );
        }
        .animating #tc-page-wrap, .sn-open #tc-sn, .tc-sn-visible:not(.sn-close) #tc-sn{
          -webkit-transform: translate3d( 0, 0, 0 );
          -moz-transform: translate3d( 0, 0, 0 );
          transform: translate3d(0,0,0) !important;
        }
    ';
    $_sidenav_desktop_css = '
        #tc-sn { width: %1$spx;}
        .tc-sn-visible[class*=sn-left] #tc-page-wrap { left: %1$spx; }
        .tc-sn-visible[class*=sn-right] #tc-page-wrap { right: %1$spx; }
        [class*=sn-right].sn-close #tc-page-wrap, [class*=sn-left].sn-open #tc-page-wrap {
          -webkit-transform: translate3d( %1$spx, 0, 0 );
          -moz-transform: translate3d( %1$spx, 0, 0 );
          transform: translate3d( %1$spx, 0, 0 );
        }
        [class*=sn-right].sn-open #tc-page-wrap, [class*=sn-left].sn-close #tc-page-wrap {
          -webkit-transform: translate3d( -%1$spx, 0, 0 );
          -moz-transform: translate3d( -%1$spx, 0, 0 );
           transform: translate3d( -%1$spx, 0, 0 );
        }
        /* stick the sticky header to the left/right of the page wrapper */
        .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-left] .tc-header { left: %1$spx; }
        .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-right] .tc-header { right: %1$spx; }
        /* ie<9 breaks using :not */
        .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-left] .tc-header { left: %1$spx; }
        .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-right] .tc-header { right: %1$spx; }
    ';

    return sprintf("%s\n%s",
      $_css,
      sprintf(
          apply_filters('tc_sidenav_inline_css',
            apply_filters( 'tc_sidenav_slide_mobile', wp_is_mobile() ) ? $_sidenav_mobile_css : $_sidenav_desktop_css
          ),
          $sidenav_width
      )
    );
  }//end user option style
}
