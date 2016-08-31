<?php
class CZR_cl_post_list_wrapper_model_class extends CZR_cl_Model {
  public $place_1 ;
  public $place_2 ;
  public $article_selectors;

  public $czr_has_post_media;

  public $czr_media_col;
  public $czr_content_col;

  public $czr_show_excerpt;

  public $post_class = 'row';

  public $is_loop_start;
  public $is_loop_end;

  public $sections_wrapper_class;

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
    $model[ 'post_list_layout' ]  = $this -> czr_fn_get_the_post_list_layout();
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

    $_layout = apply_filters( 'czr_post_list_layout', $this -> post_list_layout );
    $_current_post_format = get_post_format( $wp_query -> current_post );
    $_section_wrapper_class = '';

    $czr_has_post_media   = false;
    $this -> place_1      = 'content';
    $this -> place_2      = 'media';

    if ( $this -> czr_fn_show_media() ) {
      $czr_has_post_media = true;

      /* In the new alternate layout video takes more space when global layout has less than 2 sidebars */
      if ( in_array( $_current_post_format , apply_filters( 'czr_alternate_media_post_formats', array( 'video' ) ) ) ) {
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
    if (  $_layout[ 'alternate' ] && ( ( $wp_query -> current_post + (int) $_layout[ 'show_thumb_first' ] ) % 2 ) ||
          $_layout[ 'show_thumb_first' ] && ! $_layout[ 'alternate' ] ) {
      $this -> place_1 = 'media';
      $this -> place_2 = 'content';
    }
 
    if ( ! in_array ( $_layout['position'], array( 'top', 'bottom') ) )
      array_push( $_layout[ $this -> place_2 ], 'offset-md-1' );

    //$post_class           = $czr_has_post_media ? array_merge( array($this -> post_class), $this -> czr_fn_get_thumb_shape_name() ) : $this -> post_class;
    $article_selectors    = czr_fn_get_the_post_list_article_selectors( $this -> post_class );

    if (  ! in_array( $_current_post_format , array( 'image', 'gallery' ) ) ) {
      $_to_center = ( 'image' == $_current_post_format && is_null( get_the_content() ) ) || 'gallery' == $_current_post_format ? false :  true ;
      $_sections_wrapper_class = apply_filters( 'czr_alternate_sections_centering', $_to_center) ? 'czr-center-sections' : '';
    }

    $this -> czr_fn_update( array(
      'czr_media_col'          => $_layout[ 'media' ],
      'czr_content_col'        => $_layout[ 'content' ],
      'czr_show_excerpt'       => $this -> czr_fn_show_excerpt(),
      'czr_has_post_media'     => $czr_has_post_media,
      'article_selectors'      => $article_selectors,
      'is_loop_start'          => 0 == $wp_query -> current_post,
      'is_loop_end'            => $wp_query -> current_post == $wp_query -> post_count -1,
      'sections_wrapper_class' => $_sections_wrapper_class
    ) );

  }

  /**
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_get_the_post_list_layout() {
    $_layout                     = self::$default_post_list_layout;

    $_layout['position']         = esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_position' ) );
    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ( 0 == esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_alternate' ) )
                                   || in_array( $_layout['position'] , array( 'top', 'bottom') ) ) ? false : true;
    $_layout['show_thumb_first'] = in_array( $_layout['position'] , array( 'top', 'left') ) ? true : false;
    $_layout['content']          = ! in_array( $_layout['position'] , array( 'top', 'bottom') ) ? $_layout['content'] : array( 'col-xs-12' );
    $_layout['media']            = in_array( $_layout['position'] , array( 'top', 'bottom') ) ? array( 'col-xs-12' ) : $_layout['media'];

    return $_layout;
  }


  /**
  *
  * @return  array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_get_thumb_shape_name() {
    $position                    = esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_position' ) );
    $thumb_shape                 = esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_shape') );

    $_class                      = array( "thumb-position-{$position}", $thumb_shape);
    return $_class;
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
    return apply_filters( 'czr_show_media', array_product(
        array(
          $this -> czr_fn_show_excerpt(),
          czr_fn_has_thumb(), /* CHANGE TO HAS MEDIA */
          0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) )
        )
      )
    );
  }

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