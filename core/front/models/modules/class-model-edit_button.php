<?php
class CZR_edit_button_model_class extends CZR_Model {

  /* @override */
  function __construct( $model = array() ) {
    /* Defaults declaration */
    $this -> defaults = array(
      'edit_button_class' => '',
      'edit_button_title' => __( 'Edit', 'customizr' ),
      'edit_button_text'  => __( 'Edit', 'customizr' ),
      'edit_button_link'  => '#',
    );

    parent::__construct( $model );
  }
}