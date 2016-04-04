<?php
class TC_body_model_class extends TC_Model {
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
    $model[ 'article_wrapper_class' ]         = apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) );
    return $model;
  }


  function tc_setup_children() {
    $children = array(
      /********************************************************************
      * Left sidebar
      ********************************************************************/
      //the model content/sidebar contains the left sidebar content registration
      // array(
      //   'hook'        => '__main_container__',
      //   'id'          => 'left_sidebar',
      //   'template'    => 'modules/widget_area_wrapper',
      //   'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' ),
      //   'priority'    => 10,

      // ),

       array(
        'hook'        => false,
        'id'          => 'left_sidebar',
        'template'    => 'modules/widget_area_wrapper',
        'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' ),
        'priority'    => 10,

      ),
      /********************************************************************
      * Content wrapper : id="content" class="{article container class }"
      ********************************************************************/
      // array(
      //   'hook'        => '__main_container__',
      //   'template'    => 'content/content_wrapper',
      //   'priority'    => 20
      // ),

      array(
        'hook'        => false,
        'template'    => 'content/content_wrapper',
        'priority'    => 20
      ),

      /********************************************************************
      * Right sidebar
      ********************************************************************/
      //the model content/sidebar contains the right sidebar content registration
      // array(
      //   'hook'        => '__main_container__',
      //   'id'          => 'right_sidebar',
      //   'template'    => 'modules/widget_area_wrapper',
      //   'priority'    => 30,
      //   'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' )
      // )

      array(
        'hook'        => false,
        'id'          => 'right_sidebar',
        'template'    => 'modules/widget_area_wrapper',
        'priority'    => 30,
        'model_class' => array( 'parent' => 'modules/widget_area_wrapper', 'name' => 'content/sidebar' )
      ),







      /* OUTSIDE THE LOOP */
      //404
      array(
        'hook'        => false,
        'id' => '404',
        'template'    => 'content/content_404',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/404')
      ),
      //no results
      array(
        'hook'        => false,
        'id'          => 'no_results',
        'template'    => 'content/content_no_results',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/no_results')
      ),

      //Headings: before the loop (for list of posts, like blog, category, archives ...)
      //sub-modules registration inside
      array(
        'hook'        => false,
        'template'    => 'content/headings',
        'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/posts_list_headings'),
        'id'          => 'posts_list_headings'
      ),


      /********************************************************************
      * GENERIC LOOP
      ********************************************************************/
      array(
        'hook'        => false,
        'id'          => 'main_loop',
        'template'    => 'content/loop',
        'priority' => 20
      ),






      /*********************************************
      * GRID (POST LIST)
      *********************************************/
      // array(
      //   'hook'        => 'in_main_loop',
      //   'template'    => 'modules/grid_wrapper',
      //   'priority'    => 10,
      //   'model_class' => array( 'parent' => 'content/article', 'name' => 'modules/grid_wrapper'),
      //   'controller'  => 'post_list_grid'
      // ),
      array(
        'hook'        => false,
        'template'    => 'modules/grid_wrapper',
        'priority'    => 10,
        'controller'  => 'post_list_grid'
      ),
      /* END GRID */

      /*********************************************
      * ALTERNATE POST LIST
      *********************************************/
      /* Contains the alternate post list elements and their submodules registrations */
      // array(
      //   'hook'        => 'in_main_loop',
      //   'template'    => 'content/post_list_wrapper',
      //   'priority'    => 10,
      //   'controller'  => 'post_list',
      //   'model_class' => array( 'parent' => 'content/article', 'name' => 'content/post_list_wrapper' )
      // ),

      array(
        'hook'        => false,
        'template'    => 'content/post_list_wrapper',
        'priority'    => 10,
        'controller'  => 'post_list',
        'model_class' => array( 'parent' => 'content/article', 'name' => 'content/post_list_wrapper' )
      ),

      /*********************************************
      * Singular: PAGE POST ATTACHMENT
      *********************************************/
      /* contains post page attachement content/post-footer registration */
      // array(
      //   'hook'        => 'in_main_loop',
      //   'template'    => 'content/article',
      //   'priority'    => 10,
      //   'id'          => 'singular_article',
      //   'model_class' => array( 'parent' => 'content/article', 'name' => 'content/singular_wrapper' )
      // ),
       array(
        'hook'        => false,
        'template'    => 'content/article',
        'priority'    => 10,
        'id'          => 'singular_article'
      ),




      /*********************************************
      * Comments
      *********************************************/
      /*
      * contains the comment form
      *
      * contains comment list registration
      * the comment list contains the comment and (track|ping)back registration
      */
      array(
        'hook'     => false,
        'template' => 'content/comments',
        'priority' => 20
      ),
      /* end Comments */

      /*********************************************
      * Post navigation
      *********************************************/
      /* contains the post navigation links registration */
      /* in singlar */
      array(
        'hook' => false,
        'template' => 'content/post_navigation',
        'model_class' => array( 'parent' => 'content/post_navigation',
        'name' => 'content/post_navigation_singular' ),
        'id' => 'post_navigation_singular',
        'priority' => 40
      ),
      /* in post lists */
      array(
        'hook' => false,
        'template' => 'content/post_navigation',
        'model_class' => array( 'parent' => 'content/post_navigation', 'name' => 'content/post_navigation_posts' ),
        'id' => 'post_navigation_posts',
        'priority' => 40
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
