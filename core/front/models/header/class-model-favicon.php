<?php
class CZR_cl_favicon_model_class extends CZR_cl_Model {
  public $src;
  public $type;

  /*
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $_fav_option  = CZR_cl_utils::$inst->czr_fn_opt( 'tc_fav_upload');
    $_fav_src     = '';

    //check if option is an attachement id or a path (for backward compatibility)
    if ( is_numeric($_fav_option) ) {
      $_attachement_id  = esc_attr( $_fav_option );
      $_attachment_data = apply_filters( 'tc_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'full' ) );
      $_fav_src         = $_attachment_data[0];
    } else { //old treatment
      $_saved_path      = esc_url ( $_fav_option );
      //rebuild the path : check if the full path is already saved in DB. If not, then rebuild it.
      $upload_dir       = wp_upload_dir();
      $_fav_src         = ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
    }
    //makes ssl compliant url
    $_fav_src           = apply_filters( 'tc_fav_src' , is_ssl() ? str_replace('http://', 'https://', $_fav_src) : $_fav_src );

    $type = "image/x-icon";
    if ( strpos( $_fav_src, '.png') ) $type = "image/png";
    if ( strpos( $_fav_src, '.gif') ) $type = "image/gif";

    $model[ 'src' ]        = $_fav_src;
    $model[ 'type' ]       = $type;

    return $model;
  }
}
