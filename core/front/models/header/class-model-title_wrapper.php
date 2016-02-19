<?php
class TC_title_wrapper_model_class extends TC_Model {
  public $title_wrapper_class;
  public $tag;
  public $link_class;
  public $link_title;
  public $link_url;

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'title_wrapper_class' ] = 'brand span3 pull-left';
    $model[ 'tag'        ]          = 'h1';
    $model[ 'link_class' ]          = 'site-title';
    $model[ 'link_title' ]          = sprintf( '%1$s | %2$s' , __( esc_attr( get_bloginfo( 'name' ) ) ) , __( esc_attr( get_bloginfo( 'description' ) ) ) ) ;
    $model[ 'link_url'   ]          = esc_url( home_url( '/' ) );

    return $model;
  }
}
