<?php
/*
*
* TODO: treat case post format image with no text and post format gallery
*/
class CZR_cl_post_list_plain_model_class extends CZR_cl_Model {
  public $element_class         = array( 'grid-container__full' );

  public $article_selectors;
  public $sections_wrapper_class;

  public $has_post_media;

  //public $is_full_image;

  public $media_col = 'row';
  public $content_col = 'row';

  public $media_inner_wrapper_class = 'col-xs-12';

  public $is_loop_start;
  public $is_loop_end;

  private static $post_class    = array( 'row' );

  function czr_fn_setup_late_properties() {
    global $wp_query;

    $has_post_media          = $this -> czr_fn_show_media() ;

    // $_current_post_format    = get_post_format();

    // /* gallery and image (with no text) post formats */
    // $is_full_image           = in_array( $_current_post_format , array( 'gallery', 'image' ) ) && ( 'image' != $_current_post_format ||
    //         ( 'image' == $_current_post_format && ! $_has_excerpt  ) );

    $this -> czr_fn_update( array(
      'has_post_media'         => $has_post_media,
      'article_selectors'      => czr_fn_get_the_post_list_article_selectors( array_filter(self::$post_class) ),
      'is_loop_start'          => 0 == $wp_query -> current_post,
      'is_loop_end'            => $wp_query -> current_post == $wp_query -> post_count -1,
    ) );

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