<?php
/**
* Comments actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_comments {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action ( '__after_loop'                       , array( $this , 'tc_comments' ), 10 );

        add_action ( '__comment'                          , array( $this , 'tc_comment_title' ), 10 );
        add_action ( '__comment'                          , array( $this , 'tc_comment_list' ), 20 );
        add_action ( '__comment'                          , array( $this , 'tc_comment_navigation' ), 30 );
        add_action ( '__comment'                          , array( $this , 'tc_comment_close' ), 40 );
    }



   /**
    * Main commments template
    *
    * @package Customizr
    * @since Customizr 3.0.10
   */
    function tc_comments() {
      if ( tc__f( '__is_home' ) )
        return;
      tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      comments_template( '' , true );
    }



            
    /**
      * Comment title rendering
      *
      * @package Customizr
      * @since Customizr 3.0
     */
      function tc_comment_title() {
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ );
        
        ob_start();

          printf( '<h2 id="tc-comment-title" class="comments-title">%1$s</h2>' ,
                sprintf( _n( 'One thought on &ldquo;%2$s&rdquo;' , '%1$s thoughts on &ldquo;%2$s&rdquo;' , get_comments_number(), 'customizr' ),
                number_format_i18n( get_comments_number(), 'customizr' ), 
                '<span>' . get_the_title() . '</span>' 
              ));

        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_comment_title' , $html );
      }



     /**
      * Comment list Rendering
      *
      * @package Customizr
      * @since Customizr 3.0
     */
      function tc_comment_list() {
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      	tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ , $float = 'right');

        ob_start();
          ?>
    
        		<ul class="commentlist">
        			<?php wp_list_comments( array( 'callback' => array ( $this , 'tc_comment_callback' ) , 'style' => 'ul' ) ); ?>
        		</ul><!-- .commentlist -->

      		<?php

        $html = ob_get_contents();
        ob_end_clean();
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
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
        <article id="comment-<?php comment_ID(); ?>" class="comment">
          <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' ); ?></p>
        </article>
      <?php
          break;
        default :
        // Proceed with normal comments.
        global $post;
      ?>
      <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article class="comment">
            <div class="row-fluid">
              <div class="comment-avatar span2">
                <?php echo get_avatar( $comment, 80 ); ?>
              </div>
              <div class="span10">
                <?php if( 1 == get_option( 'thread_comments' ) && ($depth < $max_comments_depth) ) : //check if the nested comment option is checked and the authorized depth of comments?>
                    <div class="reply btn btn-small">
                      <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply' , 'customizr' ), 'after' => ' <span>&darr;</span>' , 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                    </div><!-- .reply -->
                <?php endif; ?>
                <header class="comment-meta comment-author vcard">
                    <?php
                    printf( '<cite class="fn">%1$s %2$s %3$s</cite>' ,
                      get_comment_author_link(),
                      // If current post author is also comment author, make it known visually.
                      ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author' , 'customizr' ) . '</span>' : '' ,
                      edit_comment_link( __( 'Edit' , 'customizr' ), '<p class="edit-link btn btn-success btn-mini">' , '</p>' )
                    );
                    printf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>' ,
                      esc_url( get_comment_link( $comment->comment_ID ) ),
                      get_comment_time( 'c' ),
                      /* translators: 1: date, 2: time */
                      sprintf( __( '%1$s at %2$s' , 'customizr' ), get_comment_date(), get_comment_time() )
                    );
                  ?>
                </header><!-- .comment-meta -->

                <?php if ( '0' == $comment->comment_approved ) : ?>
                  <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' , 'customizr' ); ?></p>
                <?php endif; ?>

                <section class="comment-content comment">
                  <?php comment_text(); ?>
                </section><!-- .comment-content -->
            </div><!-- .span8 -->
          </div><!-- .row -->
        </article><!-- #comment-## -->
        <?php
          break;
        endswitch; // end comment_type check

        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_comment_callback' , $html );
      }




      /**
      * Comments navigation rendering
      *
      * @package Customizr
      * @since Customizr 3.0
     */
      function tc_comment_navigation () {
        if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through
          tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

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

          ob_start();
          ?>
            <p class="nocomments"><?php _e( 'Comments are closed.' , 'customizr' ); ?></p>
          <?php 

          $html = ob_get_contents();
          ob_end_clean();
          echo apply_filters( 'tc_comment_close' , $html );

        endif;
      }
    
}//end class