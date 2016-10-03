<?php
/**
* Query related functions
*/





/**
* hook : body_class
* @return  array of classes
*
* @package Customizr
* @since Customizr 3.3.2
*/
function czr_fn_set_post_list_context_class( $_class ) {
    if ( czr_fn_is_list_of_posts() )
      array_push( $_class , 'czr-post-list-context');
    return $_class;
}





/******************************
VARIOUS QUERY HELPERS
*******************************/

/**
* Return object post type
*
* @since Customizr 3.0.10
*
*/
function czr_fn_get_post_type() {
    global $post;

    if ( ! isset($post) )
      return;

    return $post -> post_type;
}


function czr_fn_is_list_of_posts() {
    //must be archive or search result. Returns false if home is empty in options.
    return apply_filters( 'czr_is_list_of_posts',
      ! is_singular()
      && ! is_404()
      && ! czr_fn_is_home_empty()
      && ! is_admin()
    );
}


function czr_fn_is_single_post() {
    global $post;
    return apply_filters( 'czr_is_single_post', isset($post)
        && is_singular()
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && ! czr_fn_is_home_empty() );
}


function czr_fn_is_single_attachment() {
    global $post;
    return apply_filters( 'czr_is_single_attacment',
        ! ( ! isset($post) || empty($post) || 'attachment' != $post -> post_type || !is_singular() ) );
}

function czr_fn_is_single_page() {
    return apply_filters( 'czr_is_single_page',
        'page' == czr_fn_get_post_type()
        && is_singular()
        && ! czr_fn_is_home_empty()
    );
}

/**
* Boolean : check if we are in the no search results case
*
* @package Customizr
* @since 3.0.10
*/
function czr_fn_is_no_results() {
    global $wp_query;
    return ( is_search() && 0 == $wp_query -> post_count ) ? true : false;
}


/**
* Check if we are displaying posts lists or front page
*
* @since Customizr 3.0.6
*
*/
function czr_fn_is_home() {
  //get info whether the front page is a list of last posts or a page
  return ( is_home() && ( 'posts' == get_option( 'show_on_front' ) || 'nothing' == get_option( 'show_on_front' ) ) ) || is_front_page();
}


/**
* Check if we show posts or page content on home page
*
* @since Customizr 3.0.6
*
*/
function czr_fn_is_home_empty() {
    //check if the users has choosen the "no posts or page" option for home page
    return ( ( is_home() || is_front_page() ) && 'nothing' == get_option( 'show_on_front' ) ) ? true : false;
}

/**
* helper
* returns the actual page id if we are displaying the posts page
* @return  number
*
*/
function czr_fn_get_real_id() {
    global $wp_query;
    $queried_id                   = get_queried_object_id();
    return apply_filters( 'czr_get_real_id', ( ! czr_fn_is_home() && $wp_query -> is_posts_page && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
}



/**
* Returns or displays the selectors of the article depending on the context
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_post_list_article_selectors($post_class = '') {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                  = '';

    if ( isset($post) && czr_fn_is_list_of_posts() )
        //!is_singular() && !is_404() && !czr_fn_is_home_empty() ) || ( is_search() && 0 != $wp_query -> post_count )
      $selectors                = apply_filters( 'czr_post_list_selectors' , 'id="post-'.get_the_ID().'" '. czr_fn_get_the_post_class( $post_class ) );

    return apply_filters( 'czr_article_selectors', $selectors );
}//end of function






/**
* @override
* Returns or displays the selectors of the article depending on the context
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_singular_article_selectors( $post_class = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                  = '';

    // SINGLE POST/ATTACHMENT
    if ( isset($post) && 'page' != $post -> post_type && is_singular() )
      $selectors = apply_filters( "czr_single_{$post -> post_type}_selectors" ,'id="post-'.get_the_ID().'" '. czr_fn_get_the_post_class( $post_class ) );

    // PAGE
    elseif ( isset($post) && 'page' == czr_fn_get_post_type() && is_singular() && ! czr_fn_is_home_empty() )
      $selectors = apply_filters( 'czr_page_selectors' , 'id="page-'.get_the_ID().'" '. czr_fn_get_the_post_class( $post_class ) );

    $selectors = apply_filters( 'czr_article_selectors', $selectors );

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
function czr_fn_get_the_post_class( $class = '', $post_id = null ) {
    //Separates classes with a single space, collates classes for post DIV
    return 'class="' . join( ' ', get_post_class( $class, $post_id ) ) . '"';
}
