<?php
/**
* Single post content actions
*
*/
if ( ! class_exists( 'CZR_post' ) ) :
  class CZR_post {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'wp'                , array( $this , 'czr_fn_set_single_post_hooks' ));
      //Set single post thumbnail with customizer options (since 3.2.0)
      add_action( 'wp'                , array( $this , 'czr_fn_set_single_post_thumbnail_hooks' ));

      //append inline style to the custom stylesheet
      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      add_filter( 'tc_user_options_style'    , array( $this , 'czr_fn_write_thumbnail_inline_css') );
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
    function czr_fn_set_single_post_hooks() {
      //add post header, content and footer to the __loop
      add_action( '__loop'              , array( $this , 'czr_fn_post_content' ));
      //posts parts actions
      add_action( '__after_content'     , array( $this , 'czr_fn_post_footer' ));
    }



    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_single_post_thumbnail_hooks() {
      //__before_main_wrapper, 200
      //__before_content 0
      //__before_content 20
      if ( ! $this -> czr_fn_show_single_post_thumbnail() )
        return;

      $_exploded_location   = explode('|', esc_attr( czr_fn_opt( 'tc_single_post_thumb_location' )) );
      $_hook                = apply_filters( 'tc_single_post_thumb_hook', isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content' );
      $_priority            = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;

      //Hook post view
      add_action( $_hook, array($this , 'czr_fn_single_post_prepare_thumb') , $_priority );
      //Set thumb shape with customizer options (since 3.2.0)
      add_filter( 'tc_post_thumb_wrapper'      , array( $this , 'czr_fn_set_thumb_shape'), 10 , 2 );
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
    function czr_fn_post_content() {
      //check conditional tags : we want to show single post or single custom post types
      if ( ! $this -> czr_fn_single_post_display_controller() )
          return;
      //display an icon for div if there is no title
      $icon_class = in_array( get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' ) ) ? apply_filters( 'tc_post_format_icon', 'format-icon' ) :'' ;

      ob_start();
      do_action( '__before_content' );
        ?>
          <section class="<?php echo implode( ' ', apply_filters( 'tc_single_post_section_class', array( 'entry-content' ) ) ); ?> <?php echo $icon_class ?>">
              <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
              <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
              <?php do_action( '__after_single_entry_inner' ); ?>
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
    function czr_fn_post_footer() {
      //check conditional tags : we want to show single post or single custom post types
      if ( ! $this -> czr_fn_single_post_display_controller() || ! apply_filters( 'tc_show_single_post_footer', true ) )
          return;

      //@todo check if some conditions below not redundant?
      if ( ! is_singular() || ! apply_filters( 'tc_show_author_metas_in_post', true ) || ! esc_attr( czr_fn_opt( 'tc_show_author_info' ) ) ) {
        return;
      }



      $author_id       = get_the_author_meta( 'ID' );
      $authors_id      = apply_filters( 'tc_post_author_id', array( $author_id ) );
      $authors_id      = is_array( $authors_id ) ? $authors_id : array( $author_id );
      //author candidates must have a bio to be displayed
      $authors_id      = array_filter( $authors_id, 'czr_fn_get_author_meta_description_by_id' );

      if ( empty( $authors_id ) ) {
        return;
      }

      $html            = '<footer class="entry-meta"><hr class="featurette-divider"><div class="author-info-wrapper">';

      foreach ( $authors_id as $author_id ) {
        $author_name   = get_the_author_meta( 'display_name', $author_id );
        $html         .= sprintf('<div class="author-info"><div class="%1$s">%2$s %3$s</div></div>',
                            apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),

                            sprintf('<div class="%1$s">%2$s</div>',
                                    apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                                    get_avatar( get_the_author_meta( 'user_email', $author_id ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) )
                            ),
                            sprintf('<div class="%1$s"><h3>%2$s</h3><div>%3$s</div><div class="author-link">%4$s</div></div>',
                                    apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                                    sprintf( __( 'About %s' , 'customizr' ), $author_name ),
                                    apply_filters( 'the_author_description', get_the_author_meta( 'description', $author_id ) ),
                                    sprintf( '<a href="%1$s" rel="author">%2$s</a>',
                                      esc_url( get_author_posts_url( $author_id ) ),
                                      sprintf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>' , 'customizr' ), $author_name )
                                    )
                            )
        );//end sprintf
      }//end for
      $html .= '</div></footer>';

      echo apply_filters( 'tc_post_footer', $html );
    }


    /***************************
    * SINGLE POST THUMBNAIL VIEW
    ****************************/
    /**
    * Get Single post thumb model + view
    * Inject it in the view
    * hook : esc_attr( czr_fn_opt( 'tc_single_post_thumb_location' ) || '__before_content'
    * @return  void
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function czr_fn_single_post_prepare_thumb() {
      //never display the featured image if a slider is displayed
      //=> since the post thumbnail is always printed after the slider, we can check if did_action('__after_carousel_inner'). @see class-content-slider.php
      if ( 0 != did_action('__after_carousel_inner') && '__before_main_wrapper' == current_filter() )
        return;
      $_size_to_request = apply_filters( 'tc_single_post_thumb_size' , $this -> czr_fn_get_current_thumb_size() );
      //get the thumbnail data (src, width, height) if any
      //array( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" )
      $_thumb_model   = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model( $_size_to_request ) ;
      //may be render
      if ( CZR_post_thumbnails::$instance -> czr_fn_has_thumb() ) {
        $_thumb_class   = implode( " " , apply_filters( 'tc_single_post_thumb_class' , array( 'row-fluid', 'tc-single-post-thumbnail-wrapper', 'tc-singular-thumbnail-wrapper', current_filter() ) ) );
        $this -> czr_fn_render_single_post_thumb_view( $_thumb_model , $_thumb_class );
      }
    }


    /**
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function czr_fn_render_single_post_thumb_view( $_thumb_model , $_thumb_class ) {
      echo apply_filters( 'tc_render_single_post_thumb_view',
        sprintf( '<div class="%1$s">%2$s</div>' ,
          $_thumb_class,
          CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model, 'span12', false )
        )
      );
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
    function czr_fn_single_post_display_controller() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      $tc_show_single_post_content = isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! czr_fn__f( '__is_home_empty');
      return apply_filters( 'tc_show_single_post_content', $tc_show_single_post_content );
    }


    /**
    * HELPER
    * @return boolean
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function czr_fn_show_single_post_thumbnail() {
      return $this -> czr_fn_single_post_display_controller() && apply_filters( 'tc_show_single_post_thumbnail', 'hide' != esc_attr( czr_fn_opt( 'tc_single_post_thumb_location' ) ) );
    }


    /**
    * HELPER
    * @return size string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function czr_fn_get_current_thumb_size() {
      $_exploded_location   = explode( '|', esc_attr( czr_fn_opt( 'tc_single_post_thumb_location' ) ) );
      $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
      return '__before_main_wrapper' == $_hook ? 'slider-full' : 'slider';
    }


    /**
    * hook : tc_post_thumb_wrapper
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
      return sprintf('<div class="%3$s"><a class="tc-rectangular-thumb" href="%1$s">%2$s</a></div>',
            get_permalink( get_the_ID() ),
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
    function czr_fn_write_thumbnail_inline_css( $_css ) {
      if ( ! $this -> czr_fn_show_single_post_thumbnail() )
        return $_css;

      $_single_thumb_height   = apply_filters('tc_single_post_thumb_height', esc_attr( czr_fn_opt( 'tc_single_post_thumb_height' ) ) );
      $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;

      $_single_thumb_smartphone_height   = apply_filters('tc_single_post_thumb_smartphone_height', esc_attr( czr_fn_opt( 'tc_single_post_thumb_smartphone_height' ) ) );
      $_single_thumb_smartphone_height   = (! $_single_thumb_smartphone_height || ! is_numeric($_single_thumb_smartphone_height) ) ? 200 : $_single_thumb_smartphone_height;

      $_css = sprintf("%s\n%s",
        $_css,
        ".tc-single-post-thumbnail-wrapper .tc-rectangular-thumb {
          max-height: {$_single_thumb_height}px;
          height :{$_single_thumb_height}px
        }\n
        .tc-center-images .tc-single-post-thumbnail-wrapper .tc-rectangular-thumb img {
          opacity : 0;
          -webkit-transition: opacity .5s ease-in-out;
          -moz-transition: opacity .5s ease-in-out;
          -ms-transition: opacity .5s ease-in-out;
          -o-transition: opacity .5s ease-in-out;
          transition: opacity .5s ease-in-out;
        }\n"
      );

      //max-height in smartphones: max-width: 480px
      if ( $_single_thumb_smartphone_height != $_single_thumb_height ) {
        $_css                       = sprintf("%s\n@media (max-width: %spx ){\n%s\n}\n",
          $_css,
          480,
          ".tc-single-post-thumbnail-wrapper .tc-rectangular-thumb {
            max-height: {$_single_thumb_smartphone_height}px;
            height :{$_single_thumb_smartphone_height}px
          }"
        );
      }
      return $_css;
    }

  }//end of class
endif;

?>