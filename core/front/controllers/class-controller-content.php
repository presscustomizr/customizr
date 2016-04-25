<?php
if ( ! class_exists( 'CZR_cl_controller_content' ) ) :
  class CZR_cl_controller_content extends CZR_cl_controllers {
    static $instance;

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }

    function czr_fn_display_view_right_sidebar() {
      return $this -> czr_fn_display_view_sidebar( 'right' );
    }

    function czr_fn_display_view_left_sidebar() {
      return $this -> czr_fn_display_view_sidebar( 'left' );
    }

    private function czr_fn_display_view_sidebar( $position ) {
      if ( CZR_cl_utils::$inst -> czr_fn_is_home_empty() )
        return false;

      static $sidebar_map = array(
        //id => allowed layout (- b both )
        'right'  => 'r',
        'left'   => 'l'
      );

      $screen_layout        = CZR_cl_utils::czr_fn_get_layout( CZR_cl_utils::czr_fn_id() , 'sidebar'  );
      if ( ! in_array( $screen_layout, array( $sidebar_map[$position], 'b' ) ) )
        return false;
      return true;
    }

    function czr_fn_display_view_singular_headings() {
      return $this -> czr_fn_display_view_post() || $this -> czr_fn_display_view_attachment() || ( $this -> czr_fn_display_view_page() && ! is_front_page() );
    }

    function czr_fn_display_view_posts_list_headings() {
      return ! CZR_cl_utils::$inst -> czr_fn_is_home() && CZR_cl_utils_query::$instance -> czr_fn_is_list_of_posts();
    }

    function czr_fn_display_view_post_list() {
      return apply_filters( 'czr_fn_display_view_post_list', CZR_cl_utils_query::$instance -> czr_fn_is_list_of_posts() );
    }


    function czr_fn_display_view_posts_list_title() {
      return $this -> czr_fn_display_view_posts_list_headings() && ! is_search();
    }

    function czr_fn_display_view_posts_list_search_title() {
      return $this -> czr_fn_display_view_posts_list_headings() && is_search();
    }

    function czr_fn_display_view_posts_list_description() {
      return $this -> czr_fn_display_view_posts_list_headings() && ! is_author() && ! is_search();
    }

    function czr_fn_display_view_author_description() {
      return ( ( $this -> czr_fn_display_view_posts_list_headings() && is_author() ) &&
             apply_filters ( 'tc_show_author_meta', get_the_author_meta('description') ) )
      || ( is_single() && apply_filters ( 'tc_show_author_meta', get_the_author_meta('description') ) );
    }

    function czr_fn_display_view_page() {
      return apply_filters( 'tc_show_single_page_content', CZR_cl_utils_query::$instance -> czr_fn_is_single_page() );
    }

    function czr_fn_display_view_post() {
      //check conditional tags : we want to show single post or single custom post types
      return apply_filters( 'tc_show_single_post_content', CZR_cl_utils_query::$instance -> czr_fn_is_single_post() );
    }

    function czr_fn_display_view_single_author_info() {
      if ( ! ( $this -> czr_fn_display_view_post() && get_the_author_meta( 'description' ) ) )
        return;

      //@todo check if some conditions below not redundant?
      if ( ! apply_filters( 'tc_show_author_metas_in_post', esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_author_info' ) ) ) )
        return;

      return true;
    }

    function czr_fn_display_view_attachment() {
      return apply_filters( 'tc_show_attachment_content', CZR_cl_utils_query::$instance -> czr_fn_is_single_attachment() );
    }


    function czr_fn_display_view_singular_article() {
      return $this -> czr_fn_display_view_post() || $this -> czr_fn_display_view_page() || $this -> czr_fn_display_view_attachment() ;
    }

    function czr_fn_display_view_post_list_title() {
      return apply_filters('tc_display_customizr_headings', $this -> czr_fn_display_view_posts_list_headings() || is_front_page() );
    }

    function czr_fn_display_view_singular_title() {
      $display_singular_title = apply_filters( 'tc_display_singular_title', is_singular() && ! ( is_front_page() && 'page' == get_option( 'show_on_front' ) ));
      return apply_filters('tc_display_customizr_headings', $display_singular_title )  && ! is_feed();
    }


    function czr_fn_display_view_post_metas() {
      //disable in attachment context, attachment post metas have their own class
      if ( is_attachment() )
        $post_metas = false;

      //post metas are always insanciated in customizing context
      elseif ( CZR___::$instance -> czr_fn_is_customizing() )
        $post_metas = true;

      elseif ( 0 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_post_metas' ) ) )
        $post_metas = false;

      elseif ( is_singular() && ! is_page() && ! CZR_cl_utils::$inst -> czr_fn_is_home() )
        $post_metas = ( 0 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_post_metas_single_post' ) ) );

      elseif ( ! is_singular() && ! CZR_cl_utils::$inst -> czr_fn_is_home() && ! is_page() )
        $post_metas = ( 0 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_post_metas_post_lists' ) ) );

      elseif ( CZR_cl_utils::$inst -> czr_fn_is_home() )
        $post_metas = ( 0 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_post_metas_home' ) ) );
      else
        $post_metas = false;

      return apply_filters( 'tc_show_post_metas', $post_metas );
    }


    function czr_fn_display_view_post_metas_text() {
      return $this -> czr_fn_display_view_post_metas() && 'buttons' != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_metas_design' ) );
    }

    function czr_fn_display_view_post_metas_button() {
      return $this -> czr_fn_display_view_post_metas() && 'buttons' == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_metas_design' ) );
    }

    //when to display attachment post metas?
    //a) in single attachment page
    //b) eventually, in the search list when attachments are allowed
    function czr_fn_display_view_post_metas_attachment() {
      return is_attachment() ||
        ( is_search() && apply_filters( 'czr_fn_include_attachments_in_search_results' , false ) );
    }

    /* Thumbnails in post lists */
    function czr_fn_display_view_post_list_rectangular_thumb() {
      return $this -> czr_fn_display_view_post_list_thumbnail() &&
            FALSE !== strpos( esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape'), 'rectangular' ), 'rectangular' );
    }

    function czr_fn_display_view_post_list_standard_thumb() {
      return $this -> czr_fn_display_view_post_list_thumbnail() &&
            FALSE === strpos( esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape') ), 'rectangular' );
    }

    /* Helper */
    function czr_fn_display_view_post_list_thumbnail() {
      $display_post_list_thumbnail = $this -> czr_fn_display_view_post_list() && 'full' != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_list_length' ) ) && 0 != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_list_show_thumb' ) );
      return $display_post_list_thumbnail;
    }
    /* end  Thumbnails in post lists*/

    /* Single post thumbnail */
    function czr_fn_display_view_post_thumbnail() {
      return $this -> czr_fn_display_view_post() && 'hide' != esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_location' ) )
        && apply_filters( 'tc_show_single_post_thumbnail' , CZR_cl_utils_thumbnails::$instance -> czr_fn_has_thumb() );
    }


    function czr_fn_display_view_post_navigation_singular() {
      if ( ! $this -> czr_fn_display_post_navigation() )
        return false;

      $post_navigation_singular = false;

      $_context = $this -> czr_fn_get_post_navigation_context();
      if ( CZR___::$instance -> czr_fn_is_customizing() && in_array( $_context, array('page', 'single') ) )
        $post_navigation_singular = true;
      elseif ( $this -> czr_fn_is_post_navigation_enabled() )
        $post_navigation_singular = in_array( $_context, array('page', 'single') ) ? $this -> czr_fn_is_post_navigation_context_enabled( $_context ) : false;

      return $post_navigation_singular;
    }


    function czr_fn_display_view_post_navigation_posts() {
      if ( ! $this -> czr_fn_display_post_navigation() )
        return false;

      $post_navigation_posts = false;

      $_context = $this -> czr_fn_get_post_navigation_context();
      if ( CZR___::$instance -> czr_fn_is_customizing() && in_array( $_context, array('home', 'archive') ) )
        $post_navigation_posts = true;
      elseif ( $this -> czr_fn_is_post_navigation_enabled() )
        $post_navigation_posts = in_array( $_context, array('home', 'archive') ) ? $this -> czr_fn_is_post_navigation_context_enabled( $_context ) : false;

      return $post_navigation_posts;
    }

    function czr_fn_display_post_navigation() {
      global $wp_query;
      $bool  =  $wp_query -> post_count > 0;
      return is_singular() ?  $bool && ! is_attachment() : $bool;
    }

    function czr_fn_display_view_404() {
      return is_404();
    }

    function czr_fn_display_view_no_results() {
      return CZR_cl_utils_query::$instance -> czr_fn_is_no_results();
    }

    function czr_fn_display_view_headings() {
      return true;
    }

    function czr_fn_display_view_comments() {
      return $this -> czr_fn_are_comments_enabled();
    }

    function czr_fn_display_view_comment_list() {
      return apply_filters( 'tc_display_comment_list', (bool) esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_show_comment_list' ) ) && $this -> czr_fn_display_view_comments() );
    }

    function czr_fn_display_view_comment() {
      return $this -> czr_fn_display_view_comment_list();
    }

    function czr_fn_display_view_trackpingback() {
      return $this -> czr_fn_display_view_comment_list();
    }

   /******************************
    VARIOUS HELPERS
    *******************************/


    /**
    * 1) if the page / post is password protected OR if is_home OR ! is_singular() => false
    * 2) if comment_status == 'closed' => false
    * 3) if user defined comment option in customizer == false => false
    *
    * By default, comments are globally disabled in pages and enabled in posts
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    public function czr_fn_are_comments_enabled() {

      global $post;
      // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
      if ( isset( $post ) ) {
        $_bool = post_password_required() ? false : true;

        $_bool = in_the_loop() ? $_bool && ( CZR_cl_utils::$inst -> czr_fn_is_home() || ! is_singular() ) : $_bool;

        //2) if user has enabled comment for this specific post / page => true
        //@todo contx : update default value user's value)
        $_bool = ( 'closed' != $post -> comment_status ) ? true : $_bool;

        //3) check global user options for pages and posts
        if ( 'page' == get_post_type() )
          $_bool = 1 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_page_comments' )) && $_bool;
        else
          $_bool = 1 == esc_attr( CZR_cl_utils::$inst->czr_fn_opt( 'tc_post_comments' )) && $_bool;
      } else
        $_bool = false;

      return apply_filters( 'tc_are_comments_enabled', $_bool );
    }

    /**
    *
    * @return string or bool
    *
    */
    function czr_fn_get_post_navigation_context(){
      if ( is_page() )
        return 'page';
      if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
      if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
      if ( !is_404() && ! CZR_cl_utils_query::$instance -> czr_fn_is_home_empty() )
        return 'archive';

      return false;
    }

    /*
    * @param (string or bool) the context
    * @return bool
    */
    function czr_fn_is_post_navigation_context_enabled( $_context ) {
      return $_context && 1 == esc_attr( CZR_cl_utils::$inst -> czr_fn_opt( "tc_show_post_navigation_{$_context}" ) );
    }

    /*
    * @return bool
    */
    function czr_fn_is_post_navigation_enabled(){
      return apply_filters( 'tc_show_post_navigation', 1 == esc_attr( CZR_cl_utils::$inst -> czr_fn_opt( 'tc_show_post_navigation' ) ) );
    }

  }//end of class
endif;
