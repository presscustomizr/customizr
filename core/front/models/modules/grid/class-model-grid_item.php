<?php
class CZR_cl_grid_item_model_class extends CZR_cl_model {
  public  $thumb_img;
  public  $figure_class;

  public  $icon_attributes;
  public  $icon_enabled;

  public  $is_expanded;
  public  $title;

  public  $has_recently_updated = true;

  public  $has_edit_in_caption;
  public  $has_title_in_caption;
  public  $has_fade_expt;

  function czr_fn_setup_late_properties() {
    $grid                   = czr_fn_get( 'grid_item' );
    if ( empty( $grid ) )
      return;

    $section_cols           = isset( $grid[ 'section_cols' ] ) ? $grid[ 'section_cols' ] : '';
    $is_expanded            = isset( $grid[ 'is_expanded' ] ) ? $grid[ 'is_expanded' ] : false;

    //thumb
    $thumb_properties       = $this -> czr_fn_get_thumb_properties( $section_cols );
    $has_thumb              = isset( $thumb_properties[ 'has_thumb' ] ) ? $thumb_properties[ 'has_thumb' ] : false;
    $thumb_img              = isset( $thumb_properties[ 'thumb_img' ] ) ? $thumb_properties[ 'thumb_img' ] : ''; 

    //figure class
    $figure_class           = $this -> czr_fn_get_the_figure_class( $has_thumb, $section_cols );

    //array
    $icon_visibility        = $this -> czr_fn_set_grid_icon_visibility();

    $title                  = $this -> czr_fn_set_grid_title_length( get_the_title(), $is_expanded );

    $has_title_in_caption   = $this -> czr_fn_get_title_in_caption( $is_expanded );

    $has_edit_in_caption    = $this -> czr_fn_get_edit_in_caption( $is_expanded );

    $has_fade_expt          = $this -> czr_fn_get_fade_expt( $is_expanded, $thumb_img );
    //update the model
    $this -> czr_fn_update( array_merge(
        $icon_visibility,
        compact(
          'thumb_img',
          'figure_class',
          'is_expanded',
          'title',
          'has_title_in_caption',
          'has_fade_expt',
          'has_edit_in_caption' 
        ) 
    ) );
  }

  /*
  * has edit in caption
  */
  function czr_fn_get_edit_in_caption( $is_expanded ) {
    return $is_expanded;
  }

  /*
  * has title in caption
  */
  function czr_fn_get_title_in_caption( $is_expanded ) {
    return $is_expanded;
  }

  /*
  * has fade expt
  */
  function czr_fn_get_fade_expt( $is_expanded, $thumb_img ) {
    return ! ( $is_expanded || $thumb_img );
  }


  /**
  * Limits the length of the post titles in grids to a custom number of characters
  * @return string
  */
  function czr_fn_set_grid_title_length( $_title, $is_expanded ) {
    $_max = esc_attr( czr_fn_get_opt( 'tc_grid_num_words') );
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
  function czr_fn_get_thumb_properties( $section_cols ) {
    $has_thumb           = $this -> czr_fn_grid_show_thumb();
    $thumb_img           = '';

    if ( $has_thumb ) {
      $thumb_model                   = czr_fn_get_thumbnail_model(
          $thumb_size                = $this -> czr_fn_get_thumb_size_name( $section_cols ),
          null, null, null,
          $_filtered_thumb_size_name = $this -> czr_fn_get_filtered_thumb_size_name( $section_cols )
      );

      if ( ! isset( $thumb_model['tc_thumb'] ) )
        return;

      $thumb_img              = apply_filters( 'czr-grid-thumb-img', $thumb_model[ 'tc_thumb' ], czr_fn_get_id() );
    }

    return compact( 'has_thumb', 'thumb_img' );
  }


  /*
  * figure class
  */
  function czr_fn_get_the_figure_class( $has_thumb, $section_cols ) {
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == $section_cols ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return $figure_class;
  }





  /*
  * grid icon visibility
  * @return array
  */
  function czr_fn_set_grid_icon_visibility() {
    $icon_enabled        = (bool) esc_attr( czr_fn_get_opt( 'tc_grid_icons') );
    $icon_attributes     = '';
    if ( CZR() -> czr_fn_is_customizing() )
      $icon_attributes   = sprintf('style="display:%1$s"',
          $icon_enabled ? 'inline-block' : 'none'
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
  function czr_fn_get_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc-grid-full' : 'tc-grid';
  }


  /*
  * get the thumb size name to set the proper inline style
  * if needed, accordint to the grid element width
  */
  function czr_fn_get_filtered_thumb_size_name( $section_cols ){
    return ( 1 == $section_cols ) ? 'tc_grid_full_size' : 'tc_grid_size';
  }

  private function czr_fn_grid_show_thumb() {
    return czr_fn_has_thumb() && 0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) );
  }


  /**
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
    parent::czr_fn_sanitize_model_properties( $model );
    foreach ( array('figure') as $property )
      $model -> {"{$property}_class"} = $this -> czr_fn_stringify_model_property( "{$property}_class" );
  }
}