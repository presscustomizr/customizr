<?php
/**
* Main loop action
*
* 
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@themesandco.com>
* @copyright    Copyright (c) 2013, Nicolas GUILLAUME
* @link         http://themesandco.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class TC_loop {

    function __construct () {
        add_action ( '__loop'                             , array( $this , 'tc_loop' ));
    }


    /**
	 * The template for displaying main customizr loop
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
    function tc_loop() {

    	 //initialize the thumbnail class alternative index
        global $tc_i;
        $tc_i = 1;

        $tc_current_screen_layout = tc__f( '__screen_layout' , tc__f ( '__ID' ) );
        ?>

        <div class="<?php echo $tc_current_screen_layout['class'] ?> article-container">

        <?php
            /* get additionnal header for archive, search, 404 */
            do_action( '__post_list_header' );

              /* Start the Loop for all other case*/
              if ( have_posts() ) {

                while ( have_posts() ) {
                    the_post();
                    
                    do_action( '__content' );     
                    
                   	//if we display a page, check if comments are enabled in options. If it is a post, no conditions.
                    if ( (is_page() && esc_attr(tc__f ( '__get_option' , 'tc_page_comments' ) == 1) || is_single()) ) {
                    	comments_template( '' , true );
                    }

                $tc_i++;

                }//end while

              }//end if

              //no loop if error 404 or no search results
              else { //(is_404() || (is_search() && !have_posts())) 
                 do_action( '__content' );
              }

            /* include navigation for posts only */
            if(!is_page(tc__f ( '__ID' ))) {
               
               do_action( '__post_nav' );

             }
            ?>

        </div><!--.article-container -->
        
      <?php
    }
 }//end of class