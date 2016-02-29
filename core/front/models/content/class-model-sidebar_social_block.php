<?php
class TC_sidebar_social_block_model_class extends TC_social_block_model_class {
  public $tag        = 'aside';

  /*
  * @override
  */  
  function tc_get_socials_where( $model ) {
    $this -> where = strpos( 'right', $model['hook'] ) > 0 ? 'right' : 'left';
    parent::tc_get_socials_where( $model );
  }

  /*
  * @override
  */  
  function tc_get_before_socials() {
    $_title = esc_attr( TC_utils::$inst->tc_opt( 'tc_social_in_sidebar_title') );
    return ! $_title ? '' : apply_filters( 'tc_sidebar_socials_title' , sprintf( '<h3 class="widget-title">%1$s</h3>', $_title ) );
  }

  /*
  * @override
  */  
  function tc_social_block_get_class( $model ) {
    return apply_filters( 'tc_sidebar_block_social_class' , array('social-block', 'widget', 'widget_social'), $model );
  }
}
