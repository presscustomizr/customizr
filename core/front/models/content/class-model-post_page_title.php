<?php
class TC_post_page_title_model_class extends TC_Model {
  public $title_cb;
  private static $post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_post_has_headings') );
  }

  function tc_post_has_headings() {
    return ! ( in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', self::$post_formats_with_no_heading ) ) );
  }


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'title_cb'   ] = array( $this, 'tc_get_title' );
    return $model;
  }

  function tc_get_title() {
    echo get_the_title();
  }
}
