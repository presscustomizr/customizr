<?php
class CZR_post_metas_model_class extends CZR_Model {


  /* PUBLIC GETTERS */
  public function czr_fn_get_cat_list( $limit = false, $sep = '' ) {
    return czr_fn_is_checked( 'tc_show_post_metas_categories' ) ? $this -> czr_fn_get_meta( 'categories', $limit, $sep ) : '';
  }

  public function czr_fn_get_tag_list( $limit = false, $sep = '' ) {
    return czr_fn_is_checked( 'tc_show_post_metas_tags' ) ? $this -> czr_fn_get_meta( 'tags', $limit, $sep ) : '';
  }

  public function czr_fn_get_author( $before = null ) {
    return czr_fn_is_checked( 'tc_show_post_metas_author' ) ? $this -> czr_fn_get_meta( 'author', array( $before ) ) : '';
  }

  public function czr_fn_get_author_with_avatar( $before = null ) {
    return czr_fn_is_checked( 'tc_show_post_metas_author' ) ? $this -> czr_fn_get_meta( 'author_with_avatar', array( $before ) ) : '';
  }

  public function czr_fn_get_publication_date( $permalink = false, $before = null, $only_text = false ) {
    return czr_fn_is_checked( 'tc_show_post_metas_publication_date' ) ? $this -> czr_fn_get_meta( 'pub_date', array(
        '',
        $permalink,
        $before,
        $only_text ) ) : '';
  }


  public function czr_fn_get_update_date( $permalink = false, $before = null, $only_text = false ) {
    return ( czr_fn_is_checked( 'tc_show_post_metas_update_date' ) && false !== czr_fn_post_has_update() ) ? $this -> czr_fn_get_meta( 'up_date', array( '', $permalink, $before, $only_text ) ) : '';
  }

  public function czr_fn_get_attachment_image_info( $permalink = false, $before = null ) {
    return $this -> czr_fn_get_meta( 'attachment_image_info' );
  }
  /* END PUBLIC GETTERS */


  /* HELPERS */
  protected function czr_fn_get_meta( $meta, $params = array(), $separator = '' ) {
    //we don't don't display metas in pages, e.g. in search results
    if ( 'page' == czr_fn_get_post_type() )
      return '';

    $params = is_array( $params ) ? $params : array( $params );
    return czr_fn_stringify_array( call_user_func_array( array( $this, "czr_fn_meta_generate_{$meta}" ), $params ), $separator );

  }


  private function czr_fn_meta_generate_categories( $limit = false ) {
    return $this -> czr_fn_meta_generate_tax_list( $hierarchical = true, $limit );
  }

  private function czr_fn_meta_generate_tags( $limit = false ) {
    return $this -> czr_fn_meta_generate_tax_list( $hierarchical = false, $limit );
  }

  private function czr_fn_meta_generate_author( $before ) {
    $author = $this -> czr_fn_get_meta_author();
    $before = is_null($before) ? __( 'by', 'customizr' ) : $before;
    return sprintf( '<span class="author-meta">%1$s %2$s</span>', $before, $author );
  }

  private function czr_fn_meta_generate_author_with_avatar( $before ) {
    $author = $this -> czr_fn_get_meta_author( $get_avatar = true );
    $before = is_null($before) ? __( 'by', 'customizr' ) : $before;
    return sprintf( '<span class="author-meta">%1$s %2$s</span>', $before, $author );
  }


  private function czr_fn_meta_generate_pub_date( $format = '', $permalink = false, $before = null, $only_text = false ) {
    $date   = $this -> czr_fn_get_meta_date( 'publication', $format, $permalink, $only_text );
    $before = is_null($before) ? __( 'Published', 'customizr' ) : $before;

    return sprintf( '%1$s %2$s', $before , $date );
  }

  private function czr_fn_meta_generate_up_date( $format = '', $permalink = false, $before = null, $only_text = false ) {
    $date   = $this -> czr_fn_get_meta_date( 'update', $format, $permalink, $only_text );
    $before = is_null($before) ? __( 'Updated', 'customizr' ) : $before;

    return sprintf( '%1$s %2$s', $before , $date );
  }


  public function czr_fn_meta_generate_attachment_image_info() {
    $metadata = wp_get_attachment_metadata();

    global $post;

    $_html_parts  = array(
      ( isset($metadata['width']) && isset($metadata['height']) ) ? '<span class="attachment-size">' . __('at dimensions' , 'customizr').'<a href="'.esc_url( wp_get_attachment_url() ).'" title="'.__('Link to full-size image' , 'customizr').'" target="_blank"> '.$metadata['width'].' &times; '.$metadata['height'].'</a></span>' : '',
      //when post parent id is 0 means that the media is not attached to any post
      ( 0 != $post->post_parent ) ? '<span class="attachment-parent">' . __('in' , 'customizr') . '<a href="' . esc_url( get_permalink( $post->post_parent ) ) . '" title="' . the_title_attribute( array( 'before' => __('Return to ' , 'customizr'), 'echo' =>false ) ) .'" rel="gallery"> '.strip_tags( get_the_title( $post->post_parent ) ).'</a></span>' : ''
    );

    return apply_filters( 'tc_attachment_image_sizes_meta', join( ' ', $_html_parts ) );
  }



  protected function czr_fn_get_term_css_class( $_is_hierarchical ) {
    $_classes = array();

    if ( $_is_hierarchical )
      array_push( $_classes , 'tax__link' );
    else
      array_push( $_classes , 'tag__link btn btn-skin-dark-oh inverted' );

    return $_classes;
  }


  /* Helpers */


  /**
  * Helper
  * Return the date post metas
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  protected function czr_fn_get_meta_date( $pub_or_update = 'publication', $_format = '', $permalink = false, $only_text = false ) {
    if ( 'short' == $_format ) {
        $_format = 'j M, Y';
    }
    $user_date_format = get_option('date_format');

    $_format = apply_filters( 'czr_meta_date_format' , $_format );
    $_use_post_mod_date = apply_filters( 'czr_use_the_post_modified_date' , 'publication' != $pub_or_update );

    //time
    $date_meta = sprintf( '<time class="entry-date %1$s" datetime="%2$s">%3$s</time>',
        'publication' == $pub_or_update ? 'published updated' : 'updated',
        $_use_post_mod_date ? esc_attr( get_the_modified_date($user_date_format) ) : esc_attr( get_the_date( $user_date_format ) ),
        $_use_post_mod_date ? esc_html( get_the_modified_date( $_format ) ) : esc_html( get_the_date( $_format ) )
    );

    if ( ! $only_text ) {
        $date_meta = sprintf( '<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
            $permalink ? esc_url( get_the_permalink() ) : esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
            $permalink ? esc_attr( the_title_attribute( array( 'before' => __('Permalink to:&nbsp;', 'customizr'), 'echo' => false ) ) ) : esc_attr( get_the_time() ),
            $date_meta
        );
    }
    return apply_filters( 'tc_date_meta', $date_meta );//end filter
  }



  /**
  * Helper
  * Return the post author metas
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  private function czr_fn_get_meta_author( $get_avatar = false ) {
    $author_id = get_the_author_meta( 'ID' );

    if ( is_single() ) {
      if ( ! in_the_loop() ) {
        global $post;
        $author_id = $post->post_author;
      }
    }

    $author_id_array = apply_filters( 'tc_post_author_id', array( $author_id ) );
    $author_id_array = is_array( $author_id_array ) ? $author_id_array : array( $author_id );

    $_html  = '';
    $_i     = 1;

    foreach ( $author_id_array as $author_id ) {

      $author_name = get_the_author_meta( 'display_name', $author_id );

      if ( 1 != $_i && count( $author_id_array ) > $_i - 1 ) {
        $_html    .= ', ';
      }

      $_html      .= '<span class="author vcard">';
      if ( $get_avatar ) {
        $_html    .= sprintf( '<span class="author-avatar">%1$s</span>', get_avatar( get_the_author_meta( 'user_email', $author_id ), 48 ) );
      }
      $_html      .= sprintf( '<span class="author_name"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
            esc_url( get_author_posts_url( $author_id ) ),
            esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), $author_name ) ),
            $author_name
      );
      $_html      .= '</span>';
      $_i         +=1;
    }

    return apply_filters('tc_author_meta', $_html );
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

    if ( 0 == esc_attr( czr_fn_opt( 'tc_show_post_metas' ) ) )
       $_classes[] = 'hide-all-post-metas';

    if (
        ( is_singular() && ! is_page() && ! czr_fn_is_real_home() && 0 == esc_attr( czr_fn_opt( 'tc_show_post_metas_single_post' ) ) ) ||
        ( ! is_singular() && ! czr_fn_is_real_home() && ! is_page() && 0 == esc_attr( czr_fn_opt( 'tc_show_post_metas_post_lists' ) ) ) ||
        ( czr_fn_is_real_home() ) && 0 == esc_attr( czr_fn_opt( 'tc_show_post_metas_home' ) )
    )
      $_classes[] = 'hide-post-metas';

    return $_classes;
  }
}//end of class