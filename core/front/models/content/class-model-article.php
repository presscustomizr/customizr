<?php
class TC_article_model_class extends TC_Model {
  public $article_selectors;

  public $post_class = 'row-fluid';

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
    //gets global vars
    global $post;
    global $wp_query;

    //declares selector var
    $selectors                  = '';

    $post_class                 = $this -> tc_get_the_post_class( $this -> post_class );

    // POST LIST
    $post_list_selector_bool    = ( isset($post) && !is_singular() && !is_404() && !tc__f( '__is_home_empty') ) || ( is_search() && 0 != $wp_query -> post_count );
    $selectors                  = $post_list_selector_bool ? apply_filters( 'tc_post_list_selectors' , 'id="post-'.get_the_ID().'" '. $post_class ) : $selectors;

    $selectors = apply_filters( 'tc_article_selectors', $selectors );

    return $selectors;
  }//end of function


  /**
  * Returns the classes for the post div.
  *
  * @param string|array $class One or more classes to add to the class list.
  * @param int $post_id An optional post ID.
  * @package Customizr
  * @since 3.0.10
  */
  function tc_get_the_post_class( $class = '', $post_id = null ) {
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
  }

}
