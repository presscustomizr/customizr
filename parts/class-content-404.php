<?php
/**
* 404 content actions
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

class TC_404 {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //header for 404
        add_action  ( '__before_loop'               , array( $this , 'tc_404_header' ));

        add_action  ( '__loop'                      , array( $this , 'tc_404_content' ));

        //selector filter
        add_filter  ( '__article_selectors'         , array( $this , 'tc_404_selectors' ));
    }

     
     /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_404_selectors () {
        if ( !is_404() )
         return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="post-0" class="post error404 no-results not-found row-fluid"';
    }




     /**
     * Renders the 404 page title
     *
     * @package Customizr
     * @since Customizr 3.0.10
     */
    function tc_404_header() {
        if ( !is_404() )
         return;
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        ?>
        <?php ob_start(); ?>
        <header class="entry-header">
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

            <h1 class="entry-title"><?php _e( 'Ooops, page not found' , 'customizr' ); ?></h1>
        </header>
        <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo apply_filters( 'tc_404_header', $html );
    }
    



    /**
     * The template part for displaying error 404 page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_404_content() {
       if ( !is_404() )
            return;
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        ?>
        <?php ob_start(); ?>
        <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
            <div class="tc-content span12 format-quote">

                <div class="entry-content format-icon">

                    <blockquote><p><?php _e( 'Speaking the Truth in times of universal deceit is a revolutionary act.' , 'customizr' ) ?></p>
                    <cite><?php _e( 'George Orwell' , 'customizr' ) ?></cite>
                    </blockquote>

                    <p><?php _e( 'Sorry, but the requested page is not found. You might try a search below.' , 'customizr' ); ?></p>

                    <?php get_search_form(); ?>

                </div>

                <hr class="featurette-divider">
                
            </div><!--content -->
        <?php
        $html = ob_get_contents();
        ob_end_clean();

        echo apply_filters( 'tc_404_content', $html );
    }

}//end of class