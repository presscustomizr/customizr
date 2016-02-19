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
    $model[ 'logo_wrapper_class' ] = implode( ' ', apply_filters( 'tc_logo_class', array( 'brand', 'span3', 'pull-left'), $model ) );
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
}
