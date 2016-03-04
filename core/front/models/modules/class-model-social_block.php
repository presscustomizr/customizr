<?php
class TC_social_block_model_class extends TC_Model {
  public $content;
  public $element_class = array('social-block');
  public $controller    = 'social_block';

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'content' ]             = TC_utils::$inst -> tc_get_social_networks();
    $model[ 'element_class' ]       = $this -> tc_social_block_get_class( $model );
    $model[ 'where' ]               = $this -> tc_get_socials_where( $model );
    $model[ 'element_attributes' ]  = $this -> tc_social_block_get_attributes( $model );
    $model[ 'content' ]             = $this -> tc_get_before_socials() . $model[ 'content' ] . $this -> tc_get_after_socials();
    return $model;
  }

  protected function tc_get_socials_where( $model ) {
    return isset( $this -> where ) ? $this-> where : '';    
  }
  protected function tc_get_before_socials() {
    return '';
  }

  protected function tc_get_after_socials() {
    return '';  
  }

  protected function tc_social_block_get_class( $model ) {
    return apply_filters( "tc_social_{$this -> where}_block_class", $this -> element_class, $model );
  }

  protected function tc_social_block_get_attributes( $model ) {
    $where   = $this -> where;
    //the block must be hidden via CSS when
    //1a) the relative display option is unchecked
    //or
    //1b) there are no social icons set
    //and
    //2) customizing 
    $_hidden = ( ( $where && 0 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_{$where}" ) ) ) || ! $model['content']  ) && TC___::$instance -> tc_is_customizing();
    return $_hidden ? 'style="display:none;"' : '';
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> element_class ) )
      $model -> element_class = join( ' ', array_unique( $model -> element_class ) );
  }
}
