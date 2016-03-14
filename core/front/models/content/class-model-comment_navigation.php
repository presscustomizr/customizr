<?php
class TC_comment_navigation_model_class extends TC_Model {
  public $title;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_maybe_render_comment_navigation' ) ); 
  }

  function tc_maybe_render_comment_navigation() {
    return ( have_comments() && get_comment_pages_count() > 1 );
  }
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    if ( 1 == get_comments_number() )
      $_title = __( 'One thought on', 'customizr' );
    else 
      $_title = sprintf( '%1$s %2$s', number_format_i18n( get_comments_number(), 'customizr' ) , __( 'thoughts on', 'customizr' ) );

    $model[ 'title' ] = $_title;

    return $model;
  }

}
