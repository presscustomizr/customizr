<?php
class CZR_cl_slider_model_class extends CZR_cl_Model {
  public $inner_class;
  public $slides = array();
  public $slider_name_id;
  public $layout;
  public $img_size;

  public $left_control_class  = '';
  public $right_control_class = '';

  public $has_controls        = false;
  public $has_loader          = false;

  public $pure_css_loader     = '';

  private $queried_id;
  private $is_slider_active;


  public $current_slide      = array();

  function czr_fn_setup_children() {
    $children = array(
      array(
        'id' => 'slide',
        'model_class'  => 'modules/slider/slide',
      ),
      //edit slide button
      array(
        'id'          => 'slide_edit_button',
        'model_class' => array( 'parent' => 'modules/edit_button', 'name' => 'modules/slider/edit_button_slide'),
        'controller'  => 'edit_button'
      ),
      //edit slider button
      array(
        'id'          => 'slider_edit_button',
        'model_class' => array( 'parent' => 'modules/edit_button', 'name' => 'modules/slider/edit_button_slider'),
        'controller'  => 'edit_button',
      ),
    );

    if ( 'demo' == $this -> slider_name_id )
      array_push( $children,
      //slider helpblock
        array(
          'hook'        => '__after_carousel_inner__',
          'id'          => 'slider_notice',
          'template'    => 'modules/help_block',
          'model_class' => array( 'parent' => 'modules/help_block', 'name' => 'modules/slider_help_block' ),
        )
      );

    return $children;
  }


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    if ( ! isset( $model['id'] ) )
      return;

    //valorize this id as the model id so we can use it as filter param throughout the class
    $this -> id         = $model['id'];

    //gets the actual page id if we are displaying the posts page
    $this -> queried_id = $queried_id = $this -> czr_fn_get_real_id();

    if ( ! $this -> is_slider_active = $this -> czr_fn_is_slider_active( $queried_id ) ) {
      $model['id'] = FALSE;
      return $model;
    }

    $slider_name_id     = $this -> czr_fn_get_current_slider( $queried_id );
    $layout             = 0 == $this -> czr_fn_get_slider_layout( $queried_id, $slider_name_id ) ? 'boxed' : 'full';

    $img_size           = apply_filters( 'tc_slider_img_size' , ( 'boxed' == $layout ) ? 'slider' : 'slider-full');

    $slides             = $this -> czr_fn_get_the_slides( $slider_name_id, $img_size );

    //We need a way to silently fail when the model "decides" it doesn't have to be instantiated
    if ( empty( $slides ) ){
      $model['id'] = FALSE;
      return $model;
    }

    $element_class      = $this -> czr_fn_get_slider_element_class( $queried_id, $slider_name_id, $layout );
    $inner_class        = $this -> czr_fn_get_slider_inner_class();



    //set-up controls
    if ( apply_filters('tc_show_slider_controls' , ! wp_is_mobile() && count( $slides ) > 1) ) {
      $left_control_class  = ! is_rtl() ? 'left' : 'right';
      $right_control_class = ! is_rtl() ? 'right' : 'left';
      $has_controls        = true;
    }

    //set-up loader
    if ( $this -> czr_fn_is_slider_loader_active( $slider_name_id ) ) {
      $has_loader       = true;

      if ( ! apply_filters( 'tc_slider_loader_gif_only', false ) )
        $pure_css_loader = sprintf( '<div class="tc-css-loader %1$s">%2$s</div>',
            implode( ' ', apply_filters( 'tc_pure_css_loader_add_classes', array( 'tc-mr-loader') ) ),
            apply_filters( 'tc_pure_css_loader_inner', '<div></div><div></div><div></div>')
        );
    }

    return array_merge( $model, compact(
        'slider_name_id',
        'element_class',
        'slides',
        'inner_class',
        'img_size',
        'has_controls',
        'left_control_class',
        'right_control_class',
        'pure_css_loader',
        'has_loader'
    ) );
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
  protected function czr_fn_get_the_slides( $slider_name_id, $img_size ) {
    //returns the default slider if requested
    if ( 'demo' == $slider_name_id )
      return apply_filters( 'tc_default_slides', $this -> czr_fn_get_default_slides() );
    else if ( 'tc_posts_slider' == $slider_name_id ) {
      return array();
    }

    //if not demo or tc_posts_slider, we get slides from options
    $all_sliders    = CZR_cl_utils::$inst -> czr_fn_opt( 'tc_sliders');
    $saved_slides   = ( isset($all_sliders[$slider_name_id]) ) ? $all_sliders[$slider_name_id] : false;
    //if the slider not longer exists or exists but is empty, return false
    if ( ! $this -> czr_fn_slider_exists( $saved_slides) )
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
      $slide_model = $this -> czr_fn_get_single_slide_model( $slider_name_id, $_loop_index, $id, $img_size);
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
  protected function czr_fn_get_single_slide_model( $slider_name_id, $_loop_index , $id , $img_size ) {
    //check if slider enabled for this attachment and go to next slide if not
    $slider_checked         = esc_attr(get_post_meta( $id, $key = 'slider_check_key' , $single = true ));
    if ( ! isset( $slider_checked) || $slider_checked != 1 )
      return;
    //title
    $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
    $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );
    $title                  = $this -> czr_fn_trim_text( $title, $default_title_length, '...' );
    //lead text
    $text                   = get_post_meta( $id, $key = 'slide_text_key' , $single = true );
    $default_text_length    = apply_filters( 'tc_slide_text_length', 250 );
    $text                   = $this -> czr_fn_trim_text( $text, $default_text_length, '...' );
    //button text
    $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
    $default_button_length  = apply_filters( 'tc_slide_button_length', 80 );
    $button_text            = $this -> czr_fn_trim_text( $button_text, $default_button_length, '...' );
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
    $slide_background_attr  = array_filter( array_merge( array( 'class' => 'slide' , 'alt' => $alt ), $this -> czr_fn_set_wp_responsive_slide_img_attr() ) );

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

  /*
  * By default we don't want the slider images to be responsive as wp intends as our slider isnot completely responsive (has fixed heights for different viewports)
  *
  * return array()
  */
  protected function czr_fn_set_wp_responsive_slide_img_attr() {
    //allow responsive images?
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      if ( 0 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt('tc_resp_slider_img') ) ) {
        //trick, => will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
        //only when the passed srcset attr is not empty. This will avoid us to:
        //a) add a filter to get rid of already computed srcset
        // or
        //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
        //Side effect:
        //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
        //to avoid this we filter the attributes getting rid of the srcset if any.
        //Basically this trick, even if ugly, will avoid the srcset attr computation
        add_filter( 'wp_get_attachment_image_attributes', array( CZR_cl_utils_thumbnails::$instance, 'czr_fn_remove_srcset_attr' ) );
        return array( 'srcset' => ' ');
      }
    return array();
  }


  function czr_fn_get_has_slide() {
    $_slide = current( $this -> slides );
    if ( empty( $_slide ) )
        return false;
    $slide = & $_slide;
    $slide_id            = key( $this -> slides );
    $this -> czr_fn_set_property( 'current_slide', compact( 'slide', 'slide_id' ) );
    next( $this -> slides );
    return true;
  }

  /**
  * @override
  * parse this model properties for rendering
  */
  function czr_fn_sanitize_model_properties( $model ) {
    parent::czr_fn_sanitize_model_properties( $model );
    $model -> inner_class = $this -> czr_fn_stringify_model_property( 'inner_class' );
  }

  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/

  /*
  * Default slides: demo slider
  * Not a class property as they're really used very rarely, no need to reserve space
  * for them
  */
  protected function czr_fn_get_default_slides() {
    //Default slides content
    return array(
      1 => array(
        'title'         =>  '',
        'text'          =>  '',
        'button_text'   =>  '',
        'link_id'       =>  null,
        'link_url'      =>  null,
        'link_target'   =>  '_blank',
        'active'        =>  'active',
        'color_style'   =>  '',
        'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                    CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/img/customizr-theme-responsive.png',
                                    __( 'Customizr is a clean responsive theme' , 'customizr' )
                            )
      ),

      2 => array(
        'title'         =>  '',
        'text'          =>  '',
        'button_text'   =>  '',
        'link_id'       =>  null,
        'link_url'      =>  null,
        'link_target'   =>  '_blank',
        'active'        =>  '',
        'color_style'   =>  '',
        'slide_background'       =>  sprintf('<img width="1910" height="750" src="%1$s" class="" alt="%2$s" />',
                                    CZR_BASE_URL . CZR_ASSETS_PREFIX . 'front/img/customizr-theme-customizer.png',
                                    __( 'Many layout and design options are available from the WordPress customizer screen : see your changes live !' , 'customizr' )
                            )
      )
    );///end of slides array
  }



  /**
  * @return  array of css classes
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_get_slider_inner_class() {
    $class = array('carousel-inner');

    if( (bool) esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_center_slider_img') ) )
      array_push( $class, 'center-slides-enabled' );

    return apply_filters( 'tc_carousel_inner_classes', $class );
  }


  /*
  * getter
  * Get current slider layout class
  * @param $queried_id the current page/post id
  * @param $slider_name_id the current slider name id
  *
  * @return array()
  */
  protected function czr_fn_get_slider_element_class( $queried_id, $slider_name_id, $layout ) {
    $class        = array( 'carousel', 'customizr-slide', $slider_name_id );

    //layout
    $layout_class = apply_filters( 'tc_slider_layout_class', 'boxed' == $layout ? 'container' : '' );

    array_push( $class, $layout_class );

    //custom height
    if ( 500 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_default_height') ) )
      array_push( $class, 'custom-slider-height' );

    return array_filter( $class );
  }

  /*
  * getter
  * Get current slider layout
  * @param $queried_id the current page/post id
  * @param $slider_name_id the current slider name id
  *
  * @return bool
  */
  protected function czr_fn_get_slider_layout( $queried_id, $slider_name_id ) {
    //gets slider options if any
    $layout_value                 = CZR_cl_utils::$inst -> czr_fn_is_home() ? CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_width' ) : esc_attr( get_post_meta( $queried_id, $key = 'slider_layout_key' , $single = true ) );
    return apply_filters( 'tc_slider_layout', $layout_value, $queried_id );
  }

  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  number
  *
  */
  protected function czr_fn_get_real_id() {
    return apply_filters( 'tc_slider_get_real_id', CZR_cl_utils_query::$instance -> czr_fn_get_real_id(), $this );
  }


  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  boolean
  *
  */
  protected function czr_fn_is_slider_active( $queried_id ) {
    //is the slider set to on for the queried id?
    if ( CZR_cl_utils::$inst -> czr_fn_is_home() && CZR_cl_utils::$inst->czr_fn_opt( 'tc_front_slider' ) )
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
  function czr_fn_slider_exists( $slider ){
    //if the slider not longer exists or exists but is empty, return false
    return ! ( !isset($slider) || !is_array($slider) || empty($slider) );
  }

  /**
  * helper
  * returns the slider name id
  * @return  string
  *
  */
  private function czr_fn_get_current_slider( $queried_id ) {
    //gets the current slider id
    $_home_slider     = CZR_cl_utils::$inst->czr_fn_opt( 'tc_front_slider' );
    $slider_name_id   = ( CZR_cl_utils::$inst -> czr_fn_is_home() && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
    return apply_filters( 'tc_slider_name_id', $slider_name_id , $queried_id, $this -> id );
  }


  /**
  * helper
  * returns whether or not the slider loading icon must be displayed
  * @return  boolean
  *
  */
  private function czr_fn_is_slider_loader_active( $slider_name_id ) {
    //The slider loader must be printed when
    //a) we have to render the demo slider
    //b) display slider loading option is enabled (can be filtered)
    return ( 'demo' == $slider_name_id
        || apply_filters( 'tc_display_slider_loader', 1 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_display_slide_loader') ), $slider_name_id, $this -> id )
    );
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
  // move this into CZR_cl_utils?
  function czr_fn_trim_text( $text, $text_length, $more ) {
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

  /**
  * hook : tc_slider_height, fired in czr_fn_user_options_style
  * @return number height value
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_set_demo_slider_height( $_h ) {
    //this custom demo height is applied when :
    //1) current slider is demo
    if ( 'demo' != $this -> czr_fn_get_current_slider( $this -> czr_fn_get_real_id() ) )
      return $_h;
    //2) height option has not been changed by user yet
    //the possible customization context must be taken into account here
    if ( CZR___::$instance -> czr_fn_is_customizing() ) {
      if ( 500 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_default_height') ) )
        return $_h;
    } else {
      if ( false !== (bool) esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_default_height', CZR___::$czr_option_group, $use_default = false ) ) )
        return $_h;
    }
    return apply_filters( 'czr_set_demo_slider_height' , 750 );
  }

  /*
  * Custom CSS
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_user_options_style_cb( $_css ) {
    $slider_name_id =  $this -> czr_fn_get_current_slider( $this -> czr_fn_get_real_id() ) ;
    //custom css for the slider loader
    if ( $this -> czr_fn_is_slider_loader_active( $slider_name_id ) ) {

      $_slider_loader_src = apply_filters( 'tc_slider_loader_src' , sprintf( '%1$s/%2$s' , CZR_BASE_URL . CZR_ASSETS_PREFIX, 'img/slider-loader.gif') );
      //we can load only the gif, or use it as fallback for old browsers (.no-csstransforms3d)
      if ( ! apply_filters( 'tc_slider_loader_gif_only', false ) ) {
        $_slider_loader_gif_class  = '.no-csstransforms3d';
        // The pure css loader color depends on the skin. Why can we do this here without caring of the live preview?
        // Basically 'cause the loader is something we see when the page "loads" then it disappears so a live change of the skin
        // will still have no visive impact on it. This will avoid us to rebuild the custom skins.
        $_current_skin_colors      = CZR_cl_utils::$inst -> tc_get_skincolor( 'pair' );
        $_pure_css_loader_css      = apply_filters( 'tc_slider_loader_css', sprintf(
            '.tc-slider-loader-wrapper .tc-css-loader > div { border-color:%s; }',
            //we can use the primary or the secondary skin color
            'primary' == apply_filters( 'tc_slider_loader_color', 'primary') ? $_current_skin_colors[0] : $_current_skin_colors[1]
        ));
      }else {
        $_slider_loader_gif_class = '';
        $_pure_css_loader_css     = '';
      }

      $_slider_loader_gif_css     = $_slider_loader_src ? sprintf(
                                       '%1$s .tc-slider-loader-wrapper .tc-img-gif-loader {
                                               background: url(\'%2$s\') no-repeat center center;
                                        }',
                                        $_slider_loader_gif_class,
                                        $_slider_loader_src
                                    ) : '';
      $_css = sprintf( "%s\n%s%s", $_css, $_slider_loader_gif_css, $_pure_css_loader_css );
    }//end custom css for the slider loader

    // 1) Do we have a custom height ?
    // 2) check if the setting must be applied to all context
    $_custom_height     = esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_default_height') );
    $_custom_height     = apply_filters( 'tc_slider_height' , 'demo' != $slider_name_id ? $_custom_height : $this -> czr_fn_set_demo_slider_height( $_custom_height ) );

    $_slider_inline_css = "";
    //When shall we append custom slider style to the global custom inline stylesheet?
    $_bool = 500 != $_custom_height;
    $_bool = $_bool && ( CZR_cl_utils::$inst -> czr_fn_is_home() || 0 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_slider_default_height_apply_all') ) );
    if ( ! apply_filters( 'czr_print_slider_inline_css' , $_bool ) )
      return $_css;
    $_resp_shrink_ratios = apply_filters( 'tc_slider_resp_shrink_ratios',
      array('1200' => 0.77 , '979' => 0.618, '480' => 0.38 , '320' => 0.28 )
    );
    $_slider_inline_css = "
      .carousel .item {
        line-height: {$_custom_height}px;
        min-height:{$_custom_height}px;
        max-height:{$_custom_height}px;
      }
      .tc-slider-loader-wrapper {
        line-height: {$_custom_height}px;
        height:{$_custom_height}px;
      }
      .carousel .tc-slider-controls {
        line-height: {$_custom_height}px;
        max-height:{$_custom_height}px;
      }\n";
    foreach ( $_resp_shrink_ratios as $_w => $_ratio) {
      if ( ! is_numeric($_ratio) )
        continue;
      $_item_dyn_height     = $_custom_height * $_ratio;
      $_caption_dyn_height  = $_custom_height * ( $_ratio - 0.1 );
      $_slider_inline_css .= "
        @media (max-width: {$_w}px) {
          .carousel .item {
            line-height: {$_item_dyn_height}px;
            max-height:{$_item_dyn_height}px;
            min-height:{$_item_dyn_height}px;
          }
          .item .carousel-caption {
            max-height: {$_caption_dyn_height}px;
            overflow: hidden;
          }
          .carousel .tc-slider-loader-wrapper {
            line-height: {$_item_dyn_height}px;
            height:{$_item_dyn_height}px;
          }
        }\n";
    }//end foreach

    return sprintf("%s\n%s", $_css, $_slider_inline_css);
  }
}
