<?php
class TC_main_wrapper_model_class extends TC_Model {
  public $column_content_class  = array('row', 'column-content-wrapper');

  function tc_extend_params( $model = array() ) {
    $model[ 'column_content_class' ]      = apply_filters( 'tc_column_content_wrapper_classes' , $this -> column_content_class );
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
      )
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
  }
}
