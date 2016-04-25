<?php
class CZR_cl_post_metas_text_model_class extends CZR_cl_post_metas_model_class {
  public $type = 'post_metas_text';

  /*  @override */
  public function tc_get_publication_date() {
    return 0 != esc_attr( CZR_cl_utils::$inst->tc_opt( 'tc_show_post_metas_publication_date' ) ) ? $this -> tc_get_meta( 'pub_date', 'short' ) : '';
  }

  /* @override */
  protected function tc_get_term_css_class( $_is_hierarchical ){
    return array();
  }
}//end of class
