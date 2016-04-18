<?php
class TC_content_model_class extends TC_Model {
  public $column_content_class  = array('row', 'column-content-wrapper');
  public $article_wrapper_class;
  public $thumbnail_position;


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    //set this model's properties
    $model[ 'column_content_class' ]  = apply_filters( 'tc_column_content_wrapper_classes' , $this -> column_content_class );
    $model[ 'article_wrapper_class' ] = apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) );

    //thumb position
    $model[ 'thumbnail_position' ] = '__before_main_wrapper' == TC_utils_thumbnails::$instance -> tc_get_single_thumbnail_position() ? 'before_title_full' : '';

    return $model;
  }


  function tc_setup_children() {
    $children = array(

      /********************************************************************
      * Left sidebar
      ********************************************************************/
       array(
        'id'          => 'left_sidebar',
        'model_class' => 'content/sidebars/sidebar',

      ),

      /********************************************************************
      * Right sidebar
      ********************************************************************/
      array(
        'id'          => 'right_sidebar',
        'model_class' => 'content/sidebars/sidebar'
      ),


      /* OUTSIDE THE LOOP */


      //Headings: before the loop (for list of posts, like blog, category, archives ...)
      //sub-modules registration inside
      array(
        'model_class' => 'content/post-lists/posts_list_headings',
        'id'          => 'posts_list_headings'
      ),

      //smartload help block
      array(
        'hook'        => '__before_main_loop',
        'template'    => 'modules/help_block',
        'id'          => 'post_list_smartload_help_block',
        'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/post_list_smartload_help_block'),
      ),

      /*********************************************
      * INSIDE THE LOOP
      *********************************************/


      /*********************************************
      * GRID (POST LIST)
      *********************************************/

      array(
        'id'          => 'post_list_grid',
        'model_class' => 'modules/grid/grid_wrapper',
      ),
      /* END GRID */

      /*********************************************
      * ALTERNATE POST LIST
      *********************************************/

      array(
        'id'          => 'post_list',
        'model_class' => 'content/post-lists/post_list_wrapper'
      ),

      /*********************************************
      * Singular: PAGE POST ATTACHMENT
      *********************************************/
      array(
        'id'          => 'singular_article',
        'model_class' => 'content/singles/article'
      ),

      /*********************************************
      * Post metas
      *********************************************/
      //the default class/template is for the buttons type
      array(
        'model_class' => 'content/post-metas/post_metas',
        'id' => 'post_metas_button',
      ),
      //the text meta one uses a different template
      array(
        'id' => 'post_metas_text',
        'model_class' => array( 'parent' => 'content/post-metas/post_metas', 'name' => 'content/post-metas/post_metas_text' ),
      ),
      //attachment post metas
      array(
        'id' => 'post_metas_attachment',
        'model_class' => array( 'parent' => 'content/post-metas/post_metas', 'name' => 'content/post-metas/attachment_post_metas' )
      ),

      /**************************
      * Comment bubble
      ******************************/
      //comment bubble
      array(
        'model_class' => 'modules/comment_bubble'
      ),

      /*********************************************
      * Post navigation
      *********************************************/
      /* contains the post navigation links registration */
      /* in singlar */
      array(
        'model_class' => array( 'parent' => 'content/navigation/post_navigation',
        'name' => 'content/navigation/post_navigation_singular' ),
        'id' => 'post_navigation_singular',
      ),
      /* in post lists */
      array(
        'model_class' => array( 'parent' => 'content/navigation/post_navigation', 'name' => 'content/navigation/post_navigation_posts' ),
        'id' => 'post_navigation_posts',
      ),
    );

    return $children;
  }


  /**
  * @override
  * parse this model properties for rendering
  */
  function tc_sanitize_model_properties( $model ) {
    parent::tc_sanitize_model_properties( $model );
    foreach ( array( 'column_content', 'article_wrapper' ) as $property )
      $model -> {"{$property}_class"} = $this -> tc_stringify_model_property( "{$property}_class" );
  }



  /*
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_body_class($_classes) {
    //SKIN CLASS
    $_skin = sprintf( 'skin-%s' , basename( TC_init::$instance -> tc_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }

}
