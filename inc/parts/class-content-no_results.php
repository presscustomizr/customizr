<?php
/**
* No results content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_no_results' ) ) :
  class TC_no_results {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action  ( '__loop'                        , array( $this , 'tc_no_result_content' ));
      }

      /**
       * Rendering the no search results
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function tc_no_result_content() {
          global $wp_query;
          if ( !is_search() || (is_search() && 0 != $wp_query -> post_count) )
              return;

          $content_no_results    = apply_filters( 'tc_no_results', TC_init::$instance -> content_no_results );

          echo apply_filters( 'tc_no_result_content',
              sprintf('<div class="%1$s"><div class="entry-content %2$s">%3$s</div>%4$s</div>',
                  apply_filters( 'tc_no_results_wrapper_class', 'tc-content span12 format-quote' ),
                  apply_filters( 'tc_no_results_content_icon', 'format-icon' ),
                  sprintf('<blockquote><p>%1$s</p><cite>%2$s</cite></blockquote><p>%3$s</p>%4$s',
                                call_user_func( '__' , $content_no_results['quote'] , 'customizr' ),
                                call_user_func( '__' , $content_no_results['author'] , 'customizr' ),
                                call_user_func( '__' , $content_no_results['text'] , 'customizr' ),
                                get_search_form( $echo = false )
                  ),
                  apply_filters( 'tc_no_results_separator', '<hr class="featurette-divider '.current_filter().'">' )
              )//end sprintf
          );//end filter
      }
  }//end of class
endif;