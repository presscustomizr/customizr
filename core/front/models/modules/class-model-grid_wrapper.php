<?php
class TC_grid_wrapper_model_class extends TC_article_model_class {
  public $is_first_of_row;
  public $is_last_of_row;

  public $is_loop_start;
  public $is_loop_end;

  public $section_cols;

  public $figure_class;
  public $has_heading_in_caption;
//  public $text;

  private $expanded_sticky = false;

  private $post_id;

  /* override */
  function __construct( $model ) {
    //Fires the parent constructor
    parent::__construct( $model );

    $this -> post_id = TC_utils::tc_id();

    //inside the loop but before rendering set some properties
    add_action( $this -> hook, array( $this, 'set_this_properties' ), 0 );
  }


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $element_class = array();  
    
    //wrapper classes based on the user options
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_shadow') ) )
      array_push( $element_class, 'tc-grid-shadow' );
    if ( esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_bottom_border') ) )
      array_push( $element_class, 'tc-grid-border' );

    $model[ 'element_class' ] = $element_class;

    return $model;
  }



  function set_this_properties() {
    $element_wrapper        = $this -> tc_get_element_wrapper_properties();
    //figure class
    $figure_properties      = $this -> tc_get_figure_properties();

    $has_heading_in_caption = array(true);
    //section properties which refers to the section row wrapper
    $section_row_wrapper    = $this -> tc_get_section_row_wrapper_properties();
    $this -> tc_update( array_merge( $element_wrapper, $section_row_wrapper, $figure_properties, $has_heading_in_caption ) );

    //hack
    set_query_var( 'section_cols', $section_row_wrapper['section_cols'] );
  }

  /*
  *
  */
  function tc_get_figure_properties() {
    $has_thumb           = $this -> tc_grid_show_thumb();
    $figure_class        = array( $has_thumb ? 'has-thumb' : 'no-thumb' );

    //if 1 col layout or current post is the expanded => golden ratio should be disabled
    if ( ( '1' == $this -> tc_get_grid_cols() || $this -> tc_force_current_post_expansion() ) && ! wp_is_mobile() )
      array_push( $figure_class, 'no-gold-ratio' );
    return compact( 'figure_class', 'has_thumb' );
  }

  /*
  * post list wrapper
  */
  function tc_get_element_wrapper_properties() {
    global $wp_query;
    $is_loop_start = 0 == $wp_query -> current_post;
    $is_loop_end   = $wp_query -> current_post == $wp_query -> post_count ;

    return compact( 'is_loop_start', 'is_loop_end' );
  }

  /*
  * Wrap articles in a grid section
  */
  function tc_get_section_row_wrapper_properties() {
    global $wp_query;
    
    $current_post      = $wp_query -> current_post;
    $start_post        = $this -> expanded_sticky ? 1 : 0;
    $section_cols      = $this -> tc_get_grid_section_cols();

    $is_first_of_row = false;
    $is_last_of_row  = false;

    if ( $start_post == $current_post || 0 == ( $current_post - $start_post ) % $section_cols ) 
      $is_first_of_row = true;
   
    if ( $wp_query->post_count == ( $current_post + 1 ) || 0 == ( ( $current_post - $start_post + 1 ) % $section_cols ) )
      $is_last_of_row  = true;

    return compact( 'is_first_of_row', 'is_last_of_row', 'section_cols' );
  }


  /* retrieves number of cols option, and wrap it into a filter */
  private function tc_get_grid_cols() {
    return apply_filters( 'tc_get_grid_cols',
      esc_attr( TC_utils::$inst->tc_opt( 'tc_grid_columns') ),
      TC_utils::tc_get_layout( $this -> post_id , 'class' )
    );
  }

  /* returns articles wrapper section columns */
  private function tc_get_grid_section_cols() {
    return apply_filters( 'tc_grid_section_cols',
      $this -> tc_force_current_post_expansion() ? '1' : $this -> tc_get_grid_cols()
    );
  }

  /*
  * @return bool
  * returns if the current post is the expanded one
  */
  private function tc_force_current_post_expansion(){
    global $wp_query;
    return ( $this -> expanded_sticky && 0 == $wp_query -> current_post );
  }



  /**
  * @override
  * Returns the classes for the post div.
  *
  * @param string|array $class One or more classes to add to the class list.
  * @param int $post_id An optional post ID.
  * @package Customizr
  * @since 3.0.10
  */
  function tc_get_post_class( $class = '', $post_id = null ) {
    $_class = sprintf( '%1$s tc-grid span%2$s',
      apply_filters( 'tc_grid_add_expanded_class', $this -> tc_force_current_post_expansion() ) ? 'expanded' : '',
      is_numeric( $this -> tc_get_grid_section_cols() ) ? 12 / $this -> tc_get_grid_section_cols() : 6
    );
     
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $_class ) ) . '"';
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
