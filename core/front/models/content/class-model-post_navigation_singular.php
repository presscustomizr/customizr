<?php
class TC_post_navigation_singular_model_class extends TC_post_navigation_model_class {

  function tc_setup_children() {
    $children = array(
      //singular links'
      array(
        'hook' => 'post_navigation_singular',
        'template' => 'content/post_navigation_links',
        'model_class' => array( 'parent' => 'content/post_navigation_links', 'name' => 'content/post_navigation_links_singular'),
        'id' => 'post_navigation_links_singular'
      ),
    );
    return $children;
  }


  /*
  * @override
  */
  function tc_get_context() {
    if ( is_page() )
      return 'page';
    if ( is_single() && ! is_attachment() )
      return 'single'; // exclude attachments
    return false;
  }
}
