<?php
class TC_author_info_model_class extends TC_Model {
  public $author_wrapper_class;
  public $author_avatar_class;
  public $author_avatar_size;
  public $author_content_class;
  
  
  function __construct( $model = array() ) {
    parent::__construct( $model );

    //render this?
    add_filter( "tc_do_render_view_{$this -> id}", array( $this, 'tc_maybe_render_author_info' ) ); 
  }

  function tc_maybe_render_author_info() {
    return ( bool ) get_the_author_meta( 'description' );    
  }

  function tc_extend_params( $model = array() ) {
    $model['author_wrapper_class'] = apply_filters( 'tc_author_meta_wrapper_class', array('row-fluid' ) );
    $model['author_avatar_class']  = apply_filters( 'tc_author_meta_avatar_class', array( 'comment-avatar', 'author-avatar', 'span2') );
    $model['author_avatar_size']   = apply_filters( 'tc_author_bio_avatar_size' , 100 );
    $model['author_content_class'] = apply_filters( 'tc_author_meta_content_class', array('author-description', 'span10') );
    
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    foreach ( array('wrapper', 'avatar', 'content' ) as $property )
      $model -> {"author_{$property}_class"} = $this -> tc_stringify_model_property( "author_{$property}_class" );
  }

}  
