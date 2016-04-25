<?php
class TC_post_metas_model_class extends TC_Model {
  protected $_cache = array();

  public    $type   = 'post_metas';

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    //Since we use only one instance for every post in a post list reset the cache after the view has been rendered
    add_action( "__after_{$this -> id}", array( $this, 'tc_reset_cache' ) );
  }


  /*
  * @override
  */
  function tc_maybe_render_this_model_view() {
    if ( ! $this -> visibility )
      return;
    if ( is_attachment() )
        return;

    return $this -> tc_get_cat_list() ||
          $this -> tc_get_tag_list() ||
          $this -> tc_get_author() ||
          $this -> tc_get_publication_date() ||
          $this -> tc_get_update_date();
  }

  public function tc_reset_cache() {
    $this -> _cache = array();
  }

  /* PUBLIC GETTERS */
  public function tc_get_cat_list( $sep = '' ) {
    return 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_categories' ) ) ? $this -> tc_get_meta( 'tax', true, $sep ) : '';
  }

  public function tc_get_tag_list( $sep = '' ) {
    return 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_tags' ) ) ? $this -> tc_get_meta( 'tax', false, $sep ) : '';
  }

  public function tc_get_author() {
    return 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_author' ) ) ? $this -> tc_get_meta( 'author' ) : '';
  }

  public function tc_get_publication_date() {
    return 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_publication_date' ) ) ? $this -> tc_get_meta( 'pub_date' ) : '';
  }

  public function tc_get_update_date( $today = '', $yesterday = '', $manydays = '' ) {
    if ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_update_date' ) ) && false !== $_update_days = TC_utils::$inst -> tc_post_has_update() ) {
      if ( 'days' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_date_format' ) ) && $today && $yesterday && $manydays ) {
        $_update = ( 0 == $_update_days ) ? $today : sprintf( $manydays, $_update_days );
        $_update = ( 1 == $_update_days ) ? $yesterday : $_update;
      }
      return isset( $_update ) ? $_update : $this -> tc_get_meta( 'up_date' );
    }
    return false;
  }
  /* END PUBLIC GETTERS */

  /* HELPERS */
  protected function tc_get_meta( $meta, $param = array(), $separator = '' ) {
    if ( ! isset( $_cache[ $meta ] ) ) {
      $param = is_array( $param ) ? $param : array( $param );
      $_cache[ $meta ] = CZR() -> helpers -> tc_stringify_array( call_user_func_array( array( $this, "tc_meta_generate_{$meta}" ), $param ), $separator );
    }
    return $_cache[ $meta ];
  }

  private function tc_meta_generate_tax( $hierarchical ) {
    return $this -> tc_meta_generate_tax_list( $hierarchical );
  }

  private function tc_meta_generate_author() {
    return $this -> tc_get_meta_author();
  }

  private function tc_meta_generate_pub_date( $format = '' ) {
    return $this -> tc_get_meta_date( 'publication', $format );
  }

  private function tc_meta_generate_up_date( $format = '' ) {
    return $this -> tc_get_meta_date( 'update', $format );
  }

  /* @override */
  protected function tc_get_term_css_class( $_is_hierarchical ) {
    $_classes         =  array( 'btn' , 'btn-mini' );
    if ( $_is_hierarchical )
      array_push( $_classes , 'btn-tag' );
    return $_classes;
  }

  /**
  * Helper
  * Return the date post metas
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  protected function tc_get_meta_date( $pub_or_update = 'publication', $_format = '' ) {
    if ( 'short' == $_format )
      $_format = 'j M, Y';
    $_format = apply_filters( 'tc_meta_date_format' , $_format );
    $_use_post_mod_date = apply_filters( 'tc_use_the_post_modified_date' , 'publication' != $pub_or_update );
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
  private function tc_get_meta_author() {
    return apply_filters(
        'tc_author_meta',
        sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
            esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
            get_the_author()
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
  private function tc_meta_generate_tax_list( $hierarchical ) {
    $post_terms = $this -> tc_get_term_of_tax_type( $hierarchical );
    if ( ! $post_terms )
      return;
    $_terms_html_array  = array_map( array( $this , 'tc_meta_term_view' ), $post_terms );
    return $_terms_html_array;
              //apply_filters( 'tc_meta_generate_tax_list', implode( apply_filters( 'tc_meta_terms_glue' , '' ) , $_terms_html_array ) , $post_terms );
  }


  /**
  * Helper
  * @return string of the single term view
  * @param  $term object
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function tc_meta_term_view( $term ) {
    $_is_hierarchical  =  is_taxonomy_hierarchical( $term -> taxonomy );

    $_classes      = CZR() -> helpers -> tc_stringify_array( apply_filters( 'tc_meta_tax_class', $this -> tc_get_term_css_class( $_is_hierarchical ), $_is_hierarchical, $term ) );
    // (Rocco's PR Comment) : following to this https://wordpress.org/support/topic/empty-articles-when-upgrading-to-customizr-version-332
    // I found that at least wp 3.6.1  get_term_link($term->term_id, $term->taxonomy) returns a WP_Error
    // Looking at the codex, looks like we can just use get_term_link($term), when $term is a term object.
    // Just this change avoids the issue with 3.6.1, but I thought should be better make a check anyway on the return type of that function.
    $_term_link    = is_wp_error( get_term_link( $term ) ) ? '' : get_term_link( $term );
    $_to_return    = $_term_link ? '<a %1$s href="%2$s" title="%3$s"> %4$s </a>' :  '<span class="%1$s"> %4$s </a>';
    return apply_filters( 'tc_meta_term_view' , sprintf($_to_return,
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
  private function tc_get_term_of_tax_type( $hierarchical = true ) {
    //var declaration
    $post_type              = get_post_type( TC_utils::tc_id() );
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
      if ( ! $this -> tc_is_tax_authorized( $_tax_object, $post_type ) ) {
        next($tax_list);
        continue;
      }
      if ( (bool) $hierarchical === (bool) $_tax_object['hierarchical'] )
        $_tax_type_list[$_tax_name] = $_tax_object;

      next($tax_list);
    }
    if ( empty($_tax_type_list) )
      return false;

    //fill the post terms array
    foreach ($_tax_type_list as $tax_name => $data ) {
      $_current_tax_terms = get_the_terms( TC_utils::tc_id() , $tax_name );
      //If current post support this tax but no terms has been assigned yet = continue
      if ( ! $_current_tax_terms )
        continue;
      while( $term = current($_current_tax_terms) ) {
        $_tax_type_terms_list[$term -> term_id] = $term;
        next($_current_tax_terms);
      }
    }
    return empty($_tax_type_terms_list) ? false : apply_filters( "tc_tax_meta_list" , $_tax_type_terms_list , $hierarchical );
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
  private function tc_is_tax_authorized( $_tax_object , $post_type ) {
    $_in_exclude_list = in_array(
      $_tax_object['name'],
      apply_filters_ref_array ( 'tc_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , TC_utils::tc_id() ) )
    );
    $_is_private = false === (bool) $_tax_object['public'] && apply_filters_ref_array( 'tc_exclude_private_taxonomies', array( true, $_tax_object['public'], TC_utils::tc_id() ) );
    return ! $_in_exclude_list && ! $_is_private;
  }


  /* Customizer: allow dynamic visibility in the preview */
  function tc_body_class( $_classes/*array*/ ) {
    if ( ! CZR___::$instance -> tc_is_customizing() )
      return $_classes;

    if ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas' ) ) )
       array_push( $_classes, 'hide-all-post-metas' );

    if (
        ( is_singular() && ! is_page() && ! tc__f('__is_home') && 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_single_post' ) ) ) ||
        ( ! is_singular() && ! tc__f('__is_home') && ! is_page() && 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_post_lists' ) ) ) ||
        ( tc__f('__is_home') ) && 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_home' ) )
    )
      array_push( $_classes, 'hide-post-metas' );

    return $_classes;
  }
}//end of class
