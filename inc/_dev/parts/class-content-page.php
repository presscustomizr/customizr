<?php
/**
* Pages content actions
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
if ( ! class_exists( 'CZR_page' ) ) :
  class CZR_page {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'wp'                , array( $this , 'czr_fn_set_page_hooks' ) );
      //Set single post thumbnail with customizer options (since 3.5+)
      add_action( 'wp'                , array( $this , 'czr_fn_set_single_page_thumbnail_hooks' ));

      //append inline style to the custom stylesheet
      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      add_filter( 'tc_user_options_style'    , array( $this , 'czr_fn_write_thumbnail_inline_css') );
    }



    /***************************
    * PAGE HOOKS SETUP
    ****************************/
    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_set_page_hooks() {
      //add page content and footer to the __loop
      add_action( '__loop'              , array( $this , 'czr_fn_page_content' ) );
      //smartload help block
      add_filter( 'the_content'         , array( $this, 'czr_fn_maybe_display_img_smartload_help') , PHP_INT_MAX );
    }



    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.5+
    */
    function czr_fn_set_single_page_thumbnail_hooks() {
      if ( $this -> czr_fn_page_display_controller() && ! CZR_utils::$inst->czr_fn_is_home() ) {
        add_action( '__before_content'        , array( $this, 'czr_fn_maybe_display_featured_image_help') );
      }

      //__before_main_wrapper, 200
      //__before_content 0
      //__before_content 20
      if ( ! $this -> czr_fn_show_single_page_thumbnail() )
        return;

      $_exploded_location   = explode('|', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_page_thumb_location' )) );
      $_hook                = apply_filters( 'tc_single_page_thumb_hook', isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content' );
      $_priority            = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;

      //Hook post view
      add_action( $_hook, array($this , 'czr_fn_single_page_prepare_thumb') , $_priority );
      //Set thumb shape with customizer options (since 3.2.0)
      add_filter( 'tc_post_thumb_wrapper'      , array( $this , 'czr_fn_set_thumb_shape'), 10 , 2 );
    }




    /**
     * The template part for displaying page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function czr_fn_page_content() {
      if ( ! $this -> czr_fn_page_display_controller() )
        return;

      ob_start();

        do_action( '__before_content' );
        ?>

        <div class="entry-content">
          <?php
            the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
            wp_link_pages( array(
                'before'        => '<div class="btn-toolbar page-links"><div class="btn-group">' . __( 'Pages:' , 'customizr' ),
                'after'         => '</div></div>',
                'link_before'   => '<button class="btn btn-small">',
                'link_after'    => '</button>',
                'separator'     => '',
            )
                    );
          ?>
        </div>

        <?php
        do_action( '__after_content' );

      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_page_content', $html );
    }


    /***************************
    * SINGLE PAGE THUMBNAIL VIEW
    ****************************/
    /**
    * Get Single page thumb model + view
    * Inject it in the view
    * hook : esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_page_thumb_location' ) || '__before_content'
    * @return  void
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function czr_fn_single_page_prepare_thumb() {
      $_size_to_request = apply_filters( 'tc_single_page_thumb_size' , $this -> czr_fn_get_current_thumb_size() );
      //get the thumbnail data (src, width, height) if any
      //array( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" )
      $_thumb_model   = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model( $_size_to_request ) ;
      //may be render
      if ( CZR_post_thumbnails::$instance -> czr_fn_has_thumb() ) {
        $_thumb_class   = implode( " " , apply_filters( 'tc_single_page_thumb_class' , array( 'row-fluid', 'tc-single-page-thumbnail-wrapper', 'tc-singular-thumbnail-wrapper', current_filter() ) ) );
        $this -> czr_fn_render_single_page_thumb_view( $_thumb_model , $_thumb_class );
      }
    }


    /**
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function czr_fn_render_single_page_thumb_view( $_thumb_model , $_thumb_class ) {
      echo apply_filters( 'tc_render_single_page_thumb_view',
        sprintf( '<div class="%1$s">%2$s</div>' ,
          $_thumb_class,
          CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model, 'span12', false )
        )
      );
    }




    /***************************
    * SINGLE PAGE THUMBNAIL HELP VIEW
    ****************************/
    /**
    * Displays a help block about featured images for single pages
    * hook : __before_content
    * @since Customizr 3.5+
    */
    function czr_fn_maybe_display_featured_image_help() {
      if ( ! CZR_placeholders::czr_fn_is_thumbnail_help_on() )
        return;
      ?>
      <div class="tc-placeholder-wrap tc-thumbnail-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p><p>%3$s</p>',
              __( "You can display your page's featured image here if you have set one.", "customizr" ),
              sprintf( __("%s to display a featured image here.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', CZR_utils::czr_fn_get_customizer_url( array( "section" => "single_pages_sec") ), __( "Jump to the customizer now", "customizr") )
              ),
              sprintf( __( "Don't know how to set a featured image to a page? Learn how in the %s.", "customizr" ),
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
    * Page IMG SMARTLOAD HELP VIEW
    ****************************/
    /**
    * Displays a help block about images smartload for single pages prepended to the content
    * hook : the_content
    * @since Customizr 3.5+
    */
    function czr_fn_maybe_display_img_smartload_help( $the_content ) {
      if ( ! ( $this -> czr_fn_page_display_controller()  &&  in_the_loop() && CZR_placeholders::czr_fn_is_img_smartload_help_on( $the_content ) ) )
        return $the_content;

      return CZR_placeholders::czr_fn_get_smartload_help_block() . $the_content;
    }



    /******************************
    * SETTERS / HELPERS / CALLBACKS
    *******************************/
    /**
    * Page view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_page_display_controller() {
      $tc_show_page_content = 'page' == czr_fn__f('__post_type')
          && is_singular()
          && ! czr_fn__f( '__is_home_empty');

      return apply_filters( 'tc_show_page_content', $tc_show_page_content );
    }

    /**
    * HELPER
    * @return boolean
    * @package Customizr
    * @since Customizr 3.5+
    */
    function czr_fn_show_single_page_thumbnail() {
      return ! CZR_utils::$inst->czr_fn_is_home() && $this -> czr_fn_page_display_controller() && apply_filters( 'tc_show_single_page_thumbnail', 'hide' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_page_thumb_location' ) ) );
    }


    /**
    * HELPER
    * @return size string
    * @package Customizr
    * @since Customizr 3.5+
    */
    private function czr_fn_get_current_thumb_size() {
      $_exploded_location   = explode( '|', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_page_thumb_location' ) ) );
      $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
      return '__before_main_wrapper' == $_hook ? 'slider-full' : 'slider';
    }


    /**
    * hook : tc_page_thumb_wrapper
    * @return html string
    * @package Customizr
    * @since Customizr 3.5+
    */
    function czr_fn_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
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
    * @since Customizr 3.5+
    */
    function czr_fn_write_thumbnail_inline_css( $_css ) {
      if ( ! $this -> czr_fn_show_single_page_thumbnail() )
        return $_css;
      $_single_thumb_height   = apply_filters('tc_single_page_thumb_height', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_page_thumb_height' ) ) );
      $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
      return sprintf("%s\n%s",
        $_css,
        ".tc-single-page-thumbnail-wrapper .tc-rectangular-thumb {
          max-height: {$_single_thumb_height}px;
          height :{$_single_thumb_height}px
        }\n
        .tc-center-images .tc-single-page-thumbnail-wrapper .tc-rectangular-thumb img {
          opacity : 0;
          -webkit-transition: opacity .5s ease-in-out;
          -moz-transition: opacity .5s ease-in-out;
          -ms-transition: opacity .5s ease-in-out;
          -o-transition: opacity .5s ease-in-out;
          transition: opacity .5s ease-in-out;
        }\n"
      );
    }

  }//end of class
endif;

?>