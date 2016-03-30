<?php
class TC_singular_wrapper_model_class extends TC_article_model_class {

  function tc_setup_children() {
    $children = array(
      //page/attachment/post headings
      array(
        'hook'        => '__article__',
        'id'          => 'singular_headings',
        'template'    => 'content/headings',
        'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/post_page_headings' ),
        'priority'    => 10
      ),
      //page content
      array(
        'hook'        => '__article__',
        'template'    => 'content/page_content',
        'id'          => 'page',
        'priority'    => 20
      ),
      //post content
      array(
        'hook'        => '__article__',
        'template'    => 'content/post_content',
        'id'          => 'post',
        'priority'    => 20
      ),
      //attachment
      array(
        'hook'        => '__article__',
        'template'    => 'content/attachment_content',
        'id'          => 'attachment',
        'priority'    => 20
      ),

      //post footer
      array(
        'hook'        => '__post_footer__',
        'id'          => 'post_footer',
        'template'    => 'content/author_info'
      ),
    );
    return $children;
  }

  /**
  * @override
  * Returns or displays the selectors of the article depending on the context
  *
  * @package Customizr
  * @since 3.1.0
  */
  function tc_get_the_article_selectors() {
    //gets global vars
    global $post;
    global $wp_query;

    //declares selector var
    $selectors                  = '';

    $post_class                 = $this -> tc_get_the_post_class( $this -> post_class );

    // SINGLE POST
    $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
    $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '. $post_class ) : $selectors;

    // PAGE
    $page_selector_bool         = isset($post) && 'page' == tc__f('__post_type') && is_singular() && !tc__f( '__is_home_empty');
    $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '. $post_class ) : $selectors;

    // ATTACHMENT
    //checks if attachement is image and add a selector
    $format_image               = wp_attachment_is_image() ? 'format-image' : '';
    $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '.$this -> tc_get_post_class(array('row-fluid', $format_image) ) ) : $selectors;

    $selectors = apply_filters( 'tc_article_selectors', $selectors );

    return $selectors;
  }//end of function
}
