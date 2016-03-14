<?php
class TC_post_page_title_model_class extends TC_Model {
  public $title_cb;
  private static $post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

  private $context;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_post_has_headings') );
  }

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $this -> context            = $this -> tc_get_the_post_page_context();
    $model[ 'title_cb'   ]      = array( $this, 'tc_get_title' );
    $model['element_class']     = apply_filters( 'tc_archive_icon', $this -> tc_get_post_page_title_class( 'entry-title' ) );
    return $model;
  }

  function tc_post_has_headings() {
    return ! ( in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', self::$post_formats_with_no_heading ) ) );
  }

  function tc_get_the_post_page_context() {
    if ( ! is_singular() )  
      return 'post_list';
    if ( is_page() )
      return 'page';
    
    return 'post';
  }
  
  function tc_get_post_page_title_class( $class ) {
    $new_class = ( esc_attr( TC_utils::$inst->tc_opt( "tc_show_{$this -> context}_title_icon" ) ) 
          && esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ) ? array( 'format-icon' ) : array();
    array_push( $new_class, $class );
    return $new_class;
  }

  function tc_get_title() {
    echo get_the_title();
  }
}
