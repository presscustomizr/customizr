<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_post_list_plain_model_class extends CZR_Model {

  public $entry_header_inner_class = array( 'col-md-7', 'offset-md-4', 'col-xs-12');
  public $entry_header_class       = array( 'row' );

  public $content_inner_class      = array('col-md-7', 'offset-md-1', 'col-xs-12');

  public $post_class               = array();

  public $has_post_media;

  public $excerpt_length;

  /**
  * @override
  * fired before the model properties are parsed
  *
  * return model params array()
  */
  function czr_fn_extend_params( $model = array() ) {
    $model[ 'element_class']            = czr_fn_get_in_content_width_class();
    $model[ 'has_post_media']           = 0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) );

    //TEMP:
    if ( 'post_list_plain_excerpt' == $model['id'] ) {
      $model[ 'show_full_content' ]     = false;

      /*
      * The alternate grid does the same
      */
      add_action( '__post_list_plain_loop_start', array( $this, 'czr_fn_setup_text_hooks') );
      add_action( '__post_list_plain_loop_end'  , array( $this, 'czr_fn_reset_text_hooks') );
    }

    return $model;
  }

  /**
  * hook : __masonry_loop_start
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_setup_text_hooks( $model_id ) {
    if ( $model_id == $this->id  ) {
      //filter the excerpt length
      add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
      add_filter( 'excerpt_more'        , array( $this , 'czr_fn_set_excerpt_more') , 99999999 );
    }
  }


  /**
  * hook : __masonry_loop_end
  * @package Customizr
  * @since Customizr 4.0
  */
  function czr_fn_reset_text_hooks( $model_id ) {
    if ( $model_id == $this->id  ) {
      remove_filter( 'excerpt_length'     , array( $this , 'czr_fn_set_excerpt_length') , 999 );
      remove_filter( 'excerpt_more'        , array( $this , 'czr_fn_set_excerpt_more') , 99999999 );
    }
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


  // Replaces the excerpt "Read More" text by a button link
  function czr_fn_set_excerpt_more($more) {
    ob_start();
      czr_fn_render_template( 'modules/read_more', 'readmore' );
      $readmore = ob_get_contents();
    ob_end_clean();
    return $more . $readmore;
  }


  function czr_fn_setup_children() {

    $children = array (
      /* Temporary */
      /* Register models here so that we have their instances to pass to the views/templates */
      /* Header */
      //Post/page headings
      array(
        'id'          => 'post_list_header',
        'model_class' => 'content/post-lists/singles/headings/post_list_single_header'
      )
    );

    return $children;
  }

  function czr_fn_get_has_post_media() {
    $post_format = get_post_format();

    if ( in_array( $post_format, array( 'gallery', 'image', 'audio', 'video' ) ) )
      return true;

    if ( in_array( $post_format, array( 'quote', 'link', 'status', 'aside' ) ) )
      return false;

    return czr_fn_has_thumb();
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

    return czr_fn_get_the_post_list_article_selectors( $post_class );

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
          ! in_array( get_post_format() , apply_filters( 'czr_post_formats_with_no_media', array( 'quote', 'link', 'status', 'aside' ) ) ) &&
          czr_fn_has_thumb() &&
          0 != esc_attr( czr_fn_get_opt( 'tc_post_list_show_thumb' ) )
    );
  }

}