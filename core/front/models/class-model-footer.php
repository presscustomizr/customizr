<?php
class TC_footer_model_class extends TC_Model {

  function tc_setup_children() {
    $children = array(
      //sticky footer
      array( 'hook' => 'after_render_view_main_container', 'template' => 'footer/footer_push', 'priority' => 100 ),
      //btt arrow
      array( 'hook' => '__after_page_wrapper', 'template' => 'footer/btt_arrow')
    );

    return $children;
  }
}//end of class
