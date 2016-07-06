<?php
class CZR_cl_social_block_model_class extends CZR_cl_Model {
  public $social_block;
  public $element_class = array();
  public $element_tag   = 'div';
  public $where         = null;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
        $model[ 'social_block' ]        = CZR_cl_utils::$inst -> czr_fn_get_social_networks();
        $model[ 'element_class' ]       = $this -> czr_fn_social_block_get_class( $model );
        $model[ 'where' ]               = $this -> czr_fn_get_socials_where( $model );
        $model[ 'element_attributes' ]  = $this -> czr_fn_social_block_get_attributes( $model );
        $model[ 'social_block' ]        = $this -> czr_fn_get_before_socials() . $model[ 'social_block' ] . $this -> czr_fn_get_after_socials();
        return $model;
  }

  protected function czr_fn_get_socials_where( $model ) {
        return ! is_null( $this -> where ) ? $this-> where : '';
  }
  protected function czr_fn_get_before_socials() {
        return '';
  }

  protected function czr_fn_get_after_socials() {
        return '';
  }

  protected function czr_fn_social_block_get_class( $model ) {
        return apply_filters( "czr_{$this -> where}_block_social_class", $this -> element_class, $model );
  }

  protected function czr_fn_social_block_get_attributes( $model ) {
        $where   = $this -> where;
        //the block must be hidden via CSS when
        //1a) the relative display option is unchecked
        //or
        //1b) there are no social icons set
        //and
        //2) customizing
        $_hidden = ( ( $where && 0 == esc_attr( czr_fn_get_opt( "tc_social_in_{$where}" ) ) ) || ! $model['social_block']  ) && CZR() -> czr_fn_is_customizing();
        return $_hidden ? 'style="display:none;"' : '';
  }

}
