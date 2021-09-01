<?php
/**
* Footer actions
*
*/
if ( ! class_exists( 'CZR_footer_main' ) ) :
	class CZR_footer_main {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //All footer hooks setup
      add_action( 'wp_head'                   , array( $this , 'czr_fn_footer_hook_setup') );

      // Sticky footer style
      add_filter( 'tc_user_options_style' , array( $this , 'czr_fn_write_sticky_footer_inline_css' ) );
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
    function czr_fn_footer_hook_setup() {
      //add sticky_footer body class
      add_filter ( 'body_class' , array( $this, 'czr_fn_add_sticky_footer_body_class' ) );

      //print the sticky_footer push div
      add_action ( '__after_main_container' , array( $this, 'czr_fn_sticky_footer_push'), 100 );

      //html > footer actions
      add_action ( '__after_main_wrapper'   , 'get_footer');

      //boolean filter to control the footer's rendering
      if ( ! apply_filters( 'tc_display_footer', true ) )
        return;

      //footer actions
      add_action ( '__footer'         , array( $this , 'czr_fn_widgets_footer' ), 10 );
      add_action ( '__footer'         , array( $this , 'czr_fn_colophon_display' ), 20 );

      //colophon actions => some priorities are rtl dependants
      add_action ( '__colophon'       , array( $this , 'czr_fn_colophon_left_block' ), 10 );
      add_action ( '__colophon'       , array( $this , 'czr_fn_colophon_center_block' ), 20 );
      add_action ( '__colophon'       , array( $this , 'czr_fn_colophon_right_block' ), 30 );

      //since v3.2.0, Show back to top from the Customizer option panel
      add_action ( '__after_footer'       , array( $this , 'czr_fn_render_back_to_top') );
      //since v3.2.0, set no widget icons from the Customizer option panel
      add_filter ( 'tc_footer_widget_wrapper_class' , array( $this , 'czr_fn_set_widget_wrapper_class') );
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
	  function czr_fn_widgets_footer() {
    	//checks if there's at least one active widget area in footer.php.php
    	$status 					= false;
    	$footer_widgets 			= apply_filters( 'tc_footer_widgets', CZR_init::$instance -> footer_widgets );
    	foreach ( $footer_widgets as $key => $area ) {
    		$status = is_active_sidebar( $key ) ? true : $status;
    	}

      //if no active widget area yet, return
			if ( ! apply_filters( 'tc_has_footer_widgets', $status ) ) {
        return;
      }

			//hack to render white color icons if skin is grey or black
			$skin_class 					= ( in_array( czr_fn_opt( 'tc_skin') , array('grey.css' , 'black.css', 'black2.css')) ) ? 'white-icons' : '';
			$footer_widgets_wrapper_classes = implode(" ", apply_filters( 'tc_footer_widget_wrapper_class' , array('container' , 'footer-widgets', $skin_class) ) );
			ob_start();
			?>
				<div class="<?php echo $footer_widgets_wrapper_classes; ?>">
                    <div class="<?php echo implode( ' ' , apply_filters( 'tc_footer_widget_area', array('row' ,'widget-area') ) ) ?>" role="complementary">
						<?php do_action("__before_footer_widgets") ?>
						<?php foreach ( $footer_widgets as $key => $area )  : ?>

							<div id="<?php echo $key; ?>" class="<?php echo apply_filters( "{$key}_widget_class", "span4" ) ?>">
								<?php do_action("__before_{$key}_widgets"); ?>
								<?php if ( apply_filters( 'tc_has_footer_widgets_zone', is_active_sidebar( $key ), $key ) ) : ?>

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
		 * Displays the colophon (block below the widgets areas).
		 *
		 *
		 * @package Customizr
		 * @since Customizr 3.0.10
		 */
	    function czr_fn_colophon_display() {

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
	    function czr_fn_colophon_left_block() {
        //when do we display the socials?
        //1) must be enabled
        //the whole block will be always displayed for a matter of structure (columns)
	    	$_hide_socials = 0 == czr_fn_opt( 'tc_social_in_footer');


	      	echo apply_filters(
	      		'tc_colophon_left_block',
	      		sprintf('<div class="%1$s">%2$s</div>',
	      			implode( ' ', apply_filters( 'tc_colophon_left_block_class', array( 'span3', 'social-block', is_rtl() ? 'pull-right' : 'pull-left' ) ) ),
	      			( ! $_hide_socials ) ? sprintf('<span class="social-links">%1$s</span>',	czr_fn__f( '__get_socials' ) ) : ''
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
	    function czr_fn_colophon_center_block() {
	    	echo apply_filters(
	    		'tc_credits_display',
	    		sprintf('<div class="%1$s">%2$s</div>',
  		    		apply_filters( 'tc_colophon_center_block_class', 'span6 credits' ),
  		    		sprintf( '<p>%1$s %2$s %3$s</p>',
  						    apply_filters( 'tc_copyright_link', sprintf( '&middot; <span class="tc-copyright-text">&copy; %1$s</span> <a href="%2$s" title="%3$s" rel="bookmark">%3$s</a>', esc_attr( date( 'Y' ) ), esc_url( home_url() ), esc_attr( get_bloginfo() ) ) ),
                              apply_filters( 'tc_wp_powered',
                                  sprintf( '&middot; <span class="tc-wp-powered-text">%1$s</span> <a class="icon-wordpress" target="_blank" rel="noopener noreferrer" href="https://wordpress.org" title="%2$s"></a> &middot;',
                                      __('Powered by', 'customizr'),
                                      __('Powered by WordPress', 'customizr')
                                  )
                              ),
                              apply_filters( 'tc_credit_link',
                                  sprintf( '<span class="tc-credits-text">%1$s </span> &middot;',
                                      sprintf( __('Designed with the %s', 'customizr'), sprintf( '<a class="czr-designer-link" href="%1$s" title="%2$s">%2$s</a>', esc_url( CZR_WEBSITE . 'customizr' ), __('Customizr theme', 'customizr') ) )
                                  )
                              )
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
        function czr_fn_colophon_right_block() {
          //since 3.4.16 BTT button excludes BTT text
      if ( ! apply_filters('tc_show_text_btt', 0 == czr_fn_opt( 'tc_show_back_to_top' ) ) )
        return;

    	echo apply_filters(
    		'tc_colophon_right_block',
    		sprintf('<div class="%1$s"><p class="%3$s"><a class="back-to-top" href="#">%2$s</a></p></div>',
    			implode( ' ', apply_filters( 'tc_colophon_right_block_class', array( 'span3', 'backtop' ) ) ),
                __( 'Back to top' , 'customizr' ),
                is_rtl() ? 'pull-left' : 'pull-right'
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
    function czr_fn_set_rtl_colophon_priority( $_priority, $_location ) {
      if ( ! is_rtl() )
        return $_priority;
      //tc_colophon_right_priority OR tc_colophon_left_priority
      return 'right' == $_location ? 10 : 30;
    }


    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.3.27
    */
    function czr_fn_write_sticky_footer_inline_css( $_css ){
      if ( ! ( $this -> is_sticky_footer_enabled() || czr_fn_is_customizing() ) )
        return $_css;

      $_css = sprintf("%s\n%s",
        $_css,
        "#tc-push-footer { display: none; visibility: hidden; }
         .tc-sticky-footer #tc-push-footer.sticky-footer-enabled { display: block; }
        \n"
      );
      return $_css;
    }
    /*
    * Callback of body_class hook
    *
    * @package Customizr
    * @since Customizr 3.3.27
    */
    function czr_fn_add_sticky_footer_body_class($_classes) {
      if ( $this -> is_sticky_footer_enabled() )
        $_classes = array_merge( $_classes, array( 'tc-sticky-footer') );

      return $_classes;
    }

    /**
    *
    * Print hookable sticky footer push div
    *
    *
    * @package Customizr
    * @since Customizr 3.3.27
    *
    * @hook __after_main_container
    *
    */
    function czr_fn_sticky_footer_push() {
      if ( ! ( $this -> is_sticky_footer_enabled() || czr_fn_is_customizing() ) )
        return;

      echo '<div id="tc-push-footer"></div>';
    }


		/**
		* Displays the back to top on scroll
		* Has to be enabled in the customizer
		*
		* @package Customizr
		* @since Customizr 3.2.0
		*/
		function czr_fn_render_back_to_top() {
			if ( 0 == czr_fn_opt( 'tc_show_back_to_top' ) )
                return;
            printf('<div id="tc-footer-btt-wrapper" class="tc-btt-wrapper %1$s"><i class="btt-arrow"></i></div>',
                esc_attr( czr_fn_opt( 'tc_back_to_top_position' ) )
            );
		}


		/**
		* Displays the widget icons if option is enabled in customizer
		* @uses filter tc_footer_widget_wrapper_class
		*
		* @package Customizr
		* @since Customizr 3.2.0
		*/
		function czr_fn_set_widget_wrapper_class( $_original_classes ) {
			$_no_icons_classes = array_merge($_original_classes, array('no-widget-icons'));

			if ( 1 == czr_fn_opt( 'tc_show_footer_widget_icon' ) )
				return 0 == czr_fn_opt( 'tc_show_title_icon' ) ? $_no_icons_classes : $_original_classes;
			 //last condition
          	return $_no_icons_classes;
        }


    /* Helpers */

    /*
    *  Sticky footer enabled
    *
    * @return bool
    */
    function is_sticky_footer_enabled() {
      return 1 == czr_fn_opt( 'tc_sticky_footer');
    }
  }//end of class
endif;

?>