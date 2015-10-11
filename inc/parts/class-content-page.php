<?php
/**
* Pages content actions
* Fired on 'wp'
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
if ( ! class_exists( 'TC_page' ) ) :
  class TC_page extends TC_base {
    static $instance;
    function __construct( $_args = array() ) {
      self::$instance =& $this;
      // Instanciates the parent class.
      if ( ! isset(parent::$instance) )
        parent::__construct( $_args );

      $this -> tc_set_page_hooks();
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
    function tc_set_page_hooks() {
      //add page content and footer to the __loop
      add_action( "__loop{$this -> loop_name}"           , array( $this , 'tc_page_content' ) );
      //page help blocks
      add_filter( 'the_content'       , array( $this, 'tc_maybe_display_img_smartload_help') , PHP_INT_MAX );
    }



    /**
     * The template part for displaying page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_page_content() {
      ob_start();

        do_action( "__before_content{$this -> loop_name}" );
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
        do_action( "__after_content{$this -> loop_name}" );

      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_page_content', $html );
    }



    /***************************
    * Page IMG SMARTLOAD HELP VIEW
    ****************************/
    /**
    * Displays a help block about images smartload for single posts prepended to the content
    * hook : the_content
    * @since Customizr 3.4+
    */
    function tc_maybe_display_img_smartload_help( $the_content ) {
      if ( ! in_the_loop() || ! TC_placeholders::tc_is_img_smartload_help_on( $the_content ) )
        return $the_content;

      return TC_placeholders::tc_print_smartload_help_block() . $the_content;
    }

  }//end of class
endif;