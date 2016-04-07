<?php
class TC_author_info_model_class extends TC_Model {
  public $author_wrapper_class;
  public $author_avatar_class;
  public $author_avatar_size;
  public $author_content_class;

  /*
  * @override
  */
  function tc_maybe_render_this_model_view() {
    return $this -> visibility && ( bool ) get_the_author_meta( 'description' );
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
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    foreach ( array('wrapper', 'avatar', 'content' ) as $property )
      $model -> {"author_{$property}_class"} = $this -> tc_stringify_model_property( "author_{$property}_class" );
  }

}
