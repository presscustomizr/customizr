<?php
class CZR_cl_logo_wrapper_model_class extends CZR_cl_Model {
  public $link_title;
  public $link_url;


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class' ]      = apply_filters( 'czr_logo_class', $this -> get_logo_wrapper_class(), $model );
    $model[ 'link_title' ]         = apply_filters( 'czr_site_title_link_title', sprintf( '%1$s | %2$s' ,
                                             __( esc_attr( get_bloginfo( 'name' ) ) ),
                                             __( esc_attr( get_bloginfo( 'description' ) ) )
                                         ),
                                         $model
                                     );
    $model[ 'link_url'   ]         = apply_filters( 'czr_logo_link_url', esc_url( home_url( '/' ) ), $model );

    return $model;
  }

  /* the same in the title wrapper class, some kind of unification will be needed IMHO */
  function get_logo_wrapper_class() {
    $_class     = array( 'brand', 'span3' );
    $_layout    = esc_attr( czr_fn_get_opt( 'tc_header_layout') );
    $_class[]   = 'right' == $_layout ? 'pull-right' : 'pull-left';
    return $_class;
  }

  /**
  * @override
  * Allow filtering of the header class by registering to its pre view rendering hook
  */
  function czr_fn_maybe_filter_views_model() {
    parent::czr_fn_maybe_filter_views_model();
    add_action( 'pre_rendering_view_header', array( $this, 'pre_rendering_view_header_cb' ) );
  }


  /**
  * parse header model before rendering to add 'sticky' logo wrapper visibility
  * and shrinking classes
  */
  function pre_rendering_view_header_cb( $header_model ) {
    if ( esc_attr( czr_fn_get_opt( "tc_sticky_header") || CZR() -> czr_fn_is_customizing() ) )
      array_push( $header_model -> element_class,
          0 != esc_attr( czr_fn_get_opt( 'tc_sticky_shrink_title_logo') ) ? ' tc-shrink-on' : ' tc-shrink-off',
          0 != esc_attr( czr_fn_get_opt( 'tc_sticky_show_title_logo') ) ? 'tc-title-logo-on' : 'tc-title-logo-off'

      );
  }
}
