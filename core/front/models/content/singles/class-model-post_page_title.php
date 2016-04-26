<?php
class CZR_cl_post_page_title_model_class extends CZR_cl_Model {
  public $context;

  /*
  * @override
  */
  function czr_fn_maybe_render_this_model_view() {
    return $this -> visibility && czr_fn_post_has_title();
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $this -> context            = $this -> czr_fn_get_the_post_page_context();
    $model['element_class']     = apply_filters( 'czr_content_title_icon', $this -> czr_fn_get_post_page_title_class( 'entry-title' ) );
    return $model;
  }

  function czr_fn_get_the_post_page_context() {
    if ( ! is_singular() )
      return 'post_list';
    if ( is_page() )
      return 'page';

    return 'post';
  }

  function czr_fn_get_post_page_title_class( $class ) {
    $new_class = ( esc_attr( CZR_cl_utils::$inst->czr_fn_opt( "tc_show_{$this -> context}_title_icon" ) )
          && esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_title_icon' ) ) ) ? array( 'format-icon' ) : array();
    array_push( $new_class, $class );
    return $new_class;
  }
}
