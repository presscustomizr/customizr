<?php
class CZR_cl_second_menu_model_class extends CZR_cl_menu_model_class {
  public $theme_location = 'secondary';

  /**
  * @override
  * @hook: pre_rendering_view_navbar_wrapper
  */
  function pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model ) {
    parent::pre_rendering_view_navbar_wrapper_cb( $navbar_wrapper_model );

    array_push( $navbar_wrapper_model -> element_class, esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_second_menu_position') ) );
  }


  /**
  * @override
  * @hook; pre_rendering_view_header
  *
  * parse header model before rendering to add second menu classes
  */
  function pre_rendering_view_header_cb( $header_model ) {
    parent::pre_rendering_view_header_cb( $header_model );

    if ( ! is_array( $header_model -> element_class ) )
      $header_model -> class = explode( ' ', $header_model -> element_class );

    //header class for the secondary menu
    array_push( $header_model -> element_class,
          'tc-second-menu-on',
          'tc-second-menu-' . esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_second_menu_resp_setting' ) ) . '-when-mobile'
    );
  }

  /**
  * @hook tc_user_options_style
  * Second menu
  * This actually "restores" regular menu style (user options in particular) by overriding the max-width: 979px media query
  */
  function czr_fn_user_options_style_cb( $_css ) {
    return sprintf("%s\n%s",
      $_css,
      "@media (max-width: 979px) {
        .tc-second-menu-on .nav-collapse {
          width: inherit;
          overflow: visible;
          height: inherit;
          position:relative;
          top: inherit;
          -webkit-box-shadow: none;
          -moz-box-shadow: none;
          box-shadow: none;
          background: inherit;
        }
        .tc-sticky-header.sticky-enabled #tc-page-wrap .nav-collapse, #tc-page-wrap .tc-second-menu-hide-when-mobile .nav-collapse.collapse .nav {
          display:none;
        }
        .tc-second-menu-on .tc-hover-menu.nav ul.dropdown-menu {
          display:none;
        }
        .tc-second-menu-on .navbar .nav-collapse ul.nav>li li a {
          padding: 3px 20px;
        }
        .tc-second-menu-on .nav-collapse.collapse .nav {
          display: block;
          float: left;
          margin: inherit;
        }
        .tc-second-menu-on .nav-collapse .nav>li {
          float:left;
        }
        .tc-second-menu-on .nav-collapse .dropdown-menu {
          position:absolute;
          display: none;
          -webkit-box-shadow: 0 2px 8px rgba(0,0,0,.2);
          -moz-box-shadow: 0 2px 8px rgba(0,0,0,.2);
          box-shadow: 0 2px 8px rgba(0,0,0,.2);
          background-color: #fff;
          -webkit-border-radius: 6px;
          -moz-border-radius: 6px;
          border-radius: 6px;
          -webkit-background-clip: padding-box;
          -moz-background-clip: padding;
          background-clip: padding-box;
          padding: 5px 0;
        }
        .tc-second-menu-on .navbar .nav>li>.dropdown-menu:after, .navbar .nav>li>.dropdown-menu:before{
          content: '';
          display: inline-block;
          position: absolute;
        }
        .tc-second-menu-on .tc-hover-menu.nav .caret {
          display:inline-block;
        }
        .tc-second-menu-on .tc-hover-menu.nav li:hover>ul {
          display: block;
        }
        .tc-second-menu-on .nav a, .tc-second-menu-on .tc-hover-menu.nav a {
          border-bottom: none;
        }
        .tc-second-menu-on .dropdown-menu>li>a {
          padding: 3px 20px;
        }
        .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:focus,.tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:hover,.tc-second-menu-on .tc-submenu-move .dropdown-submenu:focus>a, .tc-second-menu-on .tc-submenu-move .dropdown-submenu:hover>a {
          padding-left: 1.63em
        }
        .tc-second-menu-on .tc-submenu-fade .nav>li>ul {
          opacity: 0;
          top: 75%;
          visibility: hidden;
          display: block;
          -webkit-transition: all .2s ease-in-out;
          -moz-transition: all .2s ease-in-out;
          -o-transition: all .2s ease-in-out;
          -ms-transition: all .2s ease-in-out;
          transition: all .2s ease-in-out;
        }
        .tc-second-menu-on .tc-submenu-fade .nav li.open>ul, .tc-second-menu-on .tc-submenu-fade .tc-hover-menu.nav li:hover>ul {
          opacity: 1;
          top: 95%;
          visibility: visible;
        }
        .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a {
          -webkit-transition: all ease .241s;
          -moz-transition: all ease .241s;
          -o-transition: all ease .241s;
          transition: all ease .241s;
        }
        .tc-second-menu-on .dropdown-submenu>.dropdown-menu {
          top: 110%;
          left: 30%;
          left: 30%\9;
          top: 0\9;
          margin-top: -6px;
          margin-left: -1px;
          -webkit-border-radius: 6px;
          -moz-border-radius: 6px;
          border-radius: 6px;
        }
        .tc-second-menu-on .dropdown-submenu>a:after {
          content: ' ';
        }
      }\n

      .sticky-enabled .tc-second-menu-on .nav-collapse.collapse {
        clear:none;
      }\n"
    );
  }//end fn

}//end class

