<?php
if ( ! class_exists( 'TC_controller_content' ) ) :
  class TC_controller_content extends TC_controllers {
    static $instance;
    static $_cache = array();

    function __construct( $_args = array()) {
      self::$instance =& $this;
    }

    function tc_display_view_right_sidebar() {
      if ( ! isset( self::$_cache['right_sidebar'] ) )
        self::$_cache['right_sidebar'] = $this -> tc_display_view_sidebar( 'right' );
      return self::$_cache['right_sidebar'];
    }

    function tc_display_view_left_sidebar() {
      if ( ! isset( self::$_cache['left_sidebar'] ) )
        self::$_cache['left_sidebar'] = $this -> tc_display_view_sidebar( 'left' );
      return self::$_cache['left_sidebar'];
    }

    private function tc_display_view_sidebar( $position ) {
      if ( TC_utils::$inst -> tc_is_home_empty() )
        return false;

      static $sidebar_map = array(
        //id => allowed layout (- b both )
        'right'  => 'r',
        'left'   => 'l'
      );

      $screen_layout        = TC_utils::tc_get_layout( TC_utils::tc_id() , 'sidebar'  );
      if ( ! in_array( $screen_layout, array( $sidebar_map[$position], 'b' ) ) )
        return false;
      return true;
    }

    function tc_display_view_singular_headings() {
      return $this -> tc_display_view_post() || $this -> tc_display_view_attachment() || ( $this -> tc_display_view_page() && ! is_front_page() );
    }

    function tc_display_view_posts_list_headings() {
      if ( ! isset( self::$_cache['posts_list_headings'] ) ) {
        self::$_cache['posts_list_headings'] = ! TC_utils::$inst -> tc_is_home() && $this -> tc_is_list_of_posts();
      }
      return self::$_cache['posts_list_headings'];
    }

    function tc_display_view_post_list() {
      return $this -> tc_is_list_of_posts() &&
            //hack until we implement the "routers"
            apply_filters( 'tc_is_not_grid', true );
    }


    function tc_display_view_posts_list_title() {
      return $this -> tc_display_view_posts_list_headings() && ! is_search();
    }

    function tc_display_view_posts_list_search_title() {
      return $this -> tc_display_view_posts_list_headings() && is_search();
    }

    function tc_display_view_posts_list_description() {
      return $this -> tc_display_view_posts_list_headings() && ! is_author() && ! is_search();
    }

    function tc_display_view_author_description() {
      return ( $this -> tc_display_view_posts_list_headings() && is_author() ) &&
             apply_filters ( 'tc_show_author_meta', get_the_author_meta('description') );
    }

    function tc_display_view_page() {
      if ( ! isset( self::$_cache['page'] ) )
        self::$_cache['page'] =  'page' == $this -> tc_get_post_type()
        && is_singular()
        && ! $this -> tc_is_home_empty();

      return apply_filters( 'tc_show_page_content', self::$_cache['page'] );
    }

    function tc_display_view_post() {
      //check conditional tags : we want to show single post or single custom post types
      if ( ! isset( self::$_cache['post'] ) ) {
        global $post;
        self::$_cache['post'] = isset($post)
                                && is_singular()
                                && 'page' != $post -> post_type
                                && 'attachment' != $post -> post_type
                                && ! $this -> tc_is_home_empty();
      }
      return apply_filters( 'tc_show_single_post_content', self::$_cache['post'] );
    }

    function tc_display_view_post_footer() {
      if ( ! $this -> tc_display_view_post() || ! apply_filters( 'tc_show_single_post_footer', true ) )
        return;

      //@todo check if some conditions below not redundant?
      if ( ! apply_filters( 'tc_show_author_metas_in_post', esc_attr( TC_utils::$inst->tc_opt( 'tc_show_author_info' ) ) ) )
        return;

      return true;
    }

    function tc_display_view_attachment() {
      if ( ! isset( self::$_cache['attachment'] ) ) {
        global $post;
        self::$_cache['attachment'] = ! ( ! isset($post) || empty($post) || 'attachment' != $post -> post_type || !is_singular() );
      }
      return self::$_cache['attachment'];
    }

    function tc_display_view_singular_article() {
      return $this -> tc_display_view_post() || $this -> tc_display_view_page() || $this -> tc_display_view_attachment() ;
    }

    function tc_display_view_post_list_title() {
      return apply_filters('tc_display_customizr_headings', $this -> tc_display_view_posts_list_headings() || is_front_page() );
    }

    function tc_display_view_singular_title() {
      if ( ! isset( self::$_cache['singular_title'] ) )
        self::$_cache['singular_title'] =  is_singular() && ! ( is_front_page() && 'page' == get_option( 'show_on_front' ) );
      return apply_filters('tc_display_customizr_headings', self::$_cache['singular_title'] )  && ! is_feed();
    }


    function tc_display_view_post_metas() {
     if ( isset( self::$_cache['post_metas'] ) )
       return self::$_cache['post_metas'];

     //disable in attachment context, attachment post metas have their own class
     if ( is_attachment() )
       self::$_cache['post_metas'] = false;

     //post metas are always insanciated in customizing context
     elseif ( TC___::$instance -> tc_is_customizing() )
       self::$_cache['post_metas'] = true;

     elseif ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas' ) ) )
       self::$_cache['post_metas'] = false;

     elseif ( is_singular() && ! is_page() && ! TC_utils::$inst -> tc_is_home() )
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_single_post' ) ) );

     elseif ( ! is_singular() && ! TC_utils::$inst -> tc_is_home() && ! is_page() )
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_post_lists' ) ) );

     elseif ( TC_utils::$inst -> tc_is_home() )
       self::$_cache['post_metas'] = ( 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_metas_home' ) ) );
     else
       self::$_cache['post_metas'] = false;

     return self::$_cache['post_metas'];
    }


    function tc_display_view_post_metas_text() {
      return $this -> tc_display_view_post_metas() && 'buttons' != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_design' ) );
    }

    function tc_display_view_post_metas_button() {
      return $this -> tc_display_view_post_metas() && 'buttons' == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_design' ) );
    }

    //when to display attachment post metas?
    //a) in single attachment page
    //b) eventually, in the search list when attachments are allowed
    function tc_display_view_post_metas_attachment() {
      return is_attachment() ||
        ( is_search() && apply_filters( 'tc_include_attachments_in_search_results' , false ) );
    }

    /* Thumbnails in post lists */
    function tc_display_view_post_list_rectangular_thumb() {
      return $this -> tc_display_view_post_list_thumbnail() &&
            FALSE !== strpos( esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape'), 'rectangular' ), 'rectangular' );
    }

    function tc_display_view_post_list_standard_thumb() {
      return $this -> tc_display_view_post_list_thumbnail() &&
            FALSE === strpos( esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_thumb_shape') ), 'rectangular' );
    }

    /* Helper */
    function tc_display_view_post_list_thumbnail() {
      if ( ! isset( self::$_cache['post_list_thumbnail'] ) )
        self::$_cache[ 'post_list_thumbnail' ] = $this -> tc_display_view_post_list() && 'full' != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_length' ) ) && 0 != esc_attr( TC_utils::$inst->tc_opt( 'tc_post_list_show_thumb' ) );
      return self::$_cache[ 'post_list_thumbnail' ];
    }
    /* end  Thumbnails in post lists*/

    /* Single post thumbnail */
    function tc_display_view_post_thumbnail() {
      return $this -> tc_display_view_post() && 'hide' != esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' ) )
        && apply_filters( 'tc_show_single_post_thumbnail' , TC_utils_thumbnails::$instance -> tc_has_thumb() );
    }


    function tc_display_view_post_navigation_singular() {
      if ( ! $this -> tc_display_post_navigation() )
        return false;

      if ( ! isset( self::$_cache['post_navigation_singular'] ) ) {

        self::$_cache['post_navigation_singular'] = false;

        $_context = $this -> tc_get_post_navigation_context();
        if ( TC___::$instance -> tc_is_customizing() && in_array( $_context, array('page', 'single') ) )
          self::$_cache['post_navigation_singular'] = true;
        elseif ( $this -> tc_is_post_navigation_enabled() )
          self::$_cache['post_navigation_singular'] = in_array( $_context, array('page', 'single') ) ? $this -> tc_is_post_navigation_context_enabled( $_context ) : false;
      }

      return self::$_cache['post_navigation_singular'];
    }


    function tc_display_view_post_navigation_posts() {
      if ( ! $this -> tc_display_post_navigation() )
        return false;

      if ( ! isset( self::$_cache['post_navigation_posts'] ) ) {

        self::$_cache['post_navigation_posts'] = false;

        $_context = $this -> tc_get_post_navigation_context();
        if ( TC___::$instance -> tc_is_customizing() && in_array( $_context, array('home', 'archive') ) )
          self::$_cache['post_navigation_posts'] = true;
        elseif ( $this -> tc_is_post_navigation_enabled() )
          self::$_cache['post_navigation_posts'] = in_array( $_context, array('home', 'archive') ) ? $this -> tc_is_post_navigation_context_enabled( $_context ) : false;
      }

      return self::$_cache['post_navigation_posts'];
    }

    function tc_display_post_navigation() {
      global $wp_query;
      $bool  =  $wp_query -> post_count > 0;
      return is_singular() ?  $bool && ! is_attachment() : $bool;
    }

    function tc_display_view_404() {
      return is_404();
    }

    function tc_display_view_no_results() {
      return $this -> tc_is_no_results();
    }

    function tc_display_view_headings() {
      return true;
    }

    function tc_display_view_comments() {
      return $this -> tc_are_comments_enabled();
    }

    function tc_display_view_comment_list() {
      return apply_filters( 'tc_display_comment_list', $this -> tc_display_view_comments() );
    }

    function tc_display_view_comment() {
      return $this -> tc_display_view_comment_list();
    }

    function tc_display_view_trackpingback() {
      return $this -> tc_display_view_comment_list();
    }

   /******************************
    VARIOUS HELPERS
    *******************************/
    public function tc_is_list_of_posts() {
      global $wp_query;
      //must be archive or search result. Returns false if home is empty in options.
      return apply_filters( 'tc_post_list_controller',
        ! is_singular()
        && ! is_404()
        && 0 != $wp_query -> post_count
        && ! $this -> tc_is_home_empty()
        && ! is_admin()
      );
    }

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
    public function tc_are_comments_enabled() {
      if ( ! isset(self::$_cache['comments_enabled'] ) ) {

        global $post;
        // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
        if ( isset( $post ) ) {
          $_bool = ( post_password_required() || TC_utils::$inst -> tc_is_home() || ! is_singular() )  ? false : true;

          //2) if user has enabled comment for this specific post / page => true
          //@todo contx : update default value user's value)
          $_bool = ( 'closed' != $post -> comment_status ) ? true && $_bool : $_bool;

          //3) check global user options for pages and posts
          if ( is_page() )
            $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_page_comments' )) && $_bool;
          else
            $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_comments' )) && $_bool;
        } else
          $_bool = false;

        self::$_cache['comments_enabled'] = $_bool;
      }
      return apply_filters( 'tc_are_comments_enabled', self::$_cache['comments_enabled'] );
    }

    /**
    *
    * @return string or bool
    *
    */
    function tc_get_post_navigation_context(){
      if ( is_page() )
        return 'page';
      if ( is_single() && ! is_attachment() )
        return 'single'; // exclude attachments
      if ( is_home() && 'posts' == get_option('show_on_front') )
        return 'home';
      if ( !is_404() && !$this -> tc_is_home_empty() )
        return 'archive';

      return false;
    }

    /*
    * @param (string or bool) the context
    * @return bool
    */
    function tc_is_post_navigation_context_enabled( $_context ) {
      return $_context && 1 == esc_attr( TC_utils::$inst -> tc_opt( "tc_show_post_navigation_{$_context}" ) );
    }

    /*
    * @return bool
    */
    function tc_is_post_navigation_enabled(){
      return 1 == esc_attr( TC_utils::$inst -> tc_opt( 'tc_show_post_navigation' ) ) ;
    }

  }//end of class
endif;
