<?php
class CZR_cl_content_model_class extends CZR_cl_Model {

  function czr_fn_setup_children() {
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
        'model_class' => array( 'parent' => 'modules/help_block',
          'name' => 'modules/post_list_smartload_help_block'),
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
      * Singular: PAGE POST ATTACHMENT HEADINGS
      *********************************************/
      array(
        'id'          => 'singular_headings',
        'model_class' => 'content/singles/post_page_headings'
      ),
      //single post thumbnail
      array(
        'id'          => 'post_thumbnail',
        'model_class' => 'content/singles/thumbnail_single'
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
        'model_class' => array( 'parent' => 'content/post-metas/post_metas',
          'name' => 'content/post-metas/post_metas_text' ),
      ),
      //attachment post metas
      array(
        'id' => 'post_metas_attachment',
        'model_class' => array( 'parent' => 'content/post-metas/post_metas',
          'name' => 'content/post-metas/attachment_post_metas' )
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
        'model_class' => array( 'parent' => 'content/navigation/post_navigation',
          'name' => 'content/navigation/post_navigation_posts' ),
        'id' => 'post_navigation_posts',
      ),
    );

    return $children;
  }


  /**
  * @override
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
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
  function czr_fn_body_class($_classes) {
    //SKIN CLASS
    $_skin = sprintf( 'skin-%s' , basename( CZR_cl_init::$instance -> czr_fn_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }

}
