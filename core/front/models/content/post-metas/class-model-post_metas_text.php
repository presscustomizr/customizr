<?php
class CZR_cl_post_metas_text_model_class extends CZR_cl_post_metas_model_class {
  public $type = 'post_metas_text';

  /*  @override */
  public function czr_get_publication_date() {
    return 0 != esc_attr( CZR_cl_utils::$inst->czr_opt( 'tc_show_post_metas_publication_date' ) ) ? $this -> czr_get_meta( 'pub_date', 'short' ) : '';
  }

  /* @override */
  protected function czr_get_term_css_class( $_is_hierarchical ){
    return array();
  }
}//end of class
