<?php
class TC_post_navigation_posts_model_class extends TC_post_navigation_model_class {


  function tc_setup_children() {
    $children = array(
      //posts links
      array(
        'hook' => 'post_navigation_posts',
        'template' => 'content/post_navigation_links',
        'model_class' => array( 'parent' => 'content/post_navigation_links', 'name' => 'content/post_navigation_links_posts'),
        'id' => 'post_navigation_links_posts'
      ),
    );
    return $children;
  }

  /*
  * @override
  */
  function tc_get_context() {
    if ( is_home() && 'posts' == get_option('show_on_front') )
      return 'home';
    if ( !is_404() && !tc__f( '__is_home_empty') )
      return 'archive';

    return false;
  }
}
