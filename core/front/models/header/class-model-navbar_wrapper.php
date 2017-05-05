<?php
class CZR_navbar_wrapper_model_class extends CZR_Model {

  /*
  * @override
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class' ] = isset( $model[ 'element_class' ] ) && is_array( $model[ 'element_class' ] ) ? $model[ 'element_class' ] : array();

    if ( ! wp_is_mobile() && 0 != esc_attr( czr_fn_get_opt( 'tc_menu_submenu_fade_effect') ) )
      array_push( $model[ 'element_class' ], 'czr-submenu-fade' );
    if ( 0 != esc_attr( czr_fn_get_opt( 'tc_menu_submenu_item_move_effect') ) )
      array_push( $model[ 'element_class' ], 'czr-submenu-move' );

    return $model;
  }
/*
  function czr_fn_get_navbar_menu_id() {
    czr_fn_is_possible( 'navbar_secondary_menu' )
  }*/
}