<?php
/**
* Header actions
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

class TC_header {

    function __construct () {

        //html > head actions
        add_action ( '__head'					, array( $this , 'tc_display_head' ));
        add_action ( 'wp_head'                  , array( $this , 'tc_write_custom_css' ), 20 );
        add_action ( '__favicon'     			, array( $this , 'tc_display_favicon' ));

        //body > header actions
        add_action ( '__logo_title' 			, array( $this , 'tc_display_logo_title' ));
        add_action ( '__tagline' 				, array( $this , 'tc_display_tagline' ));
    }
	



    /**
	 * The template for displaying <head> stuffs.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_display_head() {
	?>
		<head>
		    <meta charset="<?php bloginfo( 'charset' ); ?>" />
		    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
		    <title><?php wp_title( '|' , true, 'right' ); ?></title>
		    <meta name="viewport" content="width=device-width, initial-scale=1.0">
		    <link rel="profile" href="http://gmpg.org/xfn/11" />
		    <?php
		      /* We add some JavaScript to pages with the comment form
		       * to support sites with threaded comments (when in use).
		       */
		      if ( is_singular() && get_option( 'thread_comments' ) )
		        wp_enqueue_script( 'comment-reply' );
		    ?>

		  <!-- Favicon -->
		    <?php do_action( '__favicon' ); ?>
		    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		   
		   <!-- Icons font support for IE6-7 -->
		    <!--[if lt IE 8]>
		      <script src="<?php echo TC_BASE_URL ?>inc/css/fonts/lte-ie7.js"></script>
		    <![endif]-->
		    <?php
		      /* Always have wp_head() just before the closing </head>
		       * tag of your theme, or you will break many plugins, which
		       * generally use this hook to add elements to <head> such
		       * as styles, scripts, and meta tags.
		       */
		      wp_head();
		    ?>
		</head>
		<?php
	}





	/**
      * Render favicon from options
      *
      * @package Customizr
      * @since Customizr 3.0 
     */
      function tc_display_favicon() {

        $url = esc_url(tc__f ( '__get_option' , 'tc_fav_upload' ));

        if( $url != null)   {
          $type = "image/x-icon";
          if(strpos( $url, '.png' )) $type = "image/png";
          if(strpos( $url, '.gif' )) $type = "image/gif";
        
          $fav_link = '<link rel="shortcut icon" href="'.$url.'" type="'.$type.'">';

          echo $fav_link;
        }

      }






     /**
     * Get the sanitized custom CSS from options array : fonts, custom css, and echoes the stylesheet
     * 
     * @package Customizr
     * @since Customizr 2.0.7
     */
    function tc_write_custom_css() {
        $tc_custom_css      = esc_textarea(tc__f ( '__get_option' , 'tc_custom_css' ));
        $tc_top_border      = esc_attr(tc__f ( '__get_option' , 'tc_top_border' ));
        
        if ( isset( $tc_custom_css) && !empty( $tc_custom_css)) {
          $tc_custom_style  = '<style type="text/css">'.$tc_custom_css.'</style>';
          echo $tc_custom_style;
        }
        //disable top border in customizer skin options
        if ( isset( $tc_top_border) && $tc_top_border == 0) {
          $tc_custom_style  = '<style type="text/css">header.tc-header {border-top: none;}</style>';
          echo $tc_custom_style;
        }

      }




	/**
	 * The template for displaying the title or the logo
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_display_logo_title() {
       $logo_src    			= esc_url ( tc__f ( '__get_option' , 'tc_logo_upload' )) ;
       $logo_resize 			= esc_attr( tc__f ( '__get_option' , 'tc_logo_resize' ));
       //logo styling option
       $logo_img_style			= '';
       if( $logo_resize == 1) {
       	 $logo_img_style 		= 'style="max-width:250px;max-height:100px"';
       }
		?>

		<?php if( $logo_src != null) :?>

          <div class="brand span3">
            <h1><a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><img src="<?php echo $logo_src ?>" alt="<?php _e( 'Back Home' , 'customizr' ); ?>" <?php echo $logo_img_style ?>/></a>
            </h1>
          </div>

	    <?php else : ?>

          <div class="brand span3 pull-left">
             <h1><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a>
              </h1>
          </div>

	   <?php endif; ?>
	   
	   <?php
	}





	/**
	 * The template for displaying the tagline
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_display_tagline() {
		?>
		<div class="container outside">
	        <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
	    </div>
		<?php
	}


}//end of class