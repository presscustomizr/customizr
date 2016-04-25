<?php
class CZR_cl_posts_list_headings_model_class extends CZR_cl_Model {

  function tc_setup_children() {
    $children = array(
      //search results title
      array(
        'id'          => 'posts_list_search_title',
        'model_class' => array( 'parent' => 'content/post-lists/posts_list_title', 'name' => 'content/post-lists/posts_list_search_title' ),
      )
    );
    return $children;
  }

  /**
  * @override
  * fired before the model properties are parsed
  */
  function tc_extend_params( $model = array() ) {
    $model['element_class']     = $this -> czr_get_the_element_class();

    return $model;
  }

  function czr_get_the_element_class() {
    global $wp_query;
    $_header_class     = array( 'archive-header' );
    if ( is_404() || $wp_query -> is_posts_page && ! is_front_page() )
      $_header_class   = array( 'entry-header' );
    if ( is_search() && ! is_singular() )
      $_header_class   = array( 'search-header' );
    return $_header_class;
  }

}
