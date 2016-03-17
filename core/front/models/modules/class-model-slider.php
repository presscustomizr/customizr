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

  private $queried_id;
  private $is_slider_active;

  function __construct( $model ) {
    parent::__construct( $model );

    //hook to its own loop hook to set the current slide query var
    add_action( "in_slider_{$this -> id}", array( $this, 'setup_slide_data' ), -100, 2 );

  }

  /**
  * @override
  * fired before the model properties are parsed
  * 
  * return model params array() 
  */
  function tc_extend_params( $model = array() ) {
    //gets the actual page id if we are displaying the posts page
    $this -> queried_id = $queried_id = $this -> tc_get_real_id();

    if ( ! $this -> is_slider_active = $this -> tc_is_slider_active( $queried_id ) )
      return;

    $name_id            = $this -> tc_get_current_slider( $queried_id );
    $layout             = 0 == $this -> tc_get_slider_layout( $queried_id, $name_id ) ? 'boxed' : 'full';

    $img_size           = apply_filters( 'tc_slider_img_size' , ( 'boxed' == $layout ) ? 'slider' : 'slider-full');

    $element_class      = $this -> tc_get_slider_element_class( $queried_id, $name_id, $layout );
    $inner_class        = $this -> tc_get_inner_class();

    $slides             = $this -> tc_get_the_slides( $name_id, $img_size );

    if ( ! $slides )
      return;

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


  function setup_slide_data( $id, $data ) {
    set_query_var( 'tc_slide', array( 
          'slider_name_id' => $this -> name_id,
          'data'           => $data,
          'img_size'       => $this -> img_size,
          'id'             => $id
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
  private function tc_get_the_slides( $slider_name_id, $img_size ) {
    //returns the default slider if requested
    if ( 'demo' == $slider_name_id )
      return apply_filters( 'tc_default_slides', TC_init::$instance -> default_slides );
     
    //if not demo or tc_posts_slider, we get slides from options
    $all_sliders    = TC_utils::$inst -> tc_opt( 'tc_sliders');
    $saved_slides   = ( isset($all_sliders[$slider_name_id]) ) ? $all_sliders[$slider_name_id] : false;
    //if the slider not longer exists or exists but is empty, return false
    if ( ! $this -> tc_slider_exists( $saved_slides) )
      return;
      
    //inititalize the slides array
    $slides   = array();
    //init slide active state index
    $_loop_index        = 0;
    //GENERATE SLIDES ARRAY
    foreach ( $saved_slides as $s ) {
      $slide_object           = get_post($s);
      //next loop if attachment does not exist anymore (has been deleted for example)
      if ( ! isset( $slide_object) )
        continue;
      $id                     = $slide_object -> ID;
      $slide_model = $this -> tc_get_single_slide_model( $slider_name_id, $_loop_index, $id, $img_size);
      if ( ! $slide_model )
        continue;
      $slides[$id] = $slide_model;
      $_loop_index++;
    }//end of slides loop
    
    //returns the slides or false if nothing
    return apply_filters('tc_the_slides', ! empty($slides) ? $slides : false );
  }


  /**
  * Return a single slide model
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  protected function tc_get_single_slide_model( $slider_name_id, $_loop_index , $id , $img_size ) {
    //check if slider enabled for this attachment and go to next slide if not
    $slider_checked         = esc_attr(get_post_meta( $id, $key = 'slider_check_key' , $single = true ));
    if ( ! isset( $slider_checked) || $slider_checked != 1 )
      return;
    //title
    $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
    $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );
    $title                  = $this -> tc_trim_text( $title, $default_title_length, '...' );
    //lead text
    $text                   = get_post_meta( $id, $key = 'slide_text_key' , $single = true );
    $default_text_length    = apply_filters( 'tc_slide_text_length', 250 );
    $text                   = $this -> tc_trim_text( $text, $default_text_length, '...' );
    //button text
    $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
    $default_button_length  = apply_filters( 'tc_slide_button_length', 80 );
    $button_text            = $this -> tc_trim_text( $button_text, $default_button_length, '...' );
    //link post id
    $link_id                = apply_filters( 'tc_slide_link_id', esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true )), $id, $slider_name_id );
    //link
    $link_url               = esc_url( get_post_meta( $id, $key = 'slide_custom_link_key', $single = true ) );
    if ( ! $link_url )
      $link_url = $link_id ? get_permalink( $link_id ) : $link_url;
    $link_url               = apply_filters( 'tc_slide_link_url', $link_url, $id, $slider_name_id );
    //link target
    $link_target_bool       = esc_attr(get_post_meta( $id, $key= 'slide_link_target_key', $single = true ));
    $link_target            = apply_filters( 'tc_slide_link_target', $link_target_bool ? '_blank' : '_self', $id, $slider_name_id );
    //link the whole slide?
    $link_whole_slide       = apply_filters( 'tc_slide_link_whole_slide', esc_attr(get_post_meta( $id, $key= 'slide_link_whole_slide_key', $single = true )), $id, $slider_name_id );
    //checks if $text_color is set and create an html style attribute
    $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
    $color_style            = ( $text_color != null) ? 'style="color:'.$text_color.'"' : '';
    //attachment image
    $alt                    = apply_filters( 'tc_slide_background_alt' , trim(strip_tags(get_post_meta( $id, '_wp_attachment_image_alt' , true))) );
    $slide_background_attr  = array( 'class' => 'slide' , 'alt' => $alt );
    //allow responsive images?
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      if ( 0 == esc_attr( TC_utils::$inst->tc_opt('tc_resp_slider_img') ) ) {
        $slide_background_attr['srcset'] = " ";
        //trick, => will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
        //only when the passed srcset attr is not empty. This will avoid us to:
        //a) add a filter to get rid of already computed srcset
        // or
        //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
        //Side effect:
        //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
        //to avoid this we filter the attributes getting rid of the srcset if any.
        //Basically this trick, even if ugly, will avoid the srcset attr computation
        add_filter( 'wp_get_attachment_image_attributes', array( TC_utils_thumbnails::$instance, 'tc_remove_srcset_attr' ) );
      }
    $slide_background       = wp_get_attachment_image( $id, $img_size, false, $slide_background_attr );
    //adds all values to the slide array only if the content exists (=> handle the case when an attachment has been deleted for example). Otherwise go to next slide.
    if ( !isset($slide_background) || empty($slide_background) )
      return;
    return array(
      'title'               =>  $title,
      'text'                =>  $text,
      'button_text'         =>  $button_text,
      'link_id'             =>  $link_id,
      'link_url'            =>  $link_url,
      'link_target'         =>  $link_target,
      'link_whole_slide'    =>  $link_whole_slide,
      'active'              =>  ( 0 == $_loop_index ) ? 'active' : '',
      'color_style'         =>  $color_style,
      'slide_background'    =>  $slide_background
    );
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
  * returns the actual page id if we are displaying the posts page
  * @return  boolean
  *
  */
  protected function tc_is_slider_active( $queried_id ) {
    //is the slider set to on for the queried id?
    if ( TC_utils::$inst -> tc_is_home() && TC_utils::$inst->tc_opt( 'tc_front_slider' ) )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );
    $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );
    if ( ! empty( $_slider_on ) && $_slider_on )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );
    return apply_filters( 'tc_slider_active_status', false , $queried_id );
  }


  /**
  * helper
  * @return  boolean
  *
  * @package Customizr
  * @since Customizr 3.4.9
  */
  function tc_slider_exists( $slider ){
    //if the slider not longer exists or exists but is empty, return false
    return ! ( !isset($slider) || !is_array($slider) || empty($slider) );
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
    $name_id          = ( TC_utils::$inst -> tc_is_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
    return apply_filters( 'tc_slider_name_id', $name_id , $queried_id);
  }

  /**
  * Helper
  * Returns the passed text trimmed at $text_length char.
  * with the $more text added
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into TC_utils?
  function tc_trim_text( $text, $text_length, $more ) {
    if ( ! $text )
      return '';
    $text       = trim( strip_tags( $text ) );
    if ( ! $text_length )
      return $text;
    $end_substr = $_text_length = strlen( $text );
    if ( $_text_length > $text_length ){
      $end_substr = strpos( $text, ' ' , $text_length);
      $end_substr = ( $end_substr !== FALSE ) ? $end_substr : $text_length;
      $text = substr( $text , 0 , $end_substr );
    }
    return ( ( $end_substr < $text_length ) && $more ) ? $text : $text . ' ' .$more ;
  }

}
