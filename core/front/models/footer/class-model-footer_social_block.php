<?php
class TC_footer_social_block_model_class extends TC_social_block_model_class {
  public $element_tag      = 'span';
  public $element_class    = array('tc-footer-social-links-wrapper');
  public $where            = 'footer';
  public $wrapper_class    = array( 'span3', 'social-block');

  function __construct( $model = array() ) {
    parent::__construct( $model );
    
    add_action( "before_render_view_{$this -> id}", array( $this, 'tc_wrapper_start'), 999 );
    add_action( "after_render_view_{$this -> id}", array( $this, 'tc_wrapper_end') , 999 );
  }

  function tc_wrapper_start() {
    printf( '<div class="%1$s">', $this -> wrapper_class );
  }
  function tc_wrapper_end() {
    echo '</div>';  
  }
  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model                    = parent::tc_extend_params( $model );
    $model[ 'wrapper_class' ] = apply_filters( 'tc_colophon_left_block_class', array_merge( $this -> wrapper_class,  array( is_rtl() ? 'pull-right' : 'pull-left' ) ) );
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
