<?php
class CZR_social_block_model_class extends CZR_Model {
  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $_socials = czr_fn_get_social_networks( $output_type = 'array' );
    if ( ! empty( $_socials  ) )
      $model[ 'socials' ] = array_map( 'czr_fn_li_wrap', $_socials );

    return parent::czr_fn_extend_params( $model );
  }

}