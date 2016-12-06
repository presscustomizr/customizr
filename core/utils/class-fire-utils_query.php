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


function czr_fn_get_query_context() {
    if ( is_page() )
        return 'page';
    if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
    if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
    if ( !is_404() && ! czr_fn_is_home_empty() )
        return 'archive';

    return false;
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
    $queried_id  = get_queried_object_id();
    return apply_filters( 'czr_get_real_id', ( ! czr_fn_is_home() && $wp_query -> is_posts_page && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
}



/**
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_post_list_article_selectors( $post_class = '', $id_suffix = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                  = '';

    if ( isset($post) )
      $selectors = apply_filters( "czr_post_list_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'post', get_the_ID(), $id_suffix ),
        czr_fn_get_the_post_class( $post_class )
      ) );

    return apply_filters( 'czr_article_selectors', $selectors );
}//end of function






/**
* @override
* Returns or displays the selectors of the article depending on the context
*
* @return string
*
* @package Customizr
* @since 3.1.0
*/
function czr_fn_get_the_singular_article_selectors( $post_class = '' ) {
    //gets global vars
    global $post;

    //declares selector var
    $selectors                   = '';

    // SINGLE POST/ATTACHMENT
    if ( isset($post) ) {
      $post_type  = czr_fn_get_post_type();
      $selectors  = apply_filters( "czr_article_singular_{$post_type}_selectors", sprintf('%1$s %2$s',
        czr_fn_get_the_post_id( 'page' == $post_type ? $post_type : 'post', get_the_ID() ),
        czr_fn_get_the_post_class( $post_class )
      ) );
    }

    return apply_filters( 'czr_article_selectors', $selectors );

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

/**
* Returns the classes for the post div.
*
* @param string $type Optional. post type. Default 'post' .
* @param int $post_id An optional post ID.
* @param string $id_suffix An optional suffix.
* @package Customizr
* @since 3.0.10
*/
function czr_fn_get_the_post_id( $type = 'post', $post_id = null, $id_suffix = '' ) {
    //Separates classes with a single space, collates classes for post DIV
    return sprintf( 'id="%1$s-%2$s%3$s"', $type, $post_id, $id_suffix );
}

/**
* Returns whether or not the current wp_query post is the first one
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_start() {
    global $wp_query;
    return  0 == $wp_query -> current_post;
}

/**
* Returns whether or not the current wp_query post is the latest one
*
*
* @package Customizr
* @since 4.0
*/
function czr_fn_is_loop_end() {
    global $wp_query;
    return $wp_query -> current_post == $wp_query -> post_count -1;
}