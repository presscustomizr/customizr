<?php
if ( ! class_exists( 'CZR_prevdem' ) ) :
  final class CZR_prevdem {
    function __construct() {
      //FEATURED PAGES DISABLED BY DEFAULT
      add_filter( 'tc_opt_tc_show_featured_pages', '__return_false' );
      //SLIDER DISABLED BY DEFAULT
      add_filter( 'tc_opt_tc_front_slider', '__return_false' );
    }//construct
  }//end of class
endif;

?>