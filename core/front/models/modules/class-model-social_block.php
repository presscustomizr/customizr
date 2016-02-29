<?php
class TC_social_block_model_class extends TC_Model {
  public $content;
  public $tag        = 'div';
  public $class      = array('social-block');
  public $attributes = '';
  public $where      = 'header' ;

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'content' ]     = tc__f( '__get_socials' );
    $model[ 'class' ]       = $this -> tc_social_block_get_class( $model );
    $model[ 'attributes' ]  = $this -> tc_social_block_get_attributes();

    return $model;
  }

  protected function tc_social_block_get_class( $model ) {
    return apply_filters( "tc_social_{$this -> where}_block_class", $this -> class, $model );
  }

  protected function tc_social_block_get_attributes() {
    $where   = $this -> where;  
    //the block must be hidden via CSS when
    //1a) the relative display option is unchecked
    //or
    //1b) there are no social icons set
    //and
    //2) customizing 
    $_hidden = ( ( $where && 0 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_{$where}" ) ) ) || ! $this -> content  ) && TC___::$instance -> tc_is_customizing();
    return $_hidden ? 'style="display:none;"' : $this -> attributes;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class = join( ' ', array_unique( $model -> class ) );
  }
}

  /*
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
   */
/*
  function tc_extend_params( $model = array() ) {
    $params                = isset( $model['params'] ) ? $model['params'] : array();  
    $type                  = isset( $params['type'] )  ? $params['type']  : '';
    $where                 = isset( $params['where'] ) ? $params['where'] : '';

    $model[ 'content' ]    = tc__f( '__get_socials' );
    switch ( $type )       {
     case 'widget'           : $model[ 'tag' ] = 'aside'; break;
     case 'colophon'         : $model[ 'tag' ] = 'span'; break;
     default                 : $model[ 'tag' ] = 'div';
    }

    //the block must be hidden via CSS when
    //1a) the relative display option is unchecked
    //or
    //1b) there are no social icons set
    //and
    //2) customizing 
    $_hidden = ( ( $where && 0 == esc_attr( TC_utils::$inst->tc_opt( "tc_social_in_{$where}" ) ) ) || ! $model[ 'content' ]  ) && TC___::$instance -> tc_is_customizing();
    $model[ 'attributes' ] = $_hidden ? 'style="display:none;"' : '';

    //build class
    $model[ 'class' ] = array( 'social-block' );
    switch ( $where ) {
      case 'header' : 
        $model[ 'class' ] = implode( ' ', apply_filters( 'tc_social_header_block_class', array_merge( $model[ 'class' ], array( 'span5' ) ), $model ) );
        break;
      case 'right-sidebar':
      case 'left-sidebar' : 
        $model[ 'class' ] = implode( ' ', apply_filters( 'tc_sidebar_social_block_class', array_merge( $model[ 'class' ], array( 'widget', 'widget_social' ) ) ), $model );
        break;
      case 'footer' :
        $model[ 'class' ] = implode( ' ', array( 'tc-footer-social-links-wrapper' ) );
        break;
      default:
        $model[ 'class' ] = implode( ' ', apply_filters( "tc_social_${where}_block_class", $model['class'], $model ) ) ; 
    }

    return $model;
  }
}*/
