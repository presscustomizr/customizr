<?php
class CZR_featured_pages_model_class extends CZR_Model {

  public $fp_ids;
  public $fp_nb;
  public $fp_per_row;
  public $fp_col;

  public $featured_pages;

  public $current_fp = array();


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {

        $czr_fn_show_featured_pages_img     = $this -> czr_fn_show_featured_pages_img();

        $_skin_color                    = czr_fn_getskincolor();

        $fp_holder_img                  = apply_filters (
              'tc_fp_holder_img' ,
              sprintf('<img class="tc-holder-img" data-src="holder.js/350x350/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" style="width:350px;height:350px;"/>',
                ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
                $_skin_color
              )
        );
        //gets the featured pages array and sets the fp layout
        $fp_ids                         = apply_filters( 'czr_featured_pages_ids' , CZR_init::$instance -> fp_ids );

        $fp_nb                          = count($fp_ids);
        $fp_per_row                     = apply_filters( 'czr_fp_per_line', 3 );

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

        $fp_col         = 4;
        $fp_col         = ( $fp_per_row > 7) ? 1 : $fp_col;

        $this -> fp_col = isset( $cols_map[$fp_per_row] ) ? $cols_map[$fp_per_row] : $fp_col;
        $this -> fp_per_row = $fp_per_row;
        $this -> fp_nb      = $fp_nb;
        $this -> fp_ids     = $fp_ids;

        $this -> featured_pages = $this -> czr_fn_get_the_featured_pages( $fp_nb, $fp_ids, $czr_fn_show_featured_pages_img, $fp_holder_img );

        return $model;
  }


  function czr_fn_get_the_featured_pages( $fp_nb, $fp_ids, $czr_fn_show_featured_pages_img, $fp_holder_img ) {
        $featured_pages = array();

        foreach ( range(0, $fp_nb - 1 ) as $fp_id )
          $featured_pages[ $fp_id + 1 ] = $this -> czr_fn_get_single_fp_model( $fp_ids[$fp_id], $czr_fn_show_featured_pages_img, $fp_holder_img, $fp_id + 1 );

        return $featured_pages;
  }


  function czr_fn_get_single_fp_model( $fp_single_id, $czr_fn_show_featured_pages_img, $fp_holder_img, $fp_index ) {
        $fp_img = '';
        $thumb_wrapper_class = '';

        //if fps are not set
        if ( null == czr_fn_get_opt( 'tc_featured_page_'.$fp_single_id ) || ! czr_fn_get_opt( 'tc_featured_page_'.$fp_single_id ) ) {

        //admin link if user logged in
          $featured_page_link             = '';
          $customizr_link                 = '';

        if ( ! CZR() -> czr_fn_is_customizing() && is_user_logged_in() && current_user_can('edit_theme_options') ) {
            $customizr_link              = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
                czr_fn_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id, 'section' => 'frontpage_sec') ),
                __( 'Customizer screen' , 'customizr' ),
                __( 'Edit now.' , 'customizr' )
            );
            $featured_page_link          = apply_filters( 'czr_fp_link_url', czr_fn_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id, 'section' => 'frontpage_sec') ) );
          }

        //rendering
          $featured_page_id               =  null;
          $featured_page_title            =  apply_filters( 'czr_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
          $text                           =  apply_filters(
                                                  'tc_fp_text',
                                                  sprintf( '%1$s %2$s',
                                                  __( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen.' , 'customizr' ),
                                                   $customizr_link
                                                  ),
                                                  $fp_single_id,
                                                  $featured_page_id
                                              );
          $fp_img                       =  $czr_fn_show_featured_pages_img ? apply_filters ('fp_img_src' , $fp_holder_img, $fp_single_id , $featured_page_id ) : '';
        }
        else {
          $featured_page_id               = apply_filters( 'czr_fp_id', esc_attr( czr_fn_get_opt( 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );
          $featured_page_link             = apply_filters( 'czr_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id );
          $featured_page_title            = apply_filters( 'czr_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );
          $edit_enabled                   = false;

        //when are we displaying the edit link?
          //never display when customizing
          if ( ! CZR() -> czr_fn_is_customizing() ) {
            $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
            $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
          }

          $edit_enabled                   = apply_filters( 'czr_edit_in_fp_title', $edit_enabled );
          $featured_text                  = apply_filters( 'czr_fp_text', czr_fn_get_opt( 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
          $featured_text                  = apply_filters( 'czr_fp_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );

        //get the page/post object
          $page                           = get_post($featured_page_id);
          //set page excerpt as default text if no $featured_text
          $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
          $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

        //limit text to 200 car
          $default_fp_text_length         = apply_filters( 'czr_fp_text_length', 200, $fp_single_id, $featured_page_id );
          $text                           = ( strlen($text) > $default_fp_text_length ) ? substr( $text , 0 , strpos( $text, ' ' , $default_fp_text_length) ). ' ...' : $text;


          if ( $czr_fn_show_featured_pages_img ) {
            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $fp_img_size                    = apply_filters( 'czr_fp_img_size' , 'tc-fp-thumb', $fp_single_id, $featured_page_id );
            //allow user to specify a custom image id
            $fp_custom_img_id               = apply_filters( 'fp_img_id', null , $fp_single_id , $featured_page_id );

            $fp_img                         = $this -> czr_fn_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id);

            //we need the holder if not fp_img
            if ( ! $fp_img ) {
              $fp_img                       = $fp_holder_img;
            }

            $fp_img                         = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
          }
        }//end else

        //is the image the holder?
        if ( $fp_img == $fp_holder_img ) {
          //force enqueing holder js
          add_filter( 'czr_holder_js_required', '__return_true');
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



  function czr_fn_get_featured_page( $autoadvance = true ) {
        $fp = current( $this -> featured_pages );
        if ( empty( $fp ) )
          return false;
        if ( $autoadvance )
          next( $this -> featured_pages );

        return $fp;
  }


  function czr_fn_setup_button_block( $fp_single_id ) {
        //button block
        $fp_button_text = apply_filters( 'czr_fp_button_text' , esc_attr( czr_fn_get_opt( 'tc_featured_page_button_text') ) , $fp_single_id );
        $fp_button_class = '';

        if ( $fp_button_text || CZR() -> czr_fn_is_customizing() ){
          $fp_button_text  = '<span>' . $fp_button_text . '</span>';
          $fp_button_class = apply_filters( 'czr_fp_button_class' , 'btn btn-more', $fp_single_id );
          $fp_button_class = $fp_button_text ? $fp_button_class : $fp_button_class . ' hidden';
        }
        return compact( 'fp_button_class', 'fp_button_text' );
  }



  /******************************
  * HELPERS
  *******************************/
  function czr_fn_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id ){
        //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
        //czr_fn_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
        $_fp_img_model = czr_fn_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_custom_img_id );

      //finally we define a default holder if no thumbnail found or page is protected
        if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
          $fp_img = $_fp_img_model["tc_thumb"];
        else
          $fp_img = false;
        return $fp_img;
  }

  function czr_fn_show_featured_pages_img() {
        //gets  display img option
        return apply_filters( 'czr_show_featured_pages_img', esc_attr( czr_fn_get_opt( 'tc_show_featured_pages_img' ) ) );
  }

}