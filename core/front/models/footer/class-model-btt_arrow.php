<?php
class CZR_btt_arrow_model_class extends CZR_Model {

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    //set direction class
    $direction = esc_attr( czr_fn_opt( 'tc_back_to_top_position' ) );

    $dir_opposites = array(
      'left'  => 'right',
      'right' => 'left'
    );

    if ( ! array_key_exists( $direction, $dir_opposites ) )
      $direction = 'right';

    $_preset = array (
      'element_class'         => is_rtl() ? $dir_opposites[$direction] : $direction
    );

    return $_preset;
  }

}//end of class