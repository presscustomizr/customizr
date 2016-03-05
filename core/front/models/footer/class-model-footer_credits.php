<?php
class TC_footer_credits_model_class extends TC_Model {
  public $copyright_text;
  public $copyright_link_href;
  public $copyright_link_text_title;
  public $wp_powered_text;
  public $wp_powered_link_title;


  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    $model[ 'element_class' ]             = array( 'credits' );
    $model[ 'copyright_text' ]            = esc_attr( date( 'Y' ) );
    $model[ 'copyright_link_href' ]       = esc_url( home_url() );
    $model[ 'copyright_link_text_title' ] = esc_attr( get_bloginfo() );
    $model[ 'wp_powered_text' ]           = __('Powered by', 'customizr');
    $model[ 'wp_powered_link_title' ]     = __('Powered by Wordpress', 'customizr');

    return $model;
  }

  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    $model -> element_class = join( ' ', $model -> element_class );    
  }
}//end of class
