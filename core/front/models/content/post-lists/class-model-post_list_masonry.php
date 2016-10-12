<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_cl_post_list_masonry_model_class extends CZR_cl_Model {
  public $excerpt_length;


  //Default post list layout
  private static $default_post_list_layout   = array(
            'b'         => array('col-xs-12'),
            'f'         => array('col-xs-12', 'col-md-6', 'col-lg-4'),
            'l'         => array('col-xs-12', 'col-md-6'),
            'r'         => array('col-xs-12', 'col-md-6')
          );
  public $post_class    = array( 'grid-item' );

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $global_sidebar_layout         = czr_fn_get_layout( czr_fn_get_id() , 'sidebar' );
    $model[ 'element_class']       = czr_fn_get_in_content_width_class();

    $this->post_class              = array_merge( self::$default_post_list_layout[$global_sidebar_layout], $this->post_class );

    /*
    * The alternate grid does the same
    */
    add_action( '__masonry_loop_start', array( $this, 'czr_fn_setup_text_hooks') );
    add_action( '__masonry_loop_end'  , array( $this, 'czr_fn_reset_text_hooks') );

    return $model;
  }


  function czr_fn_get_has_post_media() {
    $has_post_media = $this -> czr_fn_show_media();
    return $has_post_media;
  }

  /*
  * Very similar to the one in the alternate...
  * probably the no-thumb/no-text should be ported somewhere else (in czr_fn_get_the_post_list_article_selectors maybe)
  */
  function czr_fn_get_article_selectors() {
    $has_post_media            = $this -> czr_fn_get_has_post_media();
    $post_class                = $this->post_class;

    /*
    * Using the excerpt filter here can cause some compatibility issues
    * See: Super Socializer plugin
    */
    $_has_excerpt            = (bool) apply_filters( 'the_excerpt', get_the_excerpt() );

    array_push( $post_class, ! $_has_excerpt ? 'no-text' : '',  ! $has_post_media ? 'no-thumb' : '' );

    $article_selectors         = czr_fn_get_the_post_list_article_selectors( $post_class );

    return $article_selectors;
  }


  function czr_fn_get_has_header_format_icon(){
    return in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside', 'chat' ) ) );
  }


  /*
  * We decided that in masonry all the images (even those with text) should be displayed like the gallery
  */
  function czr_fn_get_is_full_image() {
    return in_array( get_post_format() , array( 'gallery', 'image' ) );
  }



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
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = $this -> excerpt_length ? $this -> excerpt_length : esc_attr( czr_fn_get_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
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
          ! in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside', 'chat' ) ) ) &&
          czr_fn_has_thumb() &&
          0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) )
    );
  }

}