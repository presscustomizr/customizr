<?php
class TC_article_model_class extends TC_Model {
  public $article_selectors;
  public $post_class = 'row-fluid';
  public $thumbnail_position;

  function tc_extend_params( $model = array() ) {
    $model[ 'thumbnail_position' ] = '__before_content' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ? 'before_title' : '';

    return $model;
  }


  function tc_setup_children() {
    $children = array(
      //single post thumbnail
      array(
        'id'          => 'post_thumbnail',
        'model_class' => 'content/singles/thumbnail_single'
      ),
      array(
        'id'          => 'singular_headings',
        'model_class' => 'content/singles/post_page_headings'
      ),
      //singular smartload help block
      array(
        'hook'        => is_page() ? '__before_page_content' : '__before_post_content',
        'template'    => 'modules/help_block',
        'id'          => 'singular_smartload_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/singular_smartload_help_block'),
        'priority'    => 20
      ),
      //single post thumbnail help block
      array(
        'hook'        => '__before_inner_singular_article',
        'template'    => 'modules/help_block',
        'id'          => 'post_thumbnail_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/thumbnail_help_block'),
      )
    );
    return $children;
  }


  function tc_setup_late_properties() {
    $this -> tc_set_property( 'article_selectors', TC_utils_query::$instance -> tc_get_the_singular_article_selectors( $this -> post_class ) );
  }

}
