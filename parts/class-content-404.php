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

    function __construct () {
        add_action  ( '__404'                   , array( $this , 'tc_content_404' ));
    }


    function tc_content_404() {
    /**
     * The template part for displaying error 404 page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    
    ?>

        <?php global $content_class ?>

        <div class="tc-content <?php echo $content_class; ?> format-quote">

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
    }

}//end of class