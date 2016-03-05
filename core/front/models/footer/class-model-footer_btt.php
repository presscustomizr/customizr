<?php
class TC_footer_btt_model_class extends TC_Model {
  public $link_class;
  public $text;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]             = array( 'backtop', is_rtl() ? 'pull-left' : 'pull-right' );
    $model[ 'link_class' ]                = array( 'back-to-top' );
    $model[ 'text' ]                      = __( 'Back to top', 'customizr' );

    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    $model -> link_class = $this -> tc_stringify_model_property( 'link_class' );
  }
}//end of class
