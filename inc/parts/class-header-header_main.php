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
if ( ! class_exists( 'TC_header_main' ) ) :
	class TC_header_main {
	    static $instance;
	    function __construct () {
	        self::$instance =& $this;
	        //html > head actions
	        add_action ( '__before_body'			, array( $this , 'tc_head_display' ));
	        add_action ( 'wp_head'     				, array( $this , 'tc_favicon_display' ));

	        //html > header actions
	        add_action ( '__before_main_wrapper'	, 'get_header');
	        add_action ( '__header' 				, array( $this , 'tc_logo_title_display' ) , 10 );
	        add_action ( '__header' 				, array( $this , 'tc_tagline_display' ) , 20, 1 );
	        add_action ( '__header' 				, array( $this , 'tc_navbar_display' ) , 30 );

	        //body > header > navbar actions ordered by priority
	        add_action ( '__navbar' 				, array( $this , 'tc_social_in_header' ) , 10, 2 );
	        add_action ( '__navbar' 				, array( $this , 'tc_tagline_display' ) , 20, 1 );
	    }
		



	    /**
		* Displays what is inside the head html tag. Includes the wp_head() hook.
		*
		*
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_head_display() {
			ob_start();
				?>
				<head>
				    <meta charset="<?php bloginfo( 'charset' ); ?>" />
				    <meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
				    <title><?php wp_title( '|' , true, 'right' ); ?></title>
				    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
				    <link rel="profile" href="http://gmpg.org/xfn/11" />
				    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
				   
				   <!-- Icons font support for IE6-7  -->
				    <!--[if lt IE 8]>
				      <script src="<?php echo TC_BASE_URL ?>inc/assets/css/fonts/lte-ie7.js"></script>
				    <![endif]-->
				    <?php wp_head(); ?>
				    <!--Icons size hack for IE8 and less -->
				    <!--[if lt IE 9]>
				      <link href="<?php echo TC_BASE_URL ?>inc/assets/css/fonts/ie8-hacks.css" rel="stylesheet" type="text/css"/>
				    <![endif]-->
				</head>
				<?php
			$html = ob_get_contents();
		    if ($html) ob_end_clean();
		    echo apply_filters( 'tc_head_display', $html );
		}




		/**
	    * Render favicon from options
	    *
	    * @package Customizr
	    * @since Customizr 3.0 
	    */
	    function tc_favicon_display() {
	       	$saved_path 			= esc_url ( tc__f( '__get_option' , 'tc_fav_upload') );
	       	if ( ! $saved_path || is_null($saved_path) )
	       		return;

	       	//rebuild the path : check if the full path is already saved in DB. If not, then rebuild it.
	       	$upload_dir 			= wp_upload_dir();
	       	
	       	$url 					= ( false !== strpos( $saved_path , '/wp-content/' ) ) ? $saved_path : $upload_dir['baseurl'] . $saved_path;
	       	//makes ssl compliant url
	       	$url 					= is_ssl() ? str_replace('http://', 'https://', $url) : $url;
			$url    				= apply_filters( 'tc_fav_src' , $url );

	        if( null == $url )
	        	return;

          	$type = "image/x-icon";
          	if ( strpos( $url, '.png') ) $type = "image/png";
          	if ( strpos( $url, '.gif') ) $type = "image/gif";
        
        	echo apply_filters( 'tc_favicon_display',
	        		sprintf('<link rel="shortcut icon" href="%1$s" type="%2$s">' ,
	        			$url,
	        			$type
	        		)
        	);
	    }




	    /**
		* The template for displaying the logo (text or img)
		*
		*
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_logo_title_display() {
	       	//rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
	       	$upload_dir 			= wp_upload_dir();
	       	$saved_path 			= esc_url ( tc__f( '__get_option' , 'tc_logo_upload') );
	       	$logo_src 				= ( false !== strpos( $saved_path , '/wp-content/' ) ) ? $saved_path : $upload_dir['baseurl'] . $saved_path;
	       	//makes ssl compliant
	       	$logo_src 				= is_ssl() ? str_replace('http://', 'https://', $logo_src) : $logo_src;
	       	$logo_src    			= apply_filters( 'tc_logo_src' , $logo_src ) ;
	       	
	       	$logo_resize 			= esc_attr( tc__f( '__get_option' , 'tc_logo_resize') );
	      	$accepted_formats		= apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
	       	$filetype 				= wp_check_filetype ($logo_src);
	       	$logo_class 			= apply_filters( 'tc_logo_class', 'brand span3' );
			?>

			<?php if( ! empty($logo_src) && in_array( $filetype['ext'], $accepted_formats ) ) :?>
				
				<?php
				//filter args
		   		$filter_args 		= array( 
			       		'logo_src' 			=>	$logo_src, 
			       		'logo_resize' 		=>	$logo_resize,
			       		'logo_class'		=> 	$logo_class
		   		);

				ob_start();

				$width 				= '';
				$height 			= '';
				//gets height and width from image, we check if getimagesize can be used first with the error control operator
				if ( @getimagesize($logo_src) ) {
					list( $width, $height ) = getimagesize($logo_src);
				}

				?>

		        <div class="<?php echo $logo_class ?>">
		        	<?php 
		        	do_action( '__before_logo' );

		          	printf( '<a class="site-logo" href="%1$s" title="%2$s"><img src="%3$s" alt="%4$s" width="%5$s" height="%6$s" %7$s /></a>',
		          		apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ) ) ,
		          		apply_filters( 'tc_logo_link_title', sprintf( '%1$s | %2$s' , __( esc_attr( get_bloginfo( 'name' ) ) ) , __( esc_attr( get_bloginfo( 'description' ) ) ) ) ),
		          		$logo_src,	
		          		__( 'Back Home' , 'customizr' ),
						$width,
						$height,
						( 1 == $logo_resize) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
												apply_filters( 'tc_logo_max_width', 250 ),
												apply_filters( 'tc_logo_max_height', 100 )
												) : ''
		          	); 

		           	do_action( '__after_logo' );
		           	?>
		        </div> <!-- brand span3 -->

		        <?php 
			   	$html = ob_get_contents();
		       	if ($html) ob_end_clean();
		       	echo apply_filters( 'tc_logo_img_display', $html, $filter_args );
		       	?>

		    
		    <?php else : ?>

		    	<?php ob_start(); ?>

		        <div class="<?php echo $logo_class ?> pull-left">

		        	<?php
		        	do_action( '__before_logo' );

			          	printf('<%1$s><a class="site-title" href="%2$s" title="%3$s">%4$s</a></%1$s>',
			          		apply_filters( 'tc_site_title_tag', 'h1' ) ,
			          		apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ) ) ,
							apply_filters( 'tc_site_title_link_title', sprintf( '%1$s | %2$s' , __( esc_attr( get_bloginfo( 'name' ) ) ) , __( esc_attr( get_bloginfo( 'description' ) ) ) ) ),
			          		__( esc_attr( get_bloginfo( 'name' ) ) )
			          	);

				 	do_action( '__after_logo' ) 
				 	?>

		        </div> <!-- brand span3 pull-left -->

		        <?php 
			   	$html = ob_get_contents();
		       	if ($html) ob_end_clean();
		       	echo apply_filters( 'tc_logo_text_display', $html, $logo_class);
		       	?>

		   <?php endif; ?>

		   
		   <?php 
		}


		
		/**
		* Displays what's inside the navbar of the website. Uses the resp parameter for __navbar action.
		*
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
		function tc_navbar_display() {
			ob_start();
			do_action( '__before_navbar' ); 
			?>

	      	<div class="<?php echo apply_filters( 'tc_navbar_wrapper_class', 'navbar-wrapper clearfix span9' ) ?>">

	      		<div class="navbar notresp row-fluid pull-left">
	      			<div class="navbar-inner" role="navigation">
	      				<div class="row-fluid">
	            			<?php 
	            				do_action( '__navbar' ); //hook of social, tagline, menu, ordered by priorities 10, 20, 30 
	            			?>
	            		</div><!-- .row-fluid -->
	            	</div><!-- /.navbar-inner -->
	            </div><!-- /.navbar notresp -->

	            <div class="navbar resp">
	            	<div class="navbar-inner" role="navigation">
	            		<?php 
	            			do_action( '__navbar' , 'resp' ); //hook of social, menu, ordered by priorities 10, 20
	            		?>
	            	</div><!-- /.navbar-inner -->
	      		</div><!-- /.navbar resp -->

	    	</div><!-- /.navbar-wrapper -->

	        <?php
	        do_action( '__after_navbar' );
			
			$html = ob_get_contents();
	       	if ($html) ob_end_clean();
	       	echo apply_filters( 'tc_navbar_display', $html );
		}


		


		/**
		* Displays the social networks block in the header
		*
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
	    function tc_social_in_header($resp = null) {
	        //class added if not resp
	        $social_header_block_class 	=  ('resp' == $resp) ? '' : 'span5';
	        $social_header_block_class	=	apply_filters( 'tc_social_header_block_class', $social_header_block_class , $resp );
	        
	        $html = sprintf('<div class="social-block %1$s">%2$s</div>',
	        		$social_header_block_class,
	        		( 0 != tc__f( '__get_option', 'tc_social_in_header') ) ? tc__f( '__get_socials' ) : ''
	        );

	        echo apply_filters( 'tc_social_in_header', $html, $resp );
	    }





		/**
		* Displays the tagline. This function has two hooks : __header and __navbar
		*
		*
		* @package Customizr
		* @since Customizr 3.0
		*/
		function tc_tagline_display() {
			

			if ( '__header' == current_filter() ) { //when hooked on  __header

				$html = sprintf('<div class="container outside"><%1$s class="site-description">%2$s</%1$s></div>',
						apply_filters( 'tc_tagline_tag', 'h2' ),
						apply_filters( 'tc_tagline_text ', __( esc_attr( get_bloginfo( 'description' ) ) ) )
				);

				
			} else { //when hooked on __navbar
				$html = sprintf('<%1$s class="%2$s inside site-description">%3$s</%1$s>',
						apply_filters( 'tc_tagline_tag', 'h2' ),
						apply_filters( 'tc_tagline_class', 'span7' ),
						apply_filters( 'tc_tagline_text ', __( esc_attr( get_bloginfo( 'description' ) ) ) )
				);

			}

	        echo apply_filters( 'tc_tagline_display', $html );
		}
	}//end of class
endif;