<?php
class TC_article_model_class extends TC_Model {
  public $article_selectors;

  public $post_class = 'row-fluid';


  function tc_setup_children() {
    $children = array(
      array(
        'id'          => 'singular_headings',
        'model_class' => array( 'parent' => 'content/headings', 'name' => 'content/singles/post_page_headings' ),
      )
    );
    return $children;
  }


  function tc_setup_late_properties() {
    $this -> tc_set_property( 'article_selectors', $this -> tc_get_the_article_selectors() );
  }

  /**
  * Returns or displays the selectors of the article depending on the context
  *
  * @package Customizr
  * @since 3.1.0
  */
  function tc_get_the_article_selectors() {
    if ( 'singular_article' == $this -> id ) {
     $selectors = $this -> tc_get_singular_article_selectors();
    }else {
      //gets global vars
      global $post;
      global $wp_query;

      //declares selector var
      $selectors                  = '';

      $post_class                 = CZR() -> helpers -> tc_get_the_post_class( $this -> post_class );

      // POST LIST
      $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !tc__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
      $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '. $post_class ) : $selectors;
    }

    $selectors = apply_filters( 'tc_article_selectors', $selectors );

    return $selectors;
  }//end of function





  /**
  * @override
  * Returns or displays the selectors of the article depending on the context
  *
  * @package Customizr
  * @since 3.1.0
  */
  function tc_get_singular_article_selectors() {
    //gets global vars
    global $post;
    global $wp_query;

    //declares selector var
    $selectors                  = '';

    $post_class                 = CZR() -> helpers -> tc_get_the_post_class( $this -> post_class );

    // SINGLE POST
    $single_post_selector_bool  = isset($post) && 'page' != $post -> post_type && 'attachment' != $post -> post_type && is_singular();
    $selectors                  = $single_post_selector_bool ? apply_filters( 'tc_single_post_selectors' ,'id="post-'.get_the_ID().'" '. $post_class ) : $selectors;

    // PAGE
    $page_selector_bool         = isset($post) && 'page' == tc__f('__post_type') && is_singular() && !tc__f( '__is_home_empty');
    $selectors                  = $page_selector_bool ? apply_filters( 'tc_page_selectors' , 'id="page-'.get_the_ID().'" '. $post_class ) : $selectors;

    // ATTACHMENT
    //checks if attachement is image and add a selector
    $format_image               = wp_attachment_is_image() ? 'format-image' : '';
    $selectors                  = ( isset($post) && 'attachment' == $post -> post_type && is_singular() ) ? apply_filters( 'tc_attachment_selectors' , 'id="post-'.get_the_ID().'" '. CZR() -> helpers -> tc_get_the_post_class( "{$this -> post_class} format-image" ) ) : $selectors;

    $selectors = apply_filters( 'tc_article_selectors', $selectors );

    return $selectors;
  }//end of function











  // /**
  // * Returns the classes for the post div.
  // *
  // * @param string|array $class One or more classes to add to the class list.
  // * @param int $post_id An optional post ID.
  // * @package Customizr
  // * @since 3.0.10
  // */
  // function tc_get_the_post_class( $class = '', $post_id = null ) {
  //   //Separates classes with a single space, collates classes for post DIV
  //   return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
  // }

}
