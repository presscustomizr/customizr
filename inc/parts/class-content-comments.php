<?php
/**
* Comments actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_comments' ) ) :
  class TC_comments {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //wp hook => wp_query is built
        add_action ( 'wp'                     , array( $this , 'tc_comments_set_hooks' ) );

        //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
        //fired on hook : wp_enqueue_scripts
        //Set thumbnail specific design based on user options
        //Set user defined various inline stylings
        add_filter( 'tc_user_options_style'   , array( $this , 'tc_comment_bubble_inline_css' ) );
      }



      /***************************
      * HOOK SETUP
      ****************************/
      /**
      * Set various comment hooks
      * hook : wp
      * @package Customizr
      * @since Customizr 3.3.2
      */
      function tc_comments_set_hooks() {
        //Maybe fires the comment's template
        add_action ( '__after_loop'           , array( $this , 'tc_comments' ), 10 );

        //Apply a filter on the comment list ( comment list user defined option )
        //the filter tc_display_comment_list is fired in the comments.php template
        add_filter( 'tc_display_comment_list' , array( $this , 'tc_set_comment_list_display' ) );

        //Add actions in the comment's template
        add_action ( '__comment'              , array( $this , 'tc_comment_title' ), 10 );
        add_action ( '__comment'              , array( $this , 'tc_comment_list' ), 20 );
        add_action ( '__comment'              , array( $this , 'tc_comment_navigation' ), 30 );
        add_action ( '__comment'              , array( $this , 'tc_comment_close' ), 40 );
        add_filter ( 'comment_form_defaults'  , array( $this , 'tc_set_comment_title') );

        //Add comment bubble
        add_filter( 'tc_the_title'            , array( $this , 'tc_display_comment_bubble' ), 1 );
        //Custom Bubble comment since 3.2.6
        add_filter( 'tc_bubble_comment'       , array( $this , 'tc_custom_bubble_comment'), 10, 2 );
      }




      /***************************
      * VIEWS
      ****************************/
     /**
      * Main commments template
      *
      * @package Customizr
      * @since Customizr 3.0.10
     */
      function tc_comments() {
        if ( ! $this -> tc_are_comments_enabled() )
          return;
        do_action('tc_before_comments_template');
          comments_template( '' , true );
        do_action('tc_after_comments_template');
      }




      /**
        * Comment title rendering
        *
        *
        * @package Customizr
        * @since Customizr 3.0
       */
        function tc_comment_title() {
          if ( 1 == get_comments_number() ) {
            $_title = __( 'One thought on', 'customizr' );
          } else {
            $_title = sprintf( '%1$s %2$s', number_format_i18n( get_comments_number(), 'customizr' ) , __( 'thoughts on', 'customizr' ) );
          }

          echo apply_filters( 'tc_comment_title' ,
                sprintf( '<h2 id="tc-comment-title" class="comments-title">%1$s &ldquo;%2$s&rdquo;</h2>' ,
                  $_title,
                  '<span>' . get_the_title() . '</span>'
                )
          );
        }



       /**
        * Comment list Rendering
        *
        * @package Customizr
        * @since Customizr 3.0
       */
        function tc_comment_list() {
          $_args = apply_filters( 'tc_list_comments_args' , array( 'callback' => array ( $this , 'tc_comment_callback' ) , 'style' => 'ul' ) );
          ob_start();
            ?>
              <ul class="commentlist">
                <?php wp_list_comments( $_args ); ?>
              </ul><!-- .commentlist -->
            <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          echo apply_filters( 'tc_comment_list' , $html );
        }




       /**
        * Template for comments and pingbacks.
        *
        *
        * Used as a callback by wp_list_comments() for displaying the comments.
        *  Inspired from Twenty Twelve 1.0
        * @package Customizr
        * @since Customizr 1.0
        */
       function tc_comment_callback( $comment, $args, $depth ) {

        $GLOBALS['comment'] = $comment;
        //get user defined max comment depth
        $max_comments_depth = get_option('thread_comments_depth');
        $max_comments_depth = isset( $max_comments_depth ) ? $max_comments_depth : 5;

        ob_start();

        switch ( $comment->comment_type ) :
          case 'pingback' :
          case 'trackback' :
          // Display trackbacks differently than normal comments.
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
          <article id="comment-<?php comment_ID(); ?>" class="comment">
            <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
                <?php if ( ! TC___::$instance -> tc_is_customizing() )  edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' ); ?>
            </p>
          </article>
        <?php
            break;
          default :
          // Proceed with normal comments.
          global $post;
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

            <?php
              //when do we display the comment content?
              $tc_show_comment_content = 1 == get_option( 'thread_comments' ) && ($depth < $max_comments_depth) && comments_open();

              //gets the comment text => filter parameter!
              $comment_text = get_comment_text( $comment->comment_ID , $args );

              printf('<article id="comment-%9$s" class="comment"><div class="%1$s"><div class="%2$s">%3$s</div><div class="%4$s">%5$s %6$s %7$s %8$s</div></div></article>',
                  apply_filters( 'tc_comment_wrapper_class', 'row-fluid' ),
                  apply_filters( 'tc_comment_avatar_class', 'comment-avatar span2' ),
                  get_avatar( $comment, apply_filters( 'tc_comment_avatar_size', 80 ) ),
                  apply_filters( 'tc_comment_content_class', 'span10' ),

                  $tc_show_comment_content ? sprintf('<div class="%1$s">%2$s</div>',
                                            apply_filters( 'tc_comment_reply_btn_class', 'reply btn btn-small' ),
                                            get_comment_reply_link( array_merge(
                                                                        $args,
                                                                        array(  'reply_text' => __( 'Reply' , 'customizr' ).' <span>&darr;</span>',
                                                                                'depth' => $depth,
                                                                                'max_depth' => $args['max_depth'] ,
                                                                                'add_below' => apply_filters( 'tc_comment_reply_below' , 'comment' )
                                                                              )
                                                                  )
                                            )
                  ) : '',

                  sprintf('<header class="comment-meta comment-author vcard">%1$s %2$s</header>',
                        sprintf( '<cite class="fn">%1$s %2$s %3$s</cite>' ,
                            get_comment_author_link(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author' , 'customizr' ) . '</span>' : '' ,
                            ! TC___::$instance -> tc_is_customizing() && current_user_can( 'edit_comment', $comment->comment_ID ) ? '<p class="edit-link btn btn-success btn-mini"><a class="comment-edit-link" href="' . get_edit_comment_link( $comment->comment_ID ) . '">' . __( 'Edit' , 'customizr' ) . '</a></p>' : ''
                        ),
                        sprintf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>' ,
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( __( '%1$s at %2$s' , 'customizr' ), get_comment_date(), get_comment_time() )
                        )
                  ),

                  ( '0' == $comment->comment_approved ) ? sprintf('<p class="comment-awaiting-moderation">%1$s</p>',
                    __( 'Your comment is awaiting moderation.' , 'customizr' )
                    ) : '',

                  sprintf('<section class="comment-content comment">%1$s</section>',
                    apply_filters( 'comment_text', $comment_text, $comment, $args )
                  ),
                  $comment->comment_ID
                );//end printf
            ?>
          <!-- //#comment-## -->
        <?php
          break;
        endswitch; // end comment_type check

        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_comment_callback' , $html, $comment, $args, $depth, $max_comments_depth );
      }




    /**
    * Comments navigation rendering
    *
    * @package Customizr
    * @since Customizr 3.0
   */
    function tc_comment_navigation () {
      if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through

        ob_start();

        ?>
        <nav id="comment-nav-below" class="navigation" role="navigation">
          <h3 class="assistive-text section-heading"><?php _e( 'Comment navigation' , 'customizr' ); ?></h3>
          <ul class="pager">

            <?php if(get_previous_comments_link() != null) : ?>

              <li class="previous">
                <span class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments' , 'customizr' ) ); ?></span>
              </li>

            <?php endif; ?>

            <?php if(get_next_comments_link() != null) : ?>

              <li class="next">
                <span class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?></span>
              </li>

            <?php endif; ?>

          </ul>
        </nav>
        <?php

        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_comment_navigation' , $html );

      endif; // check for comment navigation

    }



    /**
    * Comment close rendering
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_comment_close() {
      /* If there are no comments and comments are closed, let's leave a note.
       * But we only want the note on posts and pages that had comments in the first place.
       */
      if ( ! comments_open() && get_comments_number() ) :
        echo apply_filters( 'tc_comment_close' ,
          sprintf('<p class="nocomments">%1$s</p>',
            __( 'Comments are closed.' , 'customizr' )
          )
        );

      endif;
    }





    /***************************
    * CALLBACKS
    ****************************/
    /**
    * Do we display the comment list ?
    * hook : tc_display_comment_list
    * @return  bool
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_set_comment_list_display() {
      return (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_show_comment_list' ) );
    }



    /**
    * Comment title override (comment_form_defaults filter)
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_comment_title($_defaults) {
      $_defaults['title_reply'] =  __( 'Leave a comment' , 'customizr' );
      return $_defaults;
    }



    /**
    * Callback for tc_the_title
    * @return  string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_display_comment_bubble( $_title = null ) {
      if ( ! $this -> tc_is_bubble_enabled() )
        return $_title;

      global $post;
      //checks if comments are opened AND if there are any comments to display
      return sprintf('%1$s <span class="comments-link"><a href="%2$s%3$s" title="%4$s %5$s" data-disqus-identifier="javascript:this.page.identifier">%6$s</a></span>',
        $_title,
        is_singular() ? '' : get_permalink(),
        apply_filters( 'tc_bubble_comment_anchor', '#tc-comment-title'),
        sprintf( '%1$s %2$s' , get_comments_number(), __( 'Comment(s) on' , 'customizr' ) ),
        is_null($_title) ? esc_attr( strip_tags( $post -> post_title ) ) : esc_attr( strip_tags( $_title ) ),
        0 != get_comments_number() ? apply_filters( 'tc_bubble_comment' , '' , esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_shape' ) ) ) : ''
      );
    }



   /**
    * Callback of tc_bubble_comment
    * @return string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_custom_bubble_comment( $_html , $_opt ) {
      return sprintf('%4$s<span class="tc-comment-bubble %1$s">%2$s %3$s</span>',
        'default' == $_opt ? "default-bubble" : $_opt,
        get_comments_number(),
        'default' == $_opt ? '' : sprintf( _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ),
          number_format_i18n( get_comments_number(), 'customizr' )
        ),
        $_html
      );
    }


    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    function tc_comment_bubble_inline_css( $_css ) {
      if ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_show_bubble' ) ) )
        return $_css;

      //apply custom color only if type custom
      //if color type is skin => bubble color is defined in the skin stylesheet
      if ( 'skin' != esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_color_type' ) ) ) {
        $_custom_bubble_color = esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_color' ) );
        $_css .= "
          .comments-link .tc-comment-bubble {
            color: {$_custom_bubble_color};
            border: 2px solid {$_custom_bubble_color};
          }
          .comments-link .tc-comment-bubble:before {
            border-color: {$_custom_bubble_color};
          }
        ";
      }

      if ( 'default' == esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_bubble_shape' ) ) )
        return $_css;

      $_css .= "
        .comments-link .custom-bubble-one {
          position: relative;
          bottom: 28px;
          right: 10px;
          padding: 4px;
          margin: 1em 0 3em;
          background: none;
          -webkit-border-radius: 10px;
          -moz-border-radius: 10px;
          border-radius: 10px;
          font-size: 10px;
        }
        .comments-link .custom-bubble-one:before {
          content: '';
          position: absolute;
          bottom: -14px;
          left: 10px;
          border-width: 14px 8px 0;
          border-style: solid;
          display: block;
          width: 0;
        }
        .comments-link .custom-bubble-one:after {
          content: '';
          position: absolute;
          bottom: -11px;
          left: 11px;
          border-width: 13px 7px 0;
          border-style: solid;
          border-color: #FAFAFA rgba(0, 0, 0, 0);
          display: block;
          width: 0;
        }\n";

      return $_css;
    }//end of fn



    /***************************
    * HELPERS
    ****************************/
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
    private function tc_are_comments_enabled() {
      global $post;
      // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
      if ( isset( $post ) ) {
        $_bool = ( post_password_required() || tc__f( '__is_home' ) || ! is_singular() )  ? false : true;

        //2) if user has enabled comment for this specific post / page => true
        //@todo contx : update default value user's value)
        $_bool = ( 'closed' != $post -> comment_status ) ? true : $_bool;

        //3) check global user options for pages and posts
        if ( is_page() )
          $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_page_comments' )) && $_bool;
        else
          $_bool = 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_post_comments' )) && $_bool;
      } else
        $_bool = false;

      return apply_filters( 'tc_are_comments_enabled', $_bool );
    }




    /**
    * When are we displaying the comment bubble ?
    * - Must be in the loop
    * - Bubble must be enabled by user
    * - comments are enabled
    * - there is at least one comment
    * - the comment list option is enabled
    * - post type is in the eligible post type list : default = post
    * - tc_comments_in_title boolean filter is true
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function tc_is_bubble_enabled() {
      $_bool_arr = array(
        in_the_loop(),
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_comment_show_bubble' ) ),
        $this -> tc_are_comments_enabled(),
        get_comments_number() != 0,
        (bool) esc_attr( TC_utils::$inst->tc_opt( 'tc_show_comment_list' ) ),
        (bool) apply_filters( 'tc_comments_in_title', true ),
        in_array( get_post_type(), apply_filters('tc_show_comment_bubbles_for_post_types' , array( 'post' , 'page') ) )
      );
      return (bool) array_product($_bool_arr);
    }

  }//end class
endif;
