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
	        //Set header hooks
	        add_action ( 'template_redirect' 		, array( $this , 'tc_set_header_hooks' ) );

	        //Set header options
	        add_action ( 'template_redirect' 		, array( $this , 'tc_set_header_options' ) );
	    }



	    /**
		* Set all header hooks
		* template_redirect callback
		* @return  void
		*
		* @package Customizr
		* @since Customizr 3.2.6
		*/
	    function tc_set_header_hooks() {
	    	//html > head actions
	        add_action ( '__before_body'			, array( $this , 'tc_head_display' ));
	        add_action ( 'wp_head'     				, array( $this , 'tc_favicon_display' ));

	        //html > header actions
	        add_action ( '__before_main_wrapper'	, 'get_header');
	        add_action ( '__header' 				, array( $this , 'tc_prepare_logo_title_display' ) , 10 );
	        add_action ( '__header' 				, array( $this , 'tc_tagline_display' ) , 20, 1 );
	        add_action ( '__header' 				, array( $this , 'tc_navbar_display' ) , 30 );

	        //New menu view (since 3.2.0)
          add_filter ( 'tc_navbar_display', array( $this , 'tc_new_menu_view'), 10, 2);

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
            <?php if ( ! function_exists( '_wp_render_title_tag' ) ) :?>
				      <title><?php wp_title( '|' , true, 'right' ); ?></title>
            <?php endif; ?>
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
	       	$_fav_option  			= esc_attr( tc__f( '__get_option' , 'tc_fav_upload') );
	       	if ( ! $_fav_option || is_null($_fav_option) )
	       		return;

	       	$_fav_src 				= '';
	       	//check if option is an attachement id or a path (for backward compatibility)
	       	if ( is_numeric($_fav_option) ) {
	       		$_attachement_id 	= $_fav_option;
	       		$_attachment_data 	= apply_filters( 'tc_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'large' ) );
	       		$_fav_src 			= $_attachment_data[0];
	       	} else { //old treatment
	       		$_saved_path 		= esc_url ( tc__f( '__get_option' , 'tc_fav_upload') );
	       		//rebuild the path : check if the full path is already saved in DB. If not, then rebuild it.
		       	$upload_dir 		= wp_upload_dir();
		       	$_fav_src 			= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
	       	}

	       	//makes ssl compliant url
	       	$_fav_src 				= apply_filters( 'tc_fav_src' , is_ssl() ? str_replace('http://', 'https://', $_fav_src) : $_fav_src );

	        if( null == $_fav_src || !$_fav_src )
	        	return;

          	$type = "image/x-icon";
          	if ( strpos( $_fav_src, '.png') ) $type = "image/png";
          	if ( strpos( $_fav_src, '.gif') ) $type = "image/gif";

        	echo apply_filters( 'tc_favicon_display',
	        		sprintf('<link rel="shortcut icon" href="%1$s" type="%2$s">' ,
	        			$_fav_src,
	        			$type
	        		)
        	);
	    }




    /**
		* Prepare the logo / title view
		*
		*
		* @package Customizr
		* @since Customizr 3.2.3
		*/
		function tc_prepare_logo_title_display() {
      $logos_type = array( '_sticky_', '_');
      $logos_img  = array();

      $accepted_formats		= apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
      $logo_classes 			= array( 'brand', 'span3');
      foreach ( $logos_type as $logo_type ){
          // check if we have to print the sticky logo
          if ( '_sticky_' == $logo_type && ! $this -> tc_use_sticky_logo() )
              continue;

          //check if the logo is a path or is numeric
          //get src for both cases
          $_logo_src 				= '';
          $_width 				= false;
          $_height 				= false;
          $_attachement_id 		= false;
          $_logo_option  			= esc_attr( tc__f( '__get_option' , "tc{$logo_type}logo_upload") );
          //check if option is an attachement id or a path (for backward compatibility)
          if ( is_numeric($_logo_option) ) {
              $_attachement_id 	= $_logo_option;
              $_attachment_data 	= apply_filters( "tc{$logo_type}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'large' ) );
              $_logo_src 			= $_attachment_data[0];
              $_width 			= isset($_attachment_data[1]) ? $_attachment_data[1] : $_width;
              $_height 			= isset($_attachment_data[2]) ? $_attachment_data[2] : $_height;
          } else { //old treatment
              //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
              $upload_dir 			= wp_upload_dir();
              $_saved_path 			= esc_url ( tc__f( '__get_option' , "tc{$logo_type}logo_upload") );
              $_logo_src 				= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
          }

          //hook + makes ssl compliant
          $_logo_src    			= apply_filters( "tc{$logo_type}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;

          $logo_resize 			= ( $logo_type == '_' ) ? esc_attr( tc__f( '__get_option' , 'tc_logo_resize') ) : '';
          $filetype 				= TC_utils::$instance -> tc_check_filetype ($_logo_src);
          if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) ) {
              $_args 		= array(
                      'logo_src' 				=> $_logo_src,
                      'logo_resize' 			=> $logo_resize,
                      'logo_attachment_id' 	=> $_attachement_id,
                      'logo_width' 			=> $_width,
                      'logo_height' 			=> $_height,
                      'logo_type'             => trim($logo_type,'_')
              );
              $logos_img[] = $this -> tc_logo_img_view($_args);
          }
      }//end foreach
      //render
      if ( count($logos_img) == 0 )
          $this -> tc_title_view($logo_classes);
      else
          $this -> tc_logo_view( array (
            'logo_class'   => $logo_classes,
            // normal logo first
            'logos_img'    => array_reverse($logos_img)
            )
          );
		}




		/**
		* Title view
		*
		* @package Customizr
		* @since Customizr 3.2.3
		*/
		function tc_title_view( $logo_classes ) {
			ob_start();
			?>
      <div class="<?php echo implode( " ", apply_filters( 'tc_logo_class', array_merge($logo_classes , array('pull-left') ) ) ) ?> ">

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
      echo apply_filters( 'tc_logo_text_display', $html, $logo_classes);
		}



    /**
    * Logo img view
    * @return  filtered string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function tc_logo_img_view( $_args ){
      //Extracts $args : logo_src, logo_resize, logo_attachment_id, logo_width, logo_height, logo_type
      extract($_args);
      $_html = sprintf('<img src="%1$s" alt="%2$s" %3$s %4$s %5$s %6$s class="%7$s %8$s"/>',
        $logo_src,
      	__( 'Back Home' , 'customizr' ),
        $logo_width ? sprintf( 'width="%1$s"', $logo_width ) : '',
        $logo_height ? sprintf( 'height="%1$s"', $logo_height ) : '',
        ( 1 == $logo_resize) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
                                apply_filters( 'tc_logo_max_width', 250 ),
                                apply_filters( 'tc_logo_max_height', 100 )
                                ) : '',
        implode(' ' , apply_filters('tc_logo_other_attributes' , ( 0 == tc__f( '__get_option' , 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) ),
        $logo_type,
        $logo_attachment_id ? sprintf( 'attachment-%1$s', $logo_attachment_id ) : ''
      );
      return apply_filters( 'tc_logo_img_view', $_html, $_args);
    }




    /**
    * Logo view
    *
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function tc_logo_view( $_args ) {
        //Exctracts $args : $logo_class, $logos_img (array of <img>)
        extract($_args);
        ob_start();
        ?>

        <div class="<?php echo implode( " ", apply_filters( 'tc_logo_class', $logo_class ) ) ?>">
        <?php
            do_action( '__before_logo' );

            printf( '<a class="site-logo" href="%1$s" title="%2$s">%3$s</a>',
                apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ) ) ,
                apply_filters( 'tc_logo_link_title', sprintf( '%1$s | %2$s' , __( esc_attr( get_bloginfo( 'name' ) ) ) , __( esc_attr( get_bloginfo( 'description' ) ) ) ) ),
                implode( '', $logos_img )
        	);

            do_action( '__after_logo' );
        ?>
        </div> <!-- brand span3 -->

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_logo_img_display', $html, $_args );
    }



		/**
		* Displays what's inside the navbar of the website.
		* Uses the resp parameter for __navbar action.
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
		function tc_navbar_display() {
			$_navbar_classes = implode( " ", apply_filters( 'tc_navbar_wrapper_class', array('navbar-wrapper', 'clearfix', 'span9') ) );
			ob_start();
			do_action( '__before_navbar' );
				?>
		      	<div class="<?php echo $_navbar_classes ?>">

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
  	* New menu view.
  	* One menu instead of two
  	* Original function : TC_header::tc_navbar_display
  	*
  	* @package Customizr
  	* @since Customizr 3.2.0
  	*/
  	function tc_new_menu_view() {
    	$_navbar_classes = implode( " ", apply_filters( 'tc_navbar_wrapper_class', array('navbar-wrapper', 'clearfix', 'span9') ) );
    	do_action( '__before_navbar' );
      	?>
      	<div class="<?php echo $_navbar_classes ?>">
        	<div class="navbar resp">
          		<div class="navbar-inner" role="navigation">
            		<div class="row-fluid">
              		<?php
                		do_action( '__navbar' , 'resp' ); //hook of social, menu, ordered by priorities 10, 20
              		?>
          			</div>
          		</div><!-- /.navbar-inner -->
        	</div><!-- /.navbar resp -->
      	</div><!-- /.navbar-wrapper -->
    	<?php
    	do_action( '__after_navbar' );
  	}





		/**
		* Displays the social networks block in the header
		*
		*
		* @package Customizr
		* @since Customizr 3.0.10
		*/
    function tc_social_in_header($resp = null) {
        //when do we display this block ?
        //1) if customizing always. (is hidden if empty of disabled)
        //2) if not customizing : must be enabled and have social networks.
        $_nothing_to_render = ( 0 == esc_attr( tc__f( '__get_option', 'tc_social_in_header') ) ) || ! tc__f( '__get_socials' );
        if ( ! TC_utils::$instance -> tc_is_customizing() && $_nothing_to_render )
        	return;

        //class added if not resp
        $social_header_block_class 	=  ('resp' == $resp) ? '' : 'span5';
        $social_header_block_class	=	apply_filters( 'tc_social_header_block_class', $social_header_block_class , $resp );

        $html = sprintf('<div class="social-block %1$s" %3$s>%2$s</div>',
        		$social_header_block_class,
        		tc__f( '__get_socials' ),
        		$_nothing_to_render ? 'style="display:none"' : ''
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
						apply_filters( 'tc_tagline_text', __( esc_attr( get_bloginfo( 'description' ) ) ) )
				);


			} else { //when hooked on __navbar
				$html = sprintf('<%1$s class="%2$s inside site-description">%3$s</%1$s>',
						apply_filters( 'tc_tagline_tag', 'h2' ),
						apply_filters( 'tc_tagline_class', 'span7' ),
						apply_filters( 'tc_tagline_text', __( esc_attr( get_bloginfo( 'description' ) ) ) )
				);

			}
	        echo apply_filters( 'tc_tagline_display', $html );
		}//end of fn



		/**
   	* Callback for template_redirect
   	* Set customizer user options
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function tc_set_header_options() {
			//Set some body classes
			add_filter( 'body_class'               , array( $this , 'tc_add_body_classes') );
			//Set header classes from options
			add_filter( 'tc_header_classes' 		   , array( $this , 'tc_set_header_classes') );
			//Set tagline visibility with a customizer option (since 3.2.0)
      add_filter( 'tc_tagline_display'  		 , array( $this , 'tc_set_tagline_visibility') );
      //Set logo layout with a customizer option (since 3.2.0)
      add_filter( 'tc_logo_class'  			     , array( $this , 'tc_set_logo_title_layout') );
      //Set top border style option
      add_filter( 'tc_user_options_style'		 , array( $this , 'tc_write_header_inline_css') );
		}


		/*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
		function tc_write_header_inline_css( $_css ) {
			if ( 1 != esc_attr( tc__f( '__get_option' , 'tc_top_border') ) ) {
				$_css = sprintf("%s\n%s",
					$_css,
					"header.tc-header {border-top: none;}\n"
		       	);
		    }

	     //STICKY HEADER
		    if ( 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_shrink_title_logo') ) || TC_utils::$instance -> tc_is_customizing() ) {
		    	$_logo_shrink 	= implode (';' , apply_filters('tc_logo_shrink_css' , array("height:30px!important","width:auto!important") )	);

		    	$_title_font 	= implode (';' , apply_filters('tc_title_shrink_css' , array("font-size:0.6em","opacity:0.8","line-height:1.2em") ) );

			    $_css = sprintf("%s\n%s",
			    	$_css,
			    	".sticky-enabled .tc-shrink-on .site-logo img {
						{$_logo_shrink}
					}\n
					.sticky-enabled .tc-shrink-on .brand .site-title {
						{$_title_font}
					}\n"
				);
			}

      //STICKY LOGO
      if ( $this -> tc_use_sticky_logo() ) {
        $_css = sprintf( "%s\n%s",
            $_css,
            ".site-logo img.sticky {
                display: none;
             }\n
            .sticky-enabled .tc-sticky-logo-on .site-logo img {
                display: none;
             }\n
            .sticky-enabled .tc-sticky-logo-on .site-logo img.sticky{
                display: inline-block;
            }\n"
        );
      }

			//HEADER Z-INDEX
	    if ( 100 != esc_attr( tc__f( '__get_option' , 'tc_sticky_z_index') ) ) {
	    	$_custom_z_index 	= esc_attr( tc__f( '__get_option' , 'tc_sticky_z_index') );
		    $_css = sprintf("%s\n%s",
  		      $_css,
  		      ".tc-no-sticky-header .tc-header, .tc-sticky-header .tc-header {
  					z-index:{$_custom_z_index}
  				}\n"
        );
			}
			return $_css;
		}



		/*
    * Callback of body_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_add_body_classes($_classes) {
      //STICKY HEADER
    	if ( 1 == esc_attr( tc__f( '__get_option' , 'tc_sticky_header' ) ) ) {
       		$_classes = array_merge( $_classes, array('tc-sticky-header') );
       		//STICKY TRANSPARENT ON SCROLL
	       	if ( 1 == esc_attr( tc__f( '__get_option' , 'tc_sticky_transparent_on_scroll' ) ) )
	       		$_classes = array_merge( $_classes, array('tc-transparent-on-scroll') );
	       	else
	       		$_classes = array_merge( $_classes, array('tc-solid-color-on-scroll') );
	       }
       	else {
       		$_classes = array_merge( $_classes, array('tc-no-sticky-header') );
       	}

       	return $_classes;
    }



		/**
   	* Set the header classes
   	* Callback for tc_header_classes filter
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function tc_set_header_classes( $_classes ) {
			//backward compatibility (was not handled has an array in previous versions)
			if ( ! is_array($_classes) )
				return $_classes;

			$_show_tagline 			= 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_show_tagline') );
      $_show_title_logo 		= 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_show_title_logo') );
      $_use_sticky_logo 		= $this -> tc_use_sticky_logo();
			$_shrink_title_logo 	= 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_shrink_title_logo') );
			$_show_menu 			  = 0 != esc_attr( tc__f( '__get_option' , 'tc_sticky_show_menu') );
			$_header_layout 		= "logo-" . esc_attr( tc__f( '__get_option' , 'tc_header_layout' ) );
			$_add_classes 			= array(
				$_show_tagline ? 'tc-tagline-on' : 'tc-tagline-off',
        $_show_title_logo ? 'tc-title-logo-on' : 'tc-title-logo-off',
        $_use_sticky_logo ? 'tc-sticky-logo-on' : '',
				$_shrink_title_logo ? 'tc-shrink-on' : 'tc-shrink-off',
				$_show_menu ? 'tc-menu-on' : 'tc-menu-off',
				$_header_layout
			);
			return array_merge( $_classes , $_add_classes );
		}



    /**
    * Returns a boolean wheter we're using or not a specific sticky logo
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function tc_use_sticky_logo(){
        if ( ! esc_attr( tc__f( '__get_option' , "tc_sticky_logo_upload") ) )
            return false;
        if ( ! ( esc_attr( tc__f( '__get_option' , "tc_sticky_header") ) &&
                     esc_attr( tc__f( '__get_option' , 'tc_sticky_show_title_logo') )
               )
        )
            return false;
        return true;
    }


		/**
   	* Callback for tagline view, filter : tc_tagline_display
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function tc_set_tagline_visibility($html) {
			//if customizing just hide it
			if ( TC_utils::$instance -> tc_is_customizing() && 0 == esc_attr( tc__f( '__get_option' , 'tc_show_tagline') ) )
				return str_replace('site-description"', 'site-description" style="display:none"', $html);
			//live context, don't paint it at all
			if ( ! TC_utils::$instance -> tc_is_customizing() && 0 == esc_attr( tc__f( '__get_option' , 'tc_show_tagline') ) )
				return '';
			return $html;
		}


		/**
   	* Callback for tc_logo_class
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function tc_set_logo_title_layout( $_classes ) {
			//backward compatibility (was not handled has an array in previous versions)
			if ( ! is_array($_classes) )
				return $_classes;

			$_layout = esc_attr( tc__f( '__get_option' , 'tc_header_layout') );
			switch ($_layout) {
				case 'left':
					$_classes = array('brand', 'span3' , 'pull-left');
				break;

				case 'right':
					$_classes = array('brand', 'span3' , 'pull-right');
				break;

				default :
					$_classes = array('brand', 'span3' , 'pull-left');
				break;
			}
			return $_classes;
		}
	}//end of class
endif;
