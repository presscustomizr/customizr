<?php
class TC_post_thumbnail_model_class extends TC_post_list_thumbnail_model_class {
  public $wrapper_class         = 'tc-thumbnail span12';
  public $link_class            = 'tc-rectangular-thumb';
  public $thumb_wrapper_class   = '';

  // backward compatiblity
  private $hook_map       = array(
    '__before_main_wrapper' => 'before_render_view_main_wrapper',
    '__before_content'      => 'before_render_view_post_headings',
    '__after_content_title' => '__headings_content__'    
  );

  function __construct( $model = array() ) {
    //we cannot use the "_da_hook" filter 'cause we set the model properties on the initial model hook action, in the constructor, so before the _da_hook will be changed
    $model = $this -> tc_set_this_view_hook_and_priority( $model );
    parent::__construct( $model );
  }


  /*
  * change hook/priority according to the choosen option
  */
  function tc_set_this_view_hook_and_priority( $model ) {
    $_exploded_location   = $this -> tc_get_thumb_option_hook();
    $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
    $hook                 = isset( $this -> hook_map[ $_hook ] ) ? $this -> hook_map[ $_hook ] : '';
    $priority             = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;
    return array_merge( $model, compact( 'hook', 'priority' ) );
  }

  /* override */
  function tc_get_no_effect_class( $thumb_model ) {
    return array(); 
  }

  /**
  * override
  * 
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_get_thumb_size( $_default_size = 'tc-thumb' ) {
    $_exploded_location   = $this -> tc_get_thumb_option_hook();
    $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
    return '__before_main_wrapper' == $_hook ? 'slider-full' : 'slider';
  }

  private function tc_get_thumb_option_hook() {
    return explode( '|', esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' ) ) );   
  }

  function tc_get_element_class() {
    return array( 'row-fluid', 'tc-single-post-thumbnail-wrapper', current_filter() );
      //'tc-thumbnail span12';    
  }

  function tc_has_post_thumbnail() { 
    return true; /*the actual check is done in the controller*/
  }
  
  /**
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function tc_user_options_style_cb( $_css ) {
    $_single_thumb_height   = esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_height' ) );
    $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
    return sprintf("%s\n%s",
      $_css,
      ".single .tc-rectangular-thumb {
        max-height: {$_single_thumb_height}px;
        height :{$_single_thumb_height}px
      }\n"
    );
  }
}
