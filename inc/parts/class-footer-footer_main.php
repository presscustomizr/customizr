<?php
/**
* Footer actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_footer_main' ) ) :
	class TC_footer_main {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //All footer hooks setup
      add_action( 'wp_head'                   , array( $this , 'tc_footer_hook_setup') );

      //Handles RTL block order
      //takes 2 parameters : priority, location
      add_filter( 'tc_rtl_colophon_priority' , array( $this , 'tc_set_rtl_colophon_priority'), 10, 2 );
    }


    /******************************
    * HOOK SETUP
    *******************************/

    /**
    * Footer hooks setup
    * hook : wp_head
    * @return void
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_footer_hook_setup() {
      //html > footer actions
      add_action ( '__after_main_wrapper'   , 'get_footer');
      //footer actions
      add_action ( '__footer'         , array( $this , 'tc_widgets_footer' ), 10 );
      add_action ( '__footer'         , array( $this , 'tc_colophon_display' ), 20 );

      //colophon actions => some priorities are rtl dependants
      add_action ( '__colophon'       , array( $this , 'tc_colophon_left_block' ), apply_filters( 'tc_rtl_colophon_priority', 10, 'left' ) );
      add_action ( '__colophon'       , array( $this , 'tc_colophon_center_block' ), 20 );
      add_action ( '__colophon'       , array( $this , 'tc_colophon_right_block' ), apply_filters( 'tc_rtl_colophon_priority', 30 , 'right') );

      //since v3.2.0, Show back to top from the Customizer option panel
      add_action ( '__after_footer'       , array( $this , 'tc_render_back_to_top') );
      //since v3.2.0, set no widget icons from the Customizer option panel
      add_filter ( 'tc_footer_widget_wrapper_class' , array( $this , 'tc_set_widget_wrapper_class') );
    }



    /******************************
    * VIEWS
    *******************************/
	  /**
		* Displays the footer widgets areas
		*
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
	  function tc_widgets_footer() {
    	//checks if there's at least one active widget area in footer.php.php
    	$status 					= false;
    	$footer_widgets 			= apply_filters( 'tc_footer_widgets', TC_init::$instance -> footer_widgets );
    	foreach ( $footer_widgets as $key => $area ) {
    		$status = is_active_sidebar( $key ) ? true : $status;
    	}

      //if no active widget area yet, display the footer widget placeholder
			if ( ! $status ) {
        $this -> tc_display_footer_placeholder();
        return;
      }

			//hack to render white color icons if skin is grey or black
			$skin_class 					= ( in_array( TC_utils::$inst->tc_opt( 'tc_skin') , array('grey.css' , 'black.css')) ) ? 'white-icons' : '';
			$footer_widgets_wrapper_classes = implode(" ", apply_filters( 'tc_footer_widget_wrapper_class' , array('container' , 'footer-widgets', $skin_class) ) );
			ob_start();
			?>
				<div class="<?php echo $footer_widgets_wrapper_classes; ?>">
                    <div class="<?php echo implode( ' ' , apply_filters( 'tc_footer_widget_area', array('row' ,'widget-area') ) ) ?>" role="complementary">
						<?php do_action("__before_footer_widgets") ?>
						<?php foreach ( $footer_widgets as $key => $area )  : ?>

							<div id="<?php echo $key; ?>" class="<?php echo apply_filters( "{$key}_widget_class", "span4" ) ?>">
								<?php do_action("__before_{$key}_widgets"); ?>
								<?php if ( is_active_sidebar( $key ) ) : ?>

										<?php dynamic_sidebar( $key ); ?>

								<?php endif; ?>
								<?php do_action("__after_{$key}_widgets"); ?>
							</div><!-- .{$key}_widget_class -->

						<?php endforeach; ?>
						<?php do_action("__after_footer_widgets") ?>
					</div><!-- .row.widget-area -->
				</div><!--.footer-widgets -->
			<?php
			$html = ob_get_contents();
	        if ($html) ob_end_clean();
	        echo apply_filters( 'tc_widgets_footer', $html , $footer_widgets );
		}//end of function



    /**
    * When do we display this placeholder ?
    * -User logged in
    * -Admin
    * -User did not dismiss the notice
    * @param : string position left or right
    * @since Customizr 3.3
    */
    private function tc_display_footer_placeholder() {
      if ( ! TC_widgets::$instance -> tc_is_widget_placeholder_enabled( 'footer' ) )
        return;
      ?>
      <aside class="tc-widget-placeholder">
        <?php
          printf('<span class="tc-admin-notice">%1$s</span>',
            __( 'This block is visible for admin users only.', 'customizr')
          );

          printf('<h4>%1$s</h4>',
            __( 'The footer has no widgets', 'customizr')
          );

          printf('<p>%1s <a href="%2$s" title="%3$s" target="blank">%4$s</a></p>',
            __( 'You can add widgets to the footer in :', 'customizr' ),
            admin_url( 'widgets.php' ),
            __( 'Add widgets' , 'customizr'),
            __( 'appearance > widgets' , 'customizr' )
          );

          printf('<a class="tc-dismiss-notice" data-position="footer" href="#" title="%1$s">%1$s x</a>',
              __( 'dismiss notice', 'customizr')
          );
      ?>
      </aside>
      <?php
    }



	   /**
		 * Displays the colophon (block below the widgets areas).
		 *
		 *
		 * @package Customizr
		 * @since Customizr 3.0.10
		 */
	    function tc_colophon_display() {

	    	?>
	    	<?php ob_start() ?>
			 <div class="colophon">
			 	<div class="container">
			 		<div class="<?php echo apply_filters( 'tc_colophon_class', 'row-fluid' ) ?>">
					    <?php
						    //colophon blocks actions priorities
						    //renders blocks
						    do_action( '__colophon' );
					    ?>
	      			</div><!-- .row-fluid -->
	      		</div><!-- .container -->
	      	</div><!-- .colophon -->
	    	<?php
	    	$html = ob_get_contents();
	        if ($html) ob_end_clean();
	        echo apply_filters( 'tc_colophon_display', $html );
	    }




	    /**
		 * Displays the social networks block in the footer
		 *
		 *
		 * @package Customizr
		 * @since Customizr 3.0.10
		 */
	    function tc_colophon_left_block() {
	    	//when do we display this block ?
	        //1) if customizing always. (is hidden if empty of disabled)
	        //2) if not customizing : must be enabled and have social networks.
	    	$_nothing_to_render = ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_social_in_footer') ) ) || ! tc__f( '__get_socials' );
	    	$_hide_socials = $_nothing_to_render && TC___::$instance -> tc_is_customizing();
	    	$_nothing_to_render = $_nothing_to_render && ! TC___::$instance -> tc_is_customizing();

	      	echo apply_filters(
	      		'tc_colophon_left_block',
	      		sprintf('<div class="%1$s">%2$s</div>',
	      			apply_filters( 'tc_colophon_left_block_class', 'span4 social-block pull-left' ),
	      			( ! $_nothing_to_render ) ? sprintf('<span class="tc-footer-social-links-wrapper" %1$s>%2$s</span>',
	      				( $_hide_socials ) ? 'style="display:none"' : '',
	      				tc__f( '__get_socials' )
	      			) : ''
	      		)
	      	);
	    }




	   /**
		 * Footer Credits call back functions
		 * Can be filtered using the $site_credits, $tc_credits parameters
		 *
		 *
		 * @package Customizr
		 * @since Customizr 3.0.6
		 */
	    function tc_colophon_center_block() {
	    	echo apply_filters(
	    		'tc_credits_display',
	    		sprintf('<div class="%1$s">%2$s</div>',
		    		apply_filters( 'tc_colophon_center_block_class', 'span4 credits' ),
		    		sprintf( '<p>%1$s %2$s</p>',
						    apply_filters( 'tc_copyright_link', sprintf( '&middot; &copy; %1$s <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a>', esc_attr( date( 'Y' ) ), esc_url( home_url() ), esc_attr( get_bloginfo() ) ) ),
						    apply_filters( 'tc_credit_link', sprintf( '&middot; Designed by %1$s &middot;', '<a href="'.TC_WEBSITE.'">Press Customizr</a>' ) )
					)
	    		)
	    	);
	    }


	    /**
		* Displays the back to top fixed text block in the colophon
		*
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
		function tc_colophon_right_block() {
	    	echo apply_filters(
	    		'tc_colophon_right_block',
	    		sprintf('<div class="%1$s"><p class="pull-right"><a class="back-to-top" href="#">%2$s</a></p></div>',
	    			apply_filters( 'tc_colophon_right_block_class', 'span4 backtop' ),
	    			__( 'Back to top' , 'customizr' )
	    		)
	    	);
		}


    /******************************
    * CALLBACKS / SETTERS
    *******************************/
    /**
    * Set priorities for right and left colophon blocks, depending on the hook and is_rtl bool
    * hooks : tc_rtl_colophon_priority
    * @return void
    * @param  priority number, location string
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_set_rtl_colophon_priority( $_priority, $_location ) {
      if ( ! is_rtl() )
        return $_priority;
      //tc_colophon_right_priority OR tc_colophon_left_priority
      return 'right' == $_location ? 10 : 30;
    }



		/**
		* Displays the back to top on scroll
		* Has to be enabled in the customizer
		*
		* @package Customizr
		* @since Customizr 3.2.0
		*/
		function tc_render_back_to_top() {
			if ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_back_to_top' ) ) )
				return;
			echo '<div class="tc-btt-wrapper"><i class="btt-arrow"></i></div>';
		}


		/**
		* Displays the widget icons if option is enabled in customizer
		* @uses filter tc_footer_widget_wrapper_class
		*
		* @package Customizr
		* @since Customizr 3.2.0
		*/
		function tc_set_widget_wrapper_class( $_original_classes ) {
			$_no_icons_classes = array_merge($_original_classes, array('no-widget-icons'));

			if ( 1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_footer_widget_icon' ) ) )
				return ( 0 == esc_attr( TC_utils::$inst->tc_opt( 'tc_show_title_icon' ) ) ) ? $_no_icons_classes : $_original_classes;
			 //last condition
          	return $_no_icons_classes;
		}
	}//end of class
endif;
