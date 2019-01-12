<?php
/**
* No results content actions
*
*/
if ( ! class_exists( 'CZR_no_results' ) ) :
  class CZR_no_results {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action  ( '__loop'                        , array( $this , 'czr_fn_no_result_content' ));
      }

      /**
       * Rendering the no search results
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function czr_fn_no_result_content() {
          global $wp_query;
          if ( !is_search() || (is_search() && 0 != $wp_query -> post_count) )
              return;

          echo apply_filters( 'tc_no_result_content',
              sprintf('<div class="%1$s"><div class="entry-content"><p>%2$s</p> %3$s</div>%4$s</div>',
                  'tc-content span12',
                  __( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'customizr' ),
                  get_search_form( $echo = false ),
                  '<hr class="featurette-divider '.current_filter().'">'
              )//end sprintf
          );//end filter
      }
  }//end of class
endif;

?>