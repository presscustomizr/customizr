<?php
class TC_comment_bubble_model_class extends TC_Model {
  public $link;
  public $inner_class;
  public $text;

  /* DO WE WANT TO SPLIT THIS IN TWO? USING TWO DIFFERENT TEMPLATES TOO???
  *  Maybe we can do this later when we'll have the "routers" so we can register just one of the comment bubbles type based on the user options
  */

  function __construct( $model = array() ) {
    parent::__construct( $model );

    //inside the loop but before rendering set some properties
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), -1 );
    //when in the post list loop
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_has_comment_bubble') );
  }


  /* This is actually a merge of the tc_is_bubble_enabled and tc_are_comments_enabled */
  function tc_has_comment_bubble() {
    global $post;
   
    $_bool = true;

    if ( isset( $post ) ) {

      $_bool = in_the_loop() && ! post_password_required() && 0 != get_comments_number() &&
         in_array( get_post_type(), apply_filters('tc_show_comment_bubbles_for_post_types' , array( 'post' , 'page') ) );
 
      $_bool = ( 'closed' != $post -> comment_status ) ? true && $_bool : $_bool;

     //3) check global user options for pages and posts
      if ( is_page() )
        $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_page_comments' )) && $_bool;
      else
        $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_comments' )) && $_bool;
    }else
      $_bool = false;    

    return $_bool;
  }

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $shape                      = esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_shape' ) ); 
    $model[ 'inner_class' ]     = 'default' == $shape ? 'default-bubble' : $shape;
    return $model;
  }


  function tc_set_this_properties() {
    $link = sprintf( "%s%s", 
        is_singular() ? '' : get_permalink(),
        apply_filters( 'tc_bubble_comment_anchor', '#tc-comment-title')
    );

    $text = number_format_i18n( get_comments_number() );
    $text .= FALSE === strpos( $this -> inner_class, 'default' ) ? ' ' . _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ) : '';

    $this -> tc_update( compact( 'link', 'text' ) );
  }   

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  }


  /*
  * Callback of tc_user_options_style hook
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  function tc_user_options_style_cb( $_css ) {
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
            border-color: {$_custom_bubble_color} transparent;
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
}
