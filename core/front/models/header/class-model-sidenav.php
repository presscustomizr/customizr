<?php
class TC_sidenav_model_class extends TC_Model {
  public $class;
  public $inner_class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    add_filter( 'tc_user_options_style', array( $this, 'tc_set_sidenav_style') );

    $model[ 'class' ]         = implode(' ', apply_filters('tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) );
    $model[ 'inner_class' ]   = implode(' ', apply_filters('tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) );  
    
    return $model;
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
  * Adds a specific style for the sidenav
  * hook : tc_user_options_style
  *
  * @package Customizr
  * @since Customizr 3.2.11
  */
  function tc_set_sidenav_style( $_css ) {
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
