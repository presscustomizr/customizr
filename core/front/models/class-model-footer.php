<?php
class CZR_footer_model_class extends CZR_Model {
  public $footer_clph_container_class;

  function __construct( $model = array() ) {
    parent::__construct( $model );

    $children = array(
      //footer_push
      array(
        'id'          => 'footer_push',
        'template'    => 'footer/footer_push',
        'hook'        => '__after_main_container'
      ),
      //horizontal widget area
      array(
        'id'          => 'footer_horizontal_widgets',
        'template'    => 'footer/footer_horizontal_widgets',
        'hook'        => '__before_footer',
        'priority'    => '999',
        'args'        => array(
          'inner_container_class' => 'full' == czr_fn_opt( 'tc_footer_horizontal_widgets' ) ? 'container-fluid' : 'container'
        )
      ),
    );

    $children = apply_filters( 'czr_footer_children_models', $children );
    foreach ( $children as $child_model ) {
        CZR() -> collection -> czr_fn_register( $child_model );
    }//foreach
  }

  /**
  * @override
  * fired before the model properties are parsed in the constructor
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    //footer colophon container layout:
    //two cases:
    //a) wide
    //b) boxed
    if ( 'boxed' == esc_attr( czr_fn_opt( 'tc_footer_colophon_layout' ) ) || 'boxed' == esc_attr( czr_fn_opt( 'tc_site_layout') ) ) {
        $footer_clph_container_class = 'container';

    } else {
        $footer_clph_container_class = 'container-fluid';
    }

    return array_merge( $model, array(
        'footer_clph_container_class' => $footer_clph_container_class
    ) );
  }//_construct

}