<?php
if ( ! class_exists( 'TC_controller_modules' ) ) :
  class TC_controller_modules extends TC_controllers {
    static $instance;
    private $_cache;

    function __construct( $_args = array()) {
      self::$instance =& $this;
      //why we don't call TC_controllers constructor?
      //why this class extends TC_controllers?
    }

    function tc_display_view_social_block( $model ) {
      static $socials_map = array(
        //structural hook => option filter
        '__widget_area_left__'  => 'left-sidebar',
        '__widget_area_right__' => 'right-sidebar',
        '__navbar__'            => 'header',
        '__colophon_one__'      => 'footer'
      );

      //the block must be instanciated when 
      //1) IS customizing or no model hook set
      //or
      //2a) the block is displayed in a non-standard (not option mapped) structural hook 
      //and
      //2b) There are social icons set
      //or
      //3a) the relative display option IS unchecked ( matching the map array above )
      //and
      //3b) There are social icons set
      
      //(1)
      if ( TC___::$instance -> tc_is_customizing() )
        return true;

      $_socials = TC_utils::$inst -> tc_get_social_networks();
      //(2a)
      if ( ! isset( $socials_map[ $model['hook'] ] ) )
        return (bool) $_socials;

      //(3b)
      return ( 1 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_{$socials_map[ $model['hook'] ]}" ) ) && tc__f('__get_socials') );
    }

    function tc_display_view_main_slider() {
      //gets the front slider if any
      $tc_front_slider              = esc_attr(TC_utils::$inst->tc_opt( 'tc_front_slider' ) );
      //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types)
      $_show_slider = TC_utils::$inst -> tc_is_home() ? ! empty( $tc_front_slider ) : ! is_404() && ! is_archive() && ! is_search();
    
      return apply_filters( 'tc_show_slider' , $_show_slider );
 
    }

  }//end of class
endif;
