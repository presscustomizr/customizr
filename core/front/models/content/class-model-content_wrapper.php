<?php
class TC_content_wrapper_model_class extends TC_Model {
  public $element_class;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]          = apply_filters( 'tc_article_container_class' , array( TC_utils::tc_get_layout( TC_utils::tc_id() , 'class' ) , 'article-container' ) );
    return $model;
  }

  function tc_setup_children() {
    $children = array(  /* OUTSIDE THE LOOP */
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
        'template'    => 'loop',
        'priority' => 20
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

  }//setup_children

}
