<?php
class TC_post_list_content_model_class extends TC_Model {
  public $content_cb;
  
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
    return $model;
  }

  function tc_get_element_class(){
    if ( (bool) get_query_var('tc_has_post_thumbnail', false) )
      return 'tc-content span8'; /*retrieved from the post_list layout */ 
    else
      return 'tc-content span12';
  }
}
