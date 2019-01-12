<?php
/**
* 404 content actions
*
*/
if ( ! class_exists( 'CZR_404' ) ) :
  class CZR_404 {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
          self::$instance =& $this;
          //404 content
          add_action  ( '__loop'                      , array( $this , 'czr_fn_404_content' ));
      }



      /**
       * The template part for displaying error 404 page content
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function czr_fn_404_content() {
          if ( !is_404() )
              return;

          echo apply_filters( 'tc_404_content',
              sprintf('<div class="%1$s"><div class="entry-content"><p>%2$s</p> %3$s</div>%4$s</div>',
                  'tc-content span12',
                  __( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ),
                  get_search_form( $echo = false ),
                  '<hr class="featurette-divider '.current_filter().'">'
              )
          );
      }
  }//end of class
endif;

?>