<?php
class CZR_cl_post_list_wrapper_model_class extends CZR_cl_Model {
  public $place_1 ;
  public $place_2 ;
  public $article_selectors;

  public $czr_fn_has_post_thumbnail;
  public $tc_thumbnail_width;

  public $tc_content_width;
  public $tc_show_excerpt;

  public $post_class = 'row-fluid';
  //Default post list layout
  private static $default_post_list_layout   = array(
            'content'           => 'span8',
            'thumb'             => 'span4',
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
      array(
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

    );

    return $children;
  }


  function czr_fn_setup_late_properties() {

    global $wp_query;

    extract( apply_filters( 'czr_post_list_layout', $this -> post_list_layout ) );


    $czr_fn_has_post_thumbnail   = false;
    $this -> place_1      = 'content';
    $this -> place_2      = 'thumb';

    if ( $this -> czr_fn_show_thumb() ) {
       // conditions to show the thumb first are:
       // a) alternate on
      //   a.1) position is left/top ( show_thumb_first true == 1 ) and current post number is odd (1,3,..)
      //       current_post starts by 0, hence current_post + show_thumb_first = 1..2..3.. -> so mod % 2 == 1, 0, 1 ...
      //    or
      //   a.2) position is right/bottom ( show_thumb_first false == 0 ) and current post number is even (2,4,...)
      //       current_post starts by 0, hence current_post + show_thumb_first = 0..1..2.. -> so mod % 2 == 0, 1, 0...
      //  b) alternate off & position is left/top ( show_thumb_first == true == 1)
      if (  $alternate && ( ( $wp_query -> current_post + (int) $show_thumb_first ) % 2 ) ||
            $show_thumb_first && ! $alternate ) {
        $this -> place_1 = 'thumb';
        $this -> place_2 = 'content';
      }
      $czr_fn_has_post_thumbnail = true;
    }

    $tc_content_width     = $this -> czr_fn_show_thumb() ? $content : 'span12';
    $tc_show_excerpt      = $this -> czr_fn_show_excerpt();
    $tc_thumbnail_width   = $thumb;

    $post_class           = $czr_fn_has_post_thumbnail ? array_merge( array($this -> post_class), $this -> czr_fn_get_thumb_shape_name() ) : $this -> post_class;
    $article_selectors    = czr_fn_get_the_post_list_article_selectors( $post_class );

    $this -> czr_fn_update( compact( 'tc_content_width', 'tc_show_excerpt', 'tc_thumbnail_width', 'czr_fn_has_post_thumbnail', 'article_selectors' ) );
  }

  /**
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_get_the_post_list_layout() {
    $_layout                     = self::$default_post_list_layout;
    $_position                   = esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_position' ) );
    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ( 0 == esc_attr( czr_fn_get_opt( 'tc_post_list_thumb_alternate' ) )
                                   || in_array( $_position, array( 'top', 'bottom') ) ) ? false : true;
    $_layout['show_thumb_first'] = ( 'left' == $_position || 'top' == $_position ) ? true : false;
    $_layout['content']          = ( 'left' == $_position || 'right' == $_position ) ? $_layout['content'] : 'span12';
    $_layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $_layout['thumb'];
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
    array_push( $_class , 'tc-post-list-context');
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
  private function czr_fn_show_thumb() {
    //when do we display the thumbnail ?
    //1) there must be a thumbnail
    //2) the excerpt option is not set to full
    //3) user settings in customizer
    //4) filter's conditions
    return apply_filters( 'czr_show_thumb', array_product(
        array(
          $this -> czr_fn_show_excerpt(),
          czr_fn_has_thumb(),
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
