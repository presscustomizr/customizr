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

			        <?php
			        printf( '<div class="span4 credits"><p> &middot; &copy; %1$s <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a> &middot; '.__( 'Designed by ' , 'customizr' ).'<a href="http://www.themesandco.com">Themes &amp; Co</a> &middot;</p></div>' ,
							    esc_attr( date( 'Y' ) ),
							    esc_url( home_url() ),
							    esc_attr(get_bloginfo()),
							    esc_html( get_the_date() )
							  );
			        //printf( $credits);
			        ?>

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

 }//end of class


