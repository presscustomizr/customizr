<?php
class CZR_post_metas_model_class extends CZR_Model {
  //protected $_cache = array();

  public    $type   = 'post_metas';

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );

    //Since we use only one instance for every post in a post list reset the cache at each loop cycle
    //add_action( 'the_post', array( $this, 'czr_fn_reset_cache' ) );

  }


  /*
  * @override
  */
  /*
  function czr_fn_maybe_render_this_model_view() {

    if ( ! $this -> visibility )
      return;
    if ( is_attachment() )
        return;

    return $this -> czr_fn_get_cat_list() ||
          $this -> czr_fn_get_tag_list() ||
          $this -> czr_fn_get_author() ||
          $this -> czr_fn_get_publication_date() ||
          $this -> czr_fn_get_update_date();

  }
*/
  public function czr_fn_reset_cache() {
    $this -> _cache = array();
  }

  /* PUBLIC GETTERS */
  public function czr_fn_get_cat_list( $limit = false, $sep = '' ) {
    return 0 != esc_attr( czr_fn_get_opt( 'tc_show_post_metas_categories' ) ) ? $this -> czr_fn_get_meta( 'categories', $limit, $sep ) : '';
  }

  public function czr_fn_get_tag_list( $limit = false, $sep = '' ) {
    return 0 != esc_attr( czr_fn_get_opt( 'tc_show_post_metas_tags' ) ) ? $this -> czr_fn_get_meta( 'tags', $limit, $sep ) : '';
  }

  public function czr_fn_get_author() {
    return 0 != esc_attr( czr_fn_get_opt( 'tc_show_post_metas_author' ) ) ? $this -> czr_fn_get_meta( 'author' ) : '';
  }

  public function czr_fn_get_publication_date() {
    return 0 != esc_attr( czr_fn_get_opt( 'tc_show_post_metas_publication_date' ) ) ? $this -> czr_fn_get_meta( 'pub_date' ) : '';
  }

  public function czr_fn_get_update_date( $today = '', $yesterday = '', $manydays = '' ) {
    if ( 0 != esc_attr( czr_fn_get_opt( 'tc_show_post_metas_update_date' ) ) && false !== $_update_days = czr_fn_post_has_update() ) {
      if ( 'days' == esc_attr( czr_fn_get_opt( 'tc_post_metas_update_date_format' ) ) && $today && $yesterday && $manydays ) {
        $_update = ( 0 == $_update_days ) ? $today : sprintf( $manydays, $_update_days );
        $_update = ( 1 == $_update_days ) ? $yesterday : $_update;
      }
      return isset( $_update ) ? $_update : $this -> czr_fn_get_meta( 'up_date' );
    }
    return false;
  }
  /* END PUBLIC GETTERS */

  /* HELPERS */
  protected function czr_fn_get_meta( $meta, $params = array(), $separator = '' ) {
    //if ( ! isset( $this -> _cache[ $meta ] ) ) {
    $params = is_array( $params ) ? $params : array( $params );
    $this -> _cache[ $meta ] = czr_fn_stringify_array( call_user_func_array( array( $this, "czr_fn_meta_generate_{$meta}" ), $params ), $separator );
    //}
    return $this -> _cache[ $meta ];
  }


  private function czr_fn_meta_generate_categories( $limit = false ) {
    return $this -> czr_fn_meta_generate_tax_list( $hierarchical = true, $limit );
  }

  private function czr_fn_meta_generate_tags( $limit = false ) {
    return $this -> czr_fn_meta_generate_tax_list( $hierarchical = false, $limit );
  }

  private function czr_fn_meta_generate_author() {
    return $this -> czr_fn_get_meta_author();
  }

  private function czr_fn_meta_generate_pub_date( $format = '' ) {
    return $this -> czr_fn_get_meta_date( 'publication', $format );
  }

  private function czr_fn_meta_generate_up_date( $format = '' ) {
    return $this -> czr_fn_get_meta_date( 'update', $format );
  }


  protected function czr_fn_get_term_css_class( $_is_hierarchical ) {
    $_classes = array();

    if ( $_is_hierarchical )
      array_push( $_classes , 'tax__link' );
    else
      array_push( $_classes , 'tag__link' );

    return $_classes;
  }

  /**
  * Helper
  * Return the date post metas
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  protected function czr_fn_get_meta_date( $pub_or_update = 'publication', $_format = '' ) {
    if ( 'short' == $_format )
      $_format = 'j M, Y';
    $_format = apply_filters( 'czr_meta_date_format' , $_format );
    $_use_post_mod_date = apply_filters( 'czr_use_the_post_modified_date' , 'publication' != $pub_or_update );
    return apply_filters(
      'tc_date_meta',
        sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a>' ,
          esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
          esc_attr( get_the_time() ),
          $_use_post_mod_date ? esc_attr( get_the_modified_date('c') ) : esc_attr( get_the_date( 'c' ) ),
          $_use_post_mod_date ? esc_html( get_the_modified_date( $_format ) ) : esc_html( get_the_date( $_format ) )
        ),
        $_use_post_mod_date,
        $_format
     );//end filter
  }

  /**
  * Helper
  * Return the post author metas
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  private function czr_fn_get_meta_author() {
    $author_id = null;


    if ( is_single() )
      if ( ! in_the_loop() ) {
        global $post;
        $author_id = $post->post_author;
      }

    return apply_filters(
        'tc_author_meta', ! $author_id ? '' :
        sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
            esc_url( get_author_posts_url( get_the_author_meta( 'ID', $author_id ) ) ),
            esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author_meta('nicename', $author_id ) ) ),
            get_the_author_meta('nicename', $author_id )
        )
    );//end filter
  }


  /**
  * Helper
  * @return string of all the taxonomy terms (including the category list for posts)
  * @param  hierarchical tax boolean => true = categories like, false = tags like
  *
  * @package Customizr
  * @since Customizr 3.0
  */
  private function czr_fn_meta_generate_tax_list( $hierarchical, $limit = false ) {
    $post_terms = $this -> czr_fn_get_term_of_tax_type( $hierarchical, $limit );
    if ( ! $post_terms )
      return;
    $_terms_html_array  = array_map( array( $this , 'czr_fn_meta_term_view' ), $post_terms );
    return $_terms_html_array;
  }


  /**
  * Helper
  * @return string of the single term view
  * @param  $term object
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function czr_fn_meta_term_view( $term ) {
    $_is_hierarchical  =  is_taxonomy_hierarchical( $term -> taxonomy );

    $_classes      = czr_fn_stringify_array( apply_filters( 'czr_meta_tax_class', $this -> czr_fn_get_term_css_class( $_is_hierarchical ), $_is_hierarchical, $term ) );


    // (Rocco's PR Comment) : following to this https://wordpress.org/support/topic/empty-articles-when-upgrading-to-customizr-version-332
    // I found that at least wp 3.6.1  get_term_link($term->term_id, $term->taxonomy) returns a WP_Error
    // Looking at the codex, looks like we can just use get_term_link($term), when $term is a term object.
    // Just this change avoids the issue with 3.6.1, but I thought should be better make a check anyway on the return type of that function.
    $_term_link    = is_wp_error( get_term_link( $term ) ) ? '' : get_term_link( $term );
    $_to_return    = $_term_link ? '<a %1$s href="%2$s" title="%3$s"> <span>%4$s</span> </a>' :  '<span %1$s> %4$s </span>';
    $_to_return    = $_is_hierarchical ? $_to_return : '<li>' . $_to_return . '</li>';
    return apply_filters( 'czr_meta_term_view' , sprintf($_to_return,
        $_classes ? 'class="'. $_classes .'"' : '',
        $_term_link,
        esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $term -> name ) ),
        $term -> name
      )
    );
  }


  /**
  * Helper to return the current post terms of specified taxonomy type : hierarchical or not
  *
  * @return boolean (false) or array
  * @param  boolean : hierarchical or not
  * @package Customizr
  * @since Customizr 3.1.20
  *
  */
  private function czr_fn_get_term_of_tax_type( $hierarchical = true, $limit = false ) {
    //var declaration
    $post_type              = get_post_type( czr_fn_get_id() );
    $tax_list               = get_object_taxonomies( $post_type, 'object' );
    $_tax_type_list         = array();
    $_tax_type_terms_list   = array();

    if ( empty($tax_list) )
      return false;

    //filter the post taxonomies
    while ( $_tax_object = current($tax_list) ) {
      // cast $_tax_object stdClass object in an array to access its property 'public'
      // fix for PHP version < 5.3 (?)
      $_tax_object = (array) $_tax_object;
      //Is the object well defined ?
      if ( ! isset($_tax_object['name']) ) {
        next($tax_list);
        continue;
      }
      $_tax_name = $_tax_object['name'];
      //skip the post format taxinomy
      if ( ! $this -> czr_fn_is_tax_authorized( $_tax_object, $post_type ) ) {
        next($tax_list);
        continue;
      }
      if ( (bool) $hierarchical === (bool) $_tax_object['hierarchical'] )
        $_tax_type_list[$_tax_name] = $_tax_object;

      next($tax_list);
    }
    if ( empty($_tax_type_list) )
      return false;

    $found = 0;

    //fill the post terms array
    foreach ($_tax_type_list as $tax_name => $data ) {
      $_current_tax_terms = get_the_terms( czr_fn_get_id() , $tax_name );
      //If current post support this tax but no terms has been assigned yet = continue
      if ( ! $_current_tax_terms )
        continue;
      while( $term = current($_current_tax_terms) ) {
        $_tax_type_terms_list[$term -> term_id] = $term;
        if ( $limit > 0 && ++$found == $limit )
          break 2;
        next($_current_tax_terms);
      }
    }

    /*if ( ! empty($_tax_type_terms_list) && $limit > 0 )
      $_tax_type_terms_list = array_slice( $_tax_type_terms_list, 0, $limit );
*/
    return empty($_tax_type_terms_list) ? false : apply_filters( "czr_tax_meta_list" , $_tax_type_terms_list , $hierarchical );
  }

  /**
  * Helper : check if a given tax is allowed in the post metas or not
  * A tax is authorized if :
  * 1) not in the exclude list
  * 2) AND not private
  *
  * @return boolean (false)
  * @param  $post_type, $_tax_object
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  private function czr_fn_is_tax_authorized( $_tax_object , $post_type ) {
    $_in_exclude_list = in_array(
      $_tax_object['name'],
      apply_filters_ref_array ( 'czr_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , czr_fn_get_id() ) )
    );
    $_is_private = false === (bool) $_tax_object['public'] && apply_filters_ref_array( 'czr_exclude_private_taxonomies', array( true, $_tax_object['public'], czr_fn_get_id() ) );
    return ! $_in_exclude_list && ! $_is_private;
  }


  /* Customizer: allow dynamic visibility in the preview */
  function czr_fn_body_class( $_classes/*array*/ ) {
    if ( ! czr_fn_is_customizing() )
      return $_classes;

    if ( 0 == esc_attr( czr_fn_get_opt( 'tc_show_post_metas' ) ) )
       array_push( $_classes, 'hide-all-post-metas' );

    if (
        ( is_singular() && ! is_page() && ! czr_fn_is_home() && 0 == esc_attr( czr_fn_get_opt( 'tc_show_post_metas_single_post' ) ) ) ||
        ( ! is_singular() && ! czr_fn_is_home() && ! is_page() && 0 == esc_attr( czr_fn_get_opt( 'tc_show_post_metas_post_lists' ) ) ) ||
        ( czr_fn_is_home() ) && 0 == esc_attr( czr_fn_get_opt( 'tc_show_post_metas_home' ) )
    )
      array_push( $_classes, 'hide-post-metas' );

    return $_classes;
  }
}//end of class