<?php
class TC_grid_item_model_class extends TC_model {
  public  $thumb_img;
  public  $figure_class;

  public  $icon_attributes;
  public  $has_icon;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    //inside the loop but before rendering set some properties
    //we need the -1 (or some < 0 number) as priority, as the thumb in single post page can be rendered at a certain hook with priority 0 (option based)
    add_action( $model['hook']          , array( $this, 'tc_set_this_properties' ), -1 );


  } 

  function tc_set_this_properties() {
    //thumb
    $thumb_properties       = $this -> tc_get_thumb_properties();
    extract( $thumb_properties );
    //figure class
    $figure_class           = $this -> tc_get_figure_class( $has_thumb );

    $icon_visibility        = $this -> tc_set_grid_icon_visibility(); 
    //update the model
    $this -> tc_update( array_merge( $icon_visibility, compact( 'thumb_img', 'figure_class') ) );
  }

  /*
  * thumb properties
  */
  function tc_get_thumb_properties() {
    $has_thumb           = $this -> tc_grid_show_thumb();
    $thumb_img           = null;
    
    if ( $has_thumb ) {
      $thumb_model            = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model( $this -> tc_get_thumb_size() );
      extract( $thumb_model );

      if ( ! isset( $tc_thumb ) )
        return;    
      
      $thumb_img              = apply_filters( 'tc-grid-thumb-img', $tc_thumb, TC_utils::tc_id() );
    }

    return compact( 'has_thumb', 'thumb_img' );
  }


  /*
  * figure class
  */
  function tc_get_figure_class( $has_thumb ) {
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == get_query_var('section_cols') ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return $figure_class;
  }


  /*
  * hook : tc_thumb_size_name
  */
  function tc_get_thumb_size(){
    return ( 1 == get_query_var( 'section_cols', 1 ) ) ? 'tc-grid-full' : 'tc-grid';
  }

  /**
  */
  function tc_set_grid_icon_visibility() {
    $has_icon        = (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_icons') );
    $icon_attributes = '';
    if ( TC___::$instance -> tc_is_customizing() )
      $icon_attributes = sprintf('style="display:%1$s">',
          $has_icon ? 'inline-block' : 'none'
      );
    return compact( 'icon_enabled', 'icon_attributes' ); 
  }



  /**
  * @return  boolean
  */
  private function tc_grid_show_thumb() {
    return TC_utils_thumbnails::$instance -> tc_has_thumb() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) );
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
