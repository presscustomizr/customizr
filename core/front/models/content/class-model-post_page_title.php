<?php
class TC_post_page_title_model_class extends TC_Model {
  public $title_cb;
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

  function tc_get_title(){
    echo '<a href="'.get_permalink().'">'.get_the_title().'</a>';
  }
}
