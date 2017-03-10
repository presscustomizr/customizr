<?php
class CZR_content_model_class extends CZR_Model {

  function czr_fn_setup_children() {
    $children = array(
      //registered here as they act on the body class
      array(
        //registered here also because we access to its properties from other templates
        //which as of now is only possibile with already registered models
        'model_class' => 'content/post-metas/post_metas',
        'id' => 'post_metas',
      ),

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
  * Callback of body_class hook
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_body_class($_classes) {
    //SKIN CLASS
    $_skin = sprintf( 'skin-%s' , basename( CZR_init::$instance -> czr_fn_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }

  /*
  * @override
  */
  function czr_fn_get_content_to_render() {
    //fallback
    $model = array( 'loop_item' => array('content/singular/page_content' ) );

    if ( czr_fn_is_list_of_posts() ) {

      $model = array( 'loop_item' => array( 'modules/grid/grid_wrapper', array( 'model_id' => 'post_list_grid' ) ) );

      if ( czr_fn_has('post_list') ) {
        $model = array( 'loop_item' => array('content/post-lists/post_list_alternate' ));
      }elseif ( czr_fn_has('post_list_plain') ) {
        $model = array( 'loop_item' => array('content/post-lists/post_list_plain' ));
      }
    } else {

      if( is_single() )
        $model = array( 'loop_item' => array('content/singular/post_content' ));

    }

    return $model;
  }

}