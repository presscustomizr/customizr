<?php
class CZR_cl_posts_list_description_model_class extends CZR_cl_Model {
  public $description;
  private $context;

  function tc_extend_params( $model = array() ) {
    //context
    $this -> context  = $this -> czr_get_the_posts_list_context();

    if ( ! $this -> context )
      return;

    $model['description']   = apply_filters( "tc_{$this -> context}_description", $this -> czr_get_posts_list_description() );

    return $model;
  }

  //you can find the same in the posts_list_title model
  function czr_get_the_posts_list_context() {
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

  function czr_get_posts_list_description( $context = null ) {
    $context = $context ? $context : $this -> context;
   //we should have some filter here, to allow the processing of the description
    //for example to allow shortcodes in it.... (requested at least twice from users, in my memories)
    switch ( $context ) {
      case 'page_for_posts' : return get_the_content(); //use the content as description in blog page?
      case 'category'       : return category_description();
      case 'tag'            : return tag_description();
      case 'tax'            : return get_the_archive_description();
      default               : return '';
    }
  }
}
