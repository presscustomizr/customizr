<?php
/**
* No results content actions
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

class TC_no_results {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        add_action  ( '__loop'                        , array( $this , 'tc_no_result_content' ));

        //selector filter
        add_filter  ( '__article_selectors'           , array( $this , 'tc_no_results_selectors' ));
    }




     /**
     * Displays the conditional selectors of the article
     * 
     * @package Customizr
     * @since 3.0.10
     */
    function tc_no_results_selectors () {
        //must be archive or not-null search result. Returns false if home is empty in option.
        global $wp_query;
        if ( !is_search() || (is_search() && 0 != $wp_query -> post_count) )
            return;

        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        echo 'id="post-0" class="post error404 no-results not-found row-fluid"';
    }




    /**
     * Rendering the no search results
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_no_result_content() {

        global $wp_query;
        if ( !is_search() || (is_search() && 0 != $wp_query -> post_count) )
            return;
      
        tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
        ?>

        <?php ob_start(); ?>

            <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
            
            <div class="tc-content span12 format-quote">

                <div class="entry-content format-icon">

                    <blockquote><p><?php _e( 'Success is the ability to go from one failure to another with no loss of enthusiasm...' , 'customizr' ) ?></p>
                    <cite><?php _e( 'Sir Winston Churchill' , 'customizr' ) ?></cite>
                    </blockquote>
                    <p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' , 'customizr' ); ?></p>
                    <?php get_search_form(); ?>
                </div>

                <hr class="featurette-divider">

            </div><!--content -->
            

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_no_result_content' , $html );
    }

}//end of class