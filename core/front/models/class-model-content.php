<?php
class CZR_cl_content_model_class extends CZR_cl_Model {

  function czr_fn_setup_children() {
    $children = array(
      //registered here as they act on the body class
      array(
        'model_class' => 'content/post-metas/post_metas',
        'id' => 'post_metas',
      ),
      array(
        'model_class' => 'content/post-lists/post_list_wrapper',
        'id' => 'post_list',
      ),
      array(
        'model_class' => 'content/post-lists/post_list_masonry_wrapper',
        'id' => 'post_list_masonry',
      ),

      //sidebars need to be registered because they use the same model which does not match the model id nor the template
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
    $_skin = sprintf( 'skin-%s' , basename( CZR_cl_init::$instance -> czr_fn_get_style_src() ) );
    array_push( $_classes, substr( $_skin , 0 , strpos($_skin, '.') ) );

    return $_classes;
  }

}