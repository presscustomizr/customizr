<?php
class TC_featured_pages_model_class extends TC_Model {

  public $fp_ids;
  public $fp_nb;
  public $fp_per_row;
  public $span_value;
  public $featured_pages;


  function __construct( $model ) {
    parent::__construct( $model );
    //hook to its own loop hook to set the current slide query var
    add_action( "in_featured_pages_{$this -> id}", array( $this, 'setup_featured_page_data' ), -100, 2 );
  }

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function tc_extend_params( $model = array() ) {

    $tc_show_featured_pages_img     = $this -> tc_show_featured_pages_img();

    $_skin_color                    = TC_utils::$inst -> tc_get_skin_color();
    $fp_holder_img                  = apply_filters (
          'tc_fp_holder_img' ,
          sprintf('<img class="tc-holder-img" data-src="holder.js/270x250/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" style="width:270px;height:250px;"/>',
            ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
            $_skin_color
          )
    );
    //gets the featured pages array and sets the fp layout
    $fp_ids                         = apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids );

    $fp_nb                          = count($fp_ids);
    $fp_per_row                     = apply_filters( 'tc_fp_per_line', 3 );

	//defines the span class
    $span_array = array(
        1 => 12,
        2 => 6,
        3 => 4,
        4 => 3,
        5 => 2,
        6 => 2,
        7 => 2
    );

    $span_value      = 4;
    $span_value      = ( $fp_per_row > 7) ? 1 : $span_value;
    $span_value      = isset( $span_array[$fp_per_row] ) ? $span_array[$fp_per_row] :  $span_value;

    $featured_pages = $this -> tc_get_the_featured_pages( $fp_nb, $fp_ids, $tc_show_featured_pages_img, $fp_holder_img );

    return array_merge( $model, compact( 'fp_nb', 'fp_per_row', 'span_value', 'featured_pages', 'fp_ids' ) );
  }



  function tc_setup_children() {
    $children = array(
      array(
        'hook'      => '__featured_page__',
        'template'  => 'modules/featured-pages/featured_page',
      ),
    );

    return $children;
  }

  //hook to its own loop hook to set the current slide query var
  function setup_featured_page_data( $index, $featured_page ) {
    $j = ( $this -> fp_per_row > 1 ) ? $index % $this -> fp_per_row : $index;

    set_query_var( 'tc_fp', array(
      'is_first_of_row' => $j == 1,
      'is_last_of_row'  => ( $j == 0 || $index == $this -> fp_nb ),
      'data'            => $featured_page,
      'fp_id'           => $this -> fp_ids[ $index - 1 ],
      'span_value'      => $this -> span_value
    ));
  }


  function tc_get_the_featured_pages( $fp_nb, $fp_ids, $tc_show_featured_pages_img, $fp_holder_img ) {
    $featured_pages = array();

    foreach ( range(0, $fp_nb - 1 ) as $fp_id )
      $featured_pages[ $fp_id + 1 ] = $this -> tc_get_single_fp_model( $fp_ids[$fp_id], $tc_show_featured_pages_img, $fp_holder_img );

    return $featured_pages;
  }


  function tc_get_single_fp_model( $fp_single_id, $tc_show_featured_pages_img, $fp_holder_img ) {
    $fp_img = '';

    //if fps are not set
    if ( null == TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id ) || ! TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id ) ) {

	  //admin link if user logged in
      $featured_page_link             = '';
      $customizr_link                 = '';

	  if ( ! TC___::$instance -> tc_is_customizing() && is_user_logged_in() && current_user_can('edit_theme_options') ) {
        $customizr_link              = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
            TC_utils::tc_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id, 'section' => 'frontpage_sec') ),
            __( 'Customizer screen' , 'customizr' ),
            __( 'Edit now.' , 'customizr' )
        );
        $featured_page_link          = apply_filters( 'tc_fp_link_url', TC_utils::tc_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id, 'section' => 'frontpage_sec') ) );
      }

	  //rendering
      $featured_page_id               =  null;
      $featured_page_title            =  apply_filters( 'tc_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
      $text                           =  apply_filters(
                                              'tc_fp_text',
                                              sprintf( '%1$s %2$s',
                                              __( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen.' , 'customizr' ),
                                               $customizr_link
                                              ),
                                              $fp_single_id,
                                              $featured_page_id
                                          );
      $fp_img                       =  $tc_show_featured_pages_img ? apply_filters ('fp_img_src' , $fp_holder_img, $fp_single_id , $featured_page_id ) : '';
    }
    else {
      $featured_page_id               = apply_filters( 'tc_fp_id', esc_attr( TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );
      $featured_page_link             = apply_filters( 'tc_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id );
      $featured_page_title            = apply_filters( 'tc_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );
      $edit_enabled                   = false;

	  //when are we displaying the edit link?
      //never display when customizing
      if ( ! TC___::$instance -> tc_is_customizing() ) {
        $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
        $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
      }

      $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );
      $featured_text                  = apply_filters( 'tc_fp_text', TC_utils::$inst->tc_opt( 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
      $featured_text                  = apply_filters( 'tc_fp_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );

	  //get the page/post object
      $page                           = get_post($featured_page_id);
      //set page excerpt as default text if no $featured_text
      $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
      $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

	  //limit text to 200 car
      $default_fp_text_length         = apply_filters( 'tc_fp_text_length', 200, $fp_single_id, $featured_page_id );
      $text                           = ( strlen($text) > $default_fp_text_length ) ? substr( $text , 0 , strpos( $text, ' ' , $default_fp_text_length) ). ' ...' : $text;


      if ( $tc_show_featured_pages_img ) {
       	//set the image : uses thumbnail if any then >> the first attached image then >> a holder script
        $fp_img_size                    = apply_filters( 'tc_fp_img_size' , 'tc-thumb', $fp_single_id, $featured_page_id );
        //allow user to specify a custom image id
        $fp_custom_img_id               = apply_filters( 'fp_img_id', null , $fp_single_id , $featured_page_id );

        $fp_img                         = $this -> tc_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id);

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
      add_filter( 'tc_holder_js_required', '__return_true');
      $has_holder                     = true;
    }
    return compact( 'featured_page_id', 'featured_page_title', 'featured_page_link', 'fp_img' , 'text', 'edit_enabled', 'has_holder' );
  }


  /******************************
  * HELPERS
  *******************************/
  function tc_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id ){
    //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
    //tc_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
    $_fp_img_model = TC_utils_thumbnails::$instance -> tc_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_custom_img_id );

	//finally we define a default holder if no thumbnail found or page is protected
    if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
      $fp_img = $_fp_img_model["tc_thumb"];
    else
      $fp_img = false;
    return $fp_img;
  }

  function tc_show_featured_pages_img() {
    //gets  display img option
    return apply_filters( 'tc_show_featured_pages_img', esc_attr( TC_utils::$inst->tc_opt( 'tc_show_featured_pages_img' ) ) );
  }

}
