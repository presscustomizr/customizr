<?php
class TC_footer_model_class extends TC_Model {

  function tc_setup_children() {
    $children = array(
      //sticky footer
      array( 'hook' => '__after_main_container', 'template' => 'footer/footer_push', 'priority' => 100 ),

     //footer widgets help block
      array(
        'hook'        => '__before_inner_footer',
        'id'          => 'footer_widgets_help_block',
        'template'    => 'modules/help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/footer_widgets_help_block' )
      )

    );

    return $children;
  }
}//end of class
