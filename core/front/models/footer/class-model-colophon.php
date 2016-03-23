<?php
class TC_colophon_model_class extends TC_colophon_base_model_class {
  public $col_1_class;
  public $col_2_class;
  public $col_3_class;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model                  = parent::tc_extend_params( $model );
    $model[ 'col_1_class' ] = apply_filters( 'tc_colophon_left_block_class', array( 'span3', is_rtl() ? 'pull-right' : 'pull-left' ) );
    $model[ 'col_2_class' ] = apply_filters( 'tc_colophon_center_block_class', array( 'span6' ) );
    $model[ 'col_3_class' ] = apply_filters( 'tc_colophon_right_block_class', array( 'span3' ) );
    
    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );  
    for ( $i = 1; $i<4; $i++ )
      $model -> {"col_{$i}_class"} = $this -> tc_stringify_model_property( "col_{$i}_class" );
  }
}//end of class
