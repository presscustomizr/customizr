<?php
class TC_edit_button_model_class extends TC_Model {

  /*
  * @override
  */
  function tc_maybe_render_this_model_view () {
    if ( ! $this -> visibility )
      return;

    //Must be in the loop
    if ( ! in_the_loop() )
      return;

    if ( ! apply_filters( 'tc_edit_in_title', $this -> tc_is_edit_enabled() ) )
      return;

    return true;
  }

  /**
  * Helper Boolean
  * @return boolean
  * @package Customizr
  * @since Customizr 3.3+
  */
  public function tc_is_edit_enabled() {
    //when are we displaying the edit link?
    $edit_enabled = ( is_page() && current_user_can('edit_pages') ) ? true : false;
    return ( 0 !== get_the_ID() && current_user_can('edit_post' , get_the_ID() ) && ! is_page() ) ? true : $edit_enabled;
  }
}
