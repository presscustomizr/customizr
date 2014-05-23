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

class TC_ressources {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;
        add_filter( '__customizr_styles'						, array( $this , 'tc_customizer_styles' ) );
        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_enqueue_customizer_styles' ) );
        add_action( 'wp_enqueue_scripts'						, array( $this , 'tc_scripts' ) );
        
        //Based on options
        add_action ( 'wp_head'                 					, array( $this , 'tc_write_custom_css' ), 20 );
        add_action ( 'wp_enqueue_scripts'						, array( $this , 'tc_optional_scripts' ) );
        add_action ( 'wp_footer'								, array( $this , 'tc_generated_scripts' ) , 20);
    }


    /**
	 * Registers and enqueues Customizr stylesheets
	 * @package Customizr
	 * @since Customizr 1.1
	 */
	  function tc_customizer_styles() {
	  	 //record for debug
      	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
      	
	      	$skin = tc__f( '__get_option' , 'tc_skin' );
	      	
		    wp_register_style( 
		      'customizr-skin' , 
		      TC_BASE_URL.'inc/css/'.$skin,
		      array(), 
		      CUSTOMIZR_VER, 
		      $media = 'all' 
		      );


		    //enqueue skin
		    wp_enqueue_style( 'customizr-skin' );

		    //enqueue WP style sheet
		    wp_enqueue_style( 
		    	'customizr-style' , 
		    	get_stylesheet_uri() , 
		    	array( 'customizr-skin' ),
		    	CUSTOMIZR_VER , 
		    	$media = 'all'  
		    );

	}


	/**
	 * Apply the __customizr_styles filter and make it filtrable.
	 * 
	 * @uses wp_enqueue_script() to manage script dependencies
	 * @package Customizr
	 * @since Customizr 3.0.11
	 */
	function tc_enqueue_customizer_styles() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		return tc__f('__customizr_styles');
	}





	/**
	 * Loads Customizr and JS script in footer for better time load.
	 * 
	 * @uses wp_enqueue_script() to manage script dependencies
	 * @package Customizr
	 * @since Customizr 1.0
	 */
	  function tc_scripts() {
	  	//record for debug
	  	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

	      wp_enqueue_script( 'jquery' );

	      wp_enqueue_script( 'jquery-ui-core' );

	      wp_enqueue_script( 'bootstrap' ,TC_BASE_URL . 'inc/js/bootstrap.min.js' ,array( 'jquery' ),null, $in_footer = true);
	     
	      //tc scripts
	      wp_enqueue_script( 'tc-scripts' ,TC_BASE_URL . 'inc/js/tc-scripts.min.js' ,array( 'jquery' ),null, $in_footer = true);

	      //holder image
	      wp_enqueue_script( 'holder' ,TC_BASE_URL . 'inc/js/holder.js' ,array( 'jquery' ),null, $in_footer = true);

	      //modernizr (must be loaded in wp_head())
	      wp_enqueue_script( 'modernizr' ,TC_BASE_URL . 'inc/js/modernizr.min.js' ,array( 'jquery' ),null, $in_footer = false);

	   }



    /**
     * Get the sanitized custom CSS from options array : fonts, custom css, and echoes the stylesheet
     * 
     * @package Customizr
     * @since Customizr 2.0.7
     */
    function tc_write_custom_css() {
    	//record for debug
    	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
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






    /**
	 * Enqueues Customizr scripts and style sheets based on user options
	 * @package Customizr
	 * @since Customizr 3.0.5
	 */
	 function tc_optional_scripts() {
	 	//record for debug
	 	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
	 	//fancybox script and style
	      $tc_fancybox = tc__f( '__get_option' , 'tc_fancybox' );
	      if ( $tc_fancybox == 1) {
	        wp_enqueue_script( 'fancyboxjs' ,TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.min.js' ,array( 'jquery' ),null, $in_footer = true);
	        wp_enqueue_style( 'fancyboxcss' , TC_BASE_URL . 'inc/js/fancybox/jquery.fancybox-1.3.4.css' );
	      }
	 }



	 /**
	 * Writes additional scripts in wp_footer. Based on user options.
	 * @package Customizr
	 * @since Customizr 3.0.5
	 */
	 function tc_generated_scripts() {
	 	//record for debug
	 	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		//is fancy box option active?
		$tc_fancybox = tc__f( '__get_option' , 'tc_fancybox' );

	 	if ( $tc_fancybox == 1) {
			//get option from customizr
			$autoscale = tc__f( '__get_option' , 'tc_fancybox_autoscale') ;
			//($autoscale == 1) ? _e('true') : _e('false');

		  	?>
			<script type="text/javascript">
				jQuery(document).ready(function( $) {
			      // Fancybox
			      $("a.grouped_elements").fancybox({
			        'transitionIn'  : 'elastic' ,
			        'transitionOut' : 'elastic' ,
			        'speedIn'   : 200, 
			        'speedOut'    : 200, 
			        'overlayShow' : false,
			        'autoScale' : <?php echo ($autoscale == 1) ? 'true' : 'false' ?>,
			        'changeFade' : 'fast',
			        'enableEscapeButton' : true
			      });
				});
			</script>

		  	<?php
		}//end if
	}

}//end of TC_ressources