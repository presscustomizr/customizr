<?php
class TC_comment_block_title_model_class extends TC_Model {
  public $title;

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  'have_comments' ); 
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
