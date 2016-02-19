<?php
class TC_logo_wrapper_model_class extends TC_Model {
  public $logo_wrapper_class;
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
    $_class                        = $this -> get_logo_wrapper_class();  
    $model[ 'logo_wrapper_class' ] = implode( ' ', apply_filters( 'tc_logo_class', $_class, $model ) );
    $model[ 'link_class' ]         = 'site-logo';
    $model[ 'link_title' ]         = apply_filters( 'tc_site_title_link_title', sprintf( '%1$s | %2$s' ,
                                             __( esc_attr( get_bloginfo( 'name' ) ) ), 
                                             __( esc_attr( get_bloginfo( 'description' ) ) )
                                         ),
                                         $model
                                     );
    $model[ 'link_url'   ]         = apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ), $model );

    return $model;
  }

  /* the same in the title wrapper class, some kind of unification will be needed IMHO */
  function get_logo_wrapper_class() {
    $_class     = array( 'brand', 'span3' );  
    $_layout    = esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') );
    $_class[] = 'right' == $_layout ? 'pull-right' : 'pull-left';
    return $_class;
  }
}
