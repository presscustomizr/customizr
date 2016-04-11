<?php
class TC_colophon_model_class extends TC_Model {
  function tc_setup_children() {
    $children = array(
      //footer social
      array( 'model_class' => array( 'parent' => 'modules/social_block', 'name' => 'footer/footer_social_block' ), 'id' => 'footer_social_block', 'controller' => 'social_block' )
    );
    return $children;
  }
}//end of class
