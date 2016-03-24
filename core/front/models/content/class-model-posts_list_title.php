<?php
class TC_posts_list_title_model_class extends TC_Model {
  public $pre_title;
  public $title;
  
  private $context;

  function tc_extend_params( $model = array() ) {
    //the controlleer will check if we're in (not singular) context
    //context
    $this -> context  = $this -> tc_get_the_posts_list_context();

    if ( ! $this -> context )
      return;

    $model['element_class']     = apply_filters( 'tc_archive_icon', $this -> tc_get_archive_title_class() );
    $model['pre_title']         = apply_filters( "tc_{$this -> context}_archive_title" , $this -> tc_get_posts_list_pre_title() );
    $model['title']             = apply_filters( "tc_{$this -> context}_title", $this -> tc_get_posts_list_title_content() );
    /*we are getting rid of
    "tc_{context}_header_content" filter
    */
    return $model;
  }

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

  function tc_get_archive_title_class() {
      return ( esc_attr( TC_utils::$inst->tc_opt( 'tc_show_archive_title_icon' ) ) 
          && esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ) ? array( 'format-icon' ) : array();
  }

  function tc_get_posts_list_pre_title( $context = null ) {
    $context = $context ? $context : $this -> context;
    $context = 'category' == $context ? 'cat' : $context;
    return esc_attr( TC_utils::$inst->tc_opt( "tc_{$context}_title" ) );   
  }

  function tc_get_posts_list_title_content( $context = null ) {
    $context = $context ? $context : $this -> context;

    switch ( $context ) {
      case 'page_for_posts' : return get_the_title( get_option('page_for_posts') );
      case 'author'         : return '<span class="vcard">' . get_the_author_meta( 'display_name' , get_query_var( 'author' ) ) . '</span>';
      case 'category'       : return single_cat_title( '', false );
      case 'day'            : return '<span>' . get_the_date() . '</span>';
      case 'month'          : return '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>';
      case 'year'           : return '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>';
      case 'tag'            : return single_tag_title( '', false );
      case 'tax'            : return get_the_archive_title();
    }
  }
}
