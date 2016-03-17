<?php
class TC_slider_model_class extends TC_Model {
  public $inner_class;
  public $slides = array();
  public $name_id;
  public $layout;
  public $img_size;

  public $left_control_class  = '';
  public $right_control_class = '';

  public $has_controls = false;

  function __construct( $model ) {
    parent::__construct( $model );

    //hook to its own loop hook to set the current slide query var
    add_action( "in_slider_{$this -> id}", array( $this, 'setup_slide_data' ), -100 );

  }

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    //gets the actual page id if we are displaying the posts page
    $queried_id         = $this -> tc_get_real_id();
    $name_id            = $this -> tc_get_current_slider( $queried_id );
    $layout             = 0 == $this -> tc_get_slider_layout( $queried_id, $name_id ) ? 'boxed' : 'full';

    $img_size           = apply_filters( 'tc_slider_img_size' , ( 'boxed' == $layout ) ? 'slider' : 'slider-full');

    $element_class      = $this -> tc_get_slider_element_class( $queried_id, $name_id, $layout );
    $inner_class        = $this -> tc_get_inner_class();

    $slides             = $this -> tc_get_the_slides( $name_id, '');

    //set-up contorls
    if ( apply_filters('tc_show_slider_controls' , ! wp_is_mobile() && count( $slides ) > 1) ) {
      $left_control_class  = ! is_rtl() ? 'left' : 'right';
      $right_control_class = ! is_rtl() ? 'right' : 'left';
      $has_controls        = true;
    }

    return array_merge( $model, compact( 
        'name_id', 
        'element_class', 
        'slides', 'layout', 
        'inner_class', 
        'img_size',
        'has_controls',
        'left_control_class',
        'right_control_class'
    ) );
  }


  function setup_slide_data( $slide ) {
    set_query_var( 'tc_slide', array( 
          'slider_name_id' => $this -> name_id,
          'data'           => $slide,
          'img_size'       => $this -> img_size
      )
    );
  }


  /**
  * Helper
  * Return an array of the slide models from option or default
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  private function tc_get_the_slides( $name_id, $img_size ) {
    //returns the default slider if requested
    if ( 'demo' == $name_id )
      return apply_filters( 'tc_default_slides', TC_init::$instance -> default_slides );
    return array();
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function pre_rendering_my_view_cb( $model ) {
    parent::pre_rendering_my_view_cb( $model );
    $model -> inner_class = $this -> tc_stringify_model_property( 'inner_class' );
  }

  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/

  /**
  * @return  array of css classes
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function tc_get_inner_class() {
    $class = array('carousel-inner');

    if( (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_center_slider_img') ) )
      array_push( $class, 'center-slides-enabled' );
    
    return apply_filters( 'tc_carousel_inner_classes', $class );
  }


  /*
  * getter
  * Get current slider layout class
  * @param $queried_id the current page/post id
  * @param $name_id the current slider name id
  *
  * @return array()
  */
  protected function tc_get_slider_element_class( $queried_id, $name_id, $layout ) {
    $class        = array( 'carousel', 'customizr-slide', $name_id );

    //layout
    $layout_class = apply_filters( 'tc_slider_layout_class', 'boxed' == $layout ? 'container' : '' );

    array_push( $class, $layout_class );

    //custom height
    if ( 500 != esc_attr( TC_utils::$inst->tc_opt( 'tc_slider_default_height') ) )
      array_push( $class, 'custom-slider-height' );

    return $class;
  }

  /*
  * getter
  * Get current slider layout
  * @param $queried_id the current page/post id
  * @param $name_id the current slider name id
  *
  * @return bool
  */
  protected function tc_get_slider_layout( $queried_id, $name_id ) {
    //gets slider options if any
    $layout_value                 = TC_utils::$inst -> tc_is_home() ? TC_utils::$inst->tc_opt( 'tc_slider_width' ) : esc_attr( get_post_meta( $queried_id, $key = 'slider_layout_key' , $single = true ) );
    return apply_filters( 'tc_slider_layout', $layout_value, $queried_id );
  }

  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  number
  *
  */
  protected function tc_get_real_id() {
    global $wp_query;
    $queried_id                   = get_queried_object_id();
    return apply_filters( 'tc_slider_get_real_id', ( ! TC_utils::$inst -> tc_is_home() && $wp_query -> is_posts_page && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
  }

  /**
  * helper
  * returns the slider name id
  * @return  string
  *
  */
  private function tc_get_current_slider($queried_id) {
    //gets the current slider id
    $_home_slider     = TC_utils::$inst->tc_opt( 'tc_front_slider' );
    $name_id   = ( TC_utils::$inst -> tc_is_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
    return apply_filters( 'tc_slider_name_id', $name_id , $queried_id);
  }
}
