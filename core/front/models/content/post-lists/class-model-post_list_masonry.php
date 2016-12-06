<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_post_list_masonry_model_class extends CZR_Model {
  //Default post list layout
  private static $default_post_list_layout   = array(
            'b'         => array('col-xs-12'),
            'f'         => array('col-xs-12', 'col-md-6', 'col-lg-4'),
            'l'         => array('col-xs-12', 'col-md-6'),
            'r'         => array('col-xs-12', 'col-md-6')
          );
  public $post_class    = array( 'grid-item' );

  public $post_list_items = array();

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $_preset = array(
      'excerpt_length'   => esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) ),
      'show_thumb'       => esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) ),
      'content_width'    => czr_fn_get_in_content_width_class(),
      'contained'        => false
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
    $model                         = parent::czr_fn_extend_params( $model );

    $global_sidebar_layout         = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
    $this->post_class              = array_merge( self::$default_post_list_layout[$global_sidebar_layout], $this->post_class );

    return $model;
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

  function czr_fn_get_is_full_image() {
    return $this -> czr_fn__get_post_list_item_property( 'is_full_image' );
  }

  function czr_fn_get_has_header_format_icon() {
    return $this -> czr_fn__get_post_list_item_property( 'has_header_format_icon' );
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
    $is_full_image          = $this -> czr_fn__get_is_full_image( $current_post_format );
    $has_post_media         = $this -> czr_fn__get_has_post_media( $current_post_format );
    $has_header_format_icon = $this -> czr_fn__get_has_header_format_icon( $current_post_format );
    $article_selectors      = $this -> czr_fn__get_article_selectors( $is_full_image, $has_post_media );

    return array(
      'article_selectors'      => $article_selectors,
      'has_post_media'         => $has_post_media,
      'is_full_image'          => $is_full_image,
      'has_header_format_icon' => $has_header_format_icon
    );
  }

  protected function czr_fn__get_post_list_item_property( $_property ) {
    if ( ! $_property )
      return;
    $_properties = end( $this->post_list_items );
    return isset( $_properties[ $_property ] ) ? $_properties[ $_property ] : null;
  }

  /*
  * Very similar to the one in the alternate...
  * probably the no-thumb/no-text should be ported somewhere else (in czr_fn_get_the_post_list_article_selectors maybe)
  */
  protected function czr_fn__get_article_selectors( $is_full_image, $has_post_media ) {
    $post_class                = $this -> post_class;

    /* Extend article selectors with info about the presence of an excerpt and/or thumb */
    array_push( $post_class,
      $is_full_image && $has_post_media ? 'full-image' : '',
      /* Find a different solution for the one below, needed just for some icon alignment*/
      $has_post_media ? 'has-thumb' : 'no-thumb'
    );

    $id_suffix               = is_main_query() ? '' : "_{$this -> id}";

    return czr_fn_get_the_post_list_article_selectors( array_filter($post_class), $id_suffix );
  }


  protected function czr_fn__get_has_post_media( $current_post_format ) {
    return $this->show_thumb && ! $this -> czr_fn__get_has_header_format_icon( $current_post_format );
  }

  /*
  * We decided that in masonry all the images (even those with text) should be displayed like the gallery
  */
  protected function czr_fn__get_is_full_image( $current_post_format ) {
    return in_array(  $current_post_format  , array( 'gallery', 'image' ) );
  }


  protected function czr_fn__get_has_header_format_icon( $current_post_format ){
    return in_array(  $current_post_format  , array( 'quote', 'link', 'status', 'aside', 'chat' ) );
  }

  /* HELPERS AND CALLBACKS */

  /*
  * Following methods: czr_fn_setup_text_hooks, czr_fn_reset_text_hooks, czr_fn_set_excerpt_length
  * are shared by the post lists classes, do we want to build a common class?
  */

  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks() {
    //filter the excerpt length
    add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks() {
    remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_post_list_items() {
    $this -> post_list_items = array();
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

}