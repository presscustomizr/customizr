<?php
class CZR_featured_pages_model_class extends CZR_Model {

  public $fp_ids;
  public $fp_nb;
  public $fp_per_row;
  public $fp_col;

  public $featured_pages;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
        $_preset = array(
          'show_thumb'                => esc_attr( czr_fn_opt( 'tc_show_featured_pages_img' ) ),
          'center_imgs'               => esc_attr( czr_fn_opt( 'tc_center_img' ) ),
          'fp_per_row'                => 3,
          'fps'                       => array(
              esc_attr( czr_fn_opt( 'tc_featured_page_one' ) ),
              esc_attr( czr_fn_opt( 'tc_featured_page_two' ) ),
              esc_attr( czr_fn_opt( 'tc_featured_page_three' ) )
          ),
          'fps_text'                  => array(
              esc_attr( czr_fn_opt( 'tc_featured_text_one' ) ),
              esc_attr( czr_fn_opt( 'tc_featured_text_two' ) ),
              esc_attr( czr_fn_opt( 'tc_featured_text_three' ) )
          ),
          'text_length'               => 200,
          'button_text'               => esc_attr( czr_fn_opt( 'tc_featured_page_button_text') )
        );

        return $_preset;
  }
  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
        //parent merges with $args
        $model                          = parent::czr_fn_extend_params( $model );

        $show_thumb                     = $model['show_thumb'];

        $_skin_color                    = czr_fn_opt( 'tc_skin_color' );
        $_center_imgs                   = $model['center_imgs'];

        $model[ 'fp_holder_img' ]       = apply_filters (
              'tc_fp_holder_img' ,
              sprintf('<img class="tc-holder-img" data-src="holder.js/270x250/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" %3$s />',
                ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
                $_skin_color,
                'style="width:270px;height:250px"'
              )
        );
        //gets the featured pages array and sets the fp layout
        $model['fp_ids']                = CZR___::$instance -> fp_ids;

        $model['fp_nb']                 = count( $model['fp_ids'] );


      //defines the cols class
        $cols_map = array(
            1 => 12,
            2 => 6,
            3 => 4,
            4 => 3,
            5 => 2,
            6 => 2,
            7 => 2
        );

        $model['fp_col']                = ( $model['fp_per_row'] > 7) ? 1 : 4;
        $model['fp_col']                = isset( $cols_map[$model['fp_per_row']] ) ? $cols_map[$model['fp_per_row']] : $model['fp_col'];


        $model['fps']                   = is_array( $model['fps'] ) ? $model['fps'] : array();
        $model['fps']                   = array_pad( $model['fps'], $model['fp_nb'], null );

        $model['fps_text']              = is_array( $model['fps_text'] ) ? $model['fps_text'] : array();
        $model['fps_text']              = array_pad( $model['fps_text'], $model['fp_nb'], '' );
        //force image centering
        $model['element_class']         = 'center-images-enabled'; //$_center_imgs ? 'center-images-enabled' : 'center-images-disabled';

        //to transform the $model array items in object fields
        $this -> czr_fn_update( $model );

        $this -> featured_pages         = $this -> czr_fn_get_the_featured_pages( $model['fp_nb'], $model['fp_ids'], $model['show_thumb'] );

        return $model;
  }




  function czr_fn_get_the_featured_pages( $fp_nb, $fp_ids, $show_thumb ) {
        $featured_pages = array();

        add_filter( 'czr_fp_text', 'do_shortcode' );
        foreach ( range(0, $fp_nb - 1 ) as $fp_id ) {
          $featured_pages[ $fp_id + 1 ] = $this -> czr_fn_get_single_fp_model(
            $fp_ids[$fp_id],
            $show_thumb,
            $fp_id + 1
          );
        }
        remove_filter( 'czr_fp_text', 'do_shortcode' );
        return $featured_pages;
  }


  function czr_fn_get_single_fp_model( $fp_single_id, $show_thumb, $fp_index ) {
        $fp_img                         = '';
        $thumb_wrapper_class            = '';
        $edit_enabled                   = false;

        $featured_page_id               = apply_filters( 'czr_fp_id', $this -> fps[$fp_index-1], $fp_single_id );


        //if fps are not set
        if ( ! $featured_page_id || ! $page = get_post($featured_page_id) ) {

            //admin link if user logged in
            $featured_page_link             = '';
            $customizr_link                 = '';

            if ( ! czr_fn_is_customizing() && is_user_logged_in() && current_user_can('edit_theme_options') ) {
                $customizr_link              = sprintf( '<br/><a href="%1$s" title="%2$s" class="btn btn-edit" style="margin-top: 1em;
        font-weight: bold;"><i class="icn-edit"></i>%3$s</a>',
                    czr_fn_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id, 'section' => 'frontpage_sec') ),
                    __( 'Customizer screen' , 'customizr' ),
                    __( 'Customize it now' , 'customizr' )
                );
                $featured_page_link           = czr_fn_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id, 'section' => 'frontpage_sec') );
            } else if ( czr_fn_is_customizing() ) {
                $customizr_link = sprintf( '<br/>%1$s<a href="%2$s" title="%3$s" class="btn btn-edit" style="margin-top: 1em;
        font-weight: bold;"><i class="icn-edit"></i>%4$s</a>',
                    sprintf( '<div style="position: relative;left: 33px;float: left;">%1$s</div>',
                       czr_fn_get_customizer_focus_icon( array( 'wot' => 'control', 'id' => 'tc_theme_options[tc_featured_page_' . $fp_single_id .']' ) )
                    ),
                    czr_fn_get_customizer_focus_link( array( 'wot' => 'control', 'id' => 'tc_theme_options[tc_featured_page_' . $fp_single_id .']' ) ),
                    __( 'Customizer screen' , 'customizr' ),
                    __( 'Customize it now' , 'customizr' )
                );
                $featured_page_link           = czr_fn_get_customizer_focus_link( array( 'wot' => 'control', 'id' => 'tc_theme_options[tc_featured_page_' . $fp_single_id .']' ) );
            }

            //rendering
            $featured_page_link             = apply_filters( 'czr_fp_link_url', $featured_page_link );
            /*
            * treat the case when we filter the fp link url in prevdem returning => 'javascript:void(0)',
            * we don't want it to be escaped otherwise the customizer preview will turn it in a link to the home page itself
            */
            $featured_page_link             = 'javascript:void(0)' == $featured_page_link ? $featured_page_link : esc_url( $featured_page_link );
            $featured_page_id               = null;
            $featured_page_title            = apply_filters( 'czr_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
            $text                           = apply_filters(
                                                    'czr_fp_text',
                                                    sprintf( '%1$s %2$s',
                                                    __( 'Featured page description text : use the page excerpt or set your own custom text in the customizer screen.' , 'customizr' ),
                                                     $customizr_link
                                                    ),
                                                    $fp_single_id,
                                                    $featured_page_id
                                                );
            $fp_img                         = $show_thumb ? apply_filters ('fp_img_src', $this -> fp_holder_img, $fp_single_id , $featured_page_id ) : '';
        }
        else {

            $featured_page_link             = esc_url( apply_filters( 'czr_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id ) );
            $featured_page_title            = apply_filters( 'czr_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );


          //when are we displaying the edit link?
            //never display when customizing
            if ( ! czr_fn_is_customizing() ) {
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;

              //disallow placeholder
              //force enqueing holder js
              add_filter( 'czr_is_one_fp_set', '__return_true' );
            }

            $edit_enabled                   = apply_filters( 'czr_edit_in_fp_title', $edit_enabled );
            $featured_text                  = apply_filters( 'czr_fp_text', $this -> fps_text[$fp_index-1], $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'czr_fp_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );


            //set page excerpt as default text if no $featured_text
            $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
            $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

          //limit text to 200 car
            $default_fp_text_length         = apply_filters( 'czr_fp_text_length', $this->text_length, $fp_single_id, $featured_page_id );
            $text                           = czr_fn_text_truncate( $text, $default_fp_text_length, $more = '...', $strip_tags = false ); //tags already stripped

            if ( $show_thumb ) {
              //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
              $fp_img_size                    = apply_filters( 'czr_fp_img_size' , 'tc-thumb', $fp_single_id, $featured_page_id );
              //allow user to specify a custom image id
              $fp_custom_img_id               = apply_filters( 'fp_img_id', null , $fp_single_id , $featured_page_id );

              $fp_img                         = $this -> czr_fn_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id);

              //we need the holder if not fp_img
              if ( ! $fp_img ) {
                $fp_img                       = $this -> fp_holder_img;
              }

              $fp_img                         = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
            }
        }//end else

        //is the image the holder?
        if ( $fp_img == $this->fp_holder_img ) {
          //force enqueing holder js
          add_filter( 'tc_holder_js_required', '__return_true');
          $thumb_wrapper_class = 'tc-holder';
        }
        //array with fp_button
        $button_block = $this -> czr_fn_setup_button_block( $fp_single_id );

        /* first and last of row */

        $j     = ( $this -> fp_per_row > 1 ) ? $fp_index % $this -> fp_per_row : $fp_index;

        $is_first_of_row = $j == 1;
        $is_last_of_row  = ( $j == 0 || $fp_index == $this -> fp_nb );


        return array(
          'featured_page_id'     => $featured_page_id,
          'featured_page_title'  => $featured_page_title,
          'featured_page_link'   => $featured_page_link,
          'fp_img'               => $fp_img ,
          'text'                 => $text,
          'edit_enabled'         => $edit_enabled,
          'thumb_wrapper_class'  => $thumb_wrapper_class,
          'fp_button_text'       => $button_block[ 'fp_button_text' ],
          'fp_button_class'      => $button_block[ 'fp_button_class' ],
          'fp_id'                => $fp_single_id,
          'is_first_of_row'      => $is_first_of_row,
          'is_last_of_row'       => $is_last_of_row,
          'fp_col'               => $this -> fp_col
        );

  }



  /*
  * Fired just after the view is rendered
  * @hook: post_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_reset_late_properties() {
    reset( $this -> featured_pages );
  }



  function czr_fn_get_featured_page( $autoadvance = true ) {
        $fp = current( $this -> featured_pages );

        if ( empty( $fp ) )
          return false;
        if ( $autoadvance )
          next( $this -> featured_pages );

        /*
        * Allow img parsing
        * E.g. for the smartloading
        * The filter callback is added at wp_head in CZR___::__construct
        */
        if ( is_array($fp) && isset( $fp[ 'fp_img' ] ) ) {
          //this filter is defined is primarily used in czr_fn_get_thumbnail_model()
          $fp[ 'fp_img' ] = apply_filters( 'czr_thumb_html', $fp[ 'fp_img' ], $requested_size = 'tc-thumb', $post_id = null , $custom_thumb_id = null, $_img_attr = null, $tc_thumb_size = null );
        }

        return $fp;
  }


  function czr_fn_setup_button_block( $fp_single_id ) {
        //button block
        $fp_button_text    = call_user_func( '__', apply_filters( 'czr_fp_button_text' , $this -> button_text , $fp_single_id ) );
        $fp_button_class   = '';

        if ( $fp_button_text || czr_fn_is_customizing() ){
          $fp_button_text  = '<span>' . $fp_button_text . '</span>';
          $fp_button_class = $fp_button_text ? '' : ' hidden';
        }
        return compact( 'fp_button_class', 'fp_button_text' );
  }



  /******************************
  * HELPERS
  *******************************/
  function czr_fn_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id ){
        //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
        //czr_fn_get_thumbnail_model defined in core/functions.php
        $_fp_img_model = czr_fn_get_thumbnail_model( array(
            'requested_size' => $fp_img_size,
            'post_id'        => $featured_page_id,
            'thumb_id'       => $fp_custom_img_id
        ));

      //finally we define a default holder if no thumbnail found or page is protected
        if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
          $fp_img = $_fp_img_model["tc_thumb"];
        else
          $fp_img = false;
        return $fp_img;
  }

}