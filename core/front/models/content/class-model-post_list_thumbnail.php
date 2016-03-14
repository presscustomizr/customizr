<?php
class TC_post_list_thumbnail_model_class extends TC_Model {
  public $content_cb;
  public $content_class = 'tc-thumb';

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    //render this?
    add_filter( "tc_do_render_view_{$this -> id}",  array( $this, 'tc_has_post_thumbnail') );
  }

  function tc_has_post_thumbnail() {
    return (bool) get_query_var('tc_has_post_thumbnail', false);
  }
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model['content_cb']  = 'the_post_thumbnail';
    return $model;
  }

  function tc_get_element_class() {
    return 'tc-thumbnail ' . get_query_var('tc_thumbnail_width'); /*retrieved from the post_list layout */
  }
}
