<?php
class TC_post_page_headings_model_class extends TC_Model {
  public $thumbnail_position;

  function tc_extend_params( $model = array() ) {
    $model[ 'thumbnail_position' ] = '__after_content_title' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ? 'after_title' : '';

    return $model;
  }

  function tc_setup_children() {
    $children = array (
      array(
        'id' => 'post_page_title',
        'model_class' => 'content/singles/post_page_title'
      ),
      //comment bubble
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/comment_bubble'
      ),
      //edit post links
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/edit_button',
        'priority'  => 20
      ),
      //recently updated
      array(
        'hook'      => '__after_inner_post_page_title__',
        'template'  => 'modules/recently_updated',
        'priority'  => 30
      )
    );
    return $children;

  }


}
