<?php
class CZR_cl_post_list_media_model_class extends CZR_cl_Model {
  public $czr_has_media;
  public $icon_type;

  function czr_fn_setup_late_properties() {
    $post_format = get_post_format();

    $this -> czr_fn_set_property( 'element_class',
      czr_fn_get( 'czr_media_col' ) );
    $this -> czr_fn_set_property( 'czr_has_media',
      czr_fn_get( 'czr_has_post_media' ) );

    $this -> czr_fn_set_property( 'icon_type', $post_format ? substr($post_format, strpos($post_format, "-" ) ) : 'text' );
  }

}