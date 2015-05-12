<?php
/**
* 404 content actions
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
if ( ! class_exists( 'TC_404' ) ) :
  class TC_404 {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
          self::$instance =& $this;
          //404 content
          add_action  ( '__loop'                      , array( $this , 'tc_404_content' ));
      }



      /**
       * The template part for displaying error 404 page content
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function tc_404_content() {
          if ( !is_404() )
              return;

          $content_404    = apply_filters( 'tc_404', TC_init::$instance -> content_404 );

          echo apply_filters( 'tc_404_content',
              sprintf('<div class="%1$s"><div class="entry-content %2$s">%3$s</div>%4$s</div>',
                  apply_filters( 'tc_404_wrapper_class', 'tc-content span12 format-quote' ),
                  apply_filters( 'tc_404_content_icon', 'format-icon' ),
                  sprintf('<blockquote><p>%1$s</p><cite>%2$s</cite></blockquote><p>%3$s</p>%4$s',
                                call_user_func( '__' , $content_404['quote'] , 'customizr' ),
                                call_user_func( '__' , $content_404['author'] , 'customizr' ),
                                call_user_func( '__' , $content_404['text'] , 'customizr' ),
                                get_search_form( $echo = false )
                  ),
                  apply_filters( 'tc_no_results_separator', '<hr class="featurette-divider '.current_filter().'">' )
              )
          );
      }
  }//end of class
endif;