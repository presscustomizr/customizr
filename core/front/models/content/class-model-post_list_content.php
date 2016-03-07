<?php
class TC_post_list_content_model_class extends TC_Model {
  public $class_cb;
  public $content_cb;
  public $type = 'content';
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $alternate = true ;
    $thumb_first = false;
    global $wp_query;
    $model[ 'content_cb' ] = 'the_excerpt';
    $model[ 'class_cb'   ] = array( $this, 'tc_get_class' );
    return $model;
  }

  function tc_get_class(){
    if ( (bool) get_query_var('tc_has_post_thumbnail', false) )
      echo 'tc-content span8'; /*retrieved from the post_list layout */ 
    else
      echo 'tc-content span12';
  }
}
