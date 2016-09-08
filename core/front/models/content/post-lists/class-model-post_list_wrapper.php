<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_cl_post_list_wrapper_model_class extends CZR_cl_Model {
  public $element_class         = array( 'grid-container__alternate' );
  public $post_class            = 'row';
  public $article_selectors;
  public $sections_wrapper_class;

  public $has_format_icon_media;
  public $has_post_media;
  public $has_narrow_layout;
  public $is_full_image;

  public $place_1 ;
  public $place_2 ;

  public $czr_media_col;
  public $czr_content_col;

  public $czr_show_excerpt;

  public $is_loop_start;
  public $is_loop_end;

  //Default post list layout
  private static $default_post_list_layout   = array(
            'content'           => array('col-md-7', 'col-xs-12'),
            'media'             => array('col-md-4', 'col-xs-12'),
            'show_thumb_first'  => false,
            'alternate'         => true
          );

  public $post_list_layout;


  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $global_sidebar_layout                 = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );

    switch ( $global_sidebar_layout ) {
      case 'b': $_class = 'narrow';
                break;
      case 'f': $_class = '';
                break;
      default : $_class = 'semi-narrow';                
    }
    
    $model[ 'element_class']          = array_merge( $this -> element_class, array($_class) );
    $model[ 'has_narrow_layout' ]     = 'b' == $global_sidebar_layout;
    $model[ 'post_list_layout' ]      = $this -> czr_fn_get_the_post_list_layout( $model[ 'has_narrow_layout' ] );
    $model[ 'has_format_icon_media' ] = ! $model[ 'has_narrow_layout' ];

    return $model;
  }


  function czr_fn_setup_children() {

    $children = array (
      /* THUMBS */
   /*   array(
        'id'          => 'post_list_standard_thumb',
        'model_class' => 'content/post-lists/thumbnail'
      ),
      //the recangular thumb has a different model + a slighty different template
      array(
        'id'          => 'post_list_rectangular_thumb',
        'model_class' => array( 'parent' => 'content/post-lists/thumbnail', 'name' => 'content/post-lists/thumbnail_rectangular')
      ),
      //Post/page headings
      array(
        'id' => 'post_page_headings',
        'model_class' => 'content/singles/post_page_headings'
      ),

    */);

    return $children;
  }


  function czr_fn_setup_late_properties() {

    global $wp_query;

    $_layout                 = apply_filters( 'czr_post_list_layout', $this -> post_list_layout );
    $maybe_center_sections   = apply_filters( 'czr_alternate_sections_centering', true );
    $_sections_wrapper_class = '';
    $has_post_media          = $this -> czr_fn_show_media() ;
    $is_full_image           = false; /* gallery and image (with no text) post formats */

    $_current_post_format    = get_post_format();

    /* In the new theme the places are defined just by the option show_thumb_first, 
    * we handle the alternate with bootstrap classes
    *
    */
    $this -> place_1          = 'show_thumb_first' == $_layout['show_thumb_first'] ? 'media' : 'content';
    $this -> place_2          = 'show_thumb_first' == $_layout['show_thumb_first'] ? 'content' : 'media';
    
    $place_2                  = $this -> place_2;


    if ( $has_post_media ) {
      /* In the new alternate layout video takes more space when global layout has less than 2 sidebars */
      if ( in_array( $_current_post_format , apply_filters( 'czr_alternate_big_media_post_formats', array( 'video' ) ) ) 
          && ! $this->has_narrow_layout ) {
        $_t_l                    = $_layout[ 'media' ];
        $_layout[ 'media' ]      = $_layout[ 'content' ];
        $_layout[ 'content' ]    = $_t_l;
      }
    }

    // conditions to show the thumb first are:
    // a) alternate on
    //   a.1) position is left/top ( show_thumb_first true == 1 ) and current post number is odd (1,3,..)
    //       current_post starts by 0, hence current_post + show_thumb_first = 1..2..3.. -> so mod % 2 == 1, 0, 1 ...
    //    or
    //   a.2) position is right/bottom ( show_thumb_first false == 0 ) and current post number is even (2,4,...)
    //       current_post starts by 0, hence current_post + show_thumb_first = 0..1..2.. -> so mod % 2 == 0, 1, 0...
    //  b) alternate off & position is left/top ( show_thumb_first == true == 1)
    
    /*
    * With the new system, change the alternate condition (added a not for the moment, the condition explained above was valid with the previous system)
    */
    if ( ! (  $_layout[ 'alternate' ] && ( ( $wp_query -> current_post + (int) $_layout[ 'show_thumb_first' ] ) % 2 ) ||
          $_layout[ 'show_thumb_first' ] && ! $_layout[ 'alternate' ] ) ) {

      //make it dynamic!! what if col-md- changes???
      $place_2  = $this -> place_1;
      $_layout[ $place_2 ] = array_merge( $_layout[ $place_2 ], str_replace( 'col-md-', 'push-md-', $_layout[ $this -> place_2 ] ) );

      $cols = ( substr(implode($_layout[$place_2]), strpos( implode($_layout[$place_2]), 'push-md-' ) + strlen('push-md-') , 1) );
      $cols = 12 - $cols; /* offset */
      array_push( $_layout[ $this -> place_2 ], 'pull-md-'.$cols );      
    }
 
    if ( ! in_array ( $_layout['position'], array( 'top', 'bottom') ) )
      array_push( $_layout[ $place_2 ], 'offset-md-1' );


    /* 
    * Gallery and images (with no text) should
    * - not be vertically centered
    * - be displayed in full-width
    * - prevent the media-content alternation
    */
    if ( in_array( $_current_post_format , array( 'gallery', 'image' ) ) ) {

      if ( 'image' != $_current_post_format ||
            ( 'image' == $_current_post_format && ! apply_filters( 'the_excerpt', get_the_excerpt() ) ) ) {
        $_sections_wrapper_class = apply_filters( 'czr_alternate_sections_centering', true) ? 'czr-no-text' : '';

        array_push( $this->post_class, 'czr-no-text' );

        $is_full_image         = true;
      
        if ( ! $this->has_narrow_layout ) {
          $_layout[ 'content' ]  = $_layout[ 'media' ]    = array( 'col-xs-12' );
        }

        $this -> place_1 = 'media';
        $this -> place_2 = 'content';
      } elseif ( ! $this->has_narrow_layout && 'image' == $_current_post_format )
        $_sections_wrapper_class = $maybe_center_sections ? 'czr-center-sections' : '';
        
    
    }elseif ( ! $this->has_narrow_layout )
      $_sections_wrapper_class = $maybe_center_sections ? 'czr-center-sections' : '';
    

    /*
    * Find a way to avoid the no-thumb here and delegate to the thumb wrapper?
    */
    $post_class           = ! $has_post_media ? array_merge( array($this -> post_class), array('no-thumb') ) : $this -> post_class;
    $article_selectors    = czr_fn_get_the_post_list_article_selectors( $post_class );

    $this -> czr_fn_update( array(
      'czr_media_col'          => $_layout[ 'media' ],
      'czr_content_col'        => $_layout[ 'content' ],
      'czr_show_excerpt'       => $this -> czr_fn_show_excerpt(),
      'has_post_media'         => $has_post_media,
      'article_selectors'      => $article_selectors,
      'is_loop_start'          => 0 == $wp_query -> current_post,
      'is_loop_end'            => $wp_query -> current_post == $wp_query -> post_count -1,
      'sections_wrapper_class' => $_sections_wrapper_class,
      'is_full_image'          => $is_full_image
    ) );

  }

  /**
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_get_the_post_list_layout( $narrow_layout = false ) {
    
    $_layout                     = self::$default_post_list_layout;

    $_layout[ 'position' ]       = esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_position' ) );
    $_layout['show_thumb_first'] = in_array( $_layout['position'] , array( 'top', 'left') );

    //since 4.5 top/bottom positions will not be optional but will be forced in narrow layouts
    if ( $narrow_layout )
      $_layout['position']       = $_layout[ 'show_thumb_first' ] ? 'top' : 'bottom';
    else {      
      if ( 'top' == $_layout[ 'position' ] )
        $_layout[ 'position' ] = 'left';
      elseif ( 'bottom' == $_layout[ 'position' ] )
        $_layout[ 'position' ] = 'right';
    } 

    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ! ( 0 == esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_alternate' ) ) || in_array( $_layout['position'] , array( 'top', 'bottom') ) );

    if ( in_array( $_layout['position'] , array( 'top', 'bottom') ) )
      $_layout['content'] = $_layout['media'] = array( 'col-xs-12', 'col-md-10' );

    return $_layout;
  }


  /**
  * hook : body_class
  * @return  array of classes
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  function czr_fn_body_class( $_class ) {
    array_push( $_class , 'czr-post-list-context');
    return $_class;
  }


  /* Following are here to allow to apply a filter on each loop ..
  *  but we can think about move them in another place if we decide
  *  the users MUST act only modifying models/templates
  *
  *  Actually they can be moved in another place anyway, but they are pretty specific of the "alternate" post list
  */
  /* HELPERS */
  /**
  * @return boolean
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function czr_fn_show_media() {
    //when do we display the thumbnail ?
    //1) there must be a thumbnail
    //2) the excerpt option is not set to full
    //3) user settings in customizer
    //4) filter's conditions
    return apply_filters( 'czr_show_media', 
          $this -> czr_fn_show_excerpt() &&
          ! in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside' ) ) ) &&
          czr_fn_has_thumb() &&
          0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) )
    );
  }

  /*
  * Todo: treat in a different model(/template ?!)
  *
  */
  /**
  * @return boolean whether excerpt instead of full content
  * @package Customizr
  * @since Customizr 3.3.2 
  */
  private function czr_fn_show_excerpt() {
    //When do we show the post excerpt?
    //1) when set in options
    //2) + other filters conditions
    return (bool) apply_filters( 'czr_show_excerpt', 'full' != esc_attr( czr_fn_get_opt( 'tc_post_list_length' ) ) );
  }
}