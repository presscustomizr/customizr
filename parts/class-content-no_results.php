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

    function __construct () {
        add_action  ( '__no_result'             , array( $this , 'tc_content_no_result' ));
    }



    /**
     * The template part for displaying no search results
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function tc_content_no_result() {
        ?>
        <?php global $content_class ?>

        <div class="tc-content <?php echo $content_class; ?> format-quote">

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
    }

}//end of class