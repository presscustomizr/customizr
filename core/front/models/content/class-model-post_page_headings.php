<?php
class TC_post_page_headings_model_class extends TC_headings_model_class {
  public $type = 'content';

  /**
  * @override
  */
  function tc_get_class( $model = array() ) {
    return 'entry-header';  
  }
}
