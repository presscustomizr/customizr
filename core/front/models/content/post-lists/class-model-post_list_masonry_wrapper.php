<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_cl_post_list_masonry_wrapper_model_class extends CZR_cl_Model {
  public $element_class = array( 'grid-container__masonry', 'grid' );
  public $post_class = 'grid-item';
  public $place_1 = 'media';
  public $place_2 = 'content';
  public $sections_wrapper_class = 'grid-post';

  public $article_selectors;

  public $has_post_media;
  public $has_header_format_icon;

  public $czr_show_excerpt;

  public $is_loop_start;
  public $is_loop_end;

  public $is_full_image;

  //Default post list layout
  private static $default_post_list_layout   = array(
            'b'         => array('col-xs-12'),
            'f'         => array('col-xs-12', 'col-md-6', 'col-lg-4'),
            'l'         => array('col-xs-12', 'col-md-6'),
            'r'         => array('col-xs-12', 'col-md-6')
          );
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
    
    $model[ 'element_class']       = array_merge( $this -> element_class, array($_class) );
    $model[ 'post_class' ]         = array_merge( self::$default_post_list_layout[$global_sidebar_layout], array($this -> post_class) );
    $model[ 'has_narrow_layout' ]  = 'b' == $global_sidebar_layout;

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
    $has_post_media      = $this -> czr_fn_show_media() ;

    /*
    * Find a way to avoid the no-thumb here and delegate to the thumb wrapper?
    */
    $post_class           = ! $has_post_media ? array_merge( $this -> post_class, array('no-thumb') ) : $this -> post_class;
    $article_selectors    = czr_fn_get_the_post_list_article_selectors( $post_class );

    $this -> czr_fn_update( array(
      'czr_show_excerpt'       => $this -> czr_fn_show_excerpt(),
      'has_post_media'         => $has_post_media,
      'article_selectors'      => $article_selectors,
      'is_loop_start'          => 0 == $wp_query -> current_post,
      'is_loop_end'            => $wp_query -> current_post == $wp_query -> post_count -1,
      'has_header_format_icon' => in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside', 'chat' ) ) )
    ));

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
          ! in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside', 'chat' ) ) ) &&
          czr_fn_has_thumb() &&
          0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) )
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