<?php
class TC_article_model_class extends TC_Model {
  public $article_selectors;  
  
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) { 
    $model[ 'article_selectors' ]         = TC_utils::$inst -> tc_article_selectors( $echo = false );

    return $model;
  }
}
