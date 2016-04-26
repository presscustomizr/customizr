<?php
class CZR_cl_edit_button_slider_model_class extends CZR_cl_edit_button_model_class {
  public $context   = 'slider';
  public $slider_edit_link;
  public $slider_edit_link_type;

  /**
  * @override
  * Helper Boolean
  * @return boolean
  */
  public function czr_fn_is_edit_enabled() {

    $slider_name_id = czr_fn_get( 'slider_name_id' );

    if ( ! $slider_name_id )
      return;

    if ( 'demo' == $slider_name_id )
      return;

    $show_slider_edit_link    = false;
    //We have to show the slider edit link to
    //a) users who can edit theme options for the slider in home -> deep link in the customizer
    //b) users who can edit the post/page where the slider is displayed for users who can edit the post/page -> deep link in the post/page slider section
    if ( CZR_cl_utils::$inst -> czr_fn_is_home() )
      $show_slider_edit_link = current_user_can('edit_theme_options') ? true : false;
    else if ( is_singular() ) // we have a snippet to display sliders in categories, we don't want the slider edit link displayed there
      $show_slider_edit_link = ( current_user_can('edit_pages') || current_user_can( 'edit_posts', $post -> ID ) ) ? true : false;

    $show_slider_edit_link = apply_filters( 'czr_show_slider_edit_link' , $show_slider_edit_link, $slider_name_id );

    return $show_slider_edit_link;
  }


  function czr_fn_setup_late_properties() {
    if ( CZR_cl_utils::$inst -> czr_fn_is_home() )
      $slider_edit_link            = CZR_cl_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec') );
    elseif ( is_singular() ) {
      global $post;
      $slider_edit_link            = get_edit_post_link( $post -> ID ) . '#slider_sectionid';
    }

    $slider_edit_link_type         = czr_fn_get( 'slider_type' );

    $this -> czr_fn_update( compact( 'slider_edit_link', 'slider_edit_link_type' ) );
  }
}
