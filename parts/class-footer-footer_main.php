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

class TC_footer_main {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        //html > footer actions
        add_action( '__after_main_wrapper'		, 'get_footer');
        add_action( '__footer'					, array( $this , 'tc_display_footer' ));
        add_action( '__credits'					, array( $this , 'tc_footer_credits' ) , 10 , 2);
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
    	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    	?>

    	<?php ob_start() ?>

		 <div class="colophon">
		 <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>

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
    	$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_display_footer', $html );
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
    	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
    	?>

    	<?php ob_start() ?>

    	<div class="span4 credits">
    	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	    	<?php
		    	$credits =  sprintf( '<p> &middot; &copy; %1$s <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a> &middot; Designed by %4$s &middot;</p>',
					    esc_attr( date( 'Y' ) ),
					    esc_url( home_url() ),
					    esc_attr(get_bloginfo()),
					    '<a href="'.TC_WEBSITE.'">Themes &amp; Co</a>'
				);
				echo $credits;
			?>
		</div>
		<?php
		$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_footer_credits', $html );
    }

 }//end of class


