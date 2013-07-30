<?php
/**
* Content actions
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

class TC_content {

    function __construct () {
    	//base content template called by the loop
        add_action  ( '__content' 				, array( $this , 'tc_display_content' ));
    }




    /**
	 * The base template for displaying content for all type of posts
	 *
	 * @package Customizr
	 * @since Customizr 1.0
	 */
    function tc_display_content() {
    	//get the post object
		global $post;
		//initialize the class alternative index
		global $tc_i;
		//initialize the content class
		global $content_class;

		//classes for content and thumbnail
		$thumb_class  				= 'span4';
		$content_class  			= ( tc__f( '__thumbnail' ) == true && !is_single()) ? 'span8' : 'span12';

		?>

		<?php if ( is_page()) : //pages ?>
			
			<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
			    <?php  	do_action( '__page' );   ?>
			</article><!-- #page -->

		<?php elseif (is_attachment()) : ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php do_action( '__attachment' ); ?>
			</article><!-- #post -->

		<?php elseif (is_404()) : ?>

			<article id="post-0" class="post error404 no-results not-found row-fluid">
				<?php do_action( '__404' ); ?>
			</article><!-- #post-0 -->

		<?php elseif (is_search() && !$post) : ?>
			
			<article id="post-0" class="post error404 no-results not-found row-fluid">
				<?php do_action( '__no_result' );?>
			</article><!-- #post-0 -->

		<?php else : // posts ?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'row-fluid' ); ?>>
				<?php
				  if( $tc_i%2 == 0) {

				      if (tc__f( '__thumbnail' ) == true) {

				        	do_action( '__post_thumbnail' , $thumb_class );
				    	}

				      do_action( '__post' );
				    }

				    else {

				      do_action( '__post' );

				      if (tc__f( '__thumbnail' ) == true) {

				        	do_action( '__post_thumbnail' , $thumb_class );
				    	}
				    }

				?>
			</article><!-- #post -->

			<?php if(!is_single()) : ?>
			  <hr class="featurette-divider">
			<?php endif; ?>

		<?php endif; ?>
		<?php
	}

}//end of class




	