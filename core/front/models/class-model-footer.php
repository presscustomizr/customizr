<?php
class TC_footer_model_class extends TC_Model {

  function tc_setup_children() {
    $children = array(
      //sticky footer
      array( 'hook' => 'after_render_view_main_container', 'template' => 'footer/footer_push', 'priority' => 100 ),
    );

    return $children;
  }
}//end of class
