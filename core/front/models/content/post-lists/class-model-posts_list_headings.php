<?php
class TC_posts_list_headings_model_class extends TC_headings_model_class {
  public $type = 'posts_list';

  function tc_setup_children() {
    $children = array(
      //search results title
      array(
        'id'          => 'posts_list_search_title',
        'model_class' => array( 'parent' => 'content/posts_list_title', 'name' => 'content/post-lists/posts_list_search_title' ),
      )
    );
    return $children;
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_get_class( $model = array() ) {
    global $wp_query;
    $_header_class     = array( 'archive-header' );

    if ( is_404() || $wp_query -> is_posts_page && ! is_front_page() )
      $_header_class   = array( 'entry-header' );
    if ( is_search() && ! is_singular() )
      $_header_class   = array( 'search-header' );

    return $_header_class;
  }
}
