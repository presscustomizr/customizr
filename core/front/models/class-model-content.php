<?php
class TC_content_model_class extends TC_Model {
  public $element_attributes;
  public $column_content_class  = array('row', 'column-content-wrapper');
  public $article_wrapper_class;


  /* TODO: SHOULD FIND A BETTER WAY TO EXTEND THE MODEL PARAMS/PROPERTIES
   *  for example, the body_class filter should be accessible to all models instances
   *  so that they can actually filter them.
   *  We might do something like:
   *  1) tc_extend_params to extend "early" params
   *  2) another method to extend the model fired just before the view is instantiated/rendered
  */

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    //set this model's properties
    $model[ 'element_attributes' ]    = apply_filters('tc_body_attributes' , 'itemscope itemtype="http://schema.org/WebPage"');
    $model[ 'column_content_class' ]  = apply_filters( 'tc_column_content_wrapper_classes' , $this -> column_content_class );
    $model[ 'article_wrapper_class' ] = apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) );
    return $model;
  }


  function tc_setup_children() {
    $children = array(
      /********************************************************************
      * Left sidebar
      ********************************************************************/
       array(
        'id'          => 'left_sidebar',
        'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebars/sidebar' ),

      ),

      /********************************************************************
      * Right sidebar
      ********************************************************************/
      array(
        'id'          => 'right_sidebar',
        'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebars/sidebar' )
      ),



      /* OUTSIDE THE LOOP */
      //404
      array(
        'id' => '404',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/singles/404')
      ),
      //no results
      array(
        'id'          => 'no_results',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/singles/no_results')
      ),

      //Headings: before the loop (for list of posts, like blog, category, archives ...)
      //sub-modules registration inside
      array(
        'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post-lists/posts_list_headings'),
        'id'          => 'posts_list_headings'
      ),



      /*********************************************
      * INSIDE THE LOOP
      *********************************************/


      /*********************************************
      * GRID (POST LIST)
      *********************************************/

      // array(
      //   'hook'        => false,
      //   'template'    => 'modules/grid_wrapper',
      //   'priority'    => 10,
      //   'controller'  => 'post_list_grid'
      // ),
      /* END GRID */

      /*********************************************
      * ALTERNATE POST LIST
      *********************************************/

      array(
        'id'          => 'post_list',
        //'controller'  => 'post_list',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/post-lists/post_list_wrapper' )
      ),

      /*********************************************
      * Singular: PAGE POST ATTACHMENT
      *********************************************/
      array(
        'id'          => 'singular_article',
        'model_class' => 'content/article'
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
    $model -> column_content_class = $this -> tc_stringify_model_property( 'column_content_class' );
    $model -> article_wrapper_class = $this -> tc_stringify_model_property( 'article_wrapper_class' );
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
