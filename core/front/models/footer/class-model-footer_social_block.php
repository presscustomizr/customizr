<?php
class TC_footer_social_block_model_class extends TC_social_block_model_class {
  public $tag            = 'span';
  public $class          = array('tc-footer-social-links-wrapper');
  public $where          = 'footer';
  public $wrapper_class;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model                    = parent::tc_extend_params( $model );
    $model[ 'wrapper_class' ] = apply_filters( 'tc_colophon_left_block_class', array( 'span3', 'social-block',  is_rtl() ? 'pull-right' : 'pull-left' ) );
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    if ( is_array( $model -> wrapper_class ) )
      $model -> wrapper_class = join( ' ', array_unique( $model -> wrapper_class ) );
  }
}
