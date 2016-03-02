<?php
class TC_posts_list_description_model_class extends TC_Model {
  public $description;  

  function tc_extend_params( $model = array() ) {
    $model['description']   = $this -> tc_get_description_content();
    return $model;
  }

  function tc_get_description_content() {
    //we should have some filter here, to allow the processing of the description
    //for example to allow shortcodes in it.... (requested at least twice from users, in my memories)
    return category_description();    
  }
}  
