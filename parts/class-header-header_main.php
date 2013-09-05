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

class TC_header_main {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;

    function __construct () {

        self::$instance =& $this;

        //html > head actions
        add_action ( '__before_body'			, array( $this , 'tc_head_display' ));
        add_action ( 'wp_head'     				, array( $this , 'tc_favicon_display' ));

        //html > header actions
        add_action ( '__before_main_wrapper'	, 'get_header');
        add_action ( '__header' 				, array( $this , 'tc_header_display' ) );

        //body > header > navbar actions ordered by priority
        add_action ( '__navbar' 				, array( $this , 'tc_logo_title_display' ) , 10 );
        add_action ( '__navbar' 				, array( $this , 'tc_tagline_display' ) , 20 );
    }
	



    /**
	 * Displays what is inside the head html tag. Includes the wp_head() hook.
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_head_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		?>
		<head>
		    <meta charset="<?php bloginfo( 'charset' ); ?>" />
		    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
		    <title><?php wp_title( '|' , true, 'right' ); ?></title>
		    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		    <link rel="profile" href="http://gmpg.org/xfn/11" />
		    <?php
		      /* We add some JavaScript to pages with the comment form
		       * to support sites with threaded comments (when in use).
		       */
		      if ( is_singular() && get_option( 'thread_comments' ) )
		        wp_enqueue_script( 'comment-reply' );
		    ?>
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
      function tc_favicon_display() {
      	tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );

        $url = esc_url( tc__f( '__get_option' , 'tc_fav_upload' ) );
        if( $url != null)   {
          $type = "image/x-icon";
          if(strpos( $url, '.png' )) $type = "image/png";
          if(strpos( $url, '.gif' )) $type = "image/gif";
        
          $html = '<link rel="shortcut icon" href="'.$url.'" type="'.$type.'">';
        
        echo apply_filters( 'tc_favicon_display', $html );
        }

      }




	/**
	 * Displays what's inside header tags of the website
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0.10
	 */
	function tc_header_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		ob_start();

		?>
		<?php do_action( 'before_navbar' ); ?>
			<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	      	<div class="navbar-wrapper clearfix row-fluid">
          	
	            <?php 
	            //This hook is filtered with the logo, tagline, menu, ordered by priorities 10, 20, 30
	            do_action( '__navbar' ); 
	            ?>

        	</div><!-- /.navbar-wrapper -->

        	<?php do_action( '__after_navbar' ); ?>
		<?php

		$html = ob_get_contents();
       	ob_end_clean();
       	echo apply_filters( 'tc_header_display', $html );
	}


	


	/**
	 * The template for displaying the title or the logo
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_logo_title_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
       $logo_src    			= esc_url ( tc__f( '__get_option' , 'tc_logo_upload') ) ;
       $logo_resize 			= esc_attr( tc__f( '__get_option' , 'tc_logo_resize') );
       //logo styling option
       $logo_img_style			= '';
       if( $logo_resize == 1) {
       	 $logo_img_style 		= 'style="max-width:250px;max-height:100px"';
       }
       ob_start();
		?>

		<?php if( $logo_src != null) :?>

          <div class="brand span3">
          	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
            <h1><a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><img src="<?php echo $logo_src ?>" alt="<?php _e( 'Back Home' , 'customizr' ); ?>" <?php echo $logo_img_style ?>/></a>
            </h1>
          </div>

	    <?php else : ?>

          <div class="brand span3 pull-left">
          	<?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
             <h1><a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' , 'display' ) ); ?> | <?php bloginfo( 'description' ); ?>"><?php bloginfo( 'name' ); ?></a>
              </h1>
          </div>

	   <?php endif; ?>
	   <?php 
	   $html = ob_get_contents();
       ob_end_clean();
       echo apply_filters( 'tc_logo_title_display', $html );
	}





	/**
	 * Displays the tagline
	 *
	 *
	 * @package Customizr
	 * @since Customizr 3.0
	 */
	function tc_tagline_display() {
		tc__f('rec' , __FILE__ , __FUNCTION__, __CLASS__ );
		ob_start();
		?>
		<div class="container outside">
			 <?php tc__f( 'tip' , __FUNCTION__ , __CLASS__, __FILE__ ); ?>
	        <h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
	    </div>
		<?php
		$html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_tagline_display', $html );
	}


}//end of class