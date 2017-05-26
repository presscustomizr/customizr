<?php
if ( ! class_exists( 'CZR_controller_header' ) ) :
  class CZR_controller_header extends CZR_controllers {
    static $instance;

    function czr_fn_display_view_head() {
      return true;
    }

    function czr_fn_display_view_header_social_block() {
      return czr_fn_has_social_links() && 1 == esc_attr( czr_fn_opt( "tc_social_in_header" ) );
    }

    function czr_fn_display_view_navbar_social_block() {
      $_topbar_on        = $this->czr_fn_display_view_topbar();
      $_social_in_navbar = $_topbar_on ? 1 != esc_attr( czr_fn_opt( "tc_social_in_topnav" ) ) : true;

      return $this -> czr_fn_display_view_header_social_block() && $_social_in_navbar;

    }

    function czr_fn_display_view_topbar_social_block() {
      return $this -> czr_fn_display_view_header_social_block() && 1 == esc_attr( czr_fn_opt( "tc_social_in_topnav" ) );
    }

    function czr_fn_display_view_branding_tagline() {
      return 1 == esc_attr( czr_fn_opt( "tc_tagline_branding" ) );
    }


    function czr_fn_display_view_topbar() {
      return 1 == esc_attr( czr_fn_opt( 'tc_header_topbar' ) );
    }


    function czr_fn_display_view_mobile_tagline() {
      return $this -> czr_fn_display_view_tagline();
    }

    //do not display the tagline when:
    //1) not in customizer preview (we just hide it in the model)
    //2) the user choose to not display it
    function czr_fn_display_view_tagline() {
      return czr_fn_is_customizing() || ! ( 0 == esc_attr( czr_fn_opt( 'tc_show_tagline') ) );
    }

    function czr_fn_display_view_title() {
      return ! $this -> czr_fn_display_view_logo_wrapper();
    }

    function czr_fn_display_view_logo_wrapper() {
      //display the logo wrapper when one of them is available;
      return $this -> czr_fn_display_view_logo() || $this -> czr_fn_display_view_sticky_logo();
    }

    function czr_fn_display_view_logo() {
      $_logo_atts = czr_fn_get_logo_atts();
      return ! empty( $_logo_atts );
    }

    function czr_fn_display_view_sticky_logo() {
      if ( esc_attr( czr_fn_opt( "tc_sticky_header" ) ) ) {
        /*sticky logo is quite new no bc needed*/
        $_logo_atts = czr_fn_get_logo_atts( 'sticky', $backward_compat = false );
        return ! empty( $_logo_atts );
      }
      return;
    }

    //when the 'main' navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is not aside (sidenav)
    function czr_fn_display_view_navbar_primary_menu() {
      return $this -> czr_fn_display_view_menu() && 'aside' != esc_attr( czr_fn_opt( 'tc_menu_style' ) );
    }

    //when the 'secondary' navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is aside (sidenav)
    function czr_fn_display_view_navbar_secondary_menu() {
      return $this -> czr_fn_display_view_menu() && czr_fn_is_secondary_menu_enabled();
    }

    //when the top navbar menu is allowed?
    //1) menu allowed
    //and
    //2) topbar is displayed
    function czr_fn_display_view_topbar_menu() {
      return $this -> czr_fn_display_view_menu() &&  esc_attr( czr_fn_opt( 'tc_header_topbar' ) );
    }

    //when the sidenav menu is allowed?
    //1) menu allowed
    //and
    //2) menu style is aside
    function czr_fn_display_view_sidenav() {
      return $this -> czr_fn_display_view_menu() && 'aside' == esc_attr( czr_fn_opt( 'tc_menu_style' ) );
    }

    function czr_fn_display_view_menu() {
      return ! czr_fn_opt('tc_hide_all_menus');
    }

    //when the 'sidevan menu button' is allowed?
    //1) menu button allowed
    //2) menu style is aside ( sidenav)
    //==
    //czr_fn_display_view_sidenav
    function czr_fn_display_view_sidenav_menu_button() {
      return $this -> czr_fn_display_view_sidenav();
    }
    function czr_fn_display_view_sidenav_navbar_menu_button() {
      return $this -> czr_fn_display_view_sidenav();
    }

    //when the 'mobile menu button' is allowed?
    //1) menu button allowed
    //2) menu style is not aside (no sidenav)
    function czr_fn_display_view_mobile_menu_button() {
      return $this -> czr_fn_display_view_menu() && ! $this -> czr_fn_display_view_sidenav();
    }

    //when the 'menu button' is allowed?
    //1) menu allowed
    function czr_fn_display_view_menu_button() {
      return $this -> czr_fn_display_view_menu();
    }


    function czr_fn_display_view_nav_search()  {
      return czr_fn_opt( 'tc_search_in_header' );
    }
  }//end of class
endif;