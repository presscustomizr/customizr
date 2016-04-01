<?php
class TC_content_wrapper_model_class extends TC_Model {
  public $element_class;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]          = apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) );
    return $model;
  }
}
