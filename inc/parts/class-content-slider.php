<?php
/**
* Slider actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_slider' ) ) :
  class TC_slider {
    static $instance;
    function __construct () {
          self::$instance =& $this;
          add_action('template_redirect'          , array($this, 'tc_set_slider_hooks') );
          //set user customizer options. @since v3.2.0
          add_action('template_redirect'          , array($this, 'tc_set_slider_options') );
    }//end of construct


    
    /**
    * callback of template_redirect
    * Set slider hooks
    * @return  void
    */
    function tc_set_slider_hooks() {
      add_action( '__after_header'                , array( $this , 'tc_slider_display' ) );
      add_action( '__after_carousel_inner'        , array( $this , 'tc_slider_control_view' ) );
    }



    /**
    * callback of template_redirect
    * Set slider user options
    * @return  void
    * 
    * @package Customizr
    * @since Customizr 3.2.0
    *
    */
    function tc_set_slider_options() {
      add_filter( 'tc_slider_layout_class'        , array( $this , 'tc_set_slider_wrapper_class' ) );
      //Set user defined height
      add_filter( 'tc_user_options_style'         , array( $this , 'tc_write_slider_inline_css' ) );
    }




    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_write_slider_inline_css( $_css ) {
      // 1) Do we have a custom height ?
      // 2) check if the setting must be applied to all context
      $_custom_height     = esc_attr( tc__f( '__get_option' , 'tc_slider_default_height') );
      $_slider_inline_css = "";

      if ( 500 != $_custom_height
        && ( tc__f('__is_home')
            || 0 != esc_attr( tc__f( '__get_option' , 'tc_slider_default_height_apply_all') )
        ) ) {
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
        }
      }
      return sprintf("%s\n%s", $_css, $_slider_inline_css);
    }



    /**
    * Set slider wrapper class
    * Callback of tc_slider_layout_class filter
    * 
    * @package Customizr
    * @since Customizr 3.2.0
    *
    */
    function tc_set_slider_wrapper_class($_classes) {
      if ( ! is_array($_classes) || 500 == esc_attr( tc__f( '__get_option' , 'tc_slider_default_height') ) )
        return $_classes;

      return array_merge( $_classes , array('custom-slider-height') );
    }



    /**
    * Get slides from option or default
    * Returns and array of slides with data
    * 
    * @package Customizr
    * @since Customizr 3.0.15
    *
    */
    function tc_get_slides( $slider_name_id, $img_size ) {

      //returns the default slider if requested
      if ( 'demo' == $slider_name_id )
        return apply_filters( 'tc_default_slides', TC_init::$instance -> default_slides );

      //if not demo, we get slides from options
      $all_sliders              = tc__f('__get_option' , 'tc_sliders');
      $saved_slides             = ( isset($all_sliders[$slider_name_id]) ) ? $all_sliders[$slider_name_id] : false;

      //if the slider not longer exists or exists but is empty, return false
      if ( !isset($saved_slides) || !is_array($saved_slides) || empty($saved_slides) )
        return;

      //inititalize the slides array
      $slides   = array();

      //init slide active state index
      $i        = 0;

      foreach ( $saved_slides as $s) {
        $slide_object           = get_post( $s);
        
        //next loop if attachment does not exist anymore (has been deleted for example)
        if (!isset( $slide_object))
          continue;

        $id                     = $slide_object -> ID;
        
        //check if slider enabled for this attachment and go to next slide if not
        $slider_checked         = esc_attr(get_post_meta( $id, $key = 'slider_check_key' , $single = true ));
        if ( !isset( $slider_checked) || $slider_checked != 1 )
          continue;
        
        //title
        $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
        $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );
        $title                  = ( strlen($title) > $default_title_length ) ? substr( $title,0,strpos( $title, ' ' , $default_title_length) ). ' ...' : $title;
        
        //lead text
        $text                   = get_post_meta( $id, $key = 'slide_text_key' , $single = true );
        $default_text_length    = apply_filters( 'tc_slide_text_length', 250 );
        $text                   = ( strlen($text) > $default_text_length ) ? substr( $text,0,strpos( $text, ' ' ,$default_text_length) ). ' ...' : $text;

        //button text
        $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
        $default_button_length  = apply_filters( 'tc_slide_button_length', 80 );
        $button_text            = ( strlen($button_text) > $default_button_length ) ? substr( $button_text,0,strpos( $button_text, ' ' ,$default_button_length)). ' ...' : $button_text;

        //link post id
        $link_id                = esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true ));
        
        //button link
        $link_url               = $link_id ? get_permalink( $link_id ) : 'javascript:void(0)';

        //sets the first slide active
        $active                 = ( 0 == $i ) ? 'active' : '';

        //checks if $text_color is set and create an html style attribute
        $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
        $color_style            = ( $text_color != null) ? 'style="color:'.$text_color.'"' : '';

        //attachment image
        $alt                    = apply_filters( 'tc_slide_background_alt' , trim(strip_tags(get_post_meta( $id, '_wp_attachment_image_alt' , true))) );
        $slide_background       = wp_get_attachment_image( $id, $img_size, false, array( 'class' => 'slide' , 'alt' => $alt ) );

        //adds all values to the slide array only if the content exists (=> handle the case when an attachment has been deleted for example). Otherwise go to next slide.
        if ( !isset($slide_background) || empty($slide_background) )
          continue;

        $slides[$id]            = array(
                                'title'               =>  $title,
                                'text'                =>  $text,
                                'button_text'         =>  $button_text,
                                'link_id'             =>  $link_id,
                                'link_url'            =>  $link_url, 
                                'active'              =>  $active,
                                'color_style'         =>  $color_style,
                                'slide_background'    =>  $slide_background,
        );

        //increments active index
        $i++;

      }//end of slides loop

      //returns the slides or false if nothing
      return ( !empty($slides) ) ? $slides : false;
    }





    /**
     * Displays the slider based on the context : home, post/page.
     * 
     * @package Customizr
     * @since Customizr 1.0
     *
     */
    function tc_slider_display() {
      global $wp_query;

      //gets the front slider if any
      $tc_front_slider              = tc__f( '__get_option' , 'tc_front_slider' );

      //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types) 
      if ( ! apply_filters( 'tc_show_slider' , !is_404() && !is_archive() && !is_search() || ( tc__f('__is_home') && $tc_front_slider ) ) )
        return;
      
      //gets the actual page id if we are displaying the posts page
      $queried_id                   = get_queried_object_id();
      $queried_id                   = ( !tc__f('__is_home') && $wp_query -> is_posts_page && !empty($queried_id) ) ?  $queried_id : get_the_ID();

      //gets the current slider id
      $slider_name_id               = ( tc__f('__is_home') && $tc_front_slider ) ? $tc_front_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
      $slider_name_id               = apply_filters( 'tc_slider_name_id', $slider_name_id , $queried_id);

      //is the slider set to on for the queried id?
      $slider_active                = ( tc__f('__is_home') && $tc_front_slider ) ? true : esc_attr(get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ));
      $slider_active                = apply_filters( 'tc_slider_active_status', $slider_active , $queried_id);

      if ( isset( $slider_active) && !$slider_active )
        return;

      //gets slider options if any
      $layout_value                 = tc__f('__is_home') ? tc__f( '__get_option' , 'tc_slider_width' ) : esc_attr(get_post_meta( $queried_id, $key = 'slider_layout_key' , $single = true ));
      $layout_value                 = apply_filters( 'tc_slider_layout', $layout_value, $queried_id );
      
      //declares the layout vars
      $layout_class                 = implode( " " , apply_filters( 'tc_slider_layout_class' , ( 0 == $layout_value ) ? array('container', 'carousel', 'slide') : array('carousel', 'slide') ) );
      $img_size                     = apply_filters( 'tc_slider_img_size' , ( 0 == $layout_value ) ? 'slider' : 'slider-full');

      //get slides
      $slides                       = $this-> tc_get_slides( $slider_name_id , $img_size );

      //returns nothing if no slides to display
      if (!$slides)
        return;
      
      ob_start();
      ?>
      <div id="customizr-slider" class="<?php echo $layout_class ?> ">
          <?php if ( 'demo' == $slider_name_id || ( 1 == esc_attr( tc__f( '__get_option' , 'tc_display_slide_loader') ) && apply_filters( 'tc_display_slider_loader' , true ) ) ) : ?>
            <div class="tc-slider-loader-wrapper">
              <div class="tc-img-gif-loader">
                <img data-no-retina src="<?php echo apply_filters('tc_slider_loader_src' , sprintf( '%1$s/%2$s' , TC_BASE_URL , 'inc/assets/img/slider-loader.gif') ) ?>">
              </div>
            </div>
          <?php endif; ?>
          <div class="carousel-inner">
            <?php foreach ($slides as $id => $data) : ?>
              <?php 
                $slide_class = sprintf('%1$s %2$s',
                  $data['active'],
                  'slide-'.$id
                );
                ?>
              <div class="item <?php echo $slide_class; ?>">

                <div class="<?php echo apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) ); ?>">
                  <?php
                    do_action('__before_all_slides');
                    do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id ) );

                      echo apply_filters( 'tc_slide_background', $data['slide_background'], $data['link_url'], $id, $slider_name_id );
                      
                    do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id ) );
                    do_action('__after_all_slides');
                  ?>
                </div> <!-- .carousel-image -->
                <?php 
                  if ( $data['title'] != null || $data['text'] != null || $data['button_text'] != null ) {
                    //apply filters first
                    $data['title']          = isset($data['title']) ? apply_filters( 'tc_slide_title', $data['title'] , $id, $slider_name_id ) : '';
                    $data['text']           = isset($data['text']) ? esc_html( apply_filters( 'tc_slide_text', $data['text'], $id, $slider_name_id ) ) : '';
                    $data['color_style']    = apply_filters( 'tc_slide_color', $data['color_style'], $id, $slider_name_id );
                    $data['link_id']        = apply_filters( 'tc_slide_link_id', $data['link_id'], $id, $slider_name_id );
                    $data['link_url']       = ( 'demo' == $slider_name_id && is_null($data['link_url']) ) ? admin_url().'customize.php' : $data['link_url'];
                    $data['link_url']       = apply_filters( 'tc_slide_link_url', $data['link_url'], $id, $slider_name_id );
                    $data['button_text']    = isset($data['button_text']) ? apply_filters( 'tc_slide_button_text', $data['button_text'], $id, $slider_name_id ) : '';

                    //computes the link
                    $button_link            = ( !is_user_logged_in() && 'demo' == $slider_name_id ) ? 'javascript:void(0)' : $data['link_url'];
                    $button_link            = ( $data['link_id'] != null && $data['link_url'] != null ) ? $data['link_url'] : $button_link;

                    printf('<div class="carousel-caption">%1$s %2$s %3$s</div>',
                      //title
                      ( $data['title'] != null ) ? sprintf('<%1$s %2$s>%3$s</%1$s>',
                                            apply_filters( 'tc_slide_title_tag', 'h1', $slider_name_id ),
                                            $data['color_style'],
                                            $data['title']
                                          ) : '',
                      //lead text
                      ( $data['text'] != null ) ? sprintf('<p class="lead" %1$s>%2$s</p>',
                                            $data['color_style'],
                                            $data['text']
                                          ) : '',
                      //button call to action
                      ( $data['button_text'] != null) ? sprintf('<a class="%1$s" href="%2$s">%3$s</a>',
                                                  apply_filters( 'tc_slide_button_class', 'btn btn-large btn-primary', $slider_name_id ),
                                                  $button_link,
                                                  $data['button_text']
                                                ) : ''
                    );
                  }//end if there content to show in caption

                  //display edit link for logged in users with edit posts capabilities
                  $show_edit_link         = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : false;
                  $show_edit_link         = apply_filters('tc_show_slider_edit_link' , $show_edit_link && !is_null($data['link_id']) );
                  if ( $show_edit_link ) {
                    printf('<span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
                      get_edit_post_link($id) . '#slider_sectionid',
                      __( 'Edit' , 'customizr' )
                    );
                  }//end edit attachment condition
                ?>

              </div><!-- /.item -->

            <?php endforeach ?>

          </div><!-- /.carousel-inner -->

          <?php  do_action( '__after_carousel_inner' , $slides )  ?>

        </div><!-- /#customizr-slider -->
              
        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_slider_display', $html, $slider_name_id );
      }



      /*
      * Slider controls view
      * @param slides
      * @hook : __after_carousel_inner
      * @since v3.2.0
      * 
      */
      function tc_slider_control_view( $_slides ) {
        if ( count( $_slides ) <= 1 )
          return;

        if ( ! apply_filters('tc_show_slider_controls' , ! wp_is_mobile() ) )
          return;

        $_html = '';
        $_html .= sprintf('<div class="tc-slider-controls left">%1$s</div>',
          sprintf('<a class="tc-carousel-control" href="#customizr-slider" data-slide="prev">%1$s</a>',
            apply_filters( 'tc_slide_left_control', '&lsaquo;' )
          )
        );
        $_html .= sprintf('<div class="tc-slider-controls right">%1$s</div>',
          sprintf('<a class="tc-carousel-control" href="#customizr-slider" data-slide="next">%1$s</a>',
            apply_filters( 'tc_slide_right_control', '&rsaquo;' )
          )
        );
        echo apply_filters( 'tc_slider_control_view', $_html );
    }



  } //end of class
endif;