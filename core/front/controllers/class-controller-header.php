<?php
if ( ! class_exists( 'TC_controller_header' ) ) :
  class TC_controller_header extends TC_controllers {
    static $instance;
    private $_cache;

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call TC_controllers constructor?
      //why this class extends TC_controllers?
      
      //init the cache
      $this -> _cache = array();
    }

    function tc_display_view_head() {
      return true;
    }

    function tc_display_view_reset_margin_top() {
      return 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_header' ) ) || TC___::$instance -> tc_is_customizing();    
    }

    function tc_display_view_header_socials() {
      //the block must be not instanciated when 
      //1) NOT customizing 
      //and
      //2a) the relative display option is unchecked
      //or
      //2b) there are no social icons set
      return ! ( ! TC___::$instance -> tc_is_customizing() && 
            ( ( 0 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_header" ) ) ) || ! tc__f('__get_socials') ) );
    }

    function tc_display_view_mobile_tagline_wrapper() {
      return true;  
    }

    //do not display the tagline when:
    //1) not in customizer preview (we just hide it in the model)
    //2) the user choose to not display it
    function tc_display_view_tagline() {
      return TC___::$instance -> tc_is_customizing() || ! ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_tagline') ) );
    }

    function tc_display_view_title() {
      return ! $this -> tc_display_view_logo_wrapper();
    }

    function tc_display_view_logo_wrapper() { 
      //display the logo wrapper when one of them is available;  
      return $this -> tc_display_view_logo() || $this -> tc_display_view_sticky_logo(); 
    }

    function tc_display_view_logo() {
      if ( isset( $this -> _cache[ 'view_logo' ] ) )
        return $this -> _cache[ 'view_logo' ];
      
      $to_return = false;
      //TODO:
        //backward compatibility when the logo was not numeric
        //unify sticky and normal logo controllers as much as possible
      $logo_option          = TC_utils::$inst->tc_opt( "tc_logo_upload");
      if ( $logo_option ) {
        //check if the attachment exists and the filetype is allowed
        $accepted_formats	    = apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
   
        $_attachment_data     = apply_filters( "tc_logo_attachment_img" , wp_get_attachment_image_src( $logo_option , 'full' ) );

        $_logo_src            = apply_filters( "tc_logo_src" , is_ssl() ? str_replace('http://', 'https://', $_attachment_data[0] ) : $_attachment_data[0] ) ;
        $filetype             = TC_utils::$inst -> tc_check_filetype ($_logo_src);
  
        if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
          $to_return = true;
      }
      
      $this -> _cache[ 'view_logo' ] = $to_return;
      return $to_return;
    }

    function tc_display_view_sticky_logo() {
      if ( isset( $this -> _cache[ 'view_sticky_logo' ] ) )
        return $this -> _cache[ 'view_sticky_logo' ];  
      
      $sticky_logo_option = TC_utils::$inst->tc_opt( "tc_sticky_logo_upload");

      if ( ! esc_attr( $sticky_logo_option ) )
        $to_return = false;
      elseif ( ! ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") ) &&
            esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') ) ) )
        $to_return = false;
      else {
        $to_return = false;
        //The sticky logo option, when exists, is numeric
        //check if the attachment exists and the filetype is allowed
        $accepted_formats	    = apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
   
        $_attachment_data     = apply_filters( "tc_sticky_logo_attachment_img" , wp_get_attachment_image_src( $sticky_logo_option , 'full' ) );

        $_logo_src            = apply_filters( "tc_sticky_logo_src" , is_ssl() ? str_replace('http://', 'https://', $_attachment_data[0] ) : $_attachment_data[0] ) ; 
        $filetype             = TC_utils::$inst -> tc_check_filetype ($_logo_src);
  
        if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
          $to_return = true;
      }
      
      $this -> _cache[ 'view_sticky_logo' ] = $to_return;
      return $to_return;
    }

    //when the 'main' navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is not aside (sidenav)
    function tc_display_view_navbar_menu() {
      if ( ! isset( $this -> _cache[ 'view_navbar_menu' ] ) )
        $this -> _cache[ 'view_navbar_menu' ] = $this -> tc_display_view_menu() && ! $this -> tc_display_view_sidenav();

      return $this -> _cache[ 'view_navbar_menu' ];
    }

    //when the secondary navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is sidenav but a secondary menu is chosen
    function tc_display_view_navbar_secondary_menu() {
      if ( ! isset( $this -> _cache[ 'view_navbar_secondary_menu' ] ) )
        $this -> _cache[ 'view_navbar_secondary_menu' ] = $this -> tc_display_view_menu() &&  ( $this -> tc_display_view_sidenav() && TC_Utils::$inst -> tc_is_secondary_menu_enabled() ) ;
      return $this -> _cache[ 'view_navbar_secondary_menu' ];
    }
 
    //when the sidenav menu is allowed?
    //1) menu allowed
    //and
    //2) menu style is aside
    function tc_display_view_sidenav() {
      if ( ! isset( $this -> _cache[ 'view_sidenav' ] ) )
        $this -> _cache[ 'view_sidenav' ] = $this -> tc_display_view_menu() && 'aside' == esc_attr( TC_utils::$inst->tc_opt( 'tc_menu_style' ) );  
      return $this -> _cache[ 'view_sidenav' ];
    }

    function tc_display_view_menu() {
      if ( ! isset( $this -> _cache[ 'view_menu' ] ) )
        $this -> _cache[ 'view_menu' ] =  ! ( (bool) TC_utils::$inst->tc_opt('tc_hide_all_menus') );
      return $this -> _cache[ 'view_menu' ];
    }
   
    //when the 'sidevan menu button' is allowed?
    //1) menu button allowed
    //2) menu style is aside ( sidenav)
    //==
    //tc_display_view_sidenav
    function tc_display_view_sidenav_menu_button() {
      return $this -> tc_display_view_sidenav(); //already cached
    }
    function tc_display_view_sidenav_navbar_menu_button() {
      return $this -> tc_display_view_sidenav(); //already cached
    }

    //when the 'mobile menu button' is allowed?
    //1) menu button allowed
    //2) menu style is not aside (no sidenav)
    function tc_display_view_mobile_menu_button() {
      return $this -> tc_display_view_menu() && ! $this -> tc_display_view_sidenav(); //already cached
    }

    //when the 'menu button' is allowed?
    //1) menu allowed
    function tc_display_view_menu_button() {
      return $this -> tc_display_view_menu(); /* already cached */
    }

  }//end of class
endif;
