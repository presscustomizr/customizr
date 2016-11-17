<?php
class CZR_post_list_alternate_model_class extends CZR_Model {
  public $has_post_media;

  public $def_place_1;
  public $def_place_2;

  public $excerpt_length;

  //Default post list layout
  private static $default_post_list_layout   = array (
                                // array( xl, lg, md, sm, xs )
            'content'           => array( '', '', '8', '', '12'),
            'media'             => array( '', '', '4', '', '12'),
            'narrow_both'       => array( '', '', '', '', '12'),
            'show_thumb_first'  => false,
            'alternate'         => true,
          );
  private static $post_class    = array( 'row' );
  private static $_col_bp = array(
      'xl', 'lg', 'md', 'sm', 'xs'
    );

  public $post_list_layout;

  public $post_list_items = array();

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $global_sidebar_layout            = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );


    $model[ 'has_narrow_layout' ]     = 'b' == $global_sidebar_layout;
    $model[ 'post_list_layout' ]      = $this -> czr_fn__get_post_list_layout( $model[ 'has_narrow_layout' ] );
    $model[ 'has_format_icon_media' ] = ! $model[ 'has_narrow_layout' ];
    $model[ 'has_post_media' ]        = 0 != esc_attr( czr_fn_opt( 'tc_post_list_show_thumb' ) );

    $model[ 'element_class']          = czr_fn_get_in_content_width_class();
    array_push( self::$post_class, $model[ 'has_post_media' ] ? 'has-media' : 'no-media' );

    /*
    * In the new theme the places are defined just by the option show_thumb_first,
    * we handle the alternate with bootstrap classes
    */
    $model[ 'def_place_1' ]           = 'show_thumb_first' == $model[ 'post_list_layout' ]['show_thumb_first'] ? 'media' : 'content';
    $model[ 'def_place_2' ]           = 'show_thumb_first' == $model[ 'post_list_layout' ]['show_thumb_first'] ? 'content' : 'media';

    /*
    * The masonry grid does the same
    */
    add_action( '__alternate_loop_start', array( $this, 'czr_fn_setup_text_hooks') );
    add_action( '__alternate_loop_end'  , array( $this, 'czr_fn_reset_text_hooks') );

    //reset alternate items at loop end? sort of garbage collector
    add_action( '__alternate_loop_end'  , array( $this, 'czr_fn_reset_post_list_items') );

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
  function czr_fn_get_content_cols() {
    return $this -> czr_fn__get_post_list_item_property( 'content_cols' );
  }

  function czr_fn_get_media_cols() {
    return $this -> czr_fn__get_post_list_item_property( 'media_cols' );
  }


  function czr_fn_get_place_1() {
    return $this -> czr_fn__get_post_list_item_property( 'place_1' );
  }


  function czr_fn_get_place_2() {
    return $this -> czr_fn__get_post_list_item_property( 'place_2' );
  }

  function czr_fn_get_sections_wrapper_class() {
    return $this -> czr_fn__get_post_list_item_property( 'sections_wrapper_class' );
  }

  function czr_fn_get_article_selectors() {
    return $this -> czr_fn__get_post_list_item_property( 'article_selectors' );
  }

  function czr_fn_get_has_format_icon_media() {
    return $this -> czr_fn__get_post_list_item_property( 'has_format_icon_media' );
  }

  function czr_fn_get_is_full_image() {
    return $this -> czr_fn__get_post_list_item_property( 'is_full_image' );
  }


  /*
  * Private/protected getters
  */

  /*
  *  Method to compute the properties of the current (in a loop) post list item
  *  @return array
  */
  protected function czr_fn__get_post_list_item() {

    global $wp_query;

    /* Define variables */
    $_layout                 = apply_filters( 'czr_post_list_layout', $this -> post_list_layout );
    $maybe_center_sections   = apply_filters( 'czr_alternate_sections_centering', true );

    $_current_post_format    = get_post_format();

    $has_post_media          = $this->has_post_media;
    $has_thumb               = czr_fn_has_thumb();
    $is_full_image           = $this->czr_fn_is_full_image( $_current_post_format );

    $has_format_icon_media   = $this->czr_fn_has_format_icon_media( $has_thumb, $_current_post_format );

    //setup article selectors;
    $article_selectors = $this -> czr_fn__get_article_selectors($is_full_image, $has_format_icon_media);


    $_sections_wrapper_class = array();

    /* Structural */
    $place_1                 = $this -> def_place_1;
    $place_2                 = $this -> def_place_2;

    $_push                   = array(
      $place_1 => array(),
      $place_2 => array()
    );

    $_pull                   = array(
      $place_1 => array(),
      $place_2 => array()
    );
    /* End define variables */

    /* Process different cases */
    /*
    * $is_full_image: Gallery and images (with no text) should
    * - not be vertically centered
    * - avoid the media-content alternation
    */
    if ( ! $is_full_image && $has_post_media ) {
        /*
        * Video post formats
        * In the new alternate layout video takes more space when global layout has less than 2 sidebars
        * same thing for the image post format with text
        *
        */
        if ( in_array( $_current_post_format , apply_filters( 'czr_alternate_big_media_post_formats', array( 'video', 'image' ) ) )
            && ! $this->has_narrow_layout ) {
          /* Swap the layouts */
          $_t_l                    = $_layout[ 'media' ];
          $_layout[ 'media' ]      = $_layout[ 'content' ];
          $_layout[ 'content' ]    = $_t_l;
        }

      // conditions to swap place_1 with place_2 are:
      // alternate on and current post number is odd (1,3,..). (First post is 0 )
      if (  $_layout[ 'alternate' ] &&  ( 0 == ( $wp_query -> current_post + 1 ) % 2 ) ) {
        /* the slice is to avoid push/pull in xs */
        $_push[ $place_1 ]        = array_slice( $_layout[ $place_2 ], 0, count($_layout[ $place_2 ]) - 1);
        $_pull[ $place_2 ]        = array_slice( $_layout[ $place_1 ], 0, count($_layout[ $place_1 ]) - 1);
      }

      if ( ! $this->has_narrow_layout )
        //allow centering sections
        array_push( $_sections_wrapper_class, apply_filters( 'czr_alternate_sections_centering', true ) ? 'czr-center-sections' : 'a');
    }
    elseif ( $is_full_image && $has_post_media ){
      /*
      * $is_full_image: Gallery and images (with no text) should
      * - be displayed in full-width
      * - media comes first, content will overlap
      */
      $_layout[ 'content' ] = $_layout[ 'media' ] = array();

      $place_1 = 'media';
      $place_2 = 'content';
    }
    elseif ( ! $has_post_media ) {
      //full width content
      $_layout[ 'content' ] = array('', '', '', '', '12');
    }

    $content_cols = $this -> czr_fn_build_cols( $_layout['content'], $_push['content'], $_pull['content']);
    $media_cols   = $this -> czr_fn_build_cols( $_layout['media'], $_push['media'], $_pull['media']);

    $post_list_item = array(
      'content_cols'            => $content_cols,
      'media_cols'              => $media_cols,
      'place_1'                 => $place_1,
      'place_2'                 => $place_2,
      'sections_wrapper_class'  => $_sections_wrapper_class,
      'article_selectors'       => $article_selectors,
      'is_full_image'           => $is_full_image,
      'has_format_icon_media'   => $has_format_icon_media
    );

    return $post_list_item;
  }



  protected function czr_fn__get_post_list_item_property( $_property ) {
    if ( ! $_property )
      return;
    $_properties = end( $this->post_list_items );
    return isset( $_properties[ $_property ] ) ? $_properties[ $_property ] : null;
  }



  protected function czr_fn__get_article_selectors( $is_full_image, $has_format_icon_media ) {

    $post_class              = self::$post_class;
    $has_post_media          = $this->has_post_media;

    /* Extend article selectors with info about the presence of an excerpt and/or thumb */
    array_push( $post_class,
      $is_full_image && $has_post_media ? 'full-image' : '',
      ! $has_format_icon_media && $has_post_media  ? 'has-thumb' : 'no-thumb'
    );

    $article_selectors       = czr_fn_get_the_post_list_article_selectors( array_filter($post_class) );

    return $article_selectors;
  }



  /**
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  protected function czr_fn__get_post_list_layout( $narrow_layout = false ) {
    $_layout                       = self::$default_post_list_layout;

    $_layout[ 'position' ]         = esc_attr( czr_fn_opt( 'tc_post_list_thumb_position' ) );
    $_layout[ 'show_thumb_first' ] = in_array( $_layout['position'] , array( 'top', 'left') );

    //since 4.5 top/bottom positions will not be optional but will be forced in narrow layouts
    if ( $narrow_layout )
      $_layout['position']         = $_layout[ 'show_thumb_first' ] ? 'top' : 'bottom';
    else {
      if ( 'top' == $_layout[ 'position' ] )
        $_layout[ 'position' ] = 'left';
      elseif ( 'bottom' == $_layout[ 'position' ] )
        $_layout[ 'position' ] = 'right';
    }

    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ! ( 0 == esc_attr( czr_fn_opt( 'tc_post_list_thumb_alternate' ) ) || in_array( $_layout['position'] , array( 'top', 'bottom') ) );

    if ( in_array( $_layout['position'] , array( 'top', 'bottom') ) )
      $_layout['content'] = $_layout['media'] = self::$default_post_list_layout['narrow_both'];

    return $_layout;
  }



  /* HELPERS AND CALLBACKS */

  function czr_fn_has_excerpt() {
    /*
    * Using the excerpt filter here can cause some compatibility issues
    * See: Super Socializer plugin
    */
    $has_excerpt = (bool) apply_filters( 'the_excerpt', get_the_excerpt() );

    return $has_excerpt;
  }


  function czr_fn_is_full_image( $_current_post_format ) {
    /* gallery and image (with no text) post formats */
    $is_full_image           = in_array( $_current_post_format , array( 'gallery', 'image' ) ) && ( 'image' != $_current_post_format ||
          ( 'image' == $_current_post_format && ! $this->czr_fn_has_excerpt() ) );

    return $is_full_image;
  }


  /*
  * Show an icon in the media block when
  * 1) this model field is true
  *  and
  * 2a) post format is one of 'quote', 'link', 'status', 'aside'
  *  or
  * 2b) not 'gallery','image', 'audio', 'video' post format and no thumb
  */
  function czr_fn_has_format_icon_media( $has_thumb, $current_post_format = null ) {

    if ( ! $this -> has_format_icon_media )
      return false;


    if ( in_array( $current_post_format, array( 'gallery', 'image', 'audio', 'video' ) ) )
      return false;

    if ( in_array( $current_post_format, array( 'quote', 'link', 'status', 'aside' ) ) )
      return true;

    return ! ( $this->has_post_media && $has_thumb );

  }


  /**
  * @return array() of bootstrap classed defining the responsive widths
  *
  */
  function czr_fn_build_cols( $_widths, $_push = array(), $_pull = array() ) {
    $_col_bp = self::$_col_bp;

    $_widths = array_filter( $_widths );
    $_push   = array_filter( $_push );
    $_pull   = array_filter( $_pull );

    $_cols   = array();

    $_push_class = $_pull_class = '';

    foreach ( $_widths as $i => $val ) {

      if ( isset($_push[$i]) )
        $_push_class    = "push-{$_col_bp[$i]}-{$_push[$i]}";

      if ( isset($_pull[$i]) )
        $_pull_class    = "pull-{$_col_bp[$i]}-{$_pull[$i]}";

      $_width_class  = "col-{$_col_bp[$i]}-$val";
      array_push( $_cols, $_width_class, $_push_class, $_pull_class );
    }
    return array_filter( array_unique( $_cols ) );
  }


  /*
  * Callbacks
  */

  /**
  * hook : __alternate_loop_start
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks( $model_id ) {
    if ( $model_id == $this->id  )
      //filter the excerpt length
      add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : __alternate_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks( $model_id ) {
    if ( $model_id == $this->id  )
      remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length ? $this -> excerpt_length : esc_attr( czr_fn_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  * hook : __alternate_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_post_list_items( $model_id ) {
    if ( $model_id == $this->id  )
      $this -> post_list_items = array();
  }

}//end of class