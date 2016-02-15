<<<<<<< HEAD
<?php
/**
* Headings actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.1.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_headings' ) ) :
  class TC_headings {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //Headings for archives, authors, search, 404
        add_action  ( '__before_loop'                 , array( $this , 'tc_archives_headings' ));
        //Headings for post, page, attachment
        add_action  ( '__before_content'              , array( $this , 'tc_content_headings' ));
      }



      /**
      * The template part for displaying the not post/page headings : archives, author, search, 404 and the post page heading (if not font page)
      *
      * @package Customizr
      * @since Customizr 3.1.0
      */
      function tc_archives_headings() {
        //case page for posts but not on front
        global $wp_query;
        if ( $wp_query -> is_posts_page && ! is_front_page() ) {
          //get page for post ID
          $page_for_post_id = get_option('page_for_posts');
          $header_class   = 'entry-header';
          $content        = sprintf('<%1$s class="entry-title %2$s">%3$s</%1$s>',
                apply_filters( 'tc_content_title_tag' , 'h1' ),
                apply_filters( 'tc_content_title_icon', 'format-icon' ),
                get_the_title( $page_for_post_id )
           );
          $content        = apply_filters( 'tc_page_for_post_header_content', $content );
        }


        //404
        if ( is_404() ) {
          $header_class   = 'entry-header';
          $content        = sprintf('<h1 class="entry-title %1$s">%2$s</h1>',
                apply_filters( 'tc_archive_icon', '' ),
                apply_filters( 'tc_404_title' , __( 'Ooops, page not found' , 'customizr' ) )
           );
          $content        = apply_filters( 'tc_404_header_content', $content );
        }

        //search results
        if ( is_search() && !is_singular() ) {
          $header_class   = 'search-header';
          $content        = sprintf( '<div class="row-fluid"><div class="%1$s"><h1 class="%2$s">%3$s%4$s %5$s </h1></div><div class="%6$s">%7$s</div></div>',
                apply_filters( 'tc_search_result_header_title_class', 'span8' ),
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                have_posts() ? '' :  __( 'No' , 'customizr' ).'&nbsp;' ,
                apply_filters( 'tc_search_results_title' , __( 'Search Results for :' , 'customizr' ) ),
                '<span>' . get_search_query() . '</span>',
                apply_filters( 'tc_search_result_header_form_class', 'span4' ),
                have_posts() ? get_search_form(false) : ''
          );
          $content       = apply_filters( 'tc_search_results_header_content', $content );
        }
        
        //author's posts page
        if ( !is_singular() && is_author() ) {
          //gets the user ID
          $user_id = get_query_var( 'author' );
          $header_class   = 'archive-header';
          $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                  apply_filters( 'tc_archive_icon', 'format-icon' ),
                  apply_filters( 'tc_author_archive_title' , __( 'Author Archives :' , 'customizr' ) ),
                  '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' , $user_id ) ) . '" rel="me">' . get_the_author_meta( 'display_name' , $user_id ) . '</a></span>' 
          );
          if ( apply_filters ( 'tc_show_author_meta' , get_the_author_meta( 'description', $user_id  ) ) ) {
            $content    .= sprintf('%1$s<div class="author-info"><div class="%2$s">%3$s</div></div>',

              apply_filters( 'tc_author_meta_separator', '<hr class="featurette-divider '.current_filter().'">' ),

              apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),

              sprintf('<div class="%1$s">%2$s</div><div class="%3$s"><h2>%4$s</h2><p>%5$s</p></div>',
                  apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                  get_avatar( get_the_author_meta( 'user_email', $user_id ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) ),
                  apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                  sprintf( __( 'About %s' , 'customizr' ), get_the_author() ),
                  get_the_author_meta( 'description' , $user_id  )
              )
            );
          }
          $content       = apply_filters( 'tc_author_header_content', $content );
        }

        //category archives
        if ( !is_singular() && is_category() ) {
          $header_class   = 'archive-header';
          $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tc_category_archive_title' , __( 'Category Archives :' , 'customizr' ) ),
                '<span>' . single_cat_title( '' , false ) . '</span>'
          );
          if ( apply_filters ( 'tc_show_cat_description' , category_description() ) ) {
            $content    .= sprintf('<div class="archive-meta">%1$s</div>',
              category_description()
            );
          }
          $content       = apply_filters( 'tc_category_archive_header_content', $content );
        }

        //tag archives
        if ( !is_singular() && is_tag() ) {
          $header_class   = 'archive-header';
          $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tag_archive_title' , __( 'Tag Archives :' , 'customizr' ) ),
                '<span>' . single_tag_title( '' , false ) . '</span>'
          );
          if ( apply_filters ( 'tc_show_tag_description' , tag_description() ) ) {
            $content    .= sprintf('<div class="archive-meta">%1$s</div>',
              tag_description()
            );
          }
          $content       = apply_filters( 'tc_tag_archive_header_content', $content );
        }

        //time archives
        if ( !is_singular() && ( is_day() || is_month() || is_year() ) ) {
          $archive_type   = is_day() ? sprintf( __( 'Daily Archives: %s' , 'customizr' ), '<span>' . get_the_date() . '</span>' ) : __( 'Archives' , 'customizr' );
          $archive_type   = is_month() ? sprintf( __( 'Monthly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
          $archive_type   = is_year() ? sprintf( __( 'Yearly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
          $header_class   = 'archive-header';
          $content        = sprintf('<h1 class="%1$s">%2$s</h1>',
            apply_filters( 'tc_archive_icon', 'format-icon' ),
            $archive_type
          );
          $content        = apply_filters( 'tc_time_archive_header_content', $content );
        }

        //renders the heading
        if ( !isset($content) || !isset($header_class) )
          return;
        global $wp_query;
        ob_start();
        ?>

        <header class="<?php echo apply_filters( 'tc_archive_header_class', $header_class ); ?>">
          <?php 
          do_action('__before_archive_title');

          echo apply_filters( 'tc_archive_header_content', $content );

          do_action('__after_archive_title');
          
          echo ( !tc__f('__is_home') && !$wp_query -> is_posts_page  ) ? apply_filters( 'tc_archives_headings_separator', '<hr class="featurette-divider '.current_filter(). '">' ) : '';
          ?>
        </header>

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_archives_headings', $html );
      }//end of function




      /**
       * The template part for displaying the post/page header
       *
       * @package Customizr
       * @since Customizr 3.1.0
       */
      function tc_content_headings() {
        //we don't display titles for some post formats
        $post_formats_with_no_header = apply_filters( 'tc_post_formats_with_no_header', TC_init::$instance -> post_formats_with_no_header );
        if( in_array( get_post_format(), $post_formats_with_no_header ) )
          return;

        //by default we don't display the title of the front page
        if( apply_filters('tc_show_page_title', is_front_page() && 'page' == get_option( 'show_on_front' ) ) )
          return
        ?>

          <header class="<?php echo apply_filters( 'tc_content_header_class', 'entry-header' ); ?>">
            
            <?php 
            do_action('__before_content_title');

            //adds filters for comment bubble style and icon
            $bubble_style                      = ( 0 == get_comments_number() ) ? 'style="color:#ECECEC" ':'';
            $bubble_style                      = apply_filters( 'tc_comment_bubble_style', $bubble_style );
            $bubble_comment                    = sprintf('<span class="fs1 icon-bubble" %1$s></span><span class="inner">%2$s</span>',
                                                    $bubble_style,
                                                    get_comments_number()
                                                    );
            $bubble_comment                    = apply_filters( 'tc_bubble_comment', $bubble_comment );

            //when are we showing the comments number in title?
            $comments_enabled                  = ( 1 == esc_attr( tc__f( '__get_option' , 'tc_page_comments' )) && comments_open() && get_comments_number() != 0 && !post_password_required() && is_page() ) ? true : false;
            $comments_enabled                  = ( comments_open() && get_comments_number() != 0 && !post_password_required() && !is_page() ) ? true : $comments_enabled;
            $comments_enabled                  = apply_filters( 'tc_comments_in_title', $comments_enabled );

            //when are we displaying the edit link?
            $edit_enabled                      = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page() ) ? true : false;
            $edit_enabled                      = ( (is_user_logged_in()) && current_user_can('edit_post' , get_the_ID() ) && ! is_page() ) ? true : $edit_enabled;
            $edit_enabled                      = apply_filters( 'tc_edit_in_title', $edit_enabled );

            //declares vars
            $html = '';
            $filter_args = array();

            if ( (get_the_title() != null) ) {
              
              //gets the post/page title
              if ( is_singular() || ! apply_filters('tc_display_link_for_post_titles' , true ) ) {
                $tc_heading_title = ( get_the_title() == null ) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : get_the_title();
              }
              else {
                $tc_heading_title = sprintf('<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
                                get_permalink(),
                                esc_attr( sprintf( apply_filters( 'tc_post_link_title' ,__( 'Permalink to %s' , 'customizr' ) ) , the_title_attribute( 'echo=0' ) ) ),
                                ( get_the_title() == null ) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : get_the_title()
                              );//end sprintf
              }

              $filter_args        =  array( $bubble_comment, $comments_enabled, $edit_enabled , $tc_heading_title );

              //renders the full title
              $html = sprintf('<%1$s class="entry-title %2$s">%3$s %4$s %5$s</%1$s>',
                              apply_filters( 'tc_content_title_tag' , is_singular() ? 'h1' : 'h2' ),
                              apply_filters( 'tc_content_title_icon', 'format-icon' ),
                              $tc_heading_title,

                              //checks if comments are opened AND if there are any comments to display
                              $comments_enabled ? sprintf('<span class="comments-link"><a href="%1$s#tc-comment-title" title="%2$s %3$s">%4$s</a></span>',
                                                    is_singular() ? '' : get_permalink(),
                                                    __( 'Comment(s) on' , 'customizr' ),
                                                    get_the_title(),
                                                    $bubble_comment
                                                    ) : '',

                              $edit_enabled ? sprintf('<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s">%2$s</a></span>',
                                                get_edit_post_link(),
                                                __( 'Edit' , 'customizr' )
                                                ) : ''
              );//end sprintf

            }//end if title exists
            echo apply_filters( 'tc_content_headings' , $html, $filter_args );

            do_action('__after_content_title');

            echo is_singular() ? apply_filters( 'tc_content_headings_separator', '<hr class="featurette-divider '.current_filter().'">' ) : '';
            ?>

          </header><!-- .entry-header -->

        <?php
      }//end of function
  }//end of class
endif;
=======
<?php
/**
* Headings actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.1.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_headings' ) ) :
  class TC_headings {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //set actions and filters for posts and page headings
        add_action( 'template_redirect'                            , array( $this , 'tc_set_post_page_heading_hooks') );
        //set actions and filters for archives headings
        add_action( 'template_redirect'                            , array( $this , 'tc_set_archives_heading_hooks') );
        //Set headings user options
        add_action( 'template_redirect'                            , array( $this , 'tc_set_headings_options') );
      }


      /******************************************
      * HOOK SETTINGS ***************************
      ******************************************/
      /**
      * @return void
      * set up hooks for archives headings
      * hook : template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_archives_heading_hooks() {
        //is there anything to render in the current context
        //by default don't display the Customizr title in feeds
        if ( apply_filters('tc_display_customizr_headings',  ! $this -> tc_archive_title_and_class_callback() || is_feed() ) )
          return;

        //Headings for archives, authors, search, 404
        add_action ( '__before_loop'                  , array( $this , 'tc_render_headings_view' ) );
        //Set archive icon with customizer options (since 3.2.0)
        add_filter ( 'tc_archive_icon'                , array( $this , 'tc_set_archive_icon' ) );

        add_filter( 'tc_archive_header_class'         , array( $this , 'tc_archive_title_and_class_callback'), 10, 2 );
        add_filter( 'tc_headings_archive_html'        , array( $this , 'tc_archive_title_and_class_callback'), 10, 1 );
        global $wp_query;
        if ( tc__f('__is_home') )
          add_filter( 'tc_archive_headings_separator' , '__return_false' );
      }


      /**
      * @return void
      * set up hooks for post and page headings
      * callback of template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_post_page_heading_hooks() {

        //by default don't display the Customizr title of the front page and in feeds
        if ( apply_filters('tc_display_customizr_headings', ( is_front_page() && 'page' == get_option( 'show_on_front' ) ) ) || is_feed() )
          return;

        //Set single post/page icon with customizer options (since 3.2.0)
        add_filter ( 'tc_content_title_icon'    , array( $this , 'tc_set_post_page_icon' ) );
        //Prepare the headings for post, page, attachment
        add_action ( '__before_content'         , array( $this , 'tc_render_headings_view' ) );
        //Populate heading with default content
        add_filter ( 'tc_headings_content_html' , array( $this , 'tc_post_page_title_callback'), 10, 2 );
        //Create the Customizr title
        add_filter( 'tc_the_title'              , array( $this , 'tc_content_heading_title' ) , 0 );
        //Add edit link
        add_filter( 'tc_the_title'              , array( $this , 'tc_add_edit_link_after_title' ), 2 );
        //Set user defined archive titles
        add_filter( 'tc_category_archive_title' , array( $this , 'tc_set_archive_custom_title' ) );
        add_filter( 'tc_tag_archive_title'      , array( $this , 'tc_set_archive_custom_title' ) );
        add_filter( 'tc_search_results_title'   , array( $this , 'tc_set_archive_custom_title' ) );
        add_filter( 'tc_author_archive_title'   , array( $this , 'tc_set_archive_custom_title' ) );


        //SOME DEFAULT OPTIONS
        //No hr if not singular
        if ( ! is_singular() )
          add_filter( 'tc_content_headings_separator' , '__return_false' );

        //No headings for some post formats
        add_filter( 'tc_headings_content_html'  , array( $this, 'tc_post_formats_heading') , 100 );

      }


      /******************************************
      * VIEWS ***********************************
      ******************************************/
      /**
      * Generic heading view : archives, author, search, 404 and the post page heading (if not font page)
      * This is the place where every heading content blocks are hooked
      * hook : __before_content AND __before_loop (for post lists)
      *
      * @package Customizr
      * @since Customizr 3.1.0
      */
      function tc_render_headings_view() {
        $_heading_type = in_the_loop() ? 'content' : 'archive';
        ob_start();
        ?>
        <header class="<?php echo implode( ' ' , apply_filters( "tc_{$_heading_type}_header_class", array('entry-header'), $_return_class = true ) ); ?>">
          <?php
            do_action( "__before_{$_heading_type}_title" );
            echo apply_filters( "tc_headings_{$_heading_type}_html", '' , $_heading_type );
            do_action( "__after_{$_heading_type}_title" );

            echo apply_filters( "tc_{$_heading_type}_headings_separator", '<hr class="featurette-divider '.current_filter(). '">' );
          ?>
        </header>
        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_render_headings_view', $html );
      }//end of function





      /******************************************
      * HELPERS / SETTERS / CALLBACKS ***********
      ******************************************/
      /**
      * @return string or boolean
      * Returns the heading html content or false
      * callback of tc_headings_{$_heading_type}_html where $_heading_type = content when in the loop
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function tc_post_formats_heading( $_html ) {
        if( in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', TC_init::$instance -> post_formats_with_no_heading ) ) )
          return;
        return $_html;
      }


      /**
      * Callback for tc_headings_content_html
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_post_page_title_callback( $_content = null , $_heading_type = null ) {
        $_title = apply_filters( 'tc_title_text', get_the_title() );
        return sprintf('<%1$s class="entry-title %2$s">%3$s</%1$s>',
              apply_filters( 'tc_content_title_tag' , is_singular() ? 'h1' : 'h2' ),
              apply_filters( 'tc_content_title_icon', 'format-icon' ),
              apply_filters( 'tc_the_title', $_title )
        );
      }

      /**
      * Callback for tc_the_title
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_content_heading_title( $_title ) {
        //Must be in the loop
        if ( ! in_the_loop() )
          return $_title;

        //gets the post/page title
        if ( is_singular() || ! apply_filters('tc_display_link_for_post_titles' , true ) )
          return is_null($_title) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : $_title;
        else
          return sprintf('<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
            get_permalink(),
            sprintf( apply_filters( 'tc_post_link_title' , __( 'Permalink to %s' , 'customizr' ) ) , esc_attr( strip_tags( get_the_title() ) ) ),
            is_null($_title) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : $_title
          );//end sprintf
      }


      /**
      * Callback for tc_the_title
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_add_edit_link_after_title( $_title ) {
        //Must be in the loop
        if ( ! in_the_loop() )
          return $_title;

        if ( ! apply_filters( 'tc_edit_in_title', $this -> tc_is_edit_enabled() ) )
          return $_title;

        return sprintf('%1$s %2$s',
          $_title,
          $this -> tc_render_edit_link_view( $_echo = false )
        );

      }


      /**
      * Helper Boolean
      * @return boolean
      * @package Customizr
      * @since Customizr 3.3+
      */
      public function tc_is_edit_enabled() {
        //when are we displaying the edit link?
        $edit_enabled = ( (is_user_logged_in()) && is_page() && current_user_can('edit_pages') ) ? true : false;
        return ( (is_user_logged_in()) && 0 !== get_the_ID() && current_user_can('edit_post' , get_the_ID() ) && ! is_page() ) ? true : $edit_enabled;
      }



      /**
      * Returns the edit link html string
      * @return  string
      * @package Customizr
      * @since Customizr 3.3+
      */
      function tc_render_edit_link_view( $_echo = true ) {
        $_view = sprintf('<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s">%2$s</a></span>',
          get_edit_post_link(),
          __( 'Edit' , 'customizr' )
        );
        if ( ! $_echo )
          return $_view;
        echo $_view;
      }


      /**
      * hook tc_content_title_icon
      * @return  boolean
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_post_page_icon( $_bool ) {
          if ( is_page() )
            $_bool = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_page_title_icon' ) ) ) ? false : $_bool;
          if ( is_single() && ! is_page() )
            $_bool = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_title_icon' ) ) ) ? false : $_bool;
          if ( ! is_single() )
            $_bool = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_post_list_title_icon' ) ) ) ? false : $_bool;
          //last condition
          return ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ) ? false : $_bool;
      }



      /**
      * hook tc_archive_icon
      * @return string
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_set_archive_icon( $_class ) {
          $_class = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_archive_title_icon' ) ) ) ? '' : $_class;
          //last condition
          return 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ? '' : $_class;
      }




      /**
      * Return 1) the archive title html content OR 2) the archive title class OR 3) the boolean
      * hook : tc_display_customizr_headings
      * @return  boolean
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_archive_title_and_class_callback( $_title = null, $_return_class = false ) {
        //declares variables to return
        $content          = false;
        $_header_class    = false;

        //case page for posts but not on front
        global $wp_query;
        if ( $wp_query -> is_posts_page && ! is_front_page() ) {
          //get page for post ID
          $page_for_post_id = get_option('page_for_posts');
          $_header_class   = array('entry-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf('<%1$s class="entry-title %2$s">%3$s</%1$s>',
                apply_filters( 'tc_content_title_tag' , 'h1' ),
                apply_filters( 'tc_content_title_icon', 'format-icon' ),
                get_the_title( $page_for_post_id )
           );
          $content        = apply_filters( 'tc_page_for_post_header_content', $content );
        }


        //404
        else if ( is_404() ) {
          $_header_class   = array('entry-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf('<h1 class="entry-title %1$s">%2$s</h1>',
                apply_filters( 'tc_archive_icon', '' ),
                apply_filters( 'tc_404_title' , __( 'Ooops, page not found' , 'customizr' ) )
           );
          $content        = apply_filters( 'tc_404_header_content', $content );
        }

        //search results
        else if ( is_search() && ! is_singular() ) {
          $_header_class   = array('search-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf( '<div class="row-fluid"><div class="%1$s"><h1 class="%2$s">%3$s%4$s %5$s </h1></div><div class="%6$s">%7$s</div></div>',
                apply_filters( 'tc_search_result_header_title_class', 'span8' ),
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                have_posts() ? '' :  __( 'No' , 'customizr' ).'&nbsp;' ,
                apply_filters( 'tc_search_results_title' , __( 'Search Results for :' , 'customizr' ) ),
                '<span>' . get_search_query() . '</span>',
                apply_filters( 'tc_search_result_header_form_class', 'span4' ),
                have_posts() ? get_search_form(false) : ''
          );
          $content       = apply_filters( 'tc_search_results_header_content', $content );
        }
        // all archives
        else if ( is_archive() ){
          $_header_class   = array('archive-header');
          if ( $_return_class )
            return $_header_class;

          //author's posts page
          if ( is_author() ) {
            //gets the user ID
            $user_id = get_query_var( 'author' );
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                  apply_filters( 'tc_archive_icon', 'format-icon' ),
                  apply_filters( 'tc_author_archive_title' , '' ),
                  '<span class="vcard">' . get_the_author_meta( 'display_name' , $user_id ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_author_meta' , get_the_author_meta( 'description', $user_id  ) ) ) {
              $content    .= sprintf('%1$s<div class="author-info"><div class="%2$s">%3$s</div></div>',

                  apply_filters( 'tc_author_meta_separator', '<hr class="featurette-divider '.current_filter().'">' ),

                  apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),

                  sprintf('<div class="%1$s">%2$s</div><div class="%3$s"><h2>%4$s</h2><p>%5$s</p></div>',
                      apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                      get_avatar( get_the_author_meta( 'user_email', $user_id ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) ),
                      apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                      sprintf( __( 'About %s' , 'customizr' ), get_the_author() ),
                      get_the_author_meta( 'description' , $user_id  )
                  )
              );
            }
            $content       = apply_filters( 'tc_author_header_content', $content );
          }

          //category archives
          else if ( is_category() ) {
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tc_category_archive_title' , '' ),
                '<span>' . single_cat_title( '' , false ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_cat_description' , category_description() ) ) {
              $content    .= sprintf('<div class="archive-meta">%1$s</div>',
                category_description()
              );
            }
            $content       = apply_filters( 'tc_category_archive_header_content', $content );
          }

          //tag archives
          else if ( is_tag() ) {
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tc_tag_archive_title' , '' ),
                '<span>' . single_tag_title( '' , false ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_tag_description' , tag_description() ) ) {
              $content    .= sprintf('<div class="archive-meta">%1$s</div>',
                tag_description()
              );
            }
            $content       = apply_filters( 'tc_tag_archive_header_content', $content );
          }

          //time archives
          else if ( is_day() || is_month() || is_year() ) {
            $archive_type   = is_day() ? sprintf( __( 'Daily Archives: %s' , 'customizr' ), '<span>' . get_the_date() . '</span>' ) : __( 'Archives' , 'customizr' );
            $archive_type   = is_month() ? sprintf( __( 'Monthly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
            $archive_type   = is_year() ? sprintf( __( 'Yearly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
            $content        = sprintf('<h1 class="%1$s">%2$s</h1>',
              apply_filters( 'tc_archive_icon', 'format-icon' ),
              $archive_type
            );
            $content        = apply_filters( 'tc_time_archive_header_content', $content );
          }
          // all other archivers ( such as custom tax archives )
          else if ( apply_filters('tc_show_tax_archive_title', true) ){
            $content   = sprintf('<h1 class="%1$s">%2$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ), /* handle tax icon? */
                apply_filters( 'tc_tax_archive_title',	get_the_archive_title() )
            );
            $tax_description = get_the_archive_description();
            if ( apply_filters( 'tc_show_tax_description', $tax_description ) )
              $content   .=  sprintf('<div class="archive-meta">%1$s</div>',
                $tax_description
              );
            $content        = apply_filters( 'tc_tax_archive_header_content', $content );
          }
        }// end all archives

        return $_return_class ? $_header_class : $content;

      }//end of fn


      /**
      * @return void
      * set up user defined options
      * callback of template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function tc_set_headings_options() {
        //by default don't display the Customizr title in feeds
        if ( apply_filters('tc_display_customizr_headings',  is_feed() ) )
          return;

        //Add update status next to the title (@since 3.2.6)
        add_filter( 'tc_the_title'                  , array( $this , 'tc_add_update_notice_in_title'), 20);
      }



      /**
      * Callback of the tc_the_title => add an updated status
      * @return string
      * User option based
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_add_update_notice_in_title($html) {
          //First checks if we are in the loop and we are not displaying a page
          if ( ! in_the_loop() || is_page() )
              return $html;

          //Is the notice option enabled AND this post type eligible for updated notice ? (default is post)
          if ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_notice_in_title' ) ) || ! in_array( get_post_type(), apply_filters('tc_show_update_notice_for_post_types' , array( 'post') ) ) )
              return $html;

          //php version check for DateTime
          //http://php.net/manual/fr/class.datetime.php
          if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
            return $html;

          //get the user defined interval in days
          $_interval = esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_notice_interval' ) );
          $_interval = ( 0 != $_interval ) ? $_interval : 30;

          //Check if the last update is less than n days old. (replace n by your own value)
          $has_recent_update = ( TC_utils::$inst -> tc_post_has_update( true ) && TC_utils::$inst -> tc_post_has_update( 'in_days') < $_interval ) ? true : false;

          if ( ! $has_recent_update )
              return $html;

          //Return the modified title
          return apply_filters(
              'tc_update_notice_in_title',
              sprintf('%1$s &nbsp; <span class="tc-update-notice label %3$s">%2$s</span>',
                  $html,
                  esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_notice_text' ) ),
                  esc_attr( TC_utils::$inst->tc_opt( 'tc_post_metas_update_notice_format' ) )
              )
          );
      }


      /**
      * hooks : 'tc_category_archive_title', 'tc_tag_archive_title', 'tc_search_results_title', 'tc_author_archive_title'
      * @param default title string
      * @return string of user defined title
      * @since Customizr 3.3+
      */
      function tc_set_archive_custom_title( $_title ) {
        switch ( current_filter() ) {
          case 'tc_category_archive_title' :
            return esc_attr( TC_utils::$inst->tc_opt( 'tc_cat_title' ) );
          break;

          case 'tc_tag_archive_title' :
            return esc_attr( TC_utils::$inst->tc_opt( 'tc_tag_title' ) );
          break;

          case 'tc_search_results_title' :
            return esc_attr( TC_utils::$inst->tc_opt( 'tc_search_title' ) );
          break;

          case 'tc_author_archive_title' :
            return esc_attr( TC_utils::$inst->tc_opt( 'tc_author_title' ) );
          break;
        }
        return $_title;
      }

  }//end of class
endif;
>>>>>>> upstream/master
