<?php
class TC_page_content_model_class extends TC_Model {

  function tc_setup_children() {
    $children = array(
      //singular smartload help block
      array(
        'hook'        => '__before_page_entry_content',
        'template'    => 'modules/help_block',
        'id'          => 'singular_smartload_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/singular_smartload_help_block'),
        'priority'    => 20
      )
    );
    return $children;
  }

  function tc_get_article_selectors( $model = array() ) {
    return TC_utils_query::$instance -> tc_get_the_singular_article_selectors( 'row-fluid' );
  }
}
