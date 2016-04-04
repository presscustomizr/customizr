<?php
class TC_posts_list_headings_model_class extends TC_headings_model_class {
  public $type = 'posts_list';

  function tc_setup_children() {
    $children = array(
      //standard archive titles
      array(
        'hook'        => '__headings_posts_list__',
        'template'    => 'content/posts_list_title',
        'priority'    => 10
      ),
      //search results title
      array(
        'hook'        => '__headings_posts_list__',
        'id'          => 'posts_list_search_title',
        'template'    => 'content/posts_list_title',
        'model_class' => array( 'parent' => 'content/posts_list_title', 'name' => 'content/posts_list_search_title' ),
        'priority'    => 10,
      ),
      //description
      array(
        'hook'        => '__headings_posts_list__',
        'template'    => 'content/posts_list_description',
        'priority'    => 20
      ),
      //author description
      array(
        'hook'        => '__headings_posts_list__',
        'id'          => 'author_description',
        'template'    => 'content/author_info',
        'priority'    => 20
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
