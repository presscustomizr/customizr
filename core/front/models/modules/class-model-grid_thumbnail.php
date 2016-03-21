<?php
class TC_grid_thumbnail_model_class extends TC_model {
  public  $thumb_img;
  public  $icon_attributes;
  public  $icon_enabled;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    //we need the -1 (or some < 0 number) as priority, as the thumb in single post page can be rendered at a certain hook with priority 0 (option based)
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), -1 );

    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_has_post_thumbnail') );
  } 


  function tc_has_post_thumbnail() { 
    return (bool) get_query_var( 'tc_has_post_thumbnail', true );
  }


  function tc_set_this_properties() {
    if ( ! $this -> tc_has_post_thumbnail() )
      return;
    $thumb_model            = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model( $this -> tc_get_thumb_size() );
    extract( $thumb_model );

    if ( ! isset( $tc_thumb ) )
      return;    

    $thumb_img              = apply_filters( 'tc-grid-thumb-img', $tc_thumb, TC_utils::tc_id() );
    if ( ! $thumb_img )
      return;     

    //update the model
    $this -> tc_update( array_merge( $this -> tc_set_grid_icon_visibility(), compact( 'thumb_img') ) );
  }

  /*
  * hook : tc_thumb_size_name
  */
  function tc_get_thumb_size(){
    return ( 1 == get_query_var( 'section_cols', 1 ) ) ? 'tc-grid-full' : 'tc-grid';
  }

  /**
  * hook : tc-grid-thumb-html
  * @return modified html string
  */
  function tc_set_grid_icon_visibility() {
    $icon_enabled    = (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_icons') );
    $icon_attributes = '';
    if ( TC___::$instance -> tc_is_customizing() )
      $icon_attributes = sprintf('style="display:%1$s">',
          $icon_enabled ? 'inline-block' : 'none'
      );
    return compact( 'icon_enabled', 'icon_attributes' ); 
  }
   
  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );  
    foreach ( array('figure') as $property )
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
  }
}
