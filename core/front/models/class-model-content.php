<?php
class CZR_content_model_class extends CZR_Model {

  function czr_fn_setup_children() {
    $children = array(

      array(
        //registered here also because we access to its properties from other templates
        //which as of now is only possibile with already registered models
        'model_class' => 'content/post-metas/post_metas',
        'id' => 'post_metas',
      ),

      /*********************************************
      * SLIDER
      *********************************************/
      /* Need to be registered before rendering because of the custom style*/
      array(
        'model_class' => 'modules/slider/slider',
        'id'          => 'main_slider'
      ),
      //slider of posts
      array(
        'id'          => 'main_posts_slider',
        'model_class' => array( 'parent' => 'modules/slider/slider', 'name' => 'modules/slider/slider_of_posts' )
      ),
      /** end slider **/

      /* Needs to access the czr_user_options_style */
      /*********************************************
      * GRID (POST LIST)
      *********************************************/
      array(
        'id'          => 'post_list_grid',
        'model_class' => 'modules/grid/grid_wrapper',
      ),
      /* END GRID */

    );

    return $children;
  }


  /*
  * @override
  */
  function czr_fn_get_content_to_render() {
    //fallback
    $to_render = array( 'loop_item' => array('content/singular/page_content' ) );

    if ( czr_fn_is_list_of_posts() ) {

      $to_render = array( 'loop_item' => array( 'modules/grid/grid_wrapper', array( 'model_id' => 'post_list_grid' ) ) );

      if ( czr_fn_has('post_list') ) {
        $to_render = array( 'loop_item' => array('content/post-lists/post_list_alternate' ));
      }elseif ( czr_fn_has('post_list_plain') ) {
        $to_render = array( 'loop_item' => array('content/post-lists/post_list_plain' ));
      }
    }
    elseif( is_single() ) {
      $to_render = array( 'loop_item' => array('content/singular/post_content' ));
    }

    return $to_render;
  }

}