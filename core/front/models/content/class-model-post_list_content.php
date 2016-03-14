<?php
class TC_post_list_content_model_class extends TC_Model {
  public  $render_content_cb;
  public  $content_cb;
  private $content;
  public  $content_class;
  public  $content_tag = 'section';

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );

    //filter the excerpt length
    add_filter( 'excerpt_length'        , array( $this , 'tc_set_excerpt_length') , 999 );

    //filter our countent
    add_filter( 'tc_the_content'        , array( $this , 'tc_add_support_for_shortcode_special_chars') );

    //inside the loop but before rendering set some properties
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), 0 );
  }


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'content_cb' ] = array($this, 'tc_the_post_list_content');
    return $model;
  }


  function tc_get_element_class(){
    return 'tc-content ' . get_query_var( 'tc_content_width' );
  }


  function tc_the_post_list_content( $more  = null ) {
    if ( $this -> content )
      echo $this -> content;
    elseif ( 'the_excerpt' == $this -> render_content_cb )
      echo apply_filters( 'the_excerpt', get_the_excerpt() );  
    else
      echo apply_filters( 'tc_the_content', call_user_func( $this -> render_content_cb, $more ) );
  }

  
  
  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_set_excerpt_length( $length ) {
    $_custom = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  * hook : tc_the_content
  * Applies tc_the_content filter to the passed string
  *
  * @param string
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function tc_add_support_for_shortcode_special_chars( $_content ) {
    return str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $_content ) );
  }


  function tc_set_this_properties() {
    $show_excerpt        = get_query_var( 'tc_show_excerpt' );  
    $content_class       = array( 'entry-summary' );
    $render_content_cb   = $show_excerpt ? 'get_the_excerpt' : 'get_the_content' ;

    if ( in_array( get_post_format(), array( 'image' , 'gallery' ) ) )
    {
      $content_class     = array( 'entry-content');
      $content           = '<p class="format-icon"></p>';
    }
    elseif ( in_array( get_post_format(), array( 'quote', 'status', 'link', 'aside', 'video' ) ) ) {
      $content_class     = array( 'entry-content', apply_filters( 'tc_post_list_content_icon', 'format-icon' ) );
      $has_pagination    = true;
      $render_content_cb = 'get_the_content';
    }

    $this -> tc_update( compact( 'content_class', 'render_content_cb', 'has_pagination', 'content' ) );
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );  
    $model -> content_class = $this -> tc_stringify_model_property( 'content_class' );
  }
}
