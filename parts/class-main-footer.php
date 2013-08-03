<?php
/**
* Footer actions
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

class TC_footer {

    function __construct () {
        add_action( '__footer'					, array( $this , 'tc_display_footer' ));
        add_action( '__credits'					, array( $this , 'tc_footer_credits' ) , 10 , 2);

        /*add_filter( '__credits_filter' 			, 
        	array( $this , 'tc_footer_credits' ) , 
        	$priority = 10, 
        	$site_credits = esc_url( home_url() ),
        	$tc_credits = '<a href="http://www.themesandco.com">Themes &amp; Co</a>'
        );*/
    }



    /**
	 * The template for displaying the footer.
	 *
	 * Contains footer content and the closing of the
	 * #main-wrapper element.
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
    function tc_display_footer() {
    	?>
		 <div class="colophon">

		 	<div class="container">

		 		<div class="row-fluid">

				     <div class="span4 social-block pull-left">
				     	<?php do_action( '__social' , 'tc_social_in_footer' ); ?>
				     </div>

			        <?php do_action ('__credits' ); ?>

			        <div class="span4 backtop">

			        	<p class="pull-right">
			        		<a href="#"><?php _e( 'Back to top' , 'customizr' ) ?></a>
			        	</p>

			        </div>

      			</div><!-- .row-fluid -->

      		</div><!-- .container -->

      	</div><!-- .colophon -->
    	<?php
    }



    /**
	 * Footer Credits call back functions
	 * Can be filtered using the $site_credits, $tc_credits parameters
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.6
	 */
    function tc_footer_credits( $site_credits = null, $tc_credits = null ) {
    	$credits =  sprintf( '<div class="span4 credits"><p> &middot; &copy; %1$s <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a> &middot; Designed by %4$s &middot;</p></div>',
			    esc_attr( date( 'Y' ) ),
			    esc_url( home_url() ),
			    esc_attr(get_bloginfo()),
			    '<a href="http://www.themesandco.com">Themes &amp; Co</a>'
		);
		echo apply_filters( 'footer_credits', $credits );
    }

 }//end of class


