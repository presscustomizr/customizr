<?php
/**
* Loads front end stylesheets and scripts
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

class TC_resources {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_styles' ) );
        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizr_scripts' ) );
        
        //Based on options
        add_action ( 'wp_head'                 					, array( $this , 'tc_write_custom_css' ), 20 );
    }


    /**
	 * Registers and enqueues Customizr stylesheets
	 * @package Customizr
	 * @since Customizr 1.1
	 */
	  function tc_enqueue_customizr_styles() {
	    wp_register_style( 
	      'customizr-skin' ,
	      TC_init::$instance -> tc_active_skin(),
	      array(), 
	      CUSTOMIZR_VER, 
	      $media = 'all' 
	      );

	    //enqueue skin
	    wp_enqueue_style( 'customizr-skin' );

	    //enqueue WP stylesheet
	    wp_enqueue_style( 
	    	'customizr-style' , 
	    	get_stylesheet_uri() , 
	    	array( 'customizr-skin' ),
	    	CUSTOMIZR_VER , 
	    	$media = 'all'  
	    );

	}






	/**
	 * Loads Customizr and JS script in footer for better time load.
	 * 
	 * @uses wp_enqueue_script() to manage script dependencies
	 * @package Customizr
	 * @since Customizr 1.0
	 */
	 function tc_enqueue_customizr_scripts() {
	    //wp scripts
	  	if ( is_singular() && get_option( 'thread_comments' ) ) {
		    wp_enqueue_script( 'comment-reply' );
		}

	    wp_enqueue_script( 'jquery' );

	    wp_enqueue_script( 'jquery-ui-core' );

	    //bootstrap scripts
	    wp_enqueue_script( 'bootstrap' ,TC_BASE_URL . 'inc/js/bootstrap.min.js' ,array( 'jquery' ),null, $in_footer = true);
	     
	    //tc scripts
	    wp_enqueue_script( 'tc-scripts' ,TC_BASE_URL . 'inc/js/tc-scripts.min.js' ,array( 'jquery' ),null, $in_footer = true);

	    //passing dynamic vars to tc-scripts
	    //fancybox options
		$tc_fancybox 		= ( 1 == tc__f( '__get_option' , 'tc_fancybox' ) ) ? true : false;
		$autoscale 			= ( 1 == tc__f( '__get_option' , 'tc_fancybox_autoscale') ) ? true : false ;

        //carousel options
        //gets slider options if any for home/front page or for others posts/pages
	    $js_slidername       = tc__f('__is_home') ? tc__f( '__get_option' , 'tc_front_slider' ) : get_post_meta( tc__f('__ID') , $key = 'post_slider_key' , $single = true );
	    $js_sliderdelay      = tc__f('__is_home') ? tc__f( '__get_option' , 'tc_slider_delay' ) : get_post_meta( tc__f('__ID') , $key = 'slider_delay_key' , $single = true );
	      
		//add those to filters
		$js_slidername 		= apply_filters( 'tc_js_slider_name', $js_slidername , tc__f('__ID') );
		$js_sliderdelay 	= apply_filters( 'tc_js_slider_delay' , $js_sliderdelay, tc__f('__ID') );

		//creates a filter for stop-on-hover option
		$sliderhover		= apply_filters( 'tc_stop_slider_hover', true );

		//Smooth scroll on click option : filtered to allow easy disabling if needed (conflict)
		$smooth_scroll		= apply_filters( 'tc_smooth_scroll', esc_attr( tc__f( '__get_option' , 'tc_link_scroll') ) );

		global $wp_query;
		//has the post comments ? adds a boolean parameter in js
		$has_post_comments = ( 0 != $wp_query -> post_count && comments_open() && get_comments_number() != 0 ) ? true : false;

		//adds the jquery effect library if smooth scroll is enabled => easeOutExpo effect
		if ( $smooth_scroll ) {
			wp_enqueue_script( 'jquery-effects-core');
		}

		//Gets the left and right sidebars class for js actions
		$left_sb_class      = sprintf('.%1$s.left.tc-sidebar',
                            	apply_filters('tc_left_sidebar_class' , 'span3' )
      	);
      	$right_sb_class      = sprintf('.%1$s.right.tc-sidebar',
                            	apply_filters('tc_right_sidebar_class' , 'span3' )
      	);

		wp_localize_script( 
	        'tc-scripts', 
	        'TCParams',
	        apply_filters('tc_js_front_end_params' , array(
		          	'FancyBoxState' 		=> $tc_fancybox,
		          	'FancyBoxAutoscale' 	=> $autoscale,
		          	'SliderName' 			=> $js_slidername,
		          	'SliderDelay' 			=> $js_sliderdelay,
		          	'SliderHover'			=> $sliderhover,
		          	'SmoothScroll'			=> $smooth_scroll ? 'easeOutExpo' : 'linear',
		          	'ReorderBlocks' 		=> esc_attr( tc__f( '__get_option' , 'tc_block_reorder') ),
		          	'CenterSlides' 			=> esc_attr( tc__f( '__get_option' , 'tc_center_slides') ),
		          	'HasComments' 			=> $has_post_comments,
		          	'LeftSidebarClass' 		=> $left_sb_class,
		          	'RightSidebarClass' 	=> $right_sb_class,
	        	)
	       	)//end of filter
         );


	    //holder image
	    wp_enqueue_script( 'holder' ,TC_BASE_URL . 'inc/js/holder.js' ,array( 'jquery' ),null, $in_footer = true);

	    //modernizr (must be loaded in wp_head())
	    wp_enqueue_script( 'modernizr' ,TC_BASE_URL . 'inc/js/modernizr.min.js' ,array( 'jquery' ),null, $in_footer = false);

	    //fancybox script and style
	    if ( 1 == tc__f( '__get_option' , 'tc_fancybox' ) ) {
	      	wp_enqueue_script( 'fancyboxjs' ,TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.min.js' ,array( 'jquery' ),null, $in_footer = true);
	      	wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.min.css' );
	    }

	    //retina support script
	    if ( 1 == tc__f( '__get_option' , 'tc_retina_support' ) ) {
	    	wp_enqueue_script( 'retinajs', TC_BASE_URL . 'inc/js/retina.min.js', null, null, $in_footer = true );
	   	}

	 }




    /**
     * Get the sanitized custom CSS from options array : fonts, custom css, and echoes the stylesheet
     * 
     * @package Customizr
     * @since Customizr 2.0.7
     */
    function tc_write_custom_css() {
        $tc_custom_css      	= esc_html( tc__f( '__get_option' , 'tc_custom_css') );
        $tc_top_border      	= esc_attr( tc__f( '__get_option' , 'tc_top_border') );
        ?>

        <?php if ( isset( $tc_custom_css) && !empty( $tc_custom_css) ) : ?>
          <style id="option-custom-css" type="text/css"><?php echo html_entity_decode($tc_custom_css) ?></style>
        <?php endif; ?>

        <?php if ( ( isset( $tc_top_border) && $tc_top_border == 0) ) :  //disable top border in customizer skin options?>
          <style id="option-top-border" type="text/css">header.tc-header {border-top: none;}</style>
        <?php endif; ?>

        <?php
    }//end of function

}//end of TC_ressources