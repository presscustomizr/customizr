<?php
class CZR_cl_edit_button_slide_model_class extends CZR_cl_edit_button_model_class {
  public $context = 'slide';
  public $edit_post_id;
  public $edit_link_suffix;

  /**
  * @override
  * Helper Boolean
  * @return boolean
  */
  public function czr_fn_is_edit_enabled() {
    //get needed info from the current slide
    $id             = czr_fn_get( 'slide_id' );
    $slider_name_id = czr_fn_get( 'slider_name_id' );

    if ( ! $id || 'demo' == $slider_name_id )
      return;

    $link_id = czr_fn_get( 'link_id' );

    $show_slide_edit_link  = current_user_can( 'edit_post', $id ) ? true : false;

    $show_slide_edit_link  = apply_filters('czr_show_slide_edit_link' , $show_slide_edit_link && ! is_null( $link_id), $id  );

    return $show_slide_edit_link;
  }


  function czr_fn_setup_late_properties() {
    $edit_post_id          =  czr_fn_get( 'slide_id' );
    $edit_link_suffix      =  'tc_posts_slider' == czr_fn_get( 'slider_name_id') ? '' : '#slider_sectionid';
    $this -> czr_fn_update( compact( 'edit_post_id', 'edit_link_suffix' ) );
  }
}
