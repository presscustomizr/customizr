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
    );
    return $children;
  }
}
