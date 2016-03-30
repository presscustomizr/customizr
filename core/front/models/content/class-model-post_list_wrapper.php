<?php
class TC_post_list_wrapper_model_class extends TC_article_model_class {
  public $place_1 ;
  public $place_2 ;

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
  function tc_extend_params( $model = array() ) {
    $model[ 'post_list_layout' ]  = $this -> tc_get_the_post_list_layout();
    return $model;
  }


  function tc_setup_children() {

    $children = array (
      /* CONTENT */
      //post content/excerpt
      array( 'hook' => '__post_list_content__', 'template' => 'content/post_list_content', 'id' => 'content' ),
        //post headings in post lists
        array( 'hook' => 'before_render_view_inner_content', 'template' => 'content/headings', 'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post_page_headings' ) ),

      /* THUMBS */
      array(
        'hook'        => '__post_list_thumb__',
        'template'    => 'content/post_list_thumbnail',
        'id'          => 'post_list_standard_thumb',
        'model_class' => 'content/thumbnail'
      ),

      //the recangular thumb has a different model + a slighty different template
      array(
        'hook'        => '__post_list_thumb__',
        'template'    => 'content/post_list_thumbnail',
        'id'          => 'post_list_rectangular_thumb',
        'model_class' => array( 'parent' => 'content/thumbnail', 'name' => 'content/thumbnail_rectangular')
      )
    );

    return $children;
  }

  function tc_setup_late_properties() {
    parent::tc_setup_late_properties();
    global $wp_query;

    extract( apply_filters( 'tc_post_list_layout', $this -> post_list_layout ) );


    $has_post_thumbnail   = false;
    $this -> place_1      = 'content';
    $this -> place_2      = 'thumb';

    if ( $this -> tc_show_thumb() ) {
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
      $has_post_thumbnail = true;
    }

    set_query_var( 'tc_has_post_thumbnail', $has_post_thumbnail );
    set_query_var( 'tc_content_width'     , $this -> tc_show_thumb() ? $content : 'span12' );
    set_query_var( 'tc_thumbnail_width'   , $thumb );
    set_query_var( 'tc_show_excerpt'      , $this -> tc_show_excerpt() );
  }

  /**
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function tc_get_the_post_list_layout() {
    $_layout                     = self::$default_post_list_layout;
    $_position                   = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_position' ) );
    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_alternate' ) )
                                   || in_array( $_position, array( 'top', 'bottom') ) ) ? false : true;
    $_layout['show_thumb_first'] = ( 'left' == $_position || 'top' == $_position ) ? true : false;
    $_layout['content']          = ( 'left' == $_position || 'right' == $_position ) ? $_layout['content'] : 'span12';
    $_layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $_layout['thumb'];
    return $_layout;
  }

  /**
  * hook : body_class
  * @return  array of classes
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  function tc_body_class( $_class ) {
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
  private function tc_show_thumb() {
    //when do we display the thumbnail ?
    //1) there must be a thumbnail
    //2) the excerpt option is not set to full
    //3) user settings in customizer
    //4) filter's conditions
    return apply_filters( 'tc_show_thumb', array_product(
        array(
          $this -> tc_show_excerpt(),
          TC_utils_thumbnails::$instance -> tc_has_thumb(),
          0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) )
        )
      )
    );
  }

  /**
  * @return boolean whether excerpt instead of full content
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function tc_show_excerpt() {
    //When do we show the post excerpt?
    //1) when set in options
    //2) + other filters conditions
    return (bool) apply_filters( 'tc_show_excerpt', 'full' != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_length' ) ) );
  }
}
