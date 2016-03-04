<?php
class TC_footer_btt_model_class extends TC_Model {
  public $class;
  public $link_class;
  public $text;


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]             = apply_filters( 'tc_colophon_right_block_class', array( 'span3', 'backtop' ) );
    $model[ 'class' ]                     = array( is_rtl() ? 'pull-left' : 'pull-right' );
    $model[ 'link_class' ]                = array( 'back-to-top' );
    $model[ 'text' ]                      = __( 'Back to top', 'customizr' );

    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    $model -> class         = join( ' ', $model -> class );    
    $model -> element_class = join( ' ', $model -> element_class );    
    $model -> link_class    = join( ' ', $model -> link_class );    
  }
}//end of class
