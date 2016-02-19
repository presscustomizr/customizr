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
    function tc_display_view_menu() {
      return true;
    }

    function tc_display_view_title() {
      return true;
    }

    function tc_display_view_title_wrapper() {
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
       //The sticky logo option, when exists, is numeric
      //check if the attachment exists and the filetype is allowed
      $accepted_formats	    = apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
   
      $_attachment_data     = apply_filters( "tc_logo_attachment_img" , wp_get_attachment_image_src( $logo_option , 'full' ) );

      $_logo_src            = apply_filters( "tc_logo_src" , is_ssl() ? str_replace('http://', 'https://', $_attachment_data[0] ) : $_attachment_data[0] ) ;
      $filetype             = TC_utils::$inst -> tc_check_filetype ($_logo_src);
  
      if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) )
        $to_return = true;

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

  }//end of class
endif;
