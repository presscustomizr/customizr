<?php
class CZR_social_block_model_class extends CZR_Model {
  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'socials' ] = array_map( array($this, 'czr_fn_li_wrap' ), czr_fn_get_social_networks( $output_type = 'array' ) );
    return parent::czr_fn_extend_params( $model );
  }

  /*
  * Helper, wrap social links in <li>
  */
  protected function czr_fn_li_wrap( $el ) {
    return "<li>$el</li>";
  }
}