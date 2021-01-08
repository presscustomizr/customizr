<?php
class CZR_post_list_plain_model_class extends CZR_Model {

  public $post_class               = array( 'col-12', 'grid-item' );
  public $post_list_items          = array();

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model preset array()
  */
  function czr_fn_get_preset_model() {
    $_preset = array(
      'show_thumb'                => esc_attr( czr_fn_opt( 'tc_post_list_show_thumb' ) ),
      'content_wrapper_breadth'   => czr_fn_get_content_breadth(),
      'excerpt_length'            => esc_attr( czr_fn_opt( 'tc_post_list_excerpt_length' ) ),
      'show_full_content'         => true, //false for post list plain excerpt
      'contained'                 => false,
      'split_layout'              => true,// czr_fn_is_checked( 'tc_post_list_plain_split_layout' ), //whether display TAX | CONTENT (horiz) or TAX/CONTENT (vertical)
      'wrapped'                   => true,
    );

    return $_preset;
  }

  /**
  * @override
  * fired before the model properties are parsed becoming model properties
  *
  * At this stage the preset model has already been parsed into the $model array passed to the constructor
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
      // merge with args.
      $model                              = parent::czr_fn_extend_params( $model );

      $model[ 'content_wrapper_breadth' ] = in_array( $model[ 'content_wrapper_breadth' ], array( 'full', 'semi-narrow', 'narrow' ) ) ?
            $model[ 'content_wrapper_breadth' ] : 'full';

      // force categories to be shown below the title in post list plain full content.
      $model[ 'split_layout' ] = $model[ 'show_full_content' ] ? false : $model[ 'split_layout' ];

      return $model;
  }

  /**
  * add custom classes to the masonry container element
  */
  function czr_fn_get_element_class() {
    $_classes = $this->content_wrapper_breadth ? array( $this->content_wrapper_breadth ) : array();

    if ( $this->split_layout && 'narrow' != $this->content_wrapper_breadth )
      $_classes[] = 'split';

    if ( ! empty( $this->contained ) )
      $_classes[] = 'container';

    return $_classes;
  }
  /*
  * Fired just before the view is rendered
  * @hook: pre_rendering_view_{$this->id}, 9999
  */
  /*
  * Each time this model view is rendered setup the current post list item
  * and add it to the post_list_items_array
  */
  function czr_fn_setup_late_properties() {
      // all post lists do this.
      if ( ! $this->show_full_content && czr_fn_is_loop_start() )
          $this->czr_fn_setup_text_hooks();

      $this->post_list_items[] = $this->czr_fn__get_post_list_item();
  }


  /*
  * Fired just before the view is rendered
  * @hook: post_rendering_view_{$this->id}, 9999
  */
  function czr_fn_reset_late_properties() {
    if ( czr_fn_is_loop_end() ) {
      if ( ! $this->show_full_content )
        // all post lists do this.
        $this->czr_fn_reset_text_hooks();

      // reset alternate items at loop end.
      $this->czr_fn_reset_post_list_items();
    }
  }


  /*
  *  Public getters
  */
  function czr_fn_get_article_selectors() {
    return $this->czr_fn__get_post_list_item_property( 'article_selectors' );
  }

  function czr_fn_get_cat_list() {
    return $this->czr_fn__get_post_list_item_property( 'cat_list' );
  }

  function czr_fn_get_cat_list_class() {
    return $this->czr_fn__get_post_list_item_property( 'cat_list_class' );
  }

  function czr_fn_get_entry_header_inner_class() {
    return $this->czr_fn__get_post_list_item_property( 'entry_header_inner_class' );
  }

  function czr_fn_get_content_inner_class() {
    return $this->czr_fn__get_post_list_item_property( 'content_inner_class' );
  }

  function czr_fn_get_media_class() {
    return $this->czr_fn__get_post_list_item_property( 'media_class' );
  }

  function czr_fn_get_has_post_media() {
    return $this->czr_fn__get_post_list_item_property( 'has_post_media' );
  }

  function czr_fn_get_print_start_wrapper() {
    return $this->wrapped && czr_fn_is_loop_start();
  }

  function czr_fn_get_print_end_wrapper() {
    return $this->wrapped && czr_fn_is_loop_end();
  }

  /*
  * Private/protected getters
  */

  /*
  *  Method to compute the properties of the current (in a loop) post list item
  *  @return array
  */
  protected function czr_fn__get_post_list_item() {
    $current_post_format         = in_the_loop() ? get_post_format() : '';

    /* retrieve category list */
    $cat_list                    = $this->czr_fn__get_cat_list();

    /* Build inner elements classes */
    $cat_list_class = $entry_header_inner_class = $content_inner_class = array( 'col-12' );

    // split layout.
    if ( $this->split_layout && $cat_list && 'narrow' != $this->content_wrapper_breadth ) {
      $bp                  = 'narrow' == $this->content_wrapper_breadth ? 'lg' : 'xl';
      $cat_list_text_align = is_rtl() ? 'left' : 'right';
      /* the header inner class (width) depends on the presence of the category list */
      array_push( $entry_header_inner_class, "col-{$bp}-7", "offset-{$bp}-4" );
      /* the content inner class (width) depends on the presence of the category list */
      array_push( $content_inner_class, "col-{$bp}-7", "offset-{$bp}-1" );
      array_push( $cat_list_class, "col-{$bp}-3 text-{$bp}-{$cat_list_text_align}" );
    }

    $article_selectors           = $this->czr_fn__get_article_selectors( $cat_list );

    // add the aspect ratio class for all media types (except audio ).
    // $media_class                 = 'audio' == $current_post_format ? '' : 'czr__r-w16by9';

    // we decided to show the original featured image.
    $media_class = '';

    return array(
        // add the aspect ratio class for all images types.
        'media_class'              => $media_class,
        'article_selectors'        => $article_selectors,
        'cat_list'                 => $cat_list,
        'cat_list_class'           => $cat_list_class,
        'entry_header_inner_class' => $entry_header_inner_class,
        'content_inner_class'      => $content_inner_class,
        'has_post_media'           => $this->show_thumb
    );

  }

  /*
  * Get the category list
  */
  protected function czr_fn__get_cat_list() {
    /* Post list plain showing excerpts limits the category to show to 3 */
    $cat_list                  = ! $this->show_full_content ? czr_fn_get_property( 'cat_list', 'post_metas',  array( 'limit' => 3 ) ) : czr_fn_get_property( 'cat_list', 'post_metas');
    return $cat_list;
  }


  /*
  * Very similar to the one in the alternate...
  */
  protected function czr_fn__get_article_selectors( $cat_list ) {
    $post_class                = $this->post_class;


    array_push( $post_class,
      ! $cat_list       ? 'no-cat-list' : ''
    );

    $id_suffix = is_main_query() ? '' : "_{$this->id}";

    return czr_fn_get_the_post_list_article_selectors( array_filter($post_class), $id_suffix );
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
    $_custom = $this->excerpt_length;
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
    return $more . czr_fn_readmore_button();
  }


  /**
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_post_list_items() {
    $this->post_list_items = array();
  }

}
