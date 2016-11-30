<?php
class CZR_social_block_model_class extends CZR_Model {
  public $socials;
  public $where         = null;
  public $defaults      = array( 'element_class' => 'socials' );
  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
        $model[ 'socials' ]             = czr_fn_get_social_networks( $output_type = 'array' );
        $model[ 'where' ]               = $this -> czr_fn_get_socials_where( $model );
        $model[ 'element_attributes' ]  = $this -> czr_fn_social_block_get_attributes( $model );
        return parent::czr_fn_extend_params( $model );
  }

  protected function czr_fn_get_socials_where( $model ) {
        return ! is_null( $this -> where ) ? $this-> where : '';
  }

  protected function czr_fn_get_social_block() {
        return array_map( array($this, 'czr_fn_li_wrap' ), $this -> socials );
  }

  protected function czr_fn_social_block_get_attributes( $model ) {
        $where   = $this -> where;
        //the block must be hidden via CSS when
        //1a) the relative display option is unchecked
        //or
        //1b) there are no social icons set
        //and
        //2) customizing
        $_hidden = ( ( $where && 0 == esc_attr( czr_fn_get_opt( "tc_social_in_{$where}" ) ) ) || ! $model['socials']  ) && czr_fn_is_customizing();
        return $_hidden ? 'style="display:none;"' : '';
  }

  /*
  * Helper, wrap social links in <li>
  */
  protected function czr_fn_li_wrap( $el ) {
        return "<li>$el</li>";
  }

}