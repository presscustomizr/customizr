<?php
class TC_thumbnail_model_class extends TC_Model {
  public $thumb_wrapper_class   = 'thumb-wrapper';
  public $link_class            = 'round-div';

  public $thumb_size;
  public $thumb_img;


  function __construct( $model = array() ) {
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    //we need the -1 (or some < 0 number) as priority, as the thumb in single post page can be rendered at a certain hook with priority 0 (option based)
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), -1 );
  }


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'thumb_size' ]     = $this -> tc_get_thumb_size();
    return $model;
  }

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function tc_maybe_filter_views_model() {
    parent::tc_maybe_filter_views_model();
    /* WARNING : HERE WE MIGHT NEED THE PARENT CONCEPT */
    add_action( 'pre_rendering_view_post_list_wrapper'         , array( $this, 'tc_add_thumb_shape_name' ) );
  }


  function tc_maybe_render_this_model_view() {
    return $this -> visibility && (bool) get_query_var( 'tc_has_post_thumbnail', false );
  }


  function tc_set_this_properties() {
    if ( ! $this -> tc_maybe_render_this_model_view() )
      return;
    $thumb_model            = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model( $this -> thumb_size );
    extract( $thumb_model );

    $thumb_img              = apply_filters( 'tc_post_thumb_img', $tc_thumb, TC_utils::tc_id() );
    if ( ! $thumb_img )
      return;     

    $element_class          = $this -> tc_get_the_wrapper_class();

    $no_effect_class = $this -> tc_get_no_effect_class( $thumb_model );

    //add the effect
    $link_class             =  apply_filters( 'tc_thumbnail_link_class', array_merge( array( $this -> link_class ), $no_effect_class ) );
    $thumb_wrapper_class    =  apply_filters( 'tc_thumb_wrapper_class', array_merge( array( $this -> thumb_wrapper_class ), $no_effect_class ) );

    //update the model
    $this -> tc_update( compact( 'thumb_wrapper', 'element_class', 'link_class', 'thumb_img') );
  }

  function tc_get_no_effect_class( $thumb_model ) {
    extract( $thumb_model );  
    //handles the case when the image dimensions are too small
    $thumb_size       = apply_filters( 'tc_thumb_size' , TC_init::$instance -> tc_thumb_size , TC_utils::tc_id() );
    $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
    $no_effect_class  = ( esc_attr( TC_utils::$inst->tc_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;
    
    return array( $no_effect_class );
  }


  /**
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  protected function tc_get_thumb_size( $_default_size = 'tc-thumb' ) {
    return $_default_size;  
  }



  /**
  * Callback of filter pre_rendering_view_post_list_wrapper
  *
  * @return  array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_add_thumb_shape_name( $model ) {
    if ( ! $this -> tc_maybe_render_this_model_view() )
      return;
    
    $position                    = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    $thumb_shape                 = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') );

    $new_class                   = "thumb-position-$position $thumb_shape";

    $model -> article_selectors  = str_replace( 'class="', 'class="' . $new_class . ' ', $model -> article_selectors );
  }


  
  /* The template wrapper class */
  function tc_get_the_wrapper_class() {
    return array( get_query_var('tc_thumbnail_width', '') ); /*retrieved from the post_list layout */
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) { 
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array( 'thumb_wrapper', 'link' ) as $property ) {
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
    }
  }

}
