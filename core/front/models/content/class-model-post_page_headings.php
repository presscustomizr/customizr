<?php
class TC_post_page_headings_model_class extends TC_headings_model_class {
  public $type                                   = 'content';
  private static $post_formats_with_no_heading   = array( 'aside' , 'status' , 'link' , 'quote' );

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_post_has_headings') );
  }

  function tc_post_has_headings() {
    return ! ( in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', self::$post_formats_with_no_heading ) ) );
  }
}
