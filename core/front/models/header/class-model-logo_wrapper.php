<?php
class TC_logo_wrapper_model_class extends TC_Model {
  public $class;
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
    $model[ 'class' ]              = apply_filters( 'tc_logo_class', $this -> get_logo_wrapper_class(), $model );
    $model[ 'link_class' ]         = array( 'site-logo' );
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
    $_class[]   = 'right' == $_layout ? 'pull-right' : 'pull-left';
    return $_class;
  }

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */ 
  function tc_maybe_filter_views_model() {
    parent::tc_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header', array( $this, 'pre_rendering_view_header_cb' ) );
  }
  /**
  * parse this model properties for rendering
  */ 
  function pre_rendering_my_view_cb( $model ) {
    if ( is_array( $model -> class ) )
      $model -> class      = join( ' ', array_unique( $model -> class ) );
    $model -> link_class = join( ' ', array_unique( $model -> link_class ) );    
  }

  /**
  * parse header model before rendering to add 'sticky' logo wrapper visibility
  * and shrinking classes
  */ 
  function pre_rendering_view_header_cb( $header_model ) {
    if ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") || TC___::$instance -> tc_is_customizing() ) )
      array_push( $header_model -> class, 
          0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_shrink_title_logo') ) ? ' tc-shrink-on' : ' tc-shrink-off',
          0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') ) ? 'tc-title-logo-on' : 'tc-title-logo-off'

      );
  } 
}
