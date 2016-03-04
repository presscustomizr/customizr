<?php
class TC_posts_list_description_model_class extends TC_Model {
  public $description;
  public $element_class = 'archive-meta'; 
  private $context;

  function tc_extend_params( $model = array() ) {
    //context
    $this -> context  = $this -> tc_get_the_posts_list_context();

    if ( ! $this -> context )
      return;   

    $model['description']   = apply_filters( "tc_{$this -> context}_description", $this -> tc_get_posts_list_description() );

    return $model;
  }

  //you can find the same in the posts_list_title model
  function tc_get_the_posts_list_context() {
    global $wp_query;  
    if ( $wp_query -> is_posts_page && ! is_front_page() )
      return 'page_for_posts';

    if ( is_archive() ) {
      if ( is_author() )
        return 'author';
      if ( is_category() )
        return 'category';
      if ( is_day() )
        return 'day';
      if ( is_month() )
        return 'month';
      if ( is_year() )
        return 'year';
      if ( is_tag() )
        return 'tag';
      if ( apply_filters('tc_show_tax_archive_title', true ) )
        return 'tax';
    }
    return false;  
  }

  function tc_get_posts_list_description( $context = null ) {
    $context = $context ? $context : $this -> context;

    switch ( $context ) {
      case 'page_for_posts' : return get_the_content(); //use the content as description in blog page?
      case 'author'         : return 'AUTHOR DESCRIPTION IS TOO MUCH COMPLEX, MOVE IT INTO ANOTHER MODEL/TEMPLATE';
      case 'category'       : return category_description();
      case 'tag'            : return tag_description();
      case 'tax'            : return get_the_archive_description();
      default               : return '';
    }
  }
  function tc_get_description_content() {
    //we should have some filter here, to allow the processing of the description
    //for example to allow shortcodes in it.... (requested at least twice from users, in my memories)
    return category_description();    
  }
}  
