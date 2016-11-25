<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_post_list_plain_model_class extends CZR_Model {

  public $post_class               = array( 'col-xs-12' );
  public $post_list_items          = array();

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $_preset = array(
      'entry_header_inner_class'  => array( 'col-md-7', 'offset-md-4', 'col-xs-12'),
      'entry_header_class'        => array( 'row' ),
      'content_inner_class'       => array('col-md-7', 'offset-md-1', 'col-xs-12'),
      'show_thumb'                => esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) ),
      'content_width'             => czr_fn_get_in_content_width_class(),
      'excerpt_length'            => esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) ),
      'contained'                 => false
    );

    return $_preset;
  }


  /**
  * add custom classes to the masonry container element
  */
  function czr_fn_get_element_class() {
    $_classes = is_array($this->content_width) ? $this->content_width : array();

    if ( ! empty( $this->contained ) )
      array_push( $_classes, 'container' );
    return $_classes;
  }
  /*
  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this -> id}, 9999
  */
  /*
  * Each time this model view is rendered setup the current post list item
  * and add it to the post_list_items_array
  */
  function czr_fn_setup_late_properties() {
    //all post lists do this
    if ( czr_fn_is_loop_start() )
      $this -> czr_fn_setup_text_hooks();
    array_push( $this->post_list_items, $this->czr_fn__get_post_list_item() );
  }


  /*
  * Fired just before the view is rendered
  * @hook: post_rendering_view_{$this -> id}, 9999
  */
  function czr_fn_reset_late_properties() {
    if ( czr_fn_is_loop_end() ) {
      //all post lists do this
      $this -> czr_fn_reset_text_hooks();
      //reset alternate items at loop end
      $this -> czr_fn_reset_post_list_items();
    }
  }


  /*
  *  Public getters
  */
  function czr_fn_get_article_selectors() {
    return $this -> czr_fn__get_post_list_item_property( 'article_selectors' );
  }

  function czr_fn_get_has_post_media() {
    return $this -> czr_fn__get_post_list_item_property( 'has_post_media' );
  }



  /*
  * Private/protected getters
  */

  /*
  *  Method to compute the properties of the current (in a loop) post list item
  *  @return array
  */
  protected function czr_fn__get_post_list_item() {
    $current_post_format    = get_post_format();
    $has_post_media         = $this -> czr_fn__get_has_post_media( $current_post_format );
    $article_selectors      = $this -> czr_fn__get_article_selectors( $has_post_media );

    return array(
      'article_selectors'      => $article_selectors,
      'has_post_media'         => $has_post_media
    );
  }

  /*
  * Very similar to the one in the alternate...
  * probably the no-thumb/no-text should be ported somewhere else (in czr_fn_get_the_post_list_article_selectors maybe)
  */
  protected function czr_fn__get_article_selectors( $has_post_media ) {
    $post_class                = $this->post_class;

    /*
    * Using the excerpt filter here can cause some compatibility issues
    * See: Super Socializer plugin
    */
    $_has_excerpt            = (bool) apply_filters( 'the_excerpt', get_the_excerpt() );

    array_push( $post_class, ! $_has_excerpt ? 'no-text' : '',  ! $has_post_media ? 'no-thumb' : '' );

    return czr_fn_get_the_post_list_article_selectors( array_filter( $post_class ) );

  }

  protected function czr_fn__get_has_post_media( $post_format ) {
    if ( ! $this->show_thumb )
      return false;

    if ( in_array( $post_format, array( 'gallery', 'image', 'audio', 'video' ) ) )
      return true;

    if ( in_array( $post_format, array( 'quote', 'link', 'status', 'aside' ) ) )
      return false;

    return czr_fn_has_thumb();

  }


  protected function czr_fn__get_post_list_item_property( $_property ) {
    if ( ! $_property )
      return;
    $_properties = end( $this->post_list_items );
    return isset( $_properties[ $_property ] ) ? $_properties[ $_property ] : null;
  }



  /* HELPERS AND CALLBACKS */

  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks() {
    //filter the excerpt length
    add_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
    add_filter( 'excerpt_more'       , array( $this , 'czr_fn_set_excerpt_more') , 99999999 );
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks() {
    remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
    remove_filter( 'excerpt_more'       , array( $this , 'czr_fn_set_excerpt_more') , 99999999 );
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length;
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /*
  * Replaces the excerpt "Read More" text by a button link
  * hook : excerpt_more
  * @return string
  * @package Customizr
  * @since Customizr 4.0.0
  */
  function czr_fn_set_excerpt_more($more) {
    ob_start();
      czr_fn_render_template( 'modules/read_more' );
      $readmore = ob_get_contents();
    ob_end_clean();
    return $more . $readmore;
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_post_list_items() {
    $this -> post_list_items = array();
  }

}