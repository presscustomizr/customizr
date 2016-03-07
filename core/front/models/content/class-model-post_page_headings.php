<?php
class TC_post_page_headings_model_class extends TC_headings_model_class {
  public $type = 'content';
  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_get_class( $model = array() ) {
    return 'entry-header';  
  }
}
