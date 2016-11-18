<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_post_list_masonry_model_class extends CZR_Model {
  public $excerpt_length;
  public $has_post_media;

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
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $global_sidebar_layout         = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
    $model[ 'element_class']       = czr_fn_get_in_content_width_class();
    $model[ 'has_post_media' ]     = 0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) );

    $this->post_class              = array_merge( self::$default_post_list_layout[$global_sidebar_layout], $this->post_class );

    /*
    * The alternate grid does the same
    */
    add_action( '__masonry_loop_start', array( $this, 'czr_fn_setup_text_hooks') );
    add_action( '__masonry_loop_end'  , array( $this, 'czr_fn_reset_text_hooks') );
    //reset masonry items at loop end? sort of garbage collector
    add_action( '__masonry_loop_end'  , array( $this, 'czr_fn_reset_post_list_items') );

    return $model;
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
    array_push( $this->post_list_items, $this->czr_fn__get_post_list_item() );
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

    $article_selectors       = czr_fn_get_the_post_list_article_selectors( array_filter($post_class) );

    return $article_selectors;
  }


  protected function czr_fn__get_has_post_media( $current_post_format ) {
    return $this->has_post_media && ! $this -> czr_fn__get_has_header_format_icon( $current_post_format );
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
  * Callbacks
  */

  /*
  * Following methods: czr_fn_setup_text_hooks, czr_fn_reset_text_hooks, czr_fn_set_excerpt_length
  * are shared by the post lists classes, do we want to build a common class?
  */

  /**
  * hook : __masonry_loop_start
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks( $model_id ) {
    if ( $model_id == $this->id  )
      //filter the excerpt length
      add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : __masonry_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks( $model_id ) {
    if ( $model_id == $this->id  )
      remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : __masonry_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_post_list_items( $model_id ) {
    if ( $model_id == $this->id  )
      $this -> post_list_items = array();
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length ? $this -> excerpt_length : esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }

}