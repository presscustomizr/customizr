<?php
class TC_post_metas_model_class extends TC_Model {
  private $_cache = array();

  function __construct( $model = array() ) {
    //Fires the parent constructor
    parent::__construct( $model );
    //Since we use only one instance for every post in a post list reset the cache after the view has been rendered
    add_action( "after_render_view_{$this -> id}", array( $this, 'tc_reset_cache' ) );
  }

  public function tc_reset_cache() {
    $this -> _cache = array();    
  }

  public function tc_get_cat_list() {
    return $this -> tc_get_meta_list( 'tax', true );    
  }
  
  public function tc_get_tag_list() {
    return $this -> tc_get_meta_list( 'tax', false );    
  }

  public function tc_get_meta_list( $meta, $param ) {
    if ( ! isset( $_cache[ $meta ] ) )  
      $_cache[ $meta ] = CZR() -> helpers -> tc_stringify_array( $this -> {"tc_meta_generate_{$meta}_list"}( $param ) );

    return $_cache[ $meta ];
  }

  private function tc_build_post_post_metas_model() {
//    $cat_list   = $this -> tc_meta_generate_tax_list( true );
    
//    $tag_list   = $this -> tc_meta_generate_tax_list( false );
//    $pub_date   = $this -> tc_get_meta_date( 'publication' );
//    $auth       = $this -> tc_get_meta_author();
//    $upd_date   = $this -> tc_get_meta_date( 'update' );
  }

          public function tc_meta_generate_tax_list( $hierarchical ) {
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
          $_classes         =  array( 'btn' , 'btn-mini' );
          $_is_hierarchical  =  is_taxonomy_hierarchical( $term -> taxonomy );
          if ( $_is_hierarchical ) //<= check if hierarchical (category) or not (tag)
            array_push( $_classes , 'btn-tag' );
          $_classes      = implode( ' ', apply_filters( 'tc_meta_tax_class', $_classes , $_is_hierarchical, $term ) );
          // (Rocco's PR Comment) : following to this https://wordpress.org/support/topic/empty-articles-when-upgrading-to-customizr-version-332
          // I found that at least wp 3.6.1  get_term_link($term->term_id, $term->taxonomy) returns a WP_Error
          // Looking at the codex, looks like we can just use get_term_link($term), when $term is a term object.
          // Just this change avoids the issue with 3.6.1, but I thought should be better make a check anyway on the return type of that function.
          $_term_link    = is_wp_error( get_term_link( $term ) ) ? '' : get_term_link( $term );
          $_to_return    = $_term_link ? '<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>' :  '<span class="%1$s"> %4$s </a>';
          return apply_filters( 'tc_meta_term_view' , sprintf($_to_return,
              $_classes,
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
        public function tc_get_term_of_tax_type( $hierarchical = true ) {
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
        public function tc_is_tax_authorized( $_tax_object , $post_type ) {
          $_in_exclude_list = in_array(
            $_tax_object['name'],
            apply_filters_ref_array ( 'tc_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , TC_utils::tc_id() ) )
          );
          $_is_private = false === (bool) $_tax_object['public'] && apply_filters_ref_array( 'tc_exclude_private_taxonomies', array( true, $_tax_object['public'], TC_utils::tc_id() ) );
          return ! $_in_exclude_list && ! $_is_private;
        }        
}
