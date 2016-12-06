<?php
if ( ! class_exists( 'CZR_controller_header' ) ) :
  class CZR_controller_header extends CZR_controllers {
    static $instance;
    private $_cache;

    function __construct( $_args = array()) {
          self::$instance =& $this;
          //why we don't call CZR_controllers constructor?
          //why this class extends CZR_controllers?

          //init the cache
          $this -> _cache = array();
    }

    function czr_fn_display_view_head() {
      return true;
    }

    function czr_fn_display_view_favicon() {
      //is there a WP favicon set ?
      //if yes then let WP do the job
      if ( function_exists('has_site_icon') && has_site_icon() )
        return;
      $_fav_option  			= esc_attr( czr_fn_get_opt( 'tc_fav_upload') );
     	if ( ! $_fav_option || is_null($_fav_option) )
     		return;
     	$_fav_src 				= '';
     	//check if option is an attachement id or a path (for backward compatibility)
     	if ( is_numeric($_fav_option) ) {
     		$_attachement_id 	= $_fav_option;
     		$_attachment_data 	= apply_filters( 'czr_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'full' ) );
     		$_fav_src 			= $_attachment_data[0];
     	} else { //old treatment
     		$_saved_path 		= esc_url ( czr_fn_get_opt( 'tc_fav_upload') );
     		//rebuild the path : check if the full path is already saved in DB. If not, then rebuild it.
       	$upload_dir 		= wp_upload_dir();
       	$_fav_src 			= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
     	}
     	//makes ssl compliant url
     	$_fav_src 				= apply_filters( 'czr_fav_src' , is_ssl() ? str_replace('http://', 'https://', $_fav_src) : $_fav_src );
      if( null == $_fav_src || !$_fav_src )
        return;
      return true;
    }

    function czr_fn_display_view_header_social_block() {
      return ( 1 == esc_attr( czr_fn_get_opt( "tc_social_in_header" ) ) ) &&
        ( czr_fn_is_customize_preview_frame()  || czr_fn_is_possible('social_block') );
    }


    function czr_fn_display_view_mobile_tagline() {
      return $this -> czr_fn_display_view_tagline();
    }

    //do not display the tagline when:
    //1) not in customizer preview (we just hide it in the model)
    //2) the user choose to not display it
    function czr_fn_display_view_tagline() {
      $CZR            = CZR();
      if ( ! isset( $this -> _cache[ 'view_tagline' ] ) )
        $this -> _cache[ 'view_tagline' ] = czr_fn_is_customizing() || ! ( 0 == esc_attr( czr_fn_get_opt( 'tc_show_tagline') ) );
      return $this -> _cache[ 'view_tagline' ];
    }

    function czr_fn_display_view_title() {
      return ! $this -> czr_fn_display_view_logo_wrapper();
    }

    function czr_fn_display_view_logo_wrapper() {
      //display the logo wrapper when one of them is available;
      return $this -> czr_fn_display_view_logo() || $this -> czr_fn_display_view_sticky_logo();
    }

    function czr_fn_display_view_logo() {
      if ( ! isset( $this -> _cache[ 'view_logo' ] ) )
        $this -> _cache[ 'view_logo' ] = ! empty( czr_fn_get_logo_atts() );

      return $this -> _cache[ 'view_logo' ];
    }

    function czr_fn_display_view_sticky_logo() {
      if ( ! isset( $this -> _cache[ 'view_sticky_logo' ] ) )
        $this -> _cache[ 'view_sticky_logo' ] = esc_attr( czr_fn_get_opt( "tc_sticky_header") ) && ! empty( czr_fn_get_logo_atts( 'sticky', $backward_compat = false ) );

      return $this -> _cache[ 'view_sticky_logo' ];
    }

    //when the 'main' navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is not aside (sidenav)
    function czr_fn_display_view_navbar_menu() {
      if ( ! isset( $this -> _cache[ 'view_navbar_menu' ] ) )
        $this -> _cache[ 'view_navbar_menu' ] = $this -> czr_fn_display_view_menu() && ! $this -> czr_fn_display_view_sidenav();

      return $this -> _cache[ 'view_navbar_menu' ];
    }

    //when the secondary navbar menu is allowed?
    //1) menu allowed
    //and
    //2) menu type is sidenav but a secondary menu is chosen
    function czr_fn_display_view_navbar_secondary_menu() {
      if ( ! isset( $this -> _cache[ 'view_navbar_secondary_menu' ] ) )
        $this -> _cache[ 'view_navbar_secondary_menu' ] = $this -> czr_fn_display_view_menu() &&  ( /*$this -> czr_fn_display_view_sidenav() && */czr_fn_is_secondary_menu_enabled() ) ;
      return $this -> _cache[ 'view_navbar_secondary_menu' ];
    }

    //when the sidenav menu is allowed?
    //1) menu allowed
    //and
    //2) menu style is aside
    function czr_fn_display_view_sidenav() {
      if ( ! isset( $this -> _cache[ 'view_sidenav' ] ) )
        $this -> _cache[ 'view_sidenav' ] = $this -> czr_fn_display_view_menu() && 'aside' == esc_attr( czr_fn_get_opt( 'tc_menu_style' ) );
      return $this -> _cache[ 'view_sidenav' ];
    }

    function czr_fn_display_view_menu() {
      if ( ! isset( $this -> _cache[ 'view_menu' ] ) )
        $this -> _cache[ 'view_menu' ] =  ! ( (bool) czr_fn_get_opt('tc_hide_all_menus') );
      return $this -> _cache[ 'view_menu' ];
    }

    //when the 'sidevan menu button' is allowed?
    //1) menu button allowed
    //2) menu style is aside ( sidenav)
    //==
    //czr_fn_display_view_sidenav
    function czr_fn_display_view_sidenav_menu_button() {
      return $this -> czr_fn_display_view_sidenav(); //already cached
    }
    function czr_fn_display_view_sidenav_navbar_menu_button() {
      return $this -> czr_fn_display_view_sidenav(); //already cached
    }

    //when the 'mobile menu button' is allowed?
    //1) menu button allowed
    //2) menu style is not aside (no sidenav)
    function czr_fn_display_view_mobile_menu_button() {
      return $this -> czr_fn_display_view_menu() && ! $this -> czr_fn_display_view_sidenav(); //already cached
    }

    //when the 'menu button' is allowed?
    //1) menu allowed
    function czr_fn_display_view_menu_button() {
      return $this -> czr_fn_display_view_menu(); /* already cached */
    }

  }//end of class
endif;
