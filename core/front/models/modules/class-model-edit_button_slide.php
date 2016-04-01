<?php
class TC_edit_button_slide_model_class extends TC_edit_button_model_class {
  public $context = 'slide';

  /**
  * @override
  * Helper Boolean
  * @return boolean
  */
  public function tc_is_edit_enabled() {
    //get needed info from the current slide
    extract( get_query_var( 'tc_slide' ) );

    if ( ! $id )
      return;

    $show_slide_edit_link  = current_user_can( 'edit_post', $id ) ? true : false;

    $show_slide_edit_link  = apply_filters('tc_show_slide_edit_link' , $show_slide_edit_link && ! is_null( $data['link_id'] ), $id );

    return $show_slide_edit_link;
  }


  function tc_setup_late_properties() {
    extract( get_query_var( 'tc_slide' ) );

    $edit_post_id          =  $id;
    $edit_link_suffix      =  'tc_posts_slider' == $slider_name_id ? '' : '#slider_sectionid';

    $this -> tc_update( compact( 'edit_post_id', 'edit_link_suffix' ) );
  }
}
