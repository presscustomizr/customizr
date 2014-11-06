<?php
/**
* Pages content actions
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
if ( ! class_exists( 'TC_page' ) ) :
    class TC_page {
        static $instance;
        function __construct () {
            self::$instance =& $this;
            //pages templates
            add_action ( '__loop'                       , array( $this , 'tc_page_content' ));
        }



        /**
         * The template part for displaying page content
         *
         * @package Customizr
         * @since Customizr 3.0
         */
        function tc_page_content() {
            if ( 'page' != tc__f('__post_type') || ! is_singular() || tc__f( '__is_home_empty') )
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
    }//end of class
endif;