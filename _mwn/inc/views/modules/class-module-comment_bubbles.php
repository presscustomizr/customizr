<?php
/**
* Comments actions
* fired on 'wp'
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_comment_bubbles' ) ) :
  class TC_comment_bubbles{
      static $instance;
      function __construct() {
        self::$instance =& $this;
        //Add comment bubble
        add_filter( 'tc_the_title'            , array( $this , 'tc_display_comment_bubble' ), 1 );
        //Custom Bubble comment since 3.2.6
        add_filter( 'tc_bubble_comment'       , array( $this , 'tc_custom_bubble_comment'), 10, 2 );

        //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
        //fired on hook : wp_enqueue_scripts
        //Set thumbnail specific design based on user options
        //Set user defined various inline stylings
        add_filter( 'tc_user_options_style'   , array( $this , 'tc_comment_bubble_inline_css' ) );
      }




    /**
    * Callback for tc_the_title
    * @return  string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_display_comment_bubble( $_title = null ) {
      if ( ! $this -> tc_is_bubble_enabled() )
        return $_title;

      global $post;
      //checks if comments are opened AND if there are any comments to display
      return sprintf('%1$s <span class="comments-link"><a href="%2$s#tc-comment-title" title="%3$s %4$s">%5$s</a></span>',
        $_title,
        is_singular() ? '' : get_permalink(),
        sprintf( '%1$s %2$s' , get_comments_number(), __( 'Comment(s) on' , 'customizr' ) ),
        is_null($_title) ? esc_attr( strip_tags( $post -> post_title ) ) : esc_attr( strip_tags( $_title ) ),
        0 != get_comments_number() ? apply_filters( 'tc_bubble_comment' , '' , esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_shape' ) ) ) : ''
      );
    }



   /**
    * Callback of tc_bubble_comment
    * @return string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_custom_bubble_comment( $_html , $_opt ) {
      return sprintf('%4$s<span class="tc-comment-bubble %1$s">%2$s %3$s</span>',
        'default' == $_opt ? "default-bubble" : $_opt,
        get_comments_number(),
        'default' == $_opt ? '' : sprintf( _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ),
          number_format_i18n( get_comments_number(), 'customizr' )
        ),
        $_html
      );
    }


    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    function tc_comment_bubble_inline_css( $_css ) {
      //apply custom color only if type custom
      //if color type is skin => bubble color is defined in the skin stylesheet
      if ( 'skin' != esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_color_type' ) ) ) {
        $_custom_bubble_color = esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_color' ) );
        $_css .= "
          .comments-link .tc-comment-bubble {
            color: {$_custom_bubble_color};
            border: 2px solid {$_custom_bubble_color};
          }
          .comments-link .tc-comment-bubble:before {
            border-color: {$_custom_bubble_color};
          }
        ";
      }

      if ( 'default' == esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_shape' ) ) )
        return $_css;

      $_css .= "
        .comments-link .custom-bubble-one {
          position: relative;
          bottom: 28px;
          right: 10px;
          padding: 4px;
          margin: 1em 0 3em;
          background: none;
          -webkit-border-radius: 10px;
          -moz-border-radius: 10px;
          border-radius: 10px;
          font-size: 10px;
        }
        .comments-link .custom-bubble-one:before {
          content: '';
          position: absolute;
          bottom: -14px;
          left: 10px;
          border-width: 14px 8px 0;
          border-style: solid;
          display: block;
          width: 0;
        }
        .comments-link .custom-bubble-one:after {
          content: '';
          position: absolute;
          bottom: -11px;
          left: 11px;
          border-width: 13px 7px 0;
          border-style: solid;
          border-color: #FAFAFA rgba(0, 0, 0, 0);
          display: block;
          width: 0;
        }\n";

      return $_css;
    }//end of fn

    /**
    * When are we displaying the comment bubble ?
    * - Must be in the loop
    * - Bubble must be enabled by user
    * - comments are enabled
    * - there is at least one comment
    * - the comment list option is enabled
    * - post type is in the eligible post type list : default = post
    * - tc_comments_in_title boolean filter is true
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_is_bubble_enabled() {
      $_bool_arr = array(
        in_the_loop(),
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_show_bubble' ) ),
        get_comments_number() != 0,
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_show_comment_list' ) ),
        (bool) apply_filters( 'tc_comments_in_title', true ),
        in_array( get_post_type(), apply_filters('tc_show_comment_bubbles_for_post_types' , array( 'post' , 'page') ) )
      );
      return (bool) array_product($_bool_arr);
    }

  }//end class
endif;