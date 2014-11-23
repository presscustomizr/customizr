<?php
/**
* Single post content actions
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post' ) ) :
  class TC_post {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          //add post header, content and footer to the __loop
          add_action  ( '__loop'                        , array( $this , 'tc_post_content' ));
          //posts parts actions
          add_action  ( '__after_content'               , array( $this , 'tc_post_footer' ));
          //Set single post thumbnail with customizer options (since 3.2.0)
          add_action  ( 'template_redirect'             , array( $this , 'tc_set_single_post_thumbnail_hooks' ));
      }


      /**
      * Single post view controller
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_single_post_display_controller() {
        //check conditional tags : we want to show single post or single custom post types
        global $post;
        $tc_show_single_post_content = isset($post) 
        && 'page' != $post -> post_type 
        && 'attachment' != $post -> post_type 
        && is_singular() 
        && ! tc__f( '__is_home_empty');
        return apply_filters( 'tc_show_single_post_content', $tc_show_single_post_content );
      }



      /**
       * Callback hooked on template_redirect.
       *
       * @package Customizr
       * @since Customizr 3.2.0
       */
      function tc_set_single_post_thumbnail_hooks() {
        //__before_main_wrapper, 200
        //__before_content 0
        //__before_content 20
        if ( ! $this -> tc_single_post_display_controller() 
          || ! esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_location' ) ) 
          || 'hide' == esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_location' ) )
          )
          return;
       
        $_exploded_location   = explode('|', esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_location' )) );
        $_hook                = isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content';
        $_priority            = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;
       
        add_action( $_hook, array($this , 'tc_single_post_thumbnail_view') , $_priority );
      }



      /**
      * Single post thumbnail view
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function tc_single_post_thumbnail_view() {
        $_exploded_location   = explode('|', esc_attr( tc__f( '__get_option' , 'tc_single_post_thumb_location' )) );
        $_hook                = isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content';
        $_size_to_request     = ( '__before_main_wrapper' == $_hook ) ? 'slider-full' : 'slider';

        //get the thumbnail data (src, width, height) if any
        $thumb_data                     = TC_post_thumbnails::$instance -> tc_get_thumbnail_data( $_size_to_request ) ;
        $_single_thumbnail_wrap_class   = implode(" " , apply_filters('tc_single_post_thumb_class' , array('row-fluid','tc-single-post-thumbnail-wrapper', current_filter() ) ) );
        ob_start();
          ?>
            <div class="<?php echo $_single_thumbnail_wrap_class ?>">
              <?php TC_post_thumbnails::$instance -> tc_display_post_thumbnail( $thumb_data, 'span12' ); ?>
            </div>
          <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_single_post_thumbnail_view', $html );
      }



      /**
       * The default template for displaying single post content
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function tc_post_content() {
        //check conditional tags : we want to show single post or single custom post types
        if ( ! $this -> tc_single_post_display_controller() )
            return;

        //display an icon for div if there is no title
        $icon_class = in_array( get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' ) ) ? apply_filters( 'tc_post_format_icon', 'format-icon' ) :'' ;

        ob_start();

        do_action( '__before_content' );

        ?>    

          <section class="entry-content <?php echo $icon_class ?>">
              <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
              <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
          </section><!-- .entry-content -->

        <?php
        do_action( '__after_content' );
                    
        $html = ob_get_contents();
        if ($html) ob_end_clean();

        echo apply_filters( 'tc_post_content', $html );
      }



      /**
      * Single post footer view
      *
      * @package Customizr
      * @since Customizr 3.0
      */
      function tc_post_footer() {
        //check conditional tags : we want to show single post or single custom post types
        if ( ! $this -> tc_single_post_display_controller() || ! apply_filters( 'tc_show_single_post_footer', true ) )
            return;
        //@todo check if some conditions below not redundant?
        if ( ! is_singular() || ! get_the_author_meta( 'description' ) || ! apply_filters( 'tc_show_author_metas_in_post', true ) )
          return;

        $html = sprintf('<footer class="entry-meta">%1$s<div class="author-info"><div class="%2$s">%3$s %4$s</div></div></footer>',
                     '<hr class="featurette-divider">',
                     
                    apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),
                     
                    sprintf('<div class="%1$s">%2$s</div>',
                            apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                            get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) )
                      ),

                    sprintf('<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><div class="author-link">%4$s</div></div>',
                            apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                            sprintf( __( 'About %s' , 'customizr' ), get_the_author() ),
                            get_the_author_meta( 'description' ),
                            sprintf( '<a href="%1$s" rel="author">%2$s</a>',
                              esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                              sprintf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>' , 'customizr' ), get_the_author() )
                            )
                      )
        );//end sprintf

        echo apply_filters( 'tc_post_footer', $html );
      }
  }//end of class
endif;