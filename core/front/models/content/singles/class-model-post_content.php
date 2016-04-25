<?php
class CZR_cl_post_content_model_class extends CZR_cl_Model {
  public $thumbnail_position;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $icon_class             = in_array( get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' ) ) ? apply_filters( 'tc_post_format_icon', 'format-icon' ) :'' ;

    $model['element_class'] = array( $icon_class );
    $model[ 'thumbnail_position' ] = '__before_content' == CZR_cl_utils_thumbnails::$instance -> czr_get_single_thumbnail_position() ? 'before_title' : '';

    return $model;
  }

  function tc_setup_children() {
    $children = array(

      //singular smartload help block
      array(
        'hook'        => '__before_post_entry_content',
        'template'    => 'modules/help_block',
        'id'          => 'singular_smartload_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/singular_smartload_help_block'),
        'priority'    => 20
      ),
      //single post thumbnail help block
      array(
        'hook'        => '__before_post_content',
        'template'    => 'modules/help_block',
        'id'          => 'post_thumbnail_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/thumbnail_help_block'),
      )
    );
    return $children;
  }

  function czr_get_article_selectors( $model = array() ) {
    return CZR_cl_utils_query::$instance -> czr_get_the_singular_article_selectors( 'row-fluid' );
  }

}
