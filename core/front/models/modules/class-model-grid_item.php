<?php
class TC_grid_item_model_class extends TC_model {
  public  $thumb_img;
  public  $figure_class;

  public  $icon_attributes;
  public  $has_icon;

  public  $is_expanded;
  public  $title;


  function tc_setup_late_properties() {
    $grid                   = get_query_var( 'grid' );
    extract( $grid );
    //thumb
    $thumb_properties       = $this -> tc_get_thumb_properties( $section_cols );
    extract( $thumb_properties );
    //figure class
    $figure_class           = $this -> tc_get_the_figure_class( $has_thumb, $section_cols );

    $icon_visibility        = $this -> tc_set_grid_icon_visibility();

    $title                  = $this -> tc_set_grid_title_length( get_the_title() );
    //update the model
    $this -> tc_update( array_merge( $icon_visibility, compact( 'thumb_img', 'figure_class', 'is_expanded', 'title' ) ) );
  }


  /**
  * Limits the length of the post titles in grids to a custom number of characters
  * @return string
  */
  function tc_set_grid_title_length( $_title ) {
    $_max = esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_num_words') );
    $_max = ( empty($_max) || ! $_max ) ? 10 : $_max;
    $_max = $_max <= 0 ? 1 : $_max;

    if ( empty($_title) || ! is_string($_title) )
      return $_title;

    if ( count( explode( ' ', $_title ) ) > $_max ) {
      $_words = array_slice( explode( ' ', $_title ), 0, $_max );
      $_title = sprintf( '%s ...',
        implode( ' ', $_words )
      );
    }
    return $_title;
  }


  /*
  * thumb properties
  */
  function tc_get_thumb_properties( $section_cols ) {
    $has_thumb           = $this -> tc_grid_show_thumb();
    $thumb_img           = '';

    if ( $has_thumb ) {
      $thumb_model                   = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model(
          $thumb_size                = $this -> tc_get_thumb_size_name( $section_cols ),
          null, null, null,
          $_filtered_thumb_size_name = $this -> tc_get_filtered_thumb_size_name( $section_cols )
      );
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
  function tc_get_the_figure_class( $has_thumb, $section_cols ) {
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == $section_cols ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return $figure_class;
  }





  /*
  * grid icon visibility
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



  /**** HELPER ****/

  /**
  * @return  boolean
  */


  /*
  * get the thumb size name to use according to the grid element width
  */
  function tc_get_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc-grid-full' : 'tc-grid';
  }


  /*
  * get the thumb size name used in the TC_utils_thumbnails to set the proper inline style
  * if needed, accordint to the grid element width
  */
  function tc_get_filtered_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc_grid_full_size' : 'tc_grid_size';
  }

  private function tc_grid_show_thumb() {
    return TC_utils_thumbnails::$instance -> tc_has_thumb() && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) );
  }


  /**
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    foreach ( array('figure') as $property )
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
  }
}
