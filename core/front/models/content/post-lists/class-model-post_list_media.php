<?php
class CZR_cl_post_list_media_model_class extends CZR_cl_Model {


  function czr_fn_setup_late_properties() {
    $this -> czr_fn_set_property( 'element_class',
      czr_fn_get( 'czr_media_col' ) );
  }

}