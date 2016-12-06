<?php
class CZR_loop_model_class extends CZR_Model {
  public $reset_query        = false;
  public $register_loop_item = true;

  public $loop_item_template, $loop_item_args;

  /*
  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_setup_late_properties() {
    $this -> czr_fn_maybe_setup_query();
    $this -> czr_fn_setup_loop_item();
  }

  function czr_fn_setup_loop_item( $_t = '', $_args = array() ) {

    if ( ! empty( $this -> loop_item ) )
      $_t = is_string( $this->loop_item[0] ) ? $this->loop_item[0] : $_t;

    if ( ! empty( $this -> loop_item ) )
      $_args = is_array( $this->loop_item[1] ) ? $this->loop_item[1] : $_args;

    $_model_id    =  ! empty( $_args['model_id'] ) ? $_args['model_id'] : basename($_t);
    $_model_class =  ! empty( $_args['model_class'] ) ? $_args['model_class'] : '';
    $_model_args  =  ! empty( $_args['model_args'] )  ? $_args['model_args']  : array();


    if ( ! empty ( $this -> register_loop_item ) ) {
      $_args[ 'model_id' ] = czr_fn_register(
        array( 'id' => $_model_id, 'template' => $_t, 'model_class' => $_model_class, 'args' => $_model_args )
      );
    }

    $this -> czr_fn_update( array(
        'loop_item_template' => $_t,
        'loop_item_args'     => $_args
    ) );
  }

  /*
  * Fired just before the view is rendered
  * @hook: post_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_reset_late_properties() {
    $this -> czr_fn_maybe_reset_query();
  }


  function czr_fn_maybe_setup_query() {
    if ( ! $this -> query )
      return;
    global $wp_query;

    $wp_query = new WP_Query( $this -> query );
  }

  function czr_fn_maybe_reset_query() {
    if ( ! $this -> query )
      return;

    if ( ! $this -> reset_query )
      return;

    wp_reset_query();
    wp_reset_postdata();
  }

}