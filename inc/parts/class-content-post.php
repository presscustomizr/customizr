<?php
/**
* Single post content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_post' ) ) :
  class TC_post {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'wp'                , array( $this , 'tc_set_single_post_hooks' ));
      //Set single post thumbnail with customizer options (since 3.2.0)
      add_action( 'wp'                , array( $this , 'tc_set_single_post_thumbnail_hooks' ));

      //append inline style to the custom stylesheet
      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      add_filter( 'tc_user_options_style'    , array( $this , 'tc_write_thumbnail_inline_css') );
    }


    /***************************
    * SINGLE POST AND THUMB HOOKS SETUP
    ****************************/
    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_single_post_hooks() {
      //add post header, content and footer to the __loop
      add_action( '__loop'              , array( $this , 'tc_post_content' ));
      //posts parts actions
      add_action( '__after_content'     , array( $this , 'tc_post_footer' ));
      //smartload help block
      add_filter( 'the_content'         , array( $this, 'tc_maybe_display_img_smartload_help') , PHP_INT_MAX );

    }



    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_single_post_thumbnail_hooks() {
      if ( $this -> tc_single_post_display_controller() )
        add_action( '__before_content'        , array( $this, 'tc_maybe_display_featured_image_help') );

      //__before_main_wrapper, 200
      //__before_content 0
      //__before_content 20
      if ( ! $this -> tc_show_single_post_thumbnail() )
        return;

      $_exploded_location   = explode('|', esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' )) );
      $_hook                = isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content';
      $_priority            = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;

      //Hook post view
      add_action( $_hook, array($this , 'tc_single_post_prepare_thumb') , $_priority );
      //Set thumb shape with customizer options (since 3.2.0)
      add_filter( 'tc_post_thumb_wrapper'      , array( $this , 'tc_set_thumb_shape'), 10 , 2 );
    }



    /***************************
    * SINGLE POST VIEW
    ****************************/
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
      if ( ! is_singular() || ! get_the_author_meta( 'description' ) || ! apply_filters( 'tc_show_author_metas_in_post', true ) || ! esc_attr( TC_utils::$inst->tc_opt( 'tc_show_author_info' ) ) )
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


    /***************************
    * SINGLE POST THUMBNAIL VIEW
    ****************************/
    /**
    * Get Single post thumb model + view
    * Inject it in the view
    * hook : esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' ) || '__before_content'
    * @return  void
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function tc_single_post_prepare_thumb() {
      $_size_to_request = apply_filters( 'tc_single_post_thumb_size' , $this -> tc_get_current_thumb_size() );
      //get the thumbnail data (src, width, height) if any
      //array( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" )
      $_thumb_model   = TC_post_thumbnails::$instance -> tc_get_thumbnail_model( $_size_to_request ) ;
      //may be render
      if ( TC_post_thumbnails::$instance -> tc_has_thumb() ) {
        $_thumb_class   = implode( " " , apply_filters( 'tc_single_post_thumb_class' , array( 'row-fluid', 'tc-single-post-thumbnail-wrapper', current_filter() ) ) );
        $this -> tc_render_single_post_view( $_thumb_model , $_thumb_class );
      }
    }


    /**
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function tc_render_single_post_view( $_thumb_model , $_thumb_class ) {
      echo apply_filters( 'tc_render_single_post_view',
        sprintf( '<div class="%1$s">%2$s</div>' ,
          $_thumb_class,
          TC_post_thumbnails::$instance -> tc_render_thumb_view( $_thumb_model, 'span12', false )
        )
      );
    }


    /***************************
    * SINGLE POST THUMBNAIL HELP VIEW
    ****************************/
    /**
    * Displays a help block about featured images for single posts
    * hook : __before_content
    * @since Customizr 3.4
    */
    function tc_maybe_display_featured_image_help() {
      if ( ! TC_placeholders::tc_is_thumbnail_help_on() )
        return;
      ?>
      <div class="tc-placeholder-wrap tc-thumbnail-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p><p>%3$s</p>',
              __( "You can display your post's featured image here if you have set one.", "customizr" ),
              sprintf( __("%s to display a featured image here.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', TC_utils::tc_get_customizer_url( array( "section" => "single_posts_sec") ), __( "Jump to the customizer now", "customizr") )
              ),
              sprintf( __( "Don't know how to set a featured image to a post? Learn how in the %s.", "customizr" ),
                sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a><span class="tc-external"></span>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }

    /***************************
    * SINGLE POST IMG SMARTLOAD HELP VIEW
    ****************************/
    /**
    * Displays a help block about images smartload for single posts prepended to the content
    * hook : the_content
    * @since Customizr 3.4+
    */
    function tc_maybe_display_img_smartload_help( $the_content ) {
      if ( ! ( $this -> tc_single_post_display_controller()  &&  in_the_loop() && TC_placeholders::tc_is_img_smartload_help_on( $the_content ) ) )
        return $the_content;

      return TC_placeholders::tc_get_smartload_help_block() . $the_content;
    }




    /******************************
    * SETTERS / HELPERS / CALLBACKS
    *******************************/
    /**
    * Single post view controller
    * @return  boolean
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
    * HELPER
    * @return boolean
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function tc_show_single_post_thumbnail() {
      return $this -> tc_single_post_display_controller()
        && 'hide' != esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' ) )
        && apply_filters( 'tc_show_single_post_thumbnail' , true );
    }


    /**
    * HELPER
    * @return size string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function tc_get_current_thumb_size() {
      $_exploded_location   = explode( '|', esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_location' ) ) );
      $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
      return '__before_main_wrapper' == $_hook ? 'slider-full' : 'slider';
    }


    /**
    * hook : tc_post_thumb_wrapper
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
      return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2$s">%3$s</a></div>',
            get_permalink( get_the_ID() ),
            esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
            $thumb_img,
            implode( " ", apply_filters( 'tc_thumb_wrapper_class', array() ) )
      );
    }


    /**
    * hook : tc_user_options_style
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_write_thumbnail_inline_css( $_css ) {
      if ( ! $this -> tc_show_single_post_thumbnail() )
        return $_css;
      $_single_thumb_height   = esc_attr( TC_utils::$inst->tc_opt( 'tc_single_post_thumb_height' ) );
      $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
      return sprintf("%s\n%s",
        $_css,
        ".single .tc-rectangular-thumb {
          max-height: {$_single_thumb_height}px;
          height :{$_single_thumb_height}px
        }\n"
      );
    }

  }//end of class
endif;
