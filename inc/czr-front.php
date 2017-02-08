<?php
/**
* Header actions
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
if ( ! class_exists( 'CZR_header_main' ) ) :
	class CZR_header_main {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //Set header hooks
      //we have to use 'wp' action hook to show header in multisite wp-signup/wp-activate.php which don't fire template_redirect hook
      //(see https://github.com/presscustomizr/customizr/issues/395)
      add_action ( 'wp'                    , array( $this , 'czr_fn_set_header_hooks' ) );

      //Set header options
      add_action ( 'wp'                    , array( $this , 'czr_fn_set_header_options' ) );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      //Set top border style option
      add_filter( 'tc_user_options_style'  , array( $this , 'czr_fn_write_header_inline_css') );
    }


    /***************************
    * HEADER HOOKS SETUP
    ****************************/
	  /**
		* Set all header hooks
		* wp callback
		* @return  void
		*
		* @package Customizr
		* @since Customizr 3.2.6
		*/
    function czr_fn_set_header_hooks() {
    	//html > head actions
      add_action ( '__before_body'	  , array( $this , 'czr_fn_head_display' ));

      //The WP favicon (introduced in WP 4.3) will be used in priority
      add_action ( 'wp_head'     		  , array( $this , 'czr_fn_favicon_display' ));

      //html > header actions
      add_action ( '__before_main_wrapper'	, 'get_header');

      //boolean filter to control the header's rendering
      if ( ! apply_filters( 'tc_display_header', true ) )
        return;

      add_action ( '__header' 				, array( $this , 'czr_fn_prepare_logo_title_display' ) , 10 );
      add_action ( '__header' 				, array( $this , 'czr_fn_tagline_display' ) , 20, 1 );
      add_action ( '__header' 				, array( $this , 'czr_fn_navbar_display' ) , 30 );

      //New menu view (since 3.2.0)
      add_filter ( 'tc_navbar_display', array( $this , 'czr_fn_new_menu_view'), 10, 2);

      //body > header > navbar actions ordered by priority
  	  // GY : switch order for RTL sites
  	  if (is_rtl()) {
        add_action ( '__navbar' 				, array( $this , 'czr_fn_social_in_header' ) , 20, 2 );
        add_action ( '__navbar' 				, array( $this , 'czr_fn_tagline_display' ) , 10, 1 );
  	  }
  	  else {
        add_action ( '__navbar' 				, array( $this , 'czr_fn_social_in_header' ) , 10, 2 );
        add_action ( '__navbar' 				, array( $this , 'czr_fn_tagline_display' ) , 20, 1 );
  	  }

      //add a 100% wide container just after the sticky header to reset margin top
      if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_header' ) ) || CZR___::$instance -> czr_fn_is_customizing() )
        add_action( '__after_header'              , array( $this, 'czr_fn_reset_margin_top_after_sticky_header'), 0 );

    }



    /**
    * Callback for wp
    * Set customizer user options
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_header_options() {
      //Set some body classes
      add_filter( 'body_class'               , array( $this , 'czr_fn_add_body_classes') );
      //Set header classes from options
      add_filter( 'tc_header_classes'        , array( $this , 'czr_fn_set_header_classes') );
      //Set logo layout with a customizer option (since 3.2.0)
      add_filter( 'tc_logo_class'            , array( $this , 'czr_fn_set_logo_title_layout') );
    }


    /***************************
    * VIEWS
    ****************************/
	  /**
		* Displays what is inside the head html tag. Includes the wp_head() hook.
		*
		*
		* @package Customizr
		* @since Customizr 3.0
		*/
		function czr_fn_head_display() {
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

				   <!-- html5shiv for IE8 and less  -->
				    <!--[if lt IE 9]>
				      <script src="<?php echo TC_BASE_URL ?>inc/assets/js/html5.js"></script>
				    <![endif]-->
				    <?php wp_head(); ?>
				</head>
				<?php
			$html = ob_get_contents();
		    if ($html) ob_end_clean();
		    echo apply_filters( 'tc_head_display', $html );
		}




		/**
    * Render favicon from options
    * Since WP 4.3 : let WP do the job if user has set the WP site_icon setting.
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_favicon_display() {
     	//is there a WP favicon set ?
      //if yes then let WP do the job
      if ( function_exists('has_site_icon') && has_site_icon() )
        return;

      $_fav_option  			= esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fav_upload') );
     	if ( ! $_fav_option || is_null($_fav_option) )
     		return;

     	$_fav_src 				= '';
     	//check if option is an attachement id or a path (for backward compatibility)
     	if ( is_numeric($_fav_option) ) {
     		$_attachement_id 	= $_fav_option;
     		$_attachment_data 	= apply_filters( 'tc_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'full' ) );
     		$_fav_src 			= $_attachment_data[0];
     	} else { //old treatment
     		$_saved_path 		= esc_url ( CZR_utils::$inst->czr_fn_opt( 'tc_fav_upload') );
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
      		sprintf('<link id="czr-favicon" rel="shortcut icon" href="%1$s" type="%2$s">' ,
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
		function czr_fn_prepare_logo_title_display() {
      $logos_type = array( '_sticky_', '_');
      $logos_img  = array();

      $accepted_formats		= apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
      $logo_classes 			= array( 'brand', 'span3');
      foreach ( $logos_type as $logo_type ){
          // check if we have to print the sticky logo
          if ( '_sticky_' == $logo_type && ! $this -> czr_fn_use_sticky_logo() )
              continue;

          //check if the logo is a path or is numeric
          //get src for both cases
          $_logo_src 				= '';
          $_width 				= false;
          $_height 				= false;
          $_attachement_id 		= false;
          $_logo_option  			= esc_attr( CZR_utils::$inst->czr_fn_opt( "tc{$logo_type}logo_upload") );
          //check if option is an attachement id or a path (for backward compatibility)
          if ( is_numeric($_logo_option) ) {
              $_attachement_id 	= $_logo_option;
              $_attachment_data 	= apply_filters( "tc{$logo_type}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
              $_logo_src 			= $_attachment_data[0];
              $_width 			= ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
              $_height 			= ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
          } else { //old treatment
              //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
              $upload_dir 			= wp_upload_dir();
              $_saved_path 			= esc_url ( CZR_utils::$inst->czr_fn_opt( "tc{$logo_type}logo_upload") );
              $_logo_src 				= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
          }

          //hook + makes ssl compliant
          $_logo_src    			= apply_filters( "tc{$logo_type}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;

          $logo_resize 			= ( $logo_type == '_' ) ? esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_logo_resize') ) : '';
          $filetype 				= CZR_utils::$inst -> czr_fn_check_filetype ($_logo_src);
          if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) ) {
              $_args 		= array(
                      'logo_src' 				=> $_logo_src,
                      'logo_resize' 			=> $logo_resize,
                      'logo_attachment_id' 	=> $_attachement_id,
                      'logo_width' 			=> $_width,
                      'logo_height' 			=> $_height,
                      'logo_type'             => trim($logo_type,'_')
              );
              $logos_img[] = $this -> czr_fn_logo_img_view($_args);
          }
      }//end foreach
      //render
      if ( count($logos_img) == 0 )
          $this -> czr_fn_title_view($logo_classes);
      else
          $this -> czr_fn_logo_view( array (
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
		function czr_fn_title_view( $logo_classes ) {
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
    function czr_fn_logo_img_view( $_args ){
      //Extracts $args : logo_src, logo_resize, logo_attachment_id, logo_width, logo_height, logo_type
      extract($_args);
      $_html = sprintf( '<img src="%1$s" alt="%2$s" %3$s %4$s %5$s %6$s class="%7$s %8$s"/>',
        $logo_src,
      	apply_filters( 'tc_logo_alt', __( 'Back Home' , 'customizr' ) ),
        $logo_width ? sprintf( 'width="%1$s"', $logo_width ) : '',
        $logo_height ? sprintf( 'height="%1$s"', $logo_height ) : '',
        ( 1 == $logo_resize) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
                                apply_filters( 'tc_logo_max_width', 250 ),
                                apply_filters( 'tc_logo_max_height', 100 )
                                ) : '',
        implode(' ' , apply_filters('tc_logo_other_attributes' , ( 0 == CZR_utils::$inst->czr_fn_opt( 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) ),
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
    function czr_fn_logo_view( $_args ) {
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
		function czr_fn_navbar_display() {
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
  	* Original function : CZR_header::tc_navbar_display
  	*
  	* @package Customizr
  	* @since Customizr 3.2.0
  	*/
  	function czr_fn_new_menu_view() {
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
          			</div><!-- /.row-fluid -->
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
    function czr_fn_social_in_header($resp = null) {
        //when do we display this block ?
        //1) if customizing: must be enabled
        //2) if not customizing : must be enabled and have social networks.
        $_nothing_to_render         = 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_social_in_header' ) ) || ! ( $_socials = czr_fn__f( '__get_socials' ) );

        if ( $_nothing_to_render )
        	return;

        //class added if not resp
        $social_header_block_class 	=  ('resp' == $resp) ? '' : 'span5';
        $social_header_block_class	=	apply_filters( 'tc_social_header_block_class', $social_header_block_class , $resp );

        $html = sprintf('<div class="social-block %1$s"><div class="social-links">%2$s</div></div>',
        		$social_header_block_class,
        		$_socials
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
		function czr_fn_tagline_display() {
      //do not display tagline if the related option is false or no tagline available
      if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_tagline' ) ) )
        return;

      $_tagline_text  = czr_fn_get_tagline_text( $echo = false );

      if ( ! $_tagline_text )
        return;

			if ( '__header' == current_filter() ) { //when hooked on  __header

				$html = sprintf('<div class="container outside"><%1$s class="site-description">%2$s</%1$s></div>',
						apply_filters( 'tc_tagline_tag', 'h2' ),
            $_tagline_text
				);


			} else { //when hooked on __navbar
				$html = sprintf('<%1$s class="%2$s inside site-description">%3$s</%1$s>',
						apply_filters( 'tc_tagline_tag', 'h2' ),
						apply_filters( 'tc_tagline_class', 'span7' ),
						$_tagline_text
				);

			}
      echo apply_filters( 'tc_tagline_display', $html );
		}//end of fn



    /*
    * hook : __after_header hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_reset_margin_top_after_sticky_header() {
      echo apply_filters(
        'tc_reset_margin_top_after_sticky_header',
        sprintf('<div id="tc-reset-margin-top" class="container-fluid" style="margin-top:%1$spx"></div>',
          apply_filters('tc_default_sticky_header_height' , 103 )
        )
      );
    }




    /***************************
    * SETTER / GETTERS / HELPERS
    ****************************/
		/*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
		function czr_fn_write_header_inline_css( $_css ) {
      //TOP BORDER
			if ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_top_border') ) ) {
  			$_css = sprintf("%s\n%s",
  				$_css,
  				"header.tc-header {border-top: none;}\n"
  	    );
	    }

      //STICKY HEADER
	    if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_shrink_title_logo') ) || CZR___::$instance -> czr_fn_is_customizing() ) {
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
      if ( $this -> czr_fn_use_sticky_logo() ) {
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
	    if ( 100 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_z_index') ) ) {
	    	$_custom_z_index 	= esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_z_index') );
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
    function czr_fn_add_body_classes($_classes) {
      //STICKY HEADER
    	if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_header' ) ) ) {
     		$_classes = array_merge( $_classes, array('tc-sticky-header', 'sticky-disabled') );
     		//STICKY TRANSPARENT ON SCROLL
       	if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_transparent_on_scroll' ) ) )
       		$_classes = array_merge( $_classes, array('tc-transparent-on-scroll') );
       	else
       		$_classes = array_merge( $_classes, array('tc-solid-color-on-scroll') );
       }
     	else {
     		$_classes = array_merge( $_classes, array('tc-no-sticky-header') );
     	}

      //No navbar box
      if ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_display_boxed_navbar') ) )
          $_classes = array_merge( $_classes , array('no-navbar' ) );

      return $_classes;
    }



		/**
   	* Set the header classes
   	* Callback for tc_header_classes filter
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function czr_fn_set_header_classes( $_classes ) {
			//backward compatibility (was not handled has an array in previous versions)
			if ( ! is_array($_classes) )
				return $_classes;

			$_show_tagline 			= 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_show_tagline') );
      $_show_title_logo 		= 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_show_title_logo') );
      $_use_sticky_logo 		= $this -> czr_fn_use_sticky_logo();
			$_shrink_title_logo 	= 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_shrink_title_logo') );
			$_show_menu 			  = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_show_menu') );
			$_header_layout 		= "logo-" . esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout' ) );
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
    function czr_fn_use_sticky_logo(){
        if ( ! esc_attr( CZR_utils::$inst->czr_fn_opt( "tc_sticky_logo_upload") ) )
            return false;
        if ( ! ( esc_attr( CZR_utils::$inst->czr_fn_opt( "tc_sticky_header") ) &&
                     esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_sticky_show_title_logo') )
               )
        )
            return false;
        return true;
    }



		/**
   	* Callback for tc_logo_class
   	*
   	* @package Customizr
   	* @since Customizr 3.2.0
   	*/
		function czr_fn_set_logo_title_layout( $_classes ) {
			//backward compatibility (was not handled has an array in previous versions)
			if ( ! is_array($_classes) )
				return $_classes;

			$_layout = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout') );
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
?><?php
/**
* Menu action
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
if ( ! class_exists( 'CZR_menu' ) ) :
  class CZR_menu {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //Set menu customizer options (since 3.2.0)
      add_action( 'wp'             , array( $this, 'czr_fn_set_menu_hooks') );
    }


    /***************************************
    * WP HOOKS SETTINGS
    ****************************************/
    /*
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_menu_hooks() {
      if ( (bool) CZR_utils::$inst->czr_fn_opt('tc_hide_all_menus') )
        return;
      //VARIOUS USER OPTIONS
      add_filter( 'body_class'                    , array( $this , 'czr_fn_add_body_classes') );
      //Set header css classes based on user options
      add_filter( 'tc_header_classes'             , array( $this , 'czr_fn_set_header_classes') );
      add_filter( 'tc_social_header_block_class'  , array( $this, 'czr_fn_set_social_header_class') );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //set second menu specific style including @media rules
      add_filter( 'tc_user_options_style'         , array( $this , 'czr_fn_add_second_menu_inline_style') );

      //SIDE MENU HOOKS SINCE v3.3+
      if ( $this -> czr_fn_is_sidenav_enabled() ){
        add_action( 'wp_head'                     , array( $this , 'czr_fn_set_sidenav_hooks') );
        add_filter( 'tc_user_options_style'       , array( $this , 'czr_fn_set_sidenav_style') );
      } else {
        // add main menu notice
        add_action( '__navbar'                    , array( $this, 'czr_fn_maybe_display_main_menu_notice'), 50 );
      }
      //this adds css classes to the navbar-wrapper :
      //1) to the main menu if regular (sidenav not enabled)
      //2) to the secondary menu if enabled
      if ( ! $this -> czr_fn_is_sidenav_enabled() || CZR_utils::$inst->czr_fn_is_secondary_menu_enabled() ) {
        add_filter( 'tc_navbar_wrapper_class'     , array( $this, 'czr_fn_set_menu_style_options'), 0 );
      }

      //body > header > navbar action ordered by priority
      add_action ( '__navbar'                     , array( $this , 'czr_fn_menu_display' ), 30 );
      //adds class
      add_filter ( 'wp_page_menu'                 , array( $this , 'czr_fn_add_menuclass' ));
    }



    /***************************************
    * WP_HEAD HOOKS SETTINGS
    ****************************************/
    /**
    * Set Various hooks for the sidemenu
    * hook : wp_head
    * @return void
    */
    function czr_fn_set_sidenav_hooks() {
      add_filter( 'body_class'              , array( $this, 'czr_fn_sidenav_body_class') );

      // disable dropdown on click
      add_filter( 'tc_menu_open_on_click'   , array( $this, 'czr_fn_disable_dropdown_on_click'), 10, 3 );

      // add side menu before the page wrapper
      add_action( '__before_page_wrapper'   , array( $this, 'czr_fn_sidenav_display'), 0 );
      // add side menu help block
      add_action( '__sidenav'               , array( $this, 'czr_fn_maybe_display_sidenav_help') );
      // add menu button to the sidebar
      add_action( '__sidenav'               , array( $this, 'czr_fn_sidenav_toggle_button_display'), 5 );
      // add menu
      add_action( '__sidenav'               , array( $this, 'czr_fn_sidenav_display_menu_customizer'), 10 );
    }


    /**
    * Displays a dismissable block of information in the sidenav wrapper when conditions are met
    * hook : __sidenav
    */
    function czr_fn_maybe_display_sidenav_help() {
      if (  ! CZR_placeholders::czr_fn_is_sidenav_help_on() )
        return;
      ?>
      <div class="tc-placeholder-wrap tc-sidenav-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p><p>%3$s</p>',
              __( "This is a default page menu.", "customizr" ),
              __( "( If you don't have any pages in your website, then this side menu is empty for the moment. )" , "customizr"),
              sprintf( __("If you have already created menu(s), you can %s. If you need to create a new menu, jump to the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( "section" => "nav") ), __( "change the default menu", "customizr"), __("replace this default menu by another one", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a>', admin_url('nav-menus.php'), __( "menu creation screen", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }


    /***************************************
    * VIEWS
    ****************************************/
    /**
    * Menu Rendering : renders the navbar menus, or just the sidenav toggle button
    * hook : '__navbar'
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_menu_display() {
      ob_start();

        //renders the regular menu + responsive button
        if ( ! $this -> czr_fn_is_sidenav_enabled() ) {
          $this -> czr_fn_regular_menu_display( 'main' );
        } else {
          $this -> czr_fn_sidenav_toggle_button_display();
          if ( $this -> czr_fn_is_second_menu_enabled() )
            $this -> czr_fn_regular_menu_display( 'secondary' );
          else
            $this -> czr_fn_maybe_display_second_menu_placeholder();
        }

      $html = ob_get_contents();
      ob_end_clean();

      echo apply_filters( 'tc_menu_display', $html );
    }


    /**
    * Menu button View
    *
    * @return html string
    * @package Customizr
    * @since v3.3+
    *
    */
    function czr_fn_menu_button_view( $args ) {
      //extracts : 'type', 'button_class', 'button_attr'
      extract( $args );

      $_button_label = sprintf( '<span class="menu-label">%s</span>',
        '__sidenav' == current_filter() ? __('Close', 'customizr') : __('Menu' , 'customizr')
      );
      $_button = sprintf( '<div class="%1$s"><button type="button" class="btn menu-btn" %2$s title="%5$s">%3$s%3$s%3$s </button>%4$s</div>',
        implode(' ', apply_filters( "tc_{$type}_button_class", $button_class ) ),
        apply_filters( "tc_{$type}_menu_button_attr", $button_attr),
        '<span class="icon-bar"></span>',
        (bool)esc_attr( CZR_utils::$inst->czr_fn_opt('tc_display_menu_label') ) ? $_button_label : '',
        '__sidenav' == current_filter() ? __('Close', 'customizr') : __('Open the menu' , 'customizr')
      );
      return apply_filters( "tc_{$type}_menu_button_view", $_button );
    }



    /**
    * Menu fallback. Link to the menu editor.
    * Thanks to tosho (http://wordpress.stackexchange.com/users/73/toscho)
    * http://wordpress.stackexchange.com/questions/64515/fall-back-for-main-menu
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_link_to_menu_editor( $args ) {
      if ( ! current_user_can( 'manage_options' ) )
          return;

      // see wp-includes/nav-menu-template.php for available arguments
      extract( $args );

      $link = sprintf('%1$s<a href="%2$s">%3$s%4$s%5$s</a>%6$s',
        $link_before,
        admin_url( 'nav-menus.php' ),
        $before,
        __('Add a menu','customizr'),
        $after,
        $link_after
      );

      // We have a list
      $link = ( FALSE !== stripos( $items_wrap, '<ul' ) || FALSE !== stripos( $items_wrap, '<ol' ) ) ? '<li>' . $link . '</li>' : $link;

      $output = sprintf( $items_wrap, $menu_id, $menu_class, $link );
      $output = ( ! empty ( $container ) ) ? sprintf('<%1$s class="%2$s" id="%3$s">%4$s</%1$s>',
                                                $container,
                                                $container_class,
                                                $container_id,
                                                $output
                                            ) : $output;

      if ( $echo ) { echo $output; }
      return $output;
    }



    /***************************************
    * REGULAR VIEWS
    ****************************************/
    /**
    *  Prepare params and echo menu views
    *
    * @return html string
    * @since v3.3+
    *
    */
    function czr_fn_regular_menu_display( $_location = 'main' ){
      $type               = 'regular';
      $where              = 'right' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $button_class       = array( 'btn-toggle-nav', $where );
      $button_attr        = 'data-toggle="collapse" data-target=".nav-collapse"';

      $menu_class         = ( ! wp_is_mobile() && 'hover' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_type' ) ) ) ? array( 'nav tc-hover-menu' ) : array( 'nav' ) ;
      $menu_wrapper_class = ( ! wp_is_mobile() && 'hover' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_type' ) ) ) ? array( 'nav-collapse collapse', 'tc-hover-menu-wrapper' ) : array( 'nav-collapse', 'collapse' );

      $menu_view = $this -> czr_fn_wp_nav_menu_view( compact( '_location', 'type', 'menu_class', 'menu_wrapper_class' ) );

      if ( $menu_view && 'main' == $_location )
        $menu_view = $menu_view . $this -> czr_fn_menu_button_view( compact( 'type', 'button_class', 'button_attr') );

      echo $menu_view;
    }



    /***************************************
    * SIDENAV VIEWS
    ****************************************/
    /**
    * @return html string
    * @since v3.3+
    *
    * hook: __before_page_wrapper
    */
    function czr_fn_sidenav_display() {
      ob_start();
        $tc_side_nav_class        = implode(' ', apply_filters( 'tc_side_nav_class', array( 'tc-sn', 'navbar' ) ) );
        $tc_side_nav_inner_class  = implode(' ', apply_filters( 'tc_side_nav_inner_class', array( 'tc-sn-inner', 'nav-collapse') ) );
        ?>
          <nav id="tc-sn" class="<?php echo $tc_side_nav_class; ?>" role="navigation">
            <div class="<?php echo $tc_side_nav_inner_class; ?>">
              <?php do_action( '__sidenav' ); ?>
            </div><!--.tc-sn-inner -->
          </nav><!-- //#tc-sn -->
        <?php
      $_sidenav = ob_get_contents();
      ob_end_clean();
      echo apply_filters( 'tc_sidenav_display', $_sidenav );
    }


    /**
    * @return html string
    * @since v3.3+
    *
    * hook: __sidenav
    */
    function czr_fn_sidenav_display_menu_customizer(){
       //menu setup
       $type               = 'sidenav';
       $menu_class         = array('nav', 'sn-nav' );
       $menu_wrapper_class = array('sn-nav-wrapper');
       //sidenav menu is always "main"
       $_location          = 'main';

       echo $this -> czr_fn_wp_nav_menu_view( compact( '_location', 'type', 'menu_class', 'menu_wrapper_class') );
    }

    /**
    * @return html string
    * @since v3.3+
    *
    * hooks: __sidenav, __navbar
    */
    function czr_fn_sidenav_toggle_button_display() {
      $type          = 'sidenav';
      $where         = 'right' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout') ) ? 'pull-right' : 'pull-left';
      $button_class  = array( 'btn-toggle-nav', 'sn-toggle', $where );
      $button_attr   = '';

      echo $this -> czr_fn_menu_button_view( compact( 'type', 'button_class', 'button_attr') );
    }


    /***************************************
    * COMMON VIEW
    ****************************************/
    /**
    * WP Nav Menu View
    *
    * @return html string
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_wp_nav_menu_view( $args ) {
      extract( $args );
      //'_location', 'type', 'menu_class', 'menu_wrapper_class'

      $menu_args = apply_filters( "tc_{$type}_menu_args",
          array(
            'theme_location'  => $_location,
            'menu_class'      => implode(' ', apply_filters( "tc_{$type}_menu_class", $menu_class ) ),
            'fallback_cb'     => array( $this, 'czr_fn_page_menu' ),
            //if no menu is set to the required location, fallsback to tc_page_menu
            //=> tc_page_menu has it's own class extension of Walker, therefore no need to specify one below
            'walker'          => ! CZR_utils::$inst -> czr_fn_has_location_menu($_location) ? '' : new CZR_nav_walker($_location),
            'echo'            => false,
        )
      );

      $menu = wp_nav_menu( $menu_args );

      if ( $menu )
        $menu = sprintf('<div class="%1$s">%2$s</div>',
            implode(' ', apply_filters( "tc_{$type}_menu_wrapper_class", $menu_wrapper_class ) ),
            $menu
        );

      return apply_filters("tc_{$type}_menu_view", $menu );
    }


    /***************************************
    * PLACEHOLDERS VIEW
    ****************************************/
    /**
    * Displays the placeholder view if conditions are met in CZR_placeholders::czr_fn_is_main_menu_notice_on()
    * fired in czr_fn_menu_display(), hook : __navbar
    * @since Customizr 3.4+
    */
    function czr_fn_maybe_display_main_menu_notice() {
      if (  ! CZR_placeholders::czr_fn_is_main_menu_notice_on() )
          return;
      ?>
      <div class="tc-placeholder-wrap tc-main-menu-notice">
        <?php
          printf('<p><strong>%1$s<br/>%2$s</strong></p>',
              __( "You can now display your menu as a vertical and mobile friendly side menu, animated when revealed.", "customizr" ),
              sprintf( __("%s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('demo.presscustomizr.com?design=nav'), __( "Try it with the demo", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( "section" => "nav") ), __( "open the customizer menu section", "customizr"), __("change your menu design now", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }


    /**
    * Displays the placeholder view if conditions are met in CZR_placeholders::czr_fn_is_second_menu_placeholder_on()
    * fired in czr_fn_menu_display(), hook : __navbar
    * @since Customizr 3.4
    */
    function czr_fn_maybe_display_second_menu_placeholder() {
      if (  ! CZR_placeholders::czr_fn_is_second_menu_placeholder_on() )
          return;
      ?>
      <div class="nav-collapse collapse tc-placeholder-wrap tc-menu-placeholder">
        <?php
          printf('<p><strong>%1$s<br/>%2$s</strong></p>',
              __( "You can display your main menu or a second menu here horizontally.", "customizr" ),
              sprintf( __("%s or read the %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( "section" => "nav") ), __( "Manage menus in the header", "customizr"), __("Manage your menus in the header now", "customizr") ),
                sprintf( '<a href="%1$s" title="%2$s" target="blank">%2$s</a><span class="tc-external"></span>', esc_url('http://docs.presscustomizr.com/article/101-customizr-theme-options-header-settings/#navigation'), __( "documentation", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }



    /***************************************
    * GETTERS / SETTERS
    ****************************************/
    /*
    * Set navbar menu css classes : effects, position...
    * hook : tc_navbar_wrapper_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_menu_style_options( $_classes ) {
      $_classes = ( ! wp_is_mobile() && 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_submenu_fade_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-fade' ) ) : $_classes;
      $_classes = ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_submenu_item_move_effect') ) ) ? array_merge( $_classes, array( 'tc-submenu-move' ) ) : $_classes;
      $_classes = ( ! wp_is_mobile() && 'hover' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_type' ) ) ) ? array_merge( $_classes, array( 'tc-open-on-hover' ) ) : array_merge( $_classes, array( 'tc-open-on-click' ) );

      //Navbar menus positions (not sidenav)
      //CASE 1 : regular menu (sidenav not enabled), controled by option 'tc_menu_position'
      //CASE 2 : second menu ( is_secondary_menu_enabled ?), controled by option 'tc_second_menu_position'
      if ( ! $this -> czr_fn_is_sidenav_enabled() )
        array_push( $_classes , esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_position') ) );
      if ( CZR_utils::$inst->czr_fn_is_secondary_menu_enabled() )
        array_push( $_classes , esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_second_menu_position') ) );

      return $_classes;
    }


    /*
    * hook : body_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_add_body_classes($_classes) {
      //menu type class
      $_menu_type = $this -> czr_fn_is_sidenav_enabled() ? 'tc-side-menu' : 'tc-regular-menu';
      array_push( $_classes, $_menu_type );

      return $_classes;
    }



    /**
    * Set the header classes
    * Callback for tc_header_classes filter
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_set_header_classes( $_classes ) {
      //backward compatibility (was not handled has an array in previous versions)
      if ( ! is_array($_classes) )
        return $_classes;

      //adds the second menu state
      if ( CZR_Utils::$inst -> czr_fn_is_secondary_menu_enabled() )
        array_push( $_classes, 'tc-second-menu-on' );
      //adds the resp. behaviour option for secondary menu
      array_push( $_classes, 'tc-second-menu-' . esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_second_menu_resp_setting' ) . '-when-mobile' ) );

      return $_classes;
    }



    /*
    * hook :  tc_social_header_block_class hook
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_social_header_class($_classes) {
      return 'span5';
    }



    /**
    * Adds a specific class to the ul wrapper
    * hook : 'wp_page_menu'
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_add_menuclass( $ulclass) {
      $html =  preg_replace( '/<ul>/' , '<ul class="nav">' , $ulclass, 1);
      return apply_filters( 'tc_add_menuclass', $html );
    }



    /*
    * Second menu
    * This actually "restore" regular menu style (user options in particular) by overriding the max-width: 979px media query
    */
    function czr_fn_add_second_menu_inline_style( $_css ) {
      if ( ! CZR_Utils::$inst -> czr_fn_is_secondary_menu_enabled() )
        return $_css;

      return sprintf("%s\n%s",
        $_css,
        "@media (max-width: 979px) {
          .tc-second-menu-on .nav-collapse {
            width: inherit;
            overflow: visible;
            height: inherit;
            position:relative;
            top: inherit;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
            box-shadow: none;
            background: inherit;
          }
          .tc-sticky-header.sticky-enabled #tc-page-wrap .nav-collapse, #tc-page-wrap .tc-second-menu-hide-when-mobile .nav-collapse.collapse .nav {
            display:none;
          }
          .tc-second-menu-on .tc-hover-menu.nav ul.dropdown-menu {
            display:none;
          }
          .tc-second-menu-on .navbar .nav-collapse ul.nav>li li a {
            padding: 3px 20px;
          }
          .tc-second-menu-on .nav-collapse.collapse .nav {
            display: block;
            float: left;
            margin: inherit;
          }
          .tc-second-menu-on .nav-collapse .nav>li {
            float:left;
          }
          .tc-second-menu-on .nav-collapse .dropdown-menu {
            position:absolute;
            display: none;
            -webkit-box-shadow: 0 2px 8px rgba(0,0,0,.2);
            -moz-box-shadow: 0 2px 8px rgba(0,0,0,.2);
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
            background-color: #fff;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            padding: 5px 0;
          }
          .tc-second-menu-on .navbar .nav>li>.dropdown-menu:after, .navbar .nav>li>.dropdown-menu:before{
            content: '';
            display: inline-block;
            position: absolute;
          }
          .tc-second-menu-on .tc-hover-menu.nav .caret {
            display:inline-block;
          }
          .tc-second-menu-on .tc-hover-menu.nav li:hover>ul {
            display: block;
          }
          .tc-second-menu-on .nav a, .tc-second-menu-on .tc-hover-menu.nav a {
            border-bottom: none;
          }
          .tc-second-menu-on .dropdown-menu>li>a {
            padding: 3px 20px;
          }
          .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:focus,.tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a:hover,.tc-second-menu-on .tc-submenu-move .dropdown-submenu:focus>a, .tc-second-menu-on .tc-submenu-move .dropdown-submenu:hover>a {
            padding-left: 1.63em
          }
          .tc-second-menu-on .tc-submenu-fade .nav>li>ul {
            opacity: 0;
            top: 75%;
            visibility: hidden;
            display: block;
            -webkit-transition: all .2s ease-in-out;
            -moz-transition: all .2s ease-in-out;
            -o-transition: all .2s ease-in-out;
            -ms-transition: all .2s ease-in-out;
            transition: all .2s ease-in-out;
          }
          .tc-second-menu-on .tc-submenu-fade .nav li.open>ul, .tc-second-menu-on .tc-submenu-fade .tc-hover-menu.nav li:hover>ul {
            opacity: 1;
            top: 95%;
            visibility: visible;
          }
          .tc-second-menu-on .tc-submenu-move .dropdown-menu>li>a {
            -webkit-transition: all ease .241s;
            -moz-transition: all ease .241s;
            -o-transition: all ease .241s;
            transition: all ease .241s;
          }
          .tc-second-menu-on .dropdown-submenu>.dropdown-menu {
            top: 110%;
            left: 30%;
            left: 30%\9;
            top: 0\9;
            margin-top: -6px;
            margin-left: -1px;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
          }
          .tc-second-menu-on .dropdown-submenu>a:after {
            content: ' ';
          }
        }\n

        .sticky-enabled .tc-second-menu-on .nav-collapse.collapse {
          clear:none;
        }\n"
      );
    }



    /**
    * Adds a specific style to the first letter of the menu item
    * hook : tc_user_options_style
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function czr_fn_set_sidenav_style( $_css ) {
      $sidenav_width = apply_filters( 'tc_sidenav_width', 330 );

      $_sidenav_mobile_css = '
          #tc-sn { width: %1$spx;}
          nav#tc-sn { z-index: 999; }
          [class*=sn-left].sn-close #tc-sn, [class*=sn-left] #tc-sn{
            -webkit-transform: translate3d( -100%%, 0, 0 );
            -moz-transform: translate3d( -100%%, 0, 0 );
            transform: translate3d(-100%%, 0, 0 );
          }
          [class*=sn-right].sn-close #tc-sn,[class*=sn-right] #tc-sn {
            -webkit-transform: translate3d( 100%%, 0, 0 );
            -moz-transform: translate3d( 100%%, 0, 0 );
            transform: translate3d( 100%%, 0, 0 );
          }
         .animating #tc-page-wrap, .sn-open #tc-sn, .tc-sn-visible:not(.sn-close) #tc-sn{
            -webkit-transform: translate3d( 0, 0, 0 );
            -moz-transform: translate3d( 0, 0, 0 );
            transform: translate3d(0,0,0) !important;
          }
      ';
      $_sidenav_desktop_css = '
          #tc-sn { width: %1$spx;}
          .tc-sn-visible[class*=sn-left] #tc-page-wrap { left: %1$spx; }
          .tc-sn-visible[class*=sn-right] #tc-page-wrap { right: %1$spx; }
          [class*=sn-right].sn-close #tc-page-wrap, [class*=sn-left].sn-open #tc-page-wrap {
            -webkit-transform: translate3d( %1$spx, 0, 0 );
            -moz-transform: translate3d( %1$spx, 0, 0 );
            transform: translate3d( %1$spx, 0, 0 );
          }
          [class*=sn-right].sn-open #tc-page-wrap, [class*=sn-left].sn-close #tc-page-wrap {
            -webkit-transform: translate3d( -%1$spx, 0, 0 );
            -moz-transform: translate3d( -%1$spx, 0, 0 );
             transform: translate3d( -%1$spx, 0, 0 );
          }
          /* stick the sticky header to the left/right of the page wrapper */
          .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-left] .tc-header { left: %1$spx; }
          .tc-sticky-header.tc-sn-visible:not(.animating)[class*=sn-right] .tc-header { right: %1$spx; }
          /* ie<9 breaks using :not */
          .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-left] .tc-header { left: %1$spx; }
          .no-csstransforms3d .tc-sticky-header.tc-sn-visible[class*=sn-right] .tc-header { right: %1$spx; }
      ';

      return sprintf("%s\n%s",
        $_css,
        sprintf(
            apply_filters('tc_sidenav_inline_css',
              apply_filters( 'tc_sidenav_slide_mobile', wp_is_mobile() ) ? $_sidenav_mobile_css : $_sidenav_desktop_css
            ),
            $sidenav_width
        )
      );
    }

    /**
    * hook : body_class filter
    *
    * @since Customizr 3.3+
    */
    function czr_fn_sidenav_body_class( $_classes ){
      $_where = 'right' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_header_layout') ) ? 'right' : 'left';
      array_push( $_classes, apply_filters( 'tc_sidenav_body_class', "sn-$_where" ) );

      return $_classes;
    }


    /**
     * This hooks is fired in the Walker_Page extensions, by the start_el() methods.
     * It only concerns the main menu, when the sidenav is enabled.
     * @since Customizr 3.4+
     *
     * hook :tc_menu_open_on_click
     */
    function czr_fn_disable_dropdown_on_click( $replace, $search, $_location = null ) {
      return 'main' == $_location ? $search : $replace ;
    }





    /***************************************
    * HELPERS
    ****************************************/
    /**
    * @return bool
    */
    function czr_fn_is_sidenav_enabled() {
      return apply_filters( 'tc_is_sidenav_enabled', 'aside' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_style' ) ) );
    }


    /**
    * @return bool
    */
    function czr_fn_is_second_menu_enabled() {
      return apply_filters( 'tc_is_second_menu_enabled', (bool)esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_display_second_menu' ) ) );
    }


    /**
     * Display or retrieve list of pages with optional home link.
     * Modified copy of wp_page_menu()
     * @return string html menu
     */
    function czr_fn_page_menu( $args = array() ) {
      $defaults = array('sort_column' => 'menu_order, post_title', 'menu_class' => 'menu', 'echo' => true, 'link_before' => '', 'link_after' => '');
      $args = wp_parse_args( $args, $defaults );

      $args = apply_filters( 'wp_page_menu_args', $args );

      $menu = '';

      $list_args = $args;

      // Show Home in the menu
      if ( ! empty($args['show_home']) ) {
        if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
          $text = __('Home' , 'customizr');
        else
          $text = $args['show_home'];
        $class = '';
        if ( is_front_page() && !is_paged() )
          $class = 'class="current_page_item"';
        $menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
        // If the front page is a page, add it to the exclude list
        if (get_option('show_on_front') == 'page') {
          if ( !empty( $list_args['exclude'] ) ) {
            $list_args['exclude'] .= ',';
          } else {
            $list_args['exclude'] = '';
          }
          $list_args['exclude'] .= get_option('page_on_front');
        }
      }

      $list_args['echo'] = false;
      $list_args['title_li'] = '';
      $menu .= str_replace( array( "\r", "\n", "\t" ), '', $this -> czr_fn_list_pages($list_args) );

      // if ( $menu )
      //   $menu = '<ul>' . $menu . '</ul>';

      //$menu = '<div class="' . esc_attr($args['menu_class']) . '">' . $menu . "</div>\n";

      if ( $menu )
        $menu = '<ul class="' . esc_attr($args['menu_class']) . '">' . $menu . '</ul>';

      //$menu = apply_filters( 'wp_page_menu', $menu, $args );
      if ( $args['echo'] )
        echo $menu;
      else
        return $menu;
    }


    /**
     * Retrieve or display list of pages in list (li) format.
     * Modified copy of wp_list_pages
     * @return string HTML list of pages.
     */
    function czr_fn_list_pages( $args = '' ) {
      $defaults = array(
        'depth' => 0, 'show_date' => '',
        'date_format' => get_option( 'date_format' ),
        'child_of' => 0, 'exclude' => '',
        'title_li' => __( 'Pages', 'customizr' ), 'echo' => 1,
        'authors' => '', 'sort_column' => 'menu_order, post_title',
        'link_before' => '', 'link_after' => '', 'walker' => '',
      );

      $r = wp_parse_args( $args, $defaults );

      $output = '';
      $current_page = 0;

      // sanitize, mostly to keep spaces out
      $r['exclude'] = preg_replace( '/[^0-9,]/', '', $r['exclude'] );

      // Allow plugins to filter an array of excluded pages (but don't put a nullstring into the array)
      $exclude_array = ( $r['exclude'] ) ? explode( ',', $r['exclude'] ) : array();

      $r['exclude'] = implode( ',', apply_filters( 'wp_list_pages_excludes', $exclude_array ) );

      // Query pages.
      $r['hierarchical'] = 0;
      $pages = get_pages( $r );

      if ( ! empty( $pages ) ) {
        if ( $r['title_li'] ) {
          $output .= '<li class="pagenav">' . $r['title_li'] . '<ul>';
        }
        global $wp_query;
        if ( is_page() || is_attachment() || $wp_query->is_posts_page ) {
          $current_page = get_queried_object_id();
        } elseif ( is_singular() ) {
          $queried_object = get_queried_object();
          if ( is_post_type_hierarchical( $queried_object->post_type ) ) {
            $current_page = $queried_object->ID;
          }
        }

        $output .= $this -> czr_fn_walk_page_tree( $pages, $r['depth'], $current_page, $r );

        if ( $r['title_li'] ) {
          $output .= '</ul></li>';
        }
      }

      $html = apply_filters( 'wp_list_pages', $output, $r );

      if ( $r['echo'] ) {
        echo $html;
      } else {
        return $html;
      }
    }


    /**
     * Retrieve HTML list content for page list.
     *
     * @uses Walker_Page to create HTML list content.
     * @since 2.1.0
     * @see Walker_Page::walk() for parameters and return description.
     */
    function czr_fn_walk_page_tree($pages, $depth, $current_page, $r) {
      // if ( empty($r['walker']) )
      //   $walker = new Walker_Page;
      // else
      //   $walker = $r['walker'];
      $walker = new CZR_nav_walker_page;

      foreach ( (array) $pages as $page ) {
        if ( $page->post_parent )
          $r['pages_with_children'][ $page->post_parent ] = true;
      }

      $args = array($pages, $depth, $r, $current_page);
      return call_user_func_array(array($walker, 'walk'), $args);
    }

  }//end of class
endif;

?><?php
/**
* Cleaner walker for wp_nav_menu()
* Used for the user created main menus, not for : default menu and widget menus
* Walker_Nav_Menu is located in /wp-includes/nav-menu-template.php
* Walker is located in wp-includes/class-wp-walker.php
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_nav_walker' ) ) :
  class CZR_nav_walker extends Walker_Nav_Menu {
    static $instance;
    public $tc_location;
    function __construct($_location) {
      self::$instance =& $this;
      $this -> tc_location = $_location;
      add_filter( 'tc_nav_menu_css_class' , array($this, 'czr_fn_add_bootstrap_classes'), 10, 4 );
    }


    /**
    * hook : nav_menu_css_class
    */
    function czr_fn_add_bootstrap_classes($classes, $item, $args, $depth ) {
      //cast $classes into array
      $classes = (array)$classes;
      //check if $item is a dropdown ( a parent )
      //this is_dropdown property has been added in the the display_element() override method
      if ( $item -> is_dropdown ) {
        if ( $depth === 0 && ! in_array( 'dropdown', $classes ) ) {
          $classes[] = 'dropdown';
        } elseif ( $depth > 0 && ! in_array( 'dropdown-submenu', $classes ) ) {
          $classes[] = 'dropdown-submenu';
        }
      }
      return $classes;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu\">\n";
    }


    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
      $item_html = '';
      //ask the parent to do the hard work
      parent::start_el( $item_html, $item, $depth, $args, $id);

      //this is_dropdown property has been added in the the display_element() override method
      if ( $item->is_dropdown ) {
        //makes top menu not clickable (default bootstrap behaviour)
        $search         = '<a';
        $replace        = ( ! wp_is_mobile() && 'hover' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_type' ) ) ) ? '<a' : '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
        $replace       .= strpos($item_html, 'href=') ? '' : ' href="#"' ;
        $replace        = apply_filters( 'tc_menu_open_on_click', $replace , $search, $this -> tc_location );
        $item_html      = str_replace( $search , $replace , $item_html);

        //adds arrows down
        if ( $depth === 0 )
            $item_html      = str_replace( '</a>' , ' <strong class="caret"></strong></a>' , $item_html);
      }
      elseif (stristr( $item_html, 'li class="divider' )) {
        $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
      }
      elseif (stristr( $item_html, 'li class="nav-header' )) {
        $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
      }

      $output .= $item_html;
    }


    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
      //we add a property here
      //will be used in override start_el() and class filter
      $element->is_dropdown = ! empty( $children_elements[$element->ID]);

      $element->classes = apply_filters( 'tc_nav_menu_css_class', array_filter( empty( $element->classes) ? array() : (array)$element->classes ), $element, $args, $depth );

      //let the parent do the rest of the job !
      parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output);
    }
  }//end of class
endif;


/**
* Replace the walker for czr_fn_page_menu()
* Used for the specific default page menu only
*
* Walker_Page is located in wp-includes/post-template.php
* Walker is located in wp-includes/class-wp-walker.php
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_nav_walker_page' ) ) :
  class CZR_nav_walker_page extends Walker_Page {
    function __construct() {
      add_filter('page_css_class' , array($this, 'czr_fn_add_bootstrap_classes'), 10, 5 );
    }


    /**
    * hook : page_css_class
    */
    function czr_fn_add_bootstrap_classes($css_class, $page = null, $depth = 0, $args = array(), $current_page = 0) {
      if ( ! is_array($css_class) )
        return $css_class;

      if ( ! empty( $args['has_children'] ) ) {
        if ( 0 === $depth ) {
          if ( ! in_array( 'dropdown', $css_class ) )
            $css_class[] = 'dropdown';
        } elseif ( $depth > 0 ) {
          if ( ! in_array( 'dropdown-submenu', $css_class ) )
            $css_class[] = 'dropdown-submenu';
        }
        /*
        * unify menu items with children whether displaying a standard menu or a page menu
        * (useful for javascript menu related code)
        */
        if ( ! in_array( 'menu-item-has-children' , $css_class ) )
          $css_class[] = 'menu-item-has-children';
      }

      if ( ! in_array( 'menu-item' , $css_class ) )
        $css_class[] = 'menu-item';

      return $css_class;
    }


    function start_lvl(&$output, $depth = 0, $args = array()) {
      $output .= "\n<ul class=\"dropdown-menu\">\n";
    }


    function start_el(&$output, $page, $depth = 0, $args = array(), $current_page = 0) {
      $item_html = '';
      //since the &$output is passed by reference, it will modify the value on the fly based on the parent method treatment
      //we just have to make some additional treatments afterwards
      parent::start_el( $item_html, $page, $depth, $args, $current_page );

      if ( ! empty( $args['has_children'] ) ) {
        //makes top menu not clickable (default bootstrap behaviour)
        $search         = '<a';
        $replace        = ( ! wp_is_mobile() && 'hover' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_menu_type' ) ) ) ? '<a' : '<a class="dropdown-toggle" data-toggle="dropdown" data-target="#"';
        $replace       .= strpos($item_html, 'href=') ? '' : ' href="#"' ;
        $replace        = apply_filters( 'tc_menu_open_on_click', $replace , $search, isset($args['theme_location']) ? $args['theme_location'] : null);
        $item_html      = str_replace( $search , $replace , $item_html);

        //adds arrows down
        if ( $depth === 0 )
            $item_html      = str_replace( '</a>' , ' <strong class="caret"></strong></a>' , $item_html);      
      }

      elseif (stristr( $item_html, 'li class="divider' )) {
        $item_html = preg_replace( '/<a[^>]*>.*?<\/a>/iU' , '' , $item_html);
      }

      elseif (stristr( $item_html, 'li class="nav-header' )) {
        $item_html = preg_replace( '/<a[^>]*>(.*)<\/a>/iU' , '$1' , $item_html);
      }

      $output .= $item_html;
    }
 }//end of class
endif;

?><?php
/**
* 404 content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_404' ) ) :
  class CZR_404 {

      //Access any method or var of the class with classname::$instance -> var or method():
      static $instance;

      function __construct () {
          self::$instance =& $this;
          //404 content
          add_action  ( '__loop'                      , array( $this , 'czr_fn_404_content' ));
      }



      /**
       * The template part for displaying error 404 page content
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function czr_fn_404_content() {
          if ( !is_404() )
              return;

          $content_404    = apply_filters( 'tc_404', CZR_init::$instance -> content_404 );

          echo apply_filters( 'tc_404_content',
              sprintf('<div class="%1$s"><div class="entry-content %2$s">%3$s</div>%4$s</div>',
                  apply_filters( 'tc_404_wrapper_class', 'tc-content span12 format-quote' ),
                  apply_filters( 'tc_404_content_icon', 'format-icon' ),
                  sprintf('<blockquote><p>%1$s</p><cite>%2$s</cite></blockquote><p>%3$s</p>%4$s',
                                call_user_func( '__' , $content_404['quote'] , 'customizr' ),
                                call_user_func( '__' , $content_404['author'] , 'customizr' ),
                                call_user_func( '__' , $content_404['text'] , 'customizr' ),
                                get_search_form( $echo = false )
                  ),
                  apply_filters( 'tc_no_results_separator', '<hr class="featurette-divider '.current_filter().'">' )
              )
          );
      }
  }//end of class
endif;

?><?php
/**
* Attachments content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_attachment' ) ) :
    class CZR_attachment {
        static $instance;
        function __construct () {
            self::$instance =& $this;
            add_action  ( '__loop'			              , array( $this , 'czr_fn_attachment_content' ));
        }




        /**
         * The template part for displaying attachment content
         * Inspired from Twenty Twelve WP Theme
         * @package Customizr
         * @since Customizr 3.0
         */
        function czr_fn_attachment_content() {
            //check conditional tags
            global $post;
            if ( ! isset($post) || empty($post) || 'attachment' != $post -> post_type || !is_singular() )
                return;

            ob_start();
            do_action( '__before_content' );
            ?>
            <nav id="image-navigation" class="navigation" role="navigation">
                <span class="previous-image"><?php previous_image_link( false, __( '&larr; Previous' , 'customizr' ) ); ?></span>
                <span class="next-image"><?php next_image_link( false, __( 'Next &rarr;' , 'customizr' ) ); ?></span>
            </nav><!-- //#image-navigation -->

            <section class="entry-content">

                <div class="entry-attachment">

                    <div class="attachment">
                        <?php

                        $attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit' , 'post_type' => 'attachment' , 'post_mime_type' => 'image' , 'order' => 'ASC' , 'orderby' => 'menu_order ID' ) ) );

                        //did we activate the fancy box in customizer?
                        $tc_fancybox = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_fancybox' ) );

                        ?>

                        <?php if ( $tc_fancybox == 0 ) : //fancy box not checked! ?>

                            <?php
                            /**
                            * Grab the IDs of all the image attachments in a gallery so we can get the URL of the next adjacent image in a gallery,
                            * or the first image (if we're looking at the last image in a gallery), or, in a gallery of one, just the link to that image file
                            */

                            foreach ( $attachments as $k => $attachment )  {
                                if ( $attachment->ID == $post->ID ) {
                                    break;
                                }
                            }

                            $k++;

                            // If there is more than 1 attachment in a gallery
                            if ( count( $attachments ) > 1 ) {

                                if ( isset( $attachments[ $k ] ) ) {
                                    // get the URL of the next image attachment
                                    $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
                                }

                                else {
                                    // or get the URL of the first image attachment
                                    $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
                                }
                            }

                            else {
                                // or, if there's only 1 image, get the URL of the image
                                $next_attachment_url = wp_get_attachment_url();
                            }

                            ?>

                            <a href="<?php echo esc_url( $next_attachment_url ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
                            $attachment_size = apply_filters( 'tc_customizr_attachment_size' , array( 960, 960 ) );
                            echo wp_get_attachment_image( $post->ID, $attachment_size );
                            ?></a>

                        <?php else : // if fancybox option checked ?>

                            <?php
                            //get attachement src
                            $attachment_infos       = wp_get_attachment_image_src( $post->ID , 'large' );
                            $attachment_src         = $attachment_infos[0];
                            ?>

                            <a href="<?php echo $attachment_src; ?>" title="<?php the_title_attribute(); ?>" class="grouped_elements" rel="tc-fancybox-group<?php echo $post -> ID ?>"><?php
                            $attachment_size = apply_filters( 'tc_customizr_attachment_size' , array( 960, 960 ) );
                            echo wp_get_attachment_image( $post->ID, $attachment_size );
                            ?></a>

                            <div id="hidden-attachment-list" style="display:none">

                                <?php foreach ( $attachments as $k => $attachment ) : //get all related galery attachement for lightbox navigation ?>

                                    <?php
                                    $rel_attachment_infos       = wp_get_attachment_image_src( $attachment->ID , 'large' );
                                    $rel_attachment_src         = $rel_attachment_infos[0];
                                    ?>

                                    <a href="<?php echo $rel_attachment_src ; ?>" title="<?php printf('%1$s', !empty( $attachment->post_excerpt ) ? $attachment->post_excerpt :  $attachment->post_title ) ?>" class="grouped_elements" rel="tc-fancybox-group<?php echo $post -> ID ?>"><?php echo $rel_attachment_src ; ?></a>

                                <?php endforeach ?>

                            </div><!-- //#hidden-attachment-list -->

                        <?php endif //end if fancybox option checked ?>

                        <?php if ( ! empty( $post->post_excerpt ) ) : ?>

                            <div class="entry-caption">
                                <?php the_excerpt(); ?>
                            </div>

                        <?php endif; ?>

                    </div><!-- .attachment -->

                </div><!-- .entry-attachment -->

            </section><!-- .entry-content -->

            <?php do_action( '__after_content' ) ?>

            <?php
            $html = ob_get_contents();
            if ($html) ob_end_clean();
            echo apply_filters( 'tc_attachment_content', $html );

        }//end of function
    }//end of class
endif;

?><?php
/**
* Breadcrumb for Customizr
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @uses 		Breadcrumb Trail - A breadcrumb menu script for WordPress.
* @author    	Justin Tadlock <justin@justintadlock.com>
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class CZR_breadcrumb {

    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    private $args;

    function __construct () {
        self::$instance =& $this;
        add_action( '__before_main_container'			, array( $this , 'czr_fn_breadcrumb_display' ), 20 );
        //since v3.2.0, customizer option
        add_filter( 'tc_show_breadcrumb_in_context' 	, array( $this , 'czr_fn_set_breadcrumb_display_in_context' ) );
    }


    function _get_args() {
    	$args =  array(
		  'container'  => 'div' , // div, nav, p, etc.
		  'separator'  => '&raquo;' ,
		  'before'     => false,
		  'after'      => false,
		  'front_page' => true,
		  'show_home'  => __( 'Home' , 'customizr' ),
		  'network'    => false,
		  'echo'       => false
	  	);

	  	/* Set up the default arguments for the breadcrumb. */
		$defaults = array(
			'container'  => 'div' , // div, nav, p, etc.
			'separator'  => '/' ,
			'before'     => __( 'Browse:' , 'customizr' ),
			'after'      => false,
			'front_page' => true,
			'show_home'  => __( 'Home' , 'customizr' ),
			'network'    => false,
			'echo'       => true
		);

		/* Allow singular post views to have a taxonomy's terms prefixing the trail. */
		if ( is_singular() ) {
			$post = get_queried_object();
			$defaults["singular_breadcrumb_taxonomy"] = apply_filters( 'tc_display_taxonomies_in_breadcrumb' , true , $post->post_type );
		}

		/* Parse the arguments and extract them for easy variable naming. */
		return  apply_filters( 'tc_breadcrumb_trail_args' , wp_parse_args( $args, $defaults) , $args , $defaults );
    }//end of function



    function czr_fn_set_breadcrumb_display_in_context( $_bool ) {
    	if ( czr_fn__f('__is_home') )
	  		return 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_breadcrumb_home' ) ) ? false : true;
	  	else {
		  	if ( is_page() && 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_breadcrumb_in_pages' ) ) )
		  		return false;
		  	if ( is_single() && 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_breadcrumb_in_single_posts' ) ) )
		  		return false;
		  	if ( ! is_page() && ! is_single() && 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_breadcrumb_in_post_lists' ) ) )
		  		return false;
		}
		return $_bool;
    }


	/**
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_breadcrumb_display() {
	  	if ( ! apply_filters( 'tc_show_breadcrumb' , 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_breadcrumb') ) ) )
	      return;

	  	if ( ! apply_filters( 'tc_show_breadcrumb_in_context' , true ) )
	      return;

	  	if ( czr_fn__f('__is_home')  && 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_breadcrumb_home' ) ) )
	  		return;

	  	//set the args properties
	  	$this -> args = $this -> _get_args();

	  	/**
	  	* Filter the default breadcrumb trails output (like the wp gallery shortcode does).
	  	*
	  	* If the filtered output isn't empty, it will be used instead of generating
	  	* the default breadcrumbs html.
	  	*
	  	* @since 3.4.38
	  	*
	  	* @param string $output The breadcrumbs output. Default empty.
	  	* @param array  $args   The computed attributes of the theme's breadcrumbs
	  	*/
	  	$breadcrumbs = apply_filters( 'tc_breadcrumbs', '', $this->args );
	  	$breadcrumbs  = $breadcrumbs ? $breadcrumbs : $this -> czr_fn_breadcrumb_trail( $this -> args );

	  	echo apply_filters(
	  		'tc_breadcrumb_display' ,
	  			sprintf('<div class="tc-hot-crumble container" role="navigation"><div class="row"><div class="%1$s">%2$s</div></div></div>',
	  				apply_filters( 'tc_breadcrumb_class', 'span12' ),
	  				$breadcrumbs
	  			)
	  	);
    }



     /**
	 * Breadcrumb Trail - A breadcrumb menu script for WordPress.
	 *
	 * Breadcrumb Trail is a script for showing a breadcrumb trail for any type of page.  It tries to
	 * anticipate any type of structure and display the best possible trail that matches your site's
	 * permalink structure.  While not perfect, it attempts to fill in the gaps left by many other
	 * breadcrumb scripts.
	 *
	 *
	 * @package   BreadcrumbTrail
	 * @version   0.5.3
	 * @author    Justin Tadlock <justin@justintadlock.com>
	 * @copyright Copyright (c) 2008 - 2012, Justin Tadlock
	 * @link      http://themehybrid.com/plugins/breadcrumb-trail
	 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
	 */




	/**
	 * Shows a breadcrumb for all types of pages.  This function is formatting the final output of the
	 * breadcrumb trail.  The breadcrumb_trail_get_items() function returns the items and this function
	 * formats those items.
	 *
	 * @since 0.1.0
	 * @access public
	 * @param array $args Mixed arguments for the menu.
	 * @return string Output of the breadcrumb menu.
	 */

	function czr_fn_breadcrumb_trail( $args = array() ) {

		/* Create an empty variable for the breadcrumb. */
		$breadcrumb = '';

		/* Get the trail items. */
		$trail = apply_filters( 'tc_breadcrumb_trail' , $this -> czr_fn_breadcrumb_trail_get_items( $args ) );

		/* Connect the breadcrumb trail if there are items in the trail. */
		if ( !empty( $trail ) && is_array( $trail ) ) {

			/* Open the breadcrumb trail containers. */
			$breadcrumb = '<' . tag_escape( $args['container'] ) . ' class="breadcrumb-trail breadcrumbs" itemprop="breadcrumb">';

			/* If $before was set, wrap it in a container. */
			$breadcrumb .= ( !empty( $args['before'] ) ? '<span class="trail-before">' . $args['before'] . '</span> ' : '' );

			/* Adds the 'trail-begin' class around first item if there's more than one item. */
			if ( 1 < count( $trail ) )
				array_unshift( $trail, '<span class="trail-begin">' . array_shift( $trail ) . '</span>' );

			/* Adds the 'trail-end' class around last item. */
			array_push( $trail, '<span class="trail-end">' . array_pop( $trail ) . '</span>' );

			/* Format the separator. */
			$separator = ! empty( $args['separator'] ) ? '<span class="sep">' . $args['separator'] . '</span>' : '<span class="sep">/</span>';

			/* Join the individual trail items into a single string. */
			$breadcrumb .= join( " {$separator} ", $trail );


			/* If $after was set, wrap it in a container. */
			$breadcrumb .= ( !empty( $args['after'] ) ? ' <span class="trail-after">' . $args['after'] . '</span>' : '' );

			/* Close the breadcrumb trail containers. */
			$breadcrumb .= '</' . tag_escape( $args['container'] ) . '>';
		}

		/* Allow developers to filter the breadcrumb trail HTML. */
		//$breadcrumb = apply_filters( array( $this , 'breadcrumb_trail' ), $breadcrumb, $args );

		/* Output the breadcrumb. */
		if ( $args['echo'] )
			echo $breadcrumb;
		else
			return $breadcrumb;
	}

	/**
	 * Gets the items for the breadcrumb trail.  This is the heart of the script.  It checks the current page
	 * being viewed and decided based on the information provided by WordPress what items should be
	 * added to the breadcrumb trail.
	 *
	 * @since 0.4.0
	 * @todo Build in caching based on the queried object ID.
	 * @access public
	 * @param array $args Mixed arguments for the menu.
	 * @return array List of items to be shown in the trail.
	 */
	function czr_fn_breadcrumb_trail_get_items( $args = array() ) {
		global $wp_rewrite;

		/* Set up an empty trail array and empty path. */
		$trail        = array();
		$path         = '';
		$maybe_paged  = true;
		/* tc addon */
		$page_for_posts 				= ( 'posts' != get_option('show_on_front') ) ? get_option('page_for_posts') : false;

		/* If $show_home is set and we're not on the front page of the site, link to the home page. */
		if ( !is_front_page() && $args['show_home'] ) {

			if ( is_multisite() && true === $args['network'] ) {
				$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
				$trail[] = '<a href="' . esc_url(home_url()) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . get_bloginfo( 'name' ) . '</a>';
			} else {
				$trail[] = '<a href="' . esc_url(home_url()) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $args['show_home'] . '</a>';
			}
		}

		/* If bbPress is installed and we're on a bbPress page. */
		if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
			$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_bbpress_items() );
		}
		/* If WooCommerce is installed and we're on a WooCommerce page. */
		elseif ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$trail 			 = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_woocommerce_items() );
			$maybe_paged = false; //czr_fn_breadcrumb_trail_get_woocommerce_items already gives us the page
		}
		/* If viewing the front page of the site. */
		elseif ( is_front_page() ) {

			if ( !is_paged() && $args['show_home'] && $args['front_page'] ) {

				if ( is_multisite() && true === $args['network'] ) {
					$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
					$trail[] = get_bloginfo( 'name' );
				} else {
					$trail[] = $args['show_home'];
				}
			}

			elseif ( is_paged() && $args['show_home'] && $args['front_page'] ) {

				if ( is_multisite() && true === $args['network'] ) {
					$trail[] = '<a href="' . network_home_url() . '">' . $args['show_home'] . '</a>';
					$trail[] = '<a href="' . esc_url(home_url()) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . get_bloginfo( 'name' ) . '</a>';
				} else {
					$trail[] = '<a href="' . esc_url(home_url()) . '" title="' . esc_attr( get_bloginfo( 'name' ) ) . '" rel="home" class="trail-begin">' . $args['show_home'] . '</a>';
				}
			}
		}

		/* If viewing the "home"/posts page. */
		elseif ( is_home() ) {
			$home_page = get_page( get_queried_object_id() );

			$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $home_page->post_parent, '' ) );

			if ( is_paged() )
				$trail[]  = '<a href="' . get_permalink( $home_page->ID ) . '" title="' . esc_attr( strip_tags( get_the_title( $home_page->ID ) ) ). '">' . get_the_title( $home_page->ID ) . '</a>';
			else
				$trail[] = get_the_title( $home_page->ID );
		}

		/* If viewing a singular post (page, attachment, etc.). */
		elseif ( is_singular() ) {

			/* Get singular post variables needed. */
			$post = get_queried_object();
			$post_id = absint( get_queried_object_id() );
			$post_type = $post->post_type;
			$parent = absint( $post->post_parent );

			/* Get the post type object. */
			$post_type_object = get_post_type_object( $post_type );

			/* If viewing a singular 'post'. */
			if ( 'post' == $post_type ) {

				/* If $front has been set, add it to the $path. */
				$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a path, check for parents. */
				if ( !empty( $path ) && !$page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );
				else if ( $page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $page_for_posts , $path ) );

				/* Map the permalink structure tags to actual links. */
				/*$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_map_rewrite_tags( $post_id, get_option( 'permalink_structure' ), $args ) );*/
			}

			/* If viewing a singular 'attachment'. */
			elseif ( 'attachment' == $post_type ) {

				/* Get the parent post ID. */
				$parent_id = $post->post_parent;

				/* If the attachment has a parent (attached to a post). */
				if ( 0 < $parent_id ) {

					/* Get the parent post type. */
					$parent_post_type = get_post_type( $parent_id );

					/* If the post type is 'post'. */
					if ( 'post' == $parent_post_type ) {

						/* If $front has been set, add it to the $path. */
						$path .= trailingslashit( $wp_rewrite->front );

						/* If there's a path, check for parents. */
						if ( !empty( $path ) )
							$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );

						/* Map the post (parent) permalink structure tags to actual links. */
						$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_map_rewrite_tags( $post->post_parent, get_option( 'permalink_structure' ), $args ) );
					}

					/* Custom post types. */
					elseif ( 'page' !== $parent_post_type ) {

						$parent_post_type_object = get_post_type_object( $parent_post_type );

						/* If $front has been set, add it to the $path. */
						if ( isset($parent_post_type_object->rewrite['with_front']) && $parent_post_type_object->rewrite['with_front'] && $wp_rewrite->front )
							$path .= trailingslashit( $wp_rewrite->front );

						/* If there's a slug, add it to the $path. */
						if ( !empty( $parent_post_type_object->rewrite['slug'] ) )
							$path .= $parent_post_type_object->rewrite['slug'];

						/* If there's a path, check for parents. */
						if ( !empty( $path ) )
							$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );

						/* If there's an archive page, add it to the trail. */
						if ( !empty( $parent_post_type_object->has_archive ) ) {

							/* Add support for a non-standard label of 'archive_title' (special use case). */
							$label = !empty( $parent_post_type_object->labels->archive_title ) ? $parent_post_type_object->labels->archive_title : $parent_post_type_object->labels->name;

							$trail[] = '<a href="' . get_post_type_archive_link( $parent_post_type ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';
						}
					}
				}
			}

			/* If a custom post type, check if there are any pages in its hierarchy based on the slug. */
			elseif ( 'page' !== $post_type ) {

				/* If $front has been set, add it to the $path. */
				if ( isset( $post_type_object) && isset($post_type_object->rewrite['with_front']) && $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a slug, add it to the $path. */
				if ( !empty( $post_type_object->rewrite['slug'] ) )
					$path .= $post_type_object->rewrite['slug'];

				/* If there's a path, check for parents. */
				if ( !empty( $path ) )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );

				/* If there's an archive page, add it to the trail. */
				if ( !empty( $post_type_object->has_archive ) ) {

					/* Add support for a non-standard label of 'archive_title' (special use case). */
					$label = !empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

					$trail[] = '<a href="' . get_post_type_archive_link( $post_type ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';
				}
			}

			/* If the post type path returns nothing and there is a parent, get its parents. */
			if ( ( empty( $path ) && 0 !== $parent ) || ( 'attachment' == $post_type ) )
				$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $parent, '' ) );

			/* Or, if the post type is hierarchical and there's a parent, get its parents. */
			elseif ( 0 !== $parent && is_post_type_hierarchical( $post_type ) )
				$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $parent, '' ) );

			/* Display terms for specific post type taxonomy if requested. */
			if (  isset($args["singular_breadcrumb_taxonomy"]) && $args["singular_breadcrumb_taxonomy"] )
				//If post has parent, then don't add the taxonomy trail part
				$trail 	= ( 1 < count($this -> czr_fn_breadcrumb_trail_get_parents($post_id) ) ) ? $trail : $this -> czr_fn_add_first_term_from_hierarchical_taxinomy( $trail , $post_id );

			/* End with the post title. */
			$post_title = single_post_title( '' , false );

			if ( 1 < get_query_var( 'page' ) && !empty( $post_title ) )
				$trail[] = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( $post_title ) . '">' . $post_title . '</a>';

			elseif ( !empty( $post_title ) )
				$trail[] = $post_title;
		}

		/* If we're viewing any type of archive. */
		elseif ( is_archive() ) {

			/* If viewing a taxonomy term archive. */
			if ( is_tax() || is_category() || is_tag() ) {

				/* Get some taxonomy and term variables. */
				$term = get_queried_object();
				$taxonomy = get_taxonomy( $term->taxonomy );

				/* Get the path to the term archive. Use this to determine if a page is present with it. */
				if ( is_category() )
					$path = get_option( 'category_base' );
				elseif ( is_tag() )
					$path = get_option( 'tag_base' );
				else {
					if ( isset($taxonomy->rewrite['with_front']) && $taxonomy->rewrite['with_front'] && $wp_rewrite->front )
						$path = trailingslashit( $wp_rewrite->front );
					$path .= $taxonomy->rewrite['slug'];
				}

				/* Get parent pages by path if they exist. */
				if ( $path && ! $page_for_posts)
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );
				else if ( $page_for_posts && ( is_category() || is_tag() ) )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $page_for_posts , $path ) );

				/* Add post type archive if its 'has_archive' matches the taxonomy rewrite 'slug'. */
				if ( $taxonomy->rewrite['slug'] ) {

					/* Get public post types that match the rewrite slug. */
					$post_types = get_post_types( array( 'public' => true, 'has_archive' => $taxonomy->rewrite['slug'] ), 'objects' );

					/**
					 * If any post types are found, loop through them to find one that matches.
					 * The reason for this is because WP doesn't match the 'has_archive' string
					 * exactly when calling get_post_types(). I'm assuming it just matches 'true'.
					 */
					if ( !empty( $post_types ) ) {

						foreach ( $post_types as $post_type_object ) {

							if ( $taxonomy->rewrite['slug'] === $post_type_object->has_archive ) {

								/* Add support for a non-standard label of 'archive_title' (special use case). */
								$label = !empty( $post_type_object->labels->archive_title ) ? $post_type_object->labels->archive_title : $post_type_object->labels->name;

								/* Add the post type archive link to the trail. */
								$trail[] = '<a href="' . get_post_type_archive_link( $post_type_object->name ) . '" title="' . esc_attr( $label ) . '">' . $label . '</a>';

								/* Break out of the loop. */
								break;
							}
						}
					}
				}

				/* If the taxonomy is hierarchical, list its parent terms. */
				if ( is_taxonomy_hierarchical( $term->taxonomy ) && $term->parent )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_term_parents( $term->parent, $term->taxonomy ) );

				/* Add the term name to the trail end. */
				if ( is_paged() )
					$trail[] = '<a href="' . esc_url( get_term_link( $term, $term->taxonomy ) ) . '" title="' . esc_attr( single_term_title( '' , false ) ) . '">' . single_term_title( '' , false ) . '</a>';
				else
					$trail[] = single_term_title( '' , false );
			}

			/* If viewing a post type archive. */
			elseif ( is_post_type_archive() ) {

				/* Get the post type object. */
				$post_type_object = ! is_array(get_query_var( 'post_type' )) ? get_post_type_object( get_query_var( 'post_type' ) ) : array();

				/* If $front has been set, add it to the $path. */
				if ( isset($post_type_object->rewrite['with_front']) && $post_type_object->rewrite['with_front'] && $wp_rewrite->front )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If there's a slug, add it to the $path. */
				if ( !empty( $post_type_object->rewrite['slug'] ) )
					$path .= $post_type_object->rewrite['slug'];

				/* If there's a path, check for parents. */
				if ( !empty( $path ) )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );

				/* Add the post type [plural] name to the trail end. */
				if ( is_paged() )
					$trail[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type_object->name ) ) . '" title="' . esc_attr( post_type_archive_title( '' , false ) ) . '">' . post_type_archive_title( '' , false ) . '</a>';
				else
					$trail[] = post_type_archive_title( '' , false );
			}

			/* If viewing an author archive. */
			elseif ( is_author() ) {

				/* Get the user ID. */
				$user_id = get_query_var( 'author' );

				/* If $front has been set, add it to $path. */
				if ( !empty( $wp_rewrite->front ) )
					$path .= trailingslashit( $wp_rewrite->front );

				/* If an $author_base exists, add it to $path. */
				if ( !empty( $wp_rewrite->author_base ) )
					$path .= $wp_rewrite->author_base;

				/* If there's a path, check for parents. */
				if ( !empty( $path ) && !$page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );
				else if ( $page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $page_for_posts , $path ) );

				/* Add the author's display name to the trail end. */
				if ( is_paged() )
					$trail[] = '<a href="'. esc_url( get_author_posts_url( $user_id ) ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' , $user_id ) ) . '">' . get_the_author_meta( 'display_name' , $user_id ) . '</a>';
				else
					$trail[] = get_the_author_meta( 'display_name' , $user_id );
			}

			/* If viewing a time-based archive. */
			elseif ( is_time() ) {

				/* If there's a path, check for parents. */
				if ( !empty( $path ) && !$page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );
				else if ( $page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $page_for_posts , $path ) );

				if ( get_query_var( 'minute' ) && get_query_var( 'hour' ) )
					$trail[] = get_the_time( __( 'g:i a' , 'customizr' ) );

				elseif ( get_query_var( 'minute' ) )
					$trail[] = sprintf( __( 'Minute %1$s' , 'customizr' ), get_the_time( __( 'i' , 'customizr' ) ) );

				elseif ( get_query_var( 'hour' ) )
					$trail[] = get_the_time( __( 'g a' , 'customizr' ) );
			}

			/* If viewing a date-based archive. */
			elseif ( is_date() ) {
				/* If there's a path, check for parents. */
				if ( !empty( $path ) && !$page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $path ) );
				else if ( $page_for_posts )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $page_for_posts , $path ) );

				/* If $front has been set, check for parent pages. */
				if ( $wp_rewrite->front )
					$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( '' , $wp_rewrite->front ) );

				if ( is_day() ) {
					$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y' , 'customizr' ) ) . '">' . get_the_time( __( 'Y' , 'customizr' ) ) . '</a>';
					$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F' , 'customizr' ) ) . '">' . get_the_time( __( 'F' , 'customizr' ) ) . '</a>';

					if ( is_paged() )
						$trail[] = '<a href="' . get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) . '" title="' . get_the_time( esc_attr__( 'd' , 'customizr' ) ) . '">' . get_the_time( __( 'd' , 'customizr' ) ) . '</a>';
					else
						$trail[] = get_the_time( __( 'd' , 'customizr' ) );
				}

				elseif ( get_query_var( 'w' ) ) {
					$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y' , 'customizr' ) ) . '">' . get_the_time( __( 'Y' , 'customizr' ) ) . '</a>';

					if ( is_paged() )
						$trail[] = get_archives_link( add_query_arg( array( 'm' => get_the_time( 'Y' ), 'w' => get_the_time( 'W' ) ), esc_url(home_url()) ), sprintf( __( 'Week %1$s' , 'customizr' ), get_the_time( esc_attr__( 'W' , 'customizr' ) ) ), false );
					else
						$trail[] = sprintf( __( 'Week %1$s' , 'customizr' ), get_the_time( esc_attr__( 'W' , 'customizr' ) ) );
				}

				elseif ( is_month() ) {
					$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . get_the_time( esc_attr__( 'Y' , 'customizr' ) ) . '">' . get_the_time( __( 'Y' , 'customizr' ) ) . '</a>';

					if ( is_paged() )
						$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '" title="' . get_the_time( esc_attr__( 'F' , 'customizr' ) ) . '">' . get_the_time( __( 'F' , 'customizr' ) ) . '</a>';
					else
						$trail[] = get_the_time( __( 'F' , 'customizr' ) );
				}

				elseif ( is_year() ) {

					if ( is_paged() )
						$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' ) ) . '" title="' . esc_attr( get_the_time( __( 'Y' , 'customizr' ) ) ) . '">' . get_the_time( __( 'Y' , 'customizr' ) ) . '</a>';
					else
						$trail[] = get_the_time( __( 'Y' , 'customizr' ) );
				}
			}
		}

		/* If viewing search results. */
		elseif ( is_search() ) {

			if ( is_paged() )
				$trail[] = '<a href="' . get_search_link() . '" title="' . sprintf( esc_attr__( 'Search results for &quot;%1$s&quot;' , 'customizr' ), esc_attr( get_search_query() ) ) . '">' . sprintf( __( 'Search results for &quot;%1$s&quot;' , 'customizr' ), esc_attr( get_search_query() ) ) . '</a>';
			else
				$trail[] = sprintf( __( 'Search results for &quot;%1$s&quot;' , 'customizr' ), esc_attr( get_search_query() ) );
		}

		/* If viewing a 404 error page. */
		elseif ( is_404() ) {
			$trail[] = __( '404 Not Found' , 'customizr' );
		}

		/* Check for pagination. */
		if ( $maybe_paged ) {
			if ( is_paged() )
				$trail[] = sprintf( __( 'Page %d' , 'customizr' ), absint( get_query_var( 'paged' ) ) );
			elseif ( is_singular() && 1 < get_query_var( 'page' ) )
				$trail[] = sprintf( __( 'Page %d' , 'customizr' ), absint( get_query_var( 'page' ) ) );
		}
		/* Allow devs to step in and filter the $trail array. */
		return apply_filters( 'tc_breadcrumb_trail_items' , $trail, $args );
	}

	/**
	 * Gets the items for the breadcrumb trail if bbPress is installed.
	 *
	 * @since 0.5.0
	 * @access public
	 * @param array $args Mixed arguments for the menu.
	 * @return array List of items to be shown in the trail.
	 */
	function czr_fn_breadcrumb_trail_get_bbpress_items( $args = array() ) {

		/* Set up a new trail items array. */
		$trail = array();

		/* Get the forum post type object. */
		$post_type_object = get_post_type_object( bbp_get_forum_post_type() );

		/* If not viewing the forum root/archive page and a forum archive exists, add it. */
		if ( !empty( $post_type_object->has_archive ) && !bbp_is_forum_archive() )
			$trail[] = '<a href="' . get_post_type_archive_link( bbp_get_forum_post_type() ) . '">' . bbp_get_forum_archive_title() . '</a>';

		/* If viewing the forum root/archive. */
		if ( bbp_is_forum_archive() ) {
			$trail[] = bbp_get_forum_archive_title();
		}

		/* If viewing the topics archive. */
		elseif ( bbp_is_topic_archive() ) {
			$trail[] = bbp_get_topic_archive_title();
		}

		/* If viewing a topic tag archive. */
		elseif ( bbp_is_topic_tag() ) {
			$trail[] = bbp_get_topic_tag_name();
		}

		/* If viewing a topic tag edit page. */
		elseif ( bbp_is_topic_tag_edit() ) {
			$trail[] = '<a href="' . bbp_get_topic_tag_link() . '">' . bbp_get_topic_tag_name() . '</a>';
			$trail[] = __( 'Edit' , 'customizr' );
		}

		/* If viewing a "view" page. */
		elseif ( bbp_is_single_view() ) {
			$trail[] = bbp_get_view_title();
		}

		/* If viewing a single topic page. */
		elseif ( bbp_is_single_topic() ) {

			/* Get the queried topic. */
			$topic_id = get_queried_object_id();

			/* Get the parent items for the topic, which would be its forum (and possibly forum grandparents). */
			$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( bbp_get_topic_forum_id( $topic_id ) ) );

			/* If viewing a split, merge, or edit topic page, show the link back to the topic.  Else, display topic title. */
			if ( bbp_is_topic_split() || bbp_is_topic_merge() || bbp_is_topic_edit() )
				$trail[] = '<a href="' . bbp_get_topic_permalink( $topic_id ) . '">' . bbp_get_topic_title( $topic_id ) . '</a>';
			else
				$trail[] = bbp_get_topic_title( $topic_id );

			/* If viewing a topic split page. */
			if ( bbp_is_topic_split() )
				$trail[] = __( 'Split' , 'customizr' );

			/* If viewing a topic merge page. */
			elseif ( bbp_is_topic_merge() )
				$trail[] = __( 'Merge' , 'customizr' );

			/* If viewing a topic edit page. */
			elseif ( bbp_is_topic_edit() )
				$trail[] = __( 'Edit' , 'customizr' );
		}

		/* If viewing a single reply page. */
		elseif ( bbp_is_single_reply() ) {

			/* Get the queried reply object ID. */
			$reply_id = get_queried_object_id();

			/* Get the parent items for the reply, which should be its topic. */
			$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( bbp_get_reply_topic_id( $reply_id ) ) );

			/* If viewing a reply edit page, link back to the reply. Else, display the reply title. */
			if ( bbp_is_reply_edit() ) {
				$trail[] = '<a href="' . bbp_get_reply_url( $reply_id ) . '">' . bbp_get_reply_title( $reply_id ) . '</a>';
				$trail[] = __( 'Edit' , 'customizr' );

			} else {
				$trail[] = bbp_get_reply_title( $reply_id );
			}

		}

		/* If viewing a single forum. */
		elseif ( bbp_is_single_forum() ) {

			/* Get the queried forum ID and its parent forum ID. */
			$forum_id = get_queried_object_id();
			$forum_parent_id = bbp_get_forum_parent_id( $forum_id );

			/* If the forum has a parent forum, get its parent(s). */
			if ( 0 !== $forum_parent_id)
				$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_parents( $forum_parent_id ) );

			/* Add the forum title to the end of the trail. */
			$trail[] = bbp_get_forum_title( $forum_id );
		}

		/* If viewing a user page or user edit page. */
		elseif ( bbp_is_single_user() || bbp_is_single_user_edit() ) {

			if ( bbp_is_single_user_edit() ) {
				$trail[] = '<a href="' . bbp_get_user_profile_url() . '">' . bbp_get_displayed_user_field( 'display_name' ) . '</a>';
				$trail[] = __( 'Edit' , 'customizr' );
			} else {
				$trail[] = bbp_get_displayed_user_field( 'display_name' );
			}
		}

		/* Return the bbPress breadcrumb trail items. */
		return apply_filters( 'breadcrumb_trail_get_bbpress_items' , $trail, $args );
	}



    /**
    * Gets the items for the breadcrumb trail in WooCoomerce contexts
	*
	* @since 3.5.0
	* @access public
	* @param array $args Mixed arguments for the menu.
	* @return array List of items to be shown in the trail.
	*/
    function czr_fn_breadcrumb_trail_get_woocommerce_items( $args = array() ) {
      $trail = array();

      if ( ! method_exists( 'WC_Breadcrumb', 'generate' ) )
        return $trail;

      $breadcrumbs = new WC_Breadcrumb();
      $wc_trails = $breadcrumbs -> generate();
      $wc_trails_length = count( $wc_trails );

      if ( ! $wc_trails_length )
        return $trail;

      //Build woocommerce breadcrumb trails
      //$breadcrumbx -> genenerate() returns a structure like:
      //array( array( Name, link) , array( Name, link)... array( Name, ) )

      $_i = 1;
      foreach ( $wc_trails as $wc_trail ) {
        if ( is_array( $wc_trail ) ) {
         if ( ! empty ( $wc_trail[1] ) && $_i < $wc_trails_length )
           $trail[] = '<a href="' . $wc_trail[1] . '" title="'. $wc_trail[0] . '">'. $wc_trail[0] .'</a>';
         elseif ( isset( $wc_trail[0] ) )
           $trail[] = $wc_trail[0];
        }
        $_i++;
      }

      /* Return the WooCommerce breadcrumb trail items. */
      return apply_filters( 'breadcrumb_trail_get_woocommerce_tems' , $trail, $args );
    }



	/**
	 * Turns %tag% from permalink structures into usable links for the breadcrumb trail.  This feels kind of
	 * hackish for now because we're checking for specific %tag% examples and only doing it for the 'post'
	 * post type.  In the future, maybe it'll handle a wider variety of possibilities, especially for custom post
	 * types.
	 *
	 * @since 0.4.0
	 * @access public
	 * @param int $post_id ID of the post whose parents we want.
	 * @param string $path Path of a potential parent page.
	 * @param array $args Mixed arguments for the menu.
	 * @return array $trail Array of links to the post breadcrumb.
	 */
	function czr_fn_breadcrumb_trail_map_rewrite_tags( $post_id = '' , $path = '' , $args = array() ) {

		/* Set up an empty $trail array. */
		$trail = array();

		/* Make sure there's a $path and $post_id before continuing. */
		if ( empty( $path ) || empty( $post_id ) )
			return $trail;

		/* Get the post based on the post ID. */
		$post = get_post( $post_id );

		/* If no post is returned, an error is returned, or the post does not have a 'post' post type, return. */
		if ( empty( $post ) || is_wp_error( $post ) || 'post' !== $post->post_type )
			return $trail;

		/* Trim '/' from both sides of the $path. */
		$path = trim( $path, '/' );

		/* Split the $path into an array of strings. */
		$matches = explode( '/' , $path );

		/* If matches are found for the path. */
		if ( is_array( $matches ) ) {

			/* Loop through each of the matches, adding each to the $trail array. */
			foreach ( $matches as $match ) {

				/* Trim any '/' from the $match. */
				$tag = trim( $match, '/' );

				/* If using the %year% tag, add a link to the yearly archive. */
				if ( '%year%' == $tag )
					$trail[] = '<a href="' . get_year_link( get_the_time( 'Y' , $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'Y' , 'customizr' ), $post_id ) . '">' . get_the_time( __( 'Y' , 'customizr' ), $post_id ) . '</a>';

				/* If using the %monthnum% tag, add a link to the monthly archive. */
				elseif ( '%monthnum%' == $tag )
					$trail[] = '<a href="' . get_month_link( get_the_time( 'Y' , $post_id ), get_the_time( 'm' , $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'F Y' , 'customizr' ), $post_id ) . '">' . get_the_time( __( 'F' , 'customizr' ), $post_id ) . '</a>';

				/* If using the %day% tag, add a link to the daily archive. */
				elseif ( '%day%' == $tag )
					$trail[] = '<a href="' . get_day_link( get_the_time( 'Y' , $post_id ), get_the_time( 'm' , $post_id ), get_the_time( 'd' , $post_id ) ) . '" title="' . get_the_time( esc_attr__( 'F j, Y' , 'customizr' ), $post_id ) . '">' . get_the_time( __( 'd' , 'customizr' ), $post_id ) . '</a>';

				/* If using the %author% tag, add a link to the post author archive. */
				elseif ( '%author%' == $tag )
					$trail[] = '<a href="' . get_author_posts_url( $post->post_author ) . '" title="' . esc_attr( get_the_author_meta( 'display_name' , $post->post_author ) ) . '">' . get_the_author_meta( 'display_name' , $post->post_author ) . '</a>';

				/* If using the %category% tag, add a link to the first category archive to match permalinks. */
				/*elseif ( '%category%' == $tag && isset($args["singular_breadcrumb_taxonomy"]) && $args["singular_breadcrumb_taxonomy"] ) {

					$trail 	= $this -> czr_fn_add_first_term_from_hierarchical_taxinomy( $trail , $post_id );
				}*/
			}
		}

		/* Return the $trail array. */
		return $trail;
	}

	/**
	 * Gets parent pages of any post type or taxonomy by the ID or Path.  The goal of this function is to create
	 * a clear path back to home given what would normally be a "ghost" directory.  If any page matches the given
	 * path, it'll be added.  But, it's also just a way to check for a hierarchy with hierarchical post types.
	 *
	 * @since 0.3.0
	 * @access public
	 * @param int $post_id ID of the post whose parents we want.
	 * @param string $path Path of a potential parent page.
	 * @return array $trail Array of parent page links.
	 */
	function czr_fn_breadcrumb_trail_get_parents( $post_id = '' , $path = '' ) {
		/* Set up an empty trail array. */
		$trail = array();

		/* Trim '/' off $path in case we just got a simple '/' instead of a real path. */
		$path = trim( $path, '/' );

		/* If neither a post ID nor path set, return an empty array. */
		if ( empty( $post_id ) && empty( $path ) )
			return $trail;

		/* If the post ID is empty, use the path to get the ID. */
		if ( empty( $post_id ) ) {

			/* Get parent post by the path. */
			$parent_page = get_page_by_path( $path );

			/* If a parent post is found, set the $post_id variable to it. */
			if ( !empty( $parent_page ) )
				$post_id = $parent_page->ID;
		}

		/* If a post ID and path is set, search for a post by the given path. */
		if ( $post_id == 0 && !empty( $path ) ) {

			/* Separate post names into separate paths by '/'. */
			$path = trim( $path, '/' );
			preg_match_all( "/\/.*?\z/", $path, $matches );

			/* If matches are found for the path. */
			if ( isset( $matches ) ) {

				/* Reverse the array of matches to search for posts in the proper order. */
				$matches = array_reverse( $matches );

				/* Loop through each of the path matches. */
				foreach ( $matches as $match ) {

					/* If a match is found. */
					if ( isset( $match[0] ) ) {

						/* Get the parent post by the given path. */
						$path = str_replace( $match[0], '' , $path );
						$parent_page = get_page_by_path( trim( $path, '/' ) );

						/* If a parent post is found, set the $post_id and break out of the loop. */
						if ( !empty( $parent_page ) && $parent_page->ID > 0 ) {
							$post_id = $parent_page->ID;
							break;
						}
					}
				}
			}
		}


		/* While there's a post ID, add the post link to the $parents array. */
		while ( $post_id ) {

			/* Get the post by ID. */
			$page = get_page( $post_id );

			/* Add the formatted post link to the array of parents. */
			$parents[$post_id]  = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( strip_tags( get_the_title( $post_id ) ) ) . '">' . get_the_title( $post_id ) . '</a>';

			/* Set the parent post's parent to the post ID. */
			$post_id = $page->post_parent;
		}


		if ( ! isset( $parents ) )
			return $trail;

		/* If we have parent posts, reverse the array to put them in the proper order for the trail. */
		//get last parents arrey key = parent post id
		while( $el = current($parents) ) {
		    $parent_key =  key($parents);
		    next($parents);
		}

		$first_parent_post 	= get_post($parent_key);
		$args				= $this -> args;

		/*if (  isset($args["singular_breadcrumb_taxonomy"]) && $args["singular_breadcrumb_taxonomy"] )
			$trail 	= $this -> czr_fn_add_first_term_from_hierarchical_taxinomy( $trail , $parent_key );*/

		foreach (array_reverse($parents) as $key => $value)
			$trail[] = $value;

		/* Return the trail of parent posts. */
		return $trail;
	}



	/**
	 * Searches for term parents of hierarchical taxonomies.  This function is similar to the WordPress
	 * function get_category_parents() but handles any type of taxonomy.
	 *
	 * @since 0.3.0
	 * @access public
	 * @param int $parent_id The ID of the first parent.
	 * @param object|string $taxonomy The taxonomy of the term whose parents we want.
	 * @return array $trail Array of links to parent terms.
	 */
	function czr_fn_breadcrumb_trail_get_term_parents( $parent_id = '' , $taxonomy = '' ) {

		/* Set up some default arrays. */
		$trail = array();
		$parents = array();

		/* If no term parent ID or taxonomy is given, return an empty array. */
		if ( empty( $parent_id ) || empty( $taxonomy ) )
			return $trail;

		/* While there is a parent ID, add the parent term link to the $parents array. */
		while ( $parent_id ) {

			/* Get the parent term. */
			$parent = get_term( $parent_id, $taxonomy );

			/* Add the formatted term link to the array of parent terms. */
			$parents[] = '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( $parent->name ) . '">' . $parent->name . '</a>';

			/* Set the parent term's parent as the parent ID. */
			$parent_id = $parent->parent;
		}

		/* If we have parent terms, reverse the array to put them in the proper order for the trail. */
		if ( !empty( $parents ) )
			$trail = array_reverse( $parents );

		/* Return the trail of parent terms. */
		return $trail;
	}


	function czr_fn_add_first_term_from_hierarchical_taxinomy( $trail , $post_id ) {
		// get post by post id
	  	$post = get_post( $post_id );

	  	// get post type by post
	  	$post_type = $post->post_type;

	  	// get post type taxonomies
	  	$taxonomies = get_object_taxonomies( $post_type, 'objects' );

	  	$first_hierarchical_tax = array();
	  	foreach ($taxonomies as $key => $data) {
	  		if ( true != $data -> hierarchical && ! empty($first_hierarchical_tax) )
	  			continue;
	  		else
	  			$first_hierarchical_tax = (true == $data -> hierarchical) ? $data : $first_hierarchical_tax;
	  	}

	  	//does nothing if no hierarchical tax was found
	  	if ( empty($first_hierarchical_tax) )
	  		return $trail;

		//get the tax terms
		$terms 			= isset($first_hierarchical_tax -> name) ? get_the_terms( $post_id ,$first_hierarchical_tax -> name ) : false;

		//does nothing if no terms was found
		if ( ! $terms || empty($terms) )
	  		return $trail;

		//get the first tax term of the list
		$first_term 	= array_shift($terms);

		// If the taxonomy term has a parent, add the hierarchy to the trail.
		if ( 0 !== $first_term -> parent )
			$trail = array_merge( $trail, $this -> czr_fn_breadcrumb_trail_get_term_parents( $first_term -> parent , $first_hierarchical_tax -> name ) );

		//Add the taxonomy term archive link to the trail.
		$trail[] = '<a href="' . get_term_link( $first_term,  $first_hierarchical_tax -> name ) . '" title="' . esc_attr( $first_term->name ) . '">' . $first_term->name . '</a>';

		return $trail;
	}//end function

}//end of class

?><?php
/**
* Comments actions
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
if ( ! class_exists( 'CZR_comments' ) ) :
  class CZR_comments {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //wp hook => wp_query is built
        add_action ( 'wp'                     , array( $this , 'czr_fn_comments_set_hooks' ) );

        //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
        //fired on hook : wp_enqueue_scripts
        //Set thumbnail specific design based on user options
        //Set user defined various inline stylings
        add_filter( 'tc_user_options_style'   , array( $this , 'czr_fn_comment_bubble_inline_css' ) );
      }



      /***************************
      * HOOK SETUP
      ****************************/
      /**
      * Set various comment hooks
      * hook : wp
      * @package Customizr
      * @since Customizr 3.3.2
      */
      function czr_fn_comments_set_hooks() {
        //Maybe fires the comment's template
        add_action ( '__after_loop'           , array( $this , 'czr_fn_comments' ), 10 );

        //Apply a filter on the comment list ( comment list user defined option )
        //the filter tc_display_comment_list is fired in the comments.php template
        add_filter( 'tc_display_comment_list' , array( $this , 'czr_fn_set_comment_list_display' ) );

        //Add actions in the comment's template
        add_action ( '__comment'              , array( $this , 'czr_fn_comment_title' ), 10 );
        add_action ( '__comment'              , array( $this , 'czr_fn_comment_list' ), 20 );
        add_action ( '__comment'              , array( $this , 'czr_fn_comment_navigation' ), 30 );
        add_action ( '__comment'              , array( $this , 'czr_fn_comment_close' ), 40 );
        add_filter ( 'comment_form_defaults'  , array( $this , 'czr_fn_set_comment_title') );

        //Add comment bubble
        add_filter( 'tc_the_title'            , array( $this , 'czr_fn_display_comment_bubble' ), 1 );
        //Custom Bubble comment since 3.2.6
        add_filter( 'tc_bubble_comment'       , array( $this , 'czr_fn_custom_bubble_comment'), 10, 2 );
      }




      /***************************
      * VIEWS
      ****************************/
     /**
      * Main commments template
      *
      * @package Customizr
      * @since Customizr 3.0.10
     */
      function czr_fn_comments() {
        if ( ! $this -> czr_fn_are_comments_enabled() )
          return;
        do_action('tc_before_comments_template');
          comments_template( '' , true );
        do_action('tc_after_comments_template');
      }




      /**
        * Comment title rendering
        *
        *
        * @package Customizr
        * @since Customizr 3.0
       */
        function czr_fn_comment_title() {
          if ( 1 == get_comments_number() ) {
            $_title = __( 'One thought on', 'customizr' );
          } else {
            $_title = sprintf( '%1$s %2$s', number_format_i18n( get_comments_number(), 'customizr' ) , __( 'thoughts on', 'customizr' ) );
          }

          echo apply_filters( 'tc_comment_title' ,
                sprintf( '<h2 id="tc-comment-title" class="comments-title">%1$s &ldquo;%2$s&rdquo;</h2>' ,
                  $_title,
                  '<span>' . get_the_title() . '</span>'
                )
          );
        }



       /**
        * Comment list Rendering
        *
        * @package Customizr
        * @since Customizr 3.0
       */
        function czr_fn_comment_list() {
          $_args = apply_filters( 'tc_list_comments_args' , array( 'callback' => array ( $this , 'czr_fn_comment_callback' ) , 'style' => 'ul' ) );
          ob_start();
            ?>
              <ul class="commentlist">
                <?php wp_list_comments( $_args ); ?>
              </ul><!-- .commentlist -->
            <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          echo apply_filters( 'tc_comment_list' , $html );
        }




       /**
        * Template for comments and pingbacks.
        *
        *
        * Used as a callback by wp_list_comments() for displaying the comments.
        *  Inspired from Twenty Twelve 1.0
        * @package Customizr
        * @since Customizr 1.0
        */
       function czr_fn_comment_callback( $comment, $args, $depth ) {

        $GLOBALS['comment'] = $comment;
        //get user defined max comment depth
        $max_comments_depth = get_option('thread_comments_depth');
        $max_comments_depth = isset( $max_comments_depth ) ? $max_comments_depth : 5;

        ob_start();

        switch ( $comment->comment_type ) :
          case 'pingback' :
          case 'trackback' :
          // Display trackbacks differently than normal comments.
        ?>
        <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
          <article id="comment-<?php comment_ID(); ?>" class="comment">
            <p><?php _e( 'Pingback:' , 'customizr' ); ?> <?php comment_author_link(); ?>
                <?php if ( ! CZR___::$instance -> czr_fn_is_customizing() )  edit_comment_link( __( '(Edit)' , 'customizr' ), '<span class="edit-link btn btn-success btn-mini">' , '</span>' ); ?>
            </p>
          </article>
        <?php
            break;
          default :
          // Proceed with normal comments.
          global $post;
        ?>
        <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

            <?php
              //when do we display the comment content?
              $tc_show_comment_content = 1 == get_option( 'thread_comments' ) && ($depth < $max_comments_depth) && comments_open();

              //gets the comment text => filter parameter!
              $comment_text = get_comment_text( $comment->comment_ID , $args );

              printf('<article id="comment-%9$s" class="comment"><div class="%1$s"><div class="%2$s">%3$s</div><div class="%4$s">%5$s %6$s %7$s %8$s</div></div></article>',
                  apply_filters( 'tc_comment_wrapper_class', 'row-fluid' ),
                  apply_filters( 'tc_comment_avatar_class', 'comment-avatar span2' ),
                  get_avatar( $comment, apply_filters( 'tc_comment_avatar_size', 80 ) ),
                  apply_filters( 'tc_comment_content_class', 'span10' ),

                  $tc_show_comment_content ? sprintf('<div class="%1$s">%2$s</div>',
                                            apply_filters( 'tc_comment_reply_btn_class', 'reply btn btn-small' ),
                                            get_comment_reply_link( array_merge(
                                                                        $args,
                                                                        array(  'reply_text' => __( 'Reply' , 'customizr' ).' <span>&darr;</span>',
                                                                                'depth' => $depth,
                                                                                'max_depth' => $args['max_depth'] ,
                                                                                'add_below' => apply_filters( 'tc_comment_reply_below' , 'comment' )
                                                                              )
                                                                  )
                                            )
                  ) : '',

                  sprintf('<header class="comment-meta comment-author vcard">%1$s %2$s</header>',
                        sprintf( '<cite class="fn">%1$s %2$s %3$s</cite>' ,
                            get_comment_author_link(),
                            // If current post author is also comment author, make it known visually.
                            ( $comment->user_id === $post->post_author ) ? '<span> ' . __( 'Post author' , 'customizr' ) . '</span>' : '' ,
                            ! CZR___::$instance -> czr_fn_is_customizing() && current_user_can( 'edit_comment', $comment->comment_ID ) ? '<p class="edit-link btn btn-success btn-mini"><a class="comment-edit-link" href="' . get_edit_comment_link( $comment->comment_ID ) . '">' . __( 'Edit' , 'customizr' ) . '</a></p>' : ''
                        ),
                        sprintf( '<a class="comment-date" href="%1$s"><time datetime="%2$s">%3$s</time></a>' ,
                            esc_url( get_comment_link( $comment->comment_ID ) ),
                            get_comment_time( 'c' ),
                            /* translators: 1: date, 2: time */
                            sprintf( __( '%1$s at %2$s' , 'customizr' ), get_comment_date(), get_comment_time() )
                        )
                  ),

                  ( '0' == $comment->comment_approved ) ? sprintf('<p class="comment-awaiting-moderation">%1$s</p>',
                    __( 'Your comment is awaiting moderation.' , 'customizr' )
                    ) : '',

                  sprintf('<section class="comment-content comment">%1$s</section>',
                    apply_filters( 'comment_text', $comment_text, $comment, $args )
                  ),
                  $comment->comment_ID
                );//end printf
            ?>
          <!-- //#comment-## -->
        <?php
          break;
        endswitch; // end comment_type check

        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_comment_callback' , $html, $comment, $args, $depth, $max_comments_depth );
      }




    /**
    * Comments navigation rendering
    *
    * @package Customizr
    * @since Customizr 3.0
   */
    function czr_fn_comment_navigation () {
      if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through

        ob_start();

        ?>
        <nav id="comment-nav-below" class="navigation" role="navigation">
          <h3 class="assistive-text section-heading"><?php _e( 'Comment navigation' , 'customizr' ); ?></h3>
          <ul class="pager">

            <?php if(get_previous_comments_link() != null) : ?>

              <li class="previous">
                <span class="nav-previous"><?php previous_comments_link( __( '<span class="meta-nav">&larr;</span> Older Comments' , 'customizr' ) ); ?></span>
              </li>

            <?php endif; ?>

            <?php if(get_next_comments_link() != null) : ?>

              <li class="next">
                <span class="nav-next"><?php next_comments_link( __( 'Newer Comments <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?></span>
              </li>

            <?php endif; ?>

          </ul>
        </nav>
        <?php

        $html = ob_get_contents();
        ob_end_clean();
        echo apply_filters( 'tc_comment_navigation' , $html );

      endif; // check for comment navigation

    }



    /**
    * Comment close rendering
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_comment_close() {
      /* If there are no comments and comments are closed, let's leave a note.
       * But we only want the note on posts and pages that had comments in the first place.
       */
      if ( ! comments_open() && get_comments_number() ) :
        echo apply_filters( 'tc_comment_close' ,
          sprintf('<p class="nocomments">%1$s</p>',
            __( 'Comments are closed.' , 'customizr' )
          )
        );

      endif;
    }





    /***************************
    * CALLBACKS
    ****************************/
    /**
    * Do we display the comment list ?
    * hook : tc_display_comment_list
    * @return  bool
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_set_comment_list_display() {
      return (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_comment_list' ) );
    }



    /**
    * Comment title override (comment_form_defaults filter)
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_comment_title($_defaults) {
      $_defaults['title_reply'] =  __( 'Leave a comment' , 'customizr' );
      return $_defaults;
    }



    /**
    * Callback for tc_the_title
    * @return  string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_display_comment_bubble( $_title = null ) {
      if ( ! $this -> czr_fn_is_bubble_enabled() )
        return $_title;

      global $post;
      //checks if comments are opened AND if there are any comments to display
      return sprintf('%1$s <span class="comments-link"><a href="%2$s%3$s" title="%4$s %5$s" data-disqus-identifier="javascript:this.page.identifier">%6$s</a></span>',
        $_title,
        is_singular() ? '' : get_permalink(),
        apply_filters( 'tc_bubble_comment_anchor', '#tc-comment-title'),
        sprintf( '%1$s %2$s' , get_comments_number(), __( 'Comment(s) on' , 'customizr' ) ),
        is_null($_title) ? esc_attr( strip_tags( $post -> post_title ) ) : esc_attr( strip_tags( $_title ) ),
        0 != get_comments_number() ? apply_filters( 'tc_bubble_comment' , '' , esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_bubble_shape' ) ) ) : ''
      );
    }



   /**
    * Callback of tc_bubble_comment
    * @return string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_custom_bubble_comment( $_html , $_opt ) {
      return sprintf('%4$s<span class="tc-comment-bubble %1$s">%2$s %3$s</span>',
        'default' == $_opt ? "default-bubble" : $_opt,
        get_comments_number(),
        'default' == $_opt ? '' : sprintf( _n( 'comment' , 'comments' , get_comments_number(), 'customizr' ),
          number_format_i18n( get_comments_number(), 'customizr' )
        ),
        $_html
      );
    }


    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    function czr_fn_comment_bubble_inline_css( $_css ) {
      if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_show_bubble' ) ) )
        return $_css;

      //apply custom color only if type custom
      //if color type is skin => bubble color is defined in the skin stylesheet
      if ( 'skin' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_bubble_color_type' ) ) ) {
        $_custom_bubble_color = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_bubble_color' ) );
        $_css .= "
          .comments-link .tc-comment-bubble {
            color: {$_custom_bubble_color};
            border: 2px solid {$_custom_bubble_color};
          }
          .comments-link .tc-comment-bubble:before {
            border-color: {$_custom_bubble_color};
          }
        ";
      }

      if ( 'default' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_bubble_shape' ) ) )
        return $_css;

      $_css .= "
        .comments-link .custom-bubble-one {
          position: relative;
          bottom: 28px;
          right: 10px;
          padding: 4px;
          margin: 1em 0 3em;
          background: none;
          -webkit-border-radius: 10px;
          -moz-border-radius: 10px;
          border-radius: 10px;
          font-size: 10px;
        }
        .comments-link .custom-bubble-one:before {
          content: '';
          position: absolute;
          bottom: -14px;
          left: 10px;
          border-width: 14px 8px 0;
          border-style: solid;
          display: block;
          width: 0;
        }
        .comments-link .custom-bubble-one:after {
          content: '';
          position: absolute;
          bottom: -11px;
          left: 11px;
          border-width: 13px 7px 0;
          border-style: solid;
          border-color: #FAFAFA rgba(0, 0, 0, 0);
          display: block;
          width: 0;
        }\n";

      return $_css;
    }//end of fn



    /***************************
    * HELPERS
    ****************************/
    /**
    * 1) if the page / post is password protected OR if is_home OR ! is_singular() => false
    * 2) if comment_status == 'closed' => false
    * 3) if user defined comment option in customizer == false => false
    *
    * By default, comments are globally disabled in pages and enabled in posts
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_are_comments_enabled() {
      global $post;
      // 1) By default not displayed on home, for protected posts, and if no comments for page option is checked
      if ( isset( $post ) ) {
        $_bool = ( post_password_required() || czr_fn__f( '__is_home' ) || ! is_singular() )  ? false : true;

        //2) if user has enabled comment for this specific post / page => true
        //@todo contx : update default value user's value)
        $_bool = ( 'closed' != $post -> comment_status ) ? true : $_bool;

        //3) check global user options for pages and posts
        if ( is_page() )
          $_bool = 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_page_comments' )) && $_bool;
        else
          $_bool = 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_comments' )) && $_bool;
      } else
        $_bool = false;

      return apply_filters( 'tc_are_comments_enabled', $_bool );
    }




    /**
    * When are we displaying the comment bubble ?
    * - Must be in the loop
    * - Bubble must be enabled by user
    * - comments are enabled
    * - there is at least one comment
    * - the comment list option is enabled
    * - post type is in the eligible post type list : default = post
    * - tc_comments_in_title boolean filter is true
    *
    * @return  boolean
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    private function czr_fn_is_bubble_enabled() {
      $_bool_arr = array(
        in_the_loop(),
        (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_comment_show_bubble' ) ),
        $this -> czr_fn_are_comments_enabled(),
        get_comments_number() != 0,
        (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_comment_list' ) ),
        (bool) apply_filters( 'tc_comments_in_title', true ),
        in_array( get_post_type(), apply_filters('tc_show_comment_bubbles_for_post_types' , array( 'post' , 'page') ) )
      );
      return (bool) array_product($_bool_arr);
    }

  }//end class
endif;

?><?php
/**
* Featured pages actions
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
if ( ! class_exists( 'CZR_featured_pages' ) ) :
  class CZR_featured_pages {
    static $instance;
    function __construct () {
        self::$instance =& $this;
        add_action( '__before_main_container'     , array( $this , 'czr_fn_fp_block_display'), 10 );
        add_action( '__after_fp'                  , array( $this , 'czr_fn_maybe_display_dismiss_notice'));
    }



    /******************************
    * FP NOTICE VIEW
    *******************************/
    /**
    * hook : __after_fp
    * @since v3.4+
    */
    function czr_fn_maybe_display_dismiss_notice() {
      if ( ! CZR_placeholders::czr_fn_is_fp_notice_on() )
        return;

      $_customizer_lnk = apply_filters( 'tc_fp_notice_customizer_url', CZR_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_show_featured_pages', 'section' => 'frontpage_sec') ) );

      ?>
      <div class="tc-placeholder-wrap tc-fp-notice">
        <?php
          printf('<p><strong>%1$s</strong></p>',
            sprintf( __("Edit those featured pages %s, or %s (you'll be able to add yours later)." , "customizr"),
              sprintf( '<a href="%3$s" title="%1$s">%2$s</a>', __( "Edit those featured pages", "customizr" ), __( "now", "customizr" ), $_customizer_lnk ),
              sprintf( '<a href="#" class="tc-inline-remove" title="%1$s">%2$s</a>', __( "Remove the featured pages", "customizr" ), __( "remove them", "customizr" ) )
            )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
            __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }



    /******************************
    * FP WRAPPER VIEW
    *******************************/
    /**
    * The template displaying the front page featured page block.
    * hook : __before_main_container
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_fp_block_display() {

      if ( ! $this -> czr_fn_show_featured_pages()  )
        return;

      $tc_show_featured_pages_img     = $this -> czr_fn_show_featured_pages_img();

      //gets the featured pages array and sets the fp layout
      $fp_ids                         = apply_filters( 'tc_featured_pages_ids' , CZR_init::$instance -> fp_ids);
      $fp_nb                          = count($fp_ids);
      $fp_per_row                     = apply_filters( 'tc_fp_per_line', 3 );

      //defines the span class
      $span_array = array(
        1 => 12,
        2 => 6,
        3 => 4,
        4 => 3,
        5 => 2,
        6 => 2,
        7 => 2
      );
      $span_value = 4;
      $span_value = ( $fp_per_row > 7) ? 1 : $span_value;
      $span_value = isset( $span_array[$fp_per_row] ) ? $span_array[$fp_per_row] :  $span_value;

      //save $args for filter
      $args = array($fp_ids, $fp_nb, $fp_per_row, $span_value);

      ?>

      <?php ob_start(); ?>

      <div class="container marketing">

        <?php
          do_action ('__before_fp') ;

          $j = 1;
          for ($i = 1; $i <= $fp_nb ; $i++ ) {
                printf('%1$s<div class="span%2$s fp-%3$s">%4$s</div>%5$s',
                    ( 1 == $j ) ? sprintf('<div class="%1$s" role="complementary">',
                                  implode(" " , apply_filters( 'tc_fp_widget_area' , array( 'row' , 'widget-area' ) ) )
                                  ) : '',
                    $span_value,
                    $fp_ids[$i - 1],
                    $this -> czr_fn_fp_single_display( $fp_ids[$i - 1] , $tc_show_featured_pages_img ),
                    ( $j == $fp_per_row || $i == $fp_nb ) ? '</div>' : ''
                );
          //set $j back to start value if reach $fp_per_row
          $j++;
          $j = ($j == ($fp_per_row + 1)) ? 1 : $j;
          }

          do_action ('__after_fp') ;
        ?>

      </div><!-- .container -->

      <?php  echo ! czr_fn__f( '__is_home_empty') ? apply_filters( 'tc_after_fp_separator', '<hr class="featurette-divider '.current_filter().'">' ) : ''; ?>

      <?php
      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_fp_block_display' , $html, $args );
     }




      /******************************
      * SINGLE FP VIEW
      *******************************/
     /**
      * The template displaying one single featured page
      * fired in : czr_fn_fp_block_display()
      *
      * @package Customizr
      * @since Customizr 3.0
      * @param area are defined in featured-pages templates,show_img is a customizer option
      * @todo better area definition : dynamic
      */
      function czr_fn_fp_single_display( $fp_single_id,$show_img) {
        $_skin_color                        = CZR_utils::$inst -> czr_fn_get_skin_color();
        $fp_holder_img                      = apply_filters (
          'tc_fp_holder_img' ,
          sprintf('<img class="tc-holder-img" data-src="holder.js/270x250/%1$s:%2$s" data-no-retina alt="Holder Thumbnail" style="width:270px;height:250px;"/>',
            ( '#E4E4E4' != $_skin_color ) ? '#EEE' : '#5A5A5A',
            $_skin_color
          )
        );
        $featured_page_id                   = 0;

        //if fps are not set
        if ( null == CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id ) || ! CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id ) ) {
            //admin link if user logged in
            $featured_page_link             = '';
            $customizr_link                 = '';
            if ( ! CZR___::$instance -> czr_fn_is_customizing() && is_user_logged_in() && current_user_can('edit_theme_options') ) {
              $customizr_link              = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>',
                CZR_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_featured_text_'.$fp_single_id, 'section' => 'frontpage_sec') ),
                __( 'Customizer screen' , 'customizr' ),
                __( 'Edit now.' , 'customizr' )
              );
            }
            $featured_page_link          = apply_filters( 'tc_fp_link_url', CZR_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_featured_page_'.$fp_single_id, 'section' => 'frontpage_sec') ) );

            //rendering
            $featured_page_id               =  null;
            $featured_page_title            =  apply_filters( 'tc_fp_title', __( 'Featured page' , 'customizr' ), $fp_single_id, $featured_page_id);
            $text                           =  apply_filters(
                                                'tc_fp_text',
                                                sprintf( '%1$s %2$s',
                                                  __( 'Featured page description text : use the page excerpt or set your own custom text in the Customizr screen.' , 'customizr' ),
                                                  $customizr_link
                                                ),
                                                $fp_single_id,
                                                $featured_page_id
                                              );
            $fp_img                         =  apply_filters ('fp_img_src' , $fp_holder_img, $fp_single_id , $featured_page_id );

        }

        else {
            $featured_page_id               = apply_filters( 'tc_fp_id', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_'.$fp_single_id) ), $fp_single_id );

            $featured_page_link             = apply_filters( 'tc_fp_link_url', get_permalink( $featured_page_id ), $fp_single_id );

            $featured_page_title            = apply_filters( 'tc_fp_title', get_the_title( $featured_page_id ), $fp_single_id, $featured_page_id );

            $edit_enabled                   = false;
            //when are we displaying the edit link?
            //never display when customizing
            if ( ! CZR___::$instance -> czr_fn_is_customizing() ) {
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_pages') && is_page( $featured_page_id ) ) ? true : $edit_enabled;
              $edit_enabled                 = ( (is_user_logged_in()) && current_user_can('edit_post' , $featured_page_id ) && ! is_page( $featured_page_id ) ) ? true : $edit_enabled;
            }

            $edit_enabled                   = apply_filters( 'tc_edit_in_fp_title', $edit_enabled );

            $featured_text                  = apply_filters( 'tc_fp_text', CZR_utils::$inst->czr_fn_opt( 'tc_featured_text_'.$fp_single_id ), $fp_single_id, $featured_page_id );
            $featured_text                  = apply_filters( 'tc_fp_text_sanitize', strip_tags( html_entity_decode( $featured_text ) ), $fp_single_id, $featured_page_id );

            //get the page/post object
            $page                           = get_post($featured_page_id);

            //set page excerpt as default text if no $featured_text
            $text                           = ( empty($featured_text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_excerpt )) : $featured_text ;
            $text                           = ( empty($text) && !post_password_required($featured_page_id) ) ? strip_tags(apply_filters( 'the_content' , $page->post_content )) : $text ;

            //limit text to 200 car
            $default_fp_text_length         = apply_filters( 'tc_fp_text_length', 200, $fp_single_id, $featured_page_id );
            $text                           = ( strlen($text) > $default_fp_text_length ) ? substr( $text , 0 , strpos( $text, ' ' , $default_fp_text_length) ). ' ...' : $text;

            //set the image : uses thumbnail if any then >> the first attached image then >> a holder script
            $fp_img_size                    = apply_filters( 'tc_fp_img_size' , 'tc-thumb', $fp_single_id, $featured_page_id );
            //allow user to specify a custom image id
            $fp_custom_img_id               = apply_filters( 'fp_img_id', null , $fp_single_id , $featured_page_id );

            $fp_img                         = $this -> czr_fn_get_fp_img( $fp_img_size, $featured_page_id, $fp_custom_img_id);
            $fp_img                         = $fp_img ? $fp_img : $fp_holder_img;

            $fp_img                         = apply_filters ('fp_img_src' , $fp_img , $fp_single_id , $featured_page_id );
          }//end if

          //Let's render this
          ob_start();
          ?>

          <div class="widget-front">
            <?php
              if ( isset( $show_img) && $show_img == 1 ) { //check if image option is checked
                printf('<div class="thumb-wrapper %1$s">%2$s%3$s</div>',
                   ( $fp_img == $fp_holder_img ) ? 'tc-holder' : '',
                   apply_filters('tc_fp_round_div' , sprintf('<a class="round-div" href="%1$s" title="%2$s"></a>',
                                                    $featured_page_link,
                                                    esc_attr( strip_tags( $featured_page_title ) )
                                                  ) ,
                                $fp_single_id,
                                $featured_page_id
                                ),
                   $fp_img
                );
              }//end if image enabled check


              //title block
              $tc_fp_title_block  = sprintf('<%1$s>%2$s %3$s</%1$s>',
                                  apply_filters( 'tc_fp_title_tag' , 'h2', $fp_single_id, $featured_page_id ),
                                  $featured_page_title,
                                  ( isset($edit_enabled) && $edit_enabled )? sprintf('<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
                                            get_edit_post_link($featured_page_id),
                                            __( 'Edit' , 'customizr' )
                                            ) : ''
              );
              echo apply_filters( 'tc_fp_title_block' , $tc_fp_title_block , $featured_page_title , $fp_single_id, $featured_page_id );

              //text block
              $tc_fp_text_block   = sprintf('<p class="fp-text-%1$s">%2$s</p>',
                                  $fp_single_id,
                                  $text
              );
              echo apply_filters( 'tc_fp_text_block' , $tc_fp_text_block , $fp_single_id , $text, $featured_page_id);

              //button block
              $tc_fp_button_text = apply_filters( 'tc_fp_button_text' , esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_featured_page_button_text') ) , $fp_single_id );

              if ( $tc_fp_button_text || CZR___::$instance -> czr_fn_is_customizing() ){
                $tc_fp_button_class = apply_filters( 'tc_fp_button_class' , 'btn btn-primary fp-button', $fp_single_id );
                $tc_fp_button_class = $tc_fp_button_text ? $tc_fp_button_class : $tc_fp_button_class . ' hidden';
                $tc_fp_button_block = sprintf('<a class="%1$s" href="%2$s" title="%3$s">%4$s</a>',
                                    $tc_fp_button_class,
                                    $featured_page_link,
                                    esc_attr( strip_tags( $featured_page_title ) ),
                                    $tc_fp_button_text

                );
                echo apply_filters( 'tc_fp_button_block' , $tc_fp_button_block , $featured_page_link , $featured_page_title , $fp_single_id, $featured_page_id );
              }
            ?>

          </div><!-- /.widget-front -->

          <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'tc_fp_single_display' , $html, $fp_single_id, $show_img, $fp_img, $featured_page_link, $featured_page_title, $text, $featured_page_id );
      }//end of function



    /******************************
    * HELPERS
    *******************************/
    function czr_fn_get_fp_img( $fp_img_size, $featured_page_id = null , $fp_custom_img_id = null ){
      //try to get "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
      //czr_fn_get_thumbnail_model( $requested_size = null, $_post_id = null , $_thumb_id = null )
      $_fp_img_model = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model( $fp_img_size, $featured_page_id, $fp_custom_img_id );

      //finally we define a default holder if no thumbnail found or page is protected
      if ( isset( $_fp_img_model["tc_thumb"]) && ! empty( $_fp_img_model["tc_thumb"] ) && ! post_password_required( $featured_page_id ) )
        $fp_img = $_fp_img_model["tc_thumb"];
      else
        $fp_img = false;

      return $fp_img;
    }


    function czr_fn_show_featured_pages() {
      //gets display fp option
      $tc_show_featured_pages         = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_featured_pages' ) );

      return apply_filters( 'tc_show_fp', 0 != $tc_show_featured_pages && czr_fn__f('__is_home') );
    }


    function czr_fn_show_featured_pages_img() {
      //gets  display img option
      return apply_filters( 'tc_show_featured_pages_img', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_featured_pages_img' ) ) );
    }

  }//end of class
endif;

?><?php
/**
* Gallery content filters
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_gallery' ) ) :
  class CZR_gallery {
      static $instance;
      function __construct () {
        self::$instance =& $this;

        add_filter ( 'tc_article_container_class' , array( $this, 'czr_fn_add_gallery_class' ), 20 );
        //adds a filter for link markup (allow lightbox)
        add_filter ( 'wp_get_attachment_link'     , array( $this, 'czr_fn_modify_attachment_link') , 20, 6 );
      }

      /**
       *
       * Add a class to the article-container to apply Customizr galleries on hover effects
       *
       * @package Customizr
       * @since Customizr 3.3.21
       *
       */
      function czr_fn_add_gallery_class( $_classes ){
        if (  $this -> czr_fn_is_gallery_enabled() && apply_filters( 'tc_gallery_style', esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_gallery_style' ) ) ) )
          array_push($_classes, 'tc-gallery-style');
        return $_classes;
      }



      /**
       * Add an optional rel="tc-fancybox[]" attribute to all images embedded in a post gallery
       * Based on the original WP function
       * @package Customizr
       * @since Customizr 3.0.5
       *
       */
      function czr_fn_modify_attachment_link( $markup, $id, $size, $permalink, $icon, $text ) {

        if ( ! $this -> czr_fn_is_gallery_enabled() )
          return $markup;

        $tc_gallery_fancybox = apply_filters( 'tc_gallery_fancybox', esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_gallery_fancybox' ) ) , $id );

        if ( $tc_gallery_fancybox == 1 && $permalink == false ) //add the filter only if link to the attachment file/image
          {
              $id = intval( $id );
              $_post = get_post( $id );

              if ( empty( $_post ) || ( 'attachment' != $_post->post_type ) || ! $url = wp_get_attachment_url( $_post->ID ) )
                return __( 'Missing Attachment' , 'customizr');

              if ( $permalink )
                $url = get_attachment_link( $_post->ID );

              $post_title = esc_attr( $_post->post_title );

              if ( $text )
                $link_text = $text;
              elseif ( $size && 'none' != $size )
                $link_text = wp_get_attachment_image( $id, $size, $icon );
              else
                $link_text = '';

              if ( trim( $link_text ) == '' )
                $link_text = $_post->post_title;
               $markup      = '<a class="grouped_elements" rel="tc-fancybox-group" href="'.$url.'" title="'.$post_title.'">'.$link_text.'</a>';
          }


        return $markup;
      }

      /*
       * HELPERS
       */
      function czr_fn_is_gallery_enabled(){
        return apply_filters('tc_enable_gallery', esc_attr( CZR_utils::$inst -> czr_fn_opt('tc_enable_gallery') ) );
      }
  }//end of class
endif;

?><?php
/**
* Headings actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.1.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_headings' ) ) :
  class CZR_headings {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        //set actions and filters for posts and page headings
        add_action( 'template_redirect'                            , array( $this , 'czr_fn_set_post_page_heading_hooks') );
        //set actions and filters for archives headings
        add_action( 'template_redirect'                            , array( $this , 'czr_fn_set_archives_heading_hooks') );
        //Set headings user options
        add_action( 'template_redirect'                            , array( $this , 'czr_fn_set_headings_options') );
      }


      /******************************************
      * HOOK SETTINGS ***************************
      ******************************************/
      /**
      * @return void
      * set up hooks for archives headings
      * hook : template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_set_archives_heading_hooks() {
        //is there anything to render in the current context
        //by default don't display the Customizr title in feeds
        if ( apply_filters('tc_display_customizr_headings',  ! $this -> czr_fn_archive_title_and_class_callback() || is_feed() ) )
          return;

        //Headings for archives, authors, search, 404
        add_action ( '__before_loop'                  , array( $this , 'czr_fn_render_headings_view' ) );
        //Set archive icon with customizer options (since 3.2.0)
        add_filter ( 'tc_archive_icon'                , array( $this , 'czr_fn_set_archive_icon' ) );

        add_filter( 'tc_archive_header_class'         , array( $this , 'czr_fn_archive_title_and_class_callback'), 10, 2 );
        add_filter( 'tc_headings_archive_html'        , array( $this , 'czr_fn_archive_title_and_class_callback'), 10, 1 );
        global $wp_query;
        if ( czr_fn__f('__is_home') )
          add_filter( 'tc_archive_headings_separator' , '__return_false' );
      }


      /**
      * @return void
      * set up hooks for post and page headings
      * callback of template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_set_post_page_heading_hooks() {

        //by default don't display the Customizr title of the front page and in feeds
        if ( apply_filters('tc_display_customizr_headings', ( is_front_page() && 'page' == get_option( 'show_on_front' ) ) ) || is_feed() )
          return;

        //Set single post/page icon with customizer options (since 3.2.0)
        add_filter ( 'tc_content_title_icon'    , array( $this , 'czr_fn_set_post_page_icon' ) );
        //Prepare the headings for post, page, attachment
        add_action ( '__before_content'         , array( $this , 'czr_fn_render_headings_view' ) );
        //Populate heading with default content
        add_filter ( 'tc_headings_content_html' , array( $this , 'czr_fn_post_page_title_callback'), 10, 2 );
        //Create the Customizr title
        add_filter( 'tc_the_title'              , array( $this , 'czr_fn_content_heading_title' ) , 0 );
        //Add edit link
        add_filter( 'tc_the_title'              , array( $this , 'czr_fn_add_edit_link_after_title' ), 2 );
        //Set user defined archive titles
        add_filter( 'tc_category_archive_title' , array( $this , 'czr_fn_set_archive_custom_title' ) );
        add_filter( 'tc_tag_archive_title'      , array( $this , 'czr_fn_set_archive_custom_title' ) );
        add_filter( 'tc_search_results_title'   , array( $this , 'czr_fn_set_archive_custom_title' ) );
        add_filter( 'tc_author_archive_title'   , array( $this , 'czr_fn_set_archive_custom_title' ) );


        //SOME DEFAULT OPTIONS
        //No hr if not singular
        if ( ! is_singular() )
          add_filter( 'tc_content_headings_separator' , '__return_false' );

        //No headings for some post formats
        add_filter( 'tc_headings_content_html'  , array( $this, 'czr_fn_post_formats_heading') , 100 );

      }


      /******************************************
      * VIEWS ***********************************
      ******************************************/
      /**
      * Generic heading view : archives, author, search, 404 and the post page heading (if not font page)
      * This is the place where every heading content blocks are hooked
      * hook : __before_content AND __before_loop (for post lists)
      *
      * @package Customizr
      * @since Customizr 3.1.0
      */
      function czr_fn_render_headings_view() {
        $_heading_type = in_the_loop() ? 'content' : 'archive';
        ob_start();
        ?>
        <header class="<?php echo implode( ' ' , apply_filters( "tc_{$_heading_type}_header_class", array('entry-header'), $_return_class = true ) ); ?>">
          <?php
            do_action( "__before_{$_heading_type}_title" );
            echo apply_filters( "tc_headings_{$_heading_type}_html", '' , $_heading_type );
            do_action( "__after_{$_heading_type}_title" );

            echo apply_filters( "tc_{$_heading_type}_headings_separator", '<hr class="featurette-divider '.current_filter(). '">' );
          ?>
        </header>
        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_render_headings_view', $html );
      }//end of function





      /******************************************
      * HELPERS / SETTERS / CALLBACKS ***********
      ******************************************/
      /**
      * @return string or boolean
      * Returns the heading html content or false
      * callback of tc_headings_{$_heading_type}_html where $_heading_type = content when in the loop
      *
      * @package Customizr
      * @since Customizr 3.2.9
      */
      function czr_fn_post_formats_heading( $_html ) {
        if( in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', CZR_init::$instance -> post_formats_with_no_heading ) ) )
          return;
        return $_html;
      }


      /**
      * Callback for tc_headings_content_html
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_post_page_title_callback( $_content = null , $_heading_type = null ) {
        $_title = apply_filters( 'tc_title_text', get_the_title() );
        return sprintf('<%1$s class="entry-title %2$s">%3$s</%1$s>',
              apply_filters( 'tc_content_title_tag' , is_singular() ? 'h1' : 'h2' ),
              apply_filters( 'tc_content_title_icon', 'format-icon' ),
              apply_filters( 'tc_the_title', $_title )
        );
      }

      /**
      * Callback for tc_the_title
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_content_heading_title( $_title ) {
        //Must be in the loop
        if ( ! in_the_loop() )
          return $_title;

        //gets the post/page title
        if ( is_singular() || ! apply_filters('tc_display_link_for_post_titles' , true ) )
          return is_null($_title) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : $_title;
        else
          return sprintf('<a href="%1$s" title="%2$s" rel="bookmark">%3$s</a>',
            get_permalink(),
            sprintf( apply_filters( 'tc_post_link_title' , __( 'Permalink to %s' , 'customizr' ) ) , esc_attr( strip_tags( get_the_title() ) ) ),
            is_null($_title) ? apply_filters( 'tc_no_title_post', __( '{no title} Read the post &raquo;' , 'customizr' ) )  : $_title
          );//end sprintf
      }


      /**
      * Callback for tc_the_title
      * @return  string
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_add_edit_link_after_title( $_title ) {
        //Must be in the loop
        if ( ! in_the_loop() )
          return $_title;

        if ( ! apply_filters( 'tc_edit_in_title', $this -> czr_fn_is_edit_enabled() ) )
          return $_title;

        return sprintf('%1$s %2$s',
          $_title,
          $this -> czr_fn_render_edit_link_view( $_echo = false )
        );

      }


      /**
      * Helper Boolean
      * @return boolean
      * @package Customizr
      * @since Customizr 3.3+
      */
      public function czr_fn_is_edit_enabled() {
        //never display when customizing
        if ( CZR___::$instance -> czr_fn_is_customizing() )
          return false;
        //when are we displaying the edit link?
        $edit_enabled = ( (is_user_logged_in()) && is_page() && current_user_can('edit_pages') ) ? true : false;
        return ( (is_user_logged_in()) && 0 !== get_the_ID() && current_user_can('edit_post' , get_the_ID() ) && ! is_page() ) ? true : $edit_enabled;
      }



      /**
      * Returns the edit link html string
      * @return  string
      * @package Customizr
      * @since Customizr 3.3+
      */
      function czr_fn_render_edit_link_view( $_echo = true ) {
        $_view = sprintf('<span class="edit-link btn btn-inverse btn-mini"><a class="post-edit-link" href="%1$s" title="%2$s">%2$s</a></span>',
          get_edit_post_link(),
          __( 'Edit' , 'customizr' )
        );
        if ( ! $_echo )
          return $_view;
        echo $_view;
      }


      /**
      * hook tc_content_title_icon
      * @return  boolean
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_post_page_icon( $_bool ) {
          if ( is_page() )
            $_bool = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_page_title_icon' ) ) ) ? false : $_bool;
          if ( is_single() && ! is_page() )
            $_bool = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_title_icon' ) ) ) ? false : $_bool;
          if ( ! is_single() )
            $_bool = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_list_title_icon' ) ) ) ? false : $_bool;
          //last condition
          return ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_title_icon' ) ) ) ? false : $_bool;
      }



      /**
      * hook tc_archive_icon
      * @return string
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_archive_icon( $_class ) {
          $_class = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_archive_title_icon' ) ) ) ? '' : $_class;
          //last condition
          return 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_title_icon' ) ) ? '' : $_class;
      }




      /**
      * Return 1) the archive title html content OR 2) the archive title class OR 3) the boolean
      * hook : tc_display_customizr_headings
      * @return  boolean
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_archive_title_and_class_callback( $_title = null, $_return_class = false ) {
        //declares variables to return
        $content          = false;
        $_header_class    = false;

        //case page for posts but not on front
        global $wp_query;
        if ( $wp_query -> is_posts_page && ! is_front_page() ) {
          //get page for post ID
          $page_for_post_id = get_option('page_for_posts');
          $_header_class   = array('entry-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf('<%1$s class="entry-title %2$s">%3$s</%1$s>',
                apply_filters( 'tc_content_title_tag' , 'h1' ),
                apply_filters( 'tc_content_title_icon', 'format-icon' ),
                get_the_title( $page_for_post_id )
           );
          $content        = apply_filters( 'tc_page_for_post_header_content', $content );
        }


        //404
        else if ( is_404() ) {
          $_header_class   = array('entry-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf('<h1 class="entry-title %1$s">%2$s</h1>',
                apply_filters( 'tc_archive_icon', '' ),
                apply_filters( 'tc_404_title' , __( 'Ooops, page not found' , 'customizr' ) )
           );
          $content        = apply_filters( 'tc_404_header_content', $content );
        }

        //search results
        else if ( is_search() && ! is_singular() ) {
          $_header_class   = array('search-header');
          if ( $_return_class )
            return $_header_class;

          $content        = sprintf( '<div class="row-fluid"><div class="%1$s"><h1 class="%2$s">%3$s%4$s %5$s </h1></div><div class="%6$s">%7$s</div></div>',
                apply_filters( 'tc_search_result_header_title_class', 'span8' ),
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                have_posts() ? '' :  __( 'No' , 'customizr' ).'&nbsp;' ,
                apply_filters( 'tc_search_results_title' , __( 'Search Results for :' , 'customizr' ) ),
                '<span>' . get_search_query() . '</span>',
                apply_filters( 'tc_search_result_header_form_class', 'span4' ),
                have_posts() ? get_search_form(false) : ''
          );
          $content       = apply_filters( 'tc_search_results_header_content', $content );
        }
        // all archives
        else if ( is_archive() ){
          $_header_class   = array('archive-header');
          if ( $_return_class )
            return $_header_class;

          //author's posts page
          if ( is_author() ) {
            //gets the user ID
            $user_id = get_query_var( 'author' );
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                  apply_filters( 'tc_archive_icon', 'format-icon' ),
                  apply_filters( 'tc_author_archive_title' , '' ),
                  '<span class="vcard">' . get_the_author_meta( 'display_name' , $user_id ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_author_meta' , get_the_author_meta( 'description', $user_id  ) ) ) {
              $content    .= sprintf('%1$s<div class="author-info"><div class="%2$s">%3$s</div></div>',

                  apply_filters( 'tc_author_meta_separator', '<hr class="featurette-divider '.current_filter().'">' ),

                  apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),

                  sprintf('<div class="%1$s">%2$s</div><div class="%3$s"><h2>%4$s</h2><p>%5$s</p></div>',
                      apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                      get_avatar( get_the_author_meta( 'user_email', $user_id ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) ),
                      apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                      sprintf( __( 'About %s' , 'customizr' ), get_the_author() ),
                      get_the_author_meta( 'description' , $user_id  )
                  )
              );
            }
            $content       = apply_filters( 'tc_author_header_content', $content );
          }

          //category archives
          else if ( is_category() ) {
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tc_category_archive_title' , '' ),
                '<span>' . single_cat_title( '' , false ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_cat_description' , category_description() ) ) {
              $content    .= sprintf('<div class="archive-meta">%1$s</div>',
                category_description()
              );
            }
            $content       = apply_filters( 'tc_category_archive_header_content', $content );
          }

          //tag archives
          else if ( is_tag() ) {
            $content    = sprintf( '<h1 class="%1$s">%2$s %3$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ),
                apply_filters( 'tc_tag_archive_title' , '' ),
                '<span>' . single_tag_title( '' , false ) . '</span>'
            );
            if ( apply_filters ( 'tc_show_tag_description' , tag_description() ) ) {
              $content    .= sprintf('<div class="archive-meta">%1$s</div>',
                tag_description()
              );
            }
            $content       = apply_filters( 'tc_tag_archive_header_content', $content );
          }

          //time archives
          else if ( is_day() || is_month() || is_year() ) {
            $archive_type   = is_day() ? sprintf( __( 'Daily Archives: %s' , 'customizr' ), '<span>' . get_the_date() . '</span>' ) : __( 'Archives' , 'customizr' );
            $archive_type   = is_month() ? sprintf( __( 'Monthly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'F Y' , 'monthly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
            $archive_type   = is_year() ? sprintf( __( 'Yearly Archives: %s' , 'customizr' ), '<span>' . get_the_date( _x( 'Y' , 'yearly archives date format' , 'customizr' ) ) . '</span>' ) : $archive_type;
            $content        = sprintf('<h1 class="%1$s">%2$s</h1>',
              apply_filters( 'tc_archive_icon', 'format-icon' ),
              $archive_type
            );
            $content        = apply_filters( 'tc_time_archive_header_content', $content );
          }
          // all other archivers ( such as custom tax archives )
          else if ( apply_filters('tc_show_tax_archive_title', true) ){
            $content   = sprintf('<h1 class="%1$s">%2$s</h1>',
                apply_filters( 'tc_archive_icon', 'format-icon' ), /* handle tax icon? */
                apply_filters( 'tc_tax_archive_title',	get_the_archive_title() )
            );
            $tax_description = get_the_archive_description();
            if ( apply_filters( 'tc_show_tax_description', $tax_description ) )
              $content   .=  sprintf('<div class="archive-meta">%1$s</div>',
                $tax_description
              );
            $content        = apply_filters( 'tc_tax_archive_header_content', $content );
          }
        }// end all archives

        return $_return_class ? $_header_class : $content;

      }//end of fn


      /**
      * @return void
      * set up user defined options
      * callback of template_redirect
      *
      * @package Customizr
      * @since Customizr 3.2.6
      */
      function czr_fn_set_headings_options() {
        //by default don't display the Customizr title in feeds
        if ( apply_filters('tc_display_customizr_headings',  is_feed() ) )
          return;

        //Add update status next to the title (@since 3.2.6)
        add_filter( 'tc_the_title'                  , array( $this , 'czr_fn_add_update_notice_in_title'), 20);
      }



      /**
      * Callback of the tc_the_title => add an updated status
      * @return string
      * User option based
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_add_update_notice_in_title($html) {
          //First checks if we are in the loop and we are not displaying a page
          if ( ! in_the_loop() || is_page() )
              return $html;

          //Is the notice option enabled AND this post type eligible for updated notice ? (default is post)
          if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_update_notice_in_title' ) ) || ! in_array( get_post_type(), apply_filters('tc_show_update_notice_for_post_types' , array( 'post') ) ) )
              return $html;

          //php version check for DateTime
          //http://php.net/manual/fr/class.datetime.php
          if ( version_compare( PHP_VERSION, '5.2.0' ) < 0 )
            return $html;

          //get the user defined interval in days
          $_interval = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_update_notice_interval' ) );
          $_interval = ( 0 != $_interval ) ? $_interval : 30;

          //Check if the last update is less than n days old. (replace n by your own value)
          $has_recent_update = ( CZR_utils::$inst -> czr_fn_post_has_update( true ) && CZR_utils::$inst -> czr_fn_post_has_update( 'in_days') < $_interval ) ? true : false;

          if ( ! $has_recent_update )
              return $html;

          //Return the modified title
          return apply_filters(
              'tc_update_notice_in_title',
              sprintf('%1$s &nbsp; <span class="tc-update-notice label %3$s">%2$s</span>',
                  $html,
                  esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_update_notice_text' ) ),
                  esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_update_notice_format' ) )
              )
          );
      }


      /**
      * hooks : 'tc_category_archive_title', 'tc_tag_archive_title', 'tc_search_results_title', 'tc_author_archive_title'
      * @param default title string
      * @return string of user defined title
      * @since Customizr 3.3+
      */
      function czr_fn_set_archive_custom_title( $_title ) {
        switch ( current_filter() ) {
          case 'tc_category_archive_title' :
            return esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_cat_title' ) );
          break;

          case 'tc_tag_archive_title' :
            return esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_tag_title' ) );
          break;

          case 'tc_search_results_title' :
            return esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_search_title' ) );
          break;

          case 'tc_author_archive_title' :
            return esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_author_title' ) );
          break;
        }
        return $_title;
      }

  }//end of class
endif;

?><?php
/**
* No results content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_no_results' ) ) :
  class CZR_no_results {
      static $instance;
      function __construct () {
          self::$instance =& $this;
          add_action  ( '__loop'                        , array( $this , 'czr_fn_no_result_content' ));
      }

      /**
       * Rendering the no search results
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function czr_fn_no_result_content() {
          global $wp_query;
          if ( !is_search() || (is_search() && 0 != $wp_query -> post_count) )
              return;

          $content_no_results    = apply_filters( 'tc_no_results', CZR_init::$instance -> content_no_results );

          echo apply_filters( 'tc_no_result_content',
              sprintf('<div class="%1$s"><div class="entry-content %2$s">%3$s</div>%4$s</div>',
                  apply_filters( 'tc_no_results_wrapper_class', 'tc-content span12 format-quote' ),
                  apply_filters( 'tc_no_results_content_icon', 'format-icon' ),
                  sprintf('<blockquote><p>%1$s</p><cite>%2$s</cite></blockquote><p>%3$s</p>%4$s',
                                call_user_func( '__' , $content_no_results['quote'] , 'customizr' ),
                                call_user_func( '__' , $content_no_results['author'] , 'customizr' ),
                                call_user_func( '__' , $content_no_results['text'] , 'customizr' ),
                                get_search_form( $echo = false )
                  ),
                  apply_filters( 'tc_no_results_separator', '<hr class="featurette-divider '.current_filter().'">' )
              )//end sprintf
          );//end filter
      }
  }//end of class
endif;

?><?php
/**
* Pages content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_page' ) ) :
  class CZR_page {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'wp'                , array( $this , 'czr_fn_set_page_hooks' ) );
    }



    /***************************
    * PAGE HOOKS SETUP
    ****************************/
    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_set_page_hooks() {
      //add page content and footer to the __loop
      add_action( '__loop'           , array( $this , 'czr_fn_page_content' ) );
      //page help blocks
      add_filter( 'the_content'       , array( $this, 'czr_fn_maybe_display_img_smartload_help') , PHP_INT_MAX );
    }



    /**
     * The template part for displaying page content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function czr_fn_page_content() {
      if ( ! $this -> czr_fn_page_display_controller() )
        return;

      ob_start();

        do_action( '__before_content' );
        ?>

        <div class="entry-content">
          <?php
            the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) );
            wp_link_pages( array(
                'before'        => '<div class="btn-toolbar page-links"><div class="btn-group">' . __( 'Pages:' , 'customizr' ),
                'after'         => '</div></div>',
                'link_before'   => '<button class="btn btn-small">',
                'link_after'    => '</button>',
                'separator'     => '',
            )
                    );
          ?>
        </div>

        <?php
        do_action( '__after_content' );

      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_page_content', $html );
    }



    /***************************
    * Page IMG SMARTLOAD HELP VIEW
    ****************************/
    /**
    * Displays a help block about images smartload for single posts prepended to the content
    * hook : the_content
    * @since Customizr 3.4+
    */
    function czr_fn_maybe_display_img_smartload_help( $the_content ) {
      if ( ! ( $this -> czr_fn_page_display_controller()  &&  in_the_loop() && CZR_placeholders::czr_fn_is_img_smartload_help_on( $the_content ) ) )
        return $the_content;

      return CZR_placeholders::czr_fn_get_smartload_help_block() . $the_content;
    }



    /******************************
    * SETTERS / HELPERS / CALLBACKS
    *******************************/
    /**
    * Page view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_page_display_controller() {
      $tc_show_page_content = 'page' == czr_fn__f('__post_type')
          && is_singular()
          && ! czr_fn__f( '__is_home_empty');

      return apply_filters( 'tc_show_page_content', $tc_show_page_content );
    }

  }//end of class
endif;

?><?php
/**
* Single post content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_post' ) ) :
  class CZR_post {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'wp'                , array( $this , 'czr_fn_set_single_post_hooks' ));
      //Set single post thumbnail with customizer options (since 3.2.0)
      add_action( 'wp'                , array( $this , 'czr_fn_set_single_post_thumbnail_hooks' ));

      //append inline style to the custom stylesheet
      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      add_filter( 'tc_user_options_style'    , array( $this , 'czr_fn_write_thumbnail_inline_css') );
    }


    /***************************
    * SINGLE POST AND THUMB HOOKS SETUP
    ****************************/
    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_single_post_hooks() {
      //add post header, content and footer to the __loop
      add_action( '__loop'              , array( $this , 'czr_fn_post_content' ));
      //posts parts actions
      add_action( '__after_content'     , array( $this , 'czr_fn_post_footer' ));
      //smartload help block
      add_filter( 'the_content'         , array( $this, 'czr_fn_maybe_display_img_smartload_help') , PHP_INT_MAX );

    }



    /**
    * hook : wp
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_single_post_thumbnail_hooks() {
      if ( $this -> czr_fn_single_post_display_controller() ) {
        add_action( '__before_content'        , array( $this, 'czr_fn_maybe_display_featured_image_help') );
      }

      //__before_main_wrapper, 200
      //__before_content 0
      //__before_content 20
      if ( ! $this -> czr_fn_show_single_post_thumbnail() )
        return;

      $_exploded_location   = explode('|', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_location' )) );
      $_hook                = apply_filters( 'tc_single_post_thumb_hook', isset($_exploded_location[0]) ? $_exploded_location[0] : '__before_content' );
      $_priority            = ( isset($_exploded_location[1]) && is_numeric($_exploded_location[1]) ) ? $_exploded_location[1] : 20;

      //Hook post view
      add_action( $_hook, array($this , 'czr_fn_single_post_prepare_thumb') , $_priority );
      //Set thumb shape with customizer options (since 3.2.0)
      add_filter( 'tc_post_thumb_wrapper'      , array( $this , 'czr_fn_set_thumb_shape'), 10 , 2 );
    }



    /***************************
    * SINGLE POST VIEW
    ****************************/
    /**
     * The default template for displaying single post content
     *
     * @package Customizr
     * @since Customizr 3.0
     */
    function czr_fn_post_content() {
      //check conditional tags : we want to show single post or single custom post types
      if ( ! $this -> czr_fn_single_post_display_controller() )
          return;
      //display an icon for div if there is no title
      $icon_class = in_array( get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' ) ) ? apply_filters( 'tc_post_format_icon', 'format-icon' ) :'' ;

      ob_start();
      do_action( '__before_content' );
        ?>
          <section class="<?php echo implode( ' ', apply_filters( 'tc_single_post_section_class', array( 'entry-content' ) ) ); ?> <?php echo $icon_class ?>">
              <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ); ?>
              <?php wp_link_pages( array( 'before' => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ), 'after' => '</div>' ) ); ?>
              <?php do_action( '__after_single_entry_inner' ); ?>
          </section><!-- .entry-content -->
        <?php
      do_action( '__after_content' );
      $html = ob_get_contents();
      if ($html) ob_end_clean();
      echo apply_filters( 'tc_post_content', $html );
    }



    /**
    * Single post footer view
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function czr_fn_post_footer() {
      //check conditional tags : we want to show single post or single custom post types
      if ( ! $this -> czr_fn_single_post_display_controller() || ! apply_filters( 'tc_show_single_post_footer', true ) )
          return;
      //@todo check if some conditions below not redundant?
      if ( ! is_singular() || ! get_the_author_meta( 'description' ) || ! apply_filters( 'tc_show_author_metas_in_post', true ) || ! esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_author_info' ) ) )
        return;

      $html = sprintf('<footer class="entry-meta">%1$s<div class="author-info"><div class="%2$s">%3$s %4$s</div></div></footer>',
                   '<hr class="featurette-divider">',

                  apply_filters( 'tc_author_meta_wrapper_class', 'row-fluid' ),

                  sprintf('<div class="%1$s">%2$s</div>',
                          apply_filters( 'tc_author_meta_avatar_class', 'comment-avatar author-avatar span2'),
                          get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'tc_author_bio_avatar_size' , 100 ) )
                    ),

                  sprintf('<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><div class="author-link">%4$s</div></div>',
                          apply_filters( 'tc_author_meta_content_class', 'author-description span10' ),
                          sprintf( __( 'About %s' , 'customizr' ), get_the_author() ),
                          get_the_author_meta( 'description' ),
                          sprintf( '<a href="%1$s" rel="author">%2$s</a>',
                            esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                            sprintf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>' , 'customizr' ), get_the_author() )
                          )
                    )
      );//end sprintf
      echo apply_filters( 'tc_post_footer', $html );
    }


    /***************************
    * SINGLE POST THUMBNAIL VIEW
    ****************************/
    /**
    * Get Single post thumb model + view
    * Inject it in the view
    * hook : esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_location' ) || '__before_content'
    * @return  void
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function czr_fn_single_post_prepare_thumb() {
      $_size_to_request = apply_filters( 'tc_single_post_thumb_size' , $this -> czr_fn_get_current_thumb_size() );
      //get the thumbnail data (src, width, height) if any
      //array( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" )
      $_thumb_model   = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model( $_size_to_request ) ;
      //may be render
      if ( CZR_post_thumbnails::$instance -> czr_fn_has_thumb() ) {
        $_thumb_class   = implode( " " , apply_filters( 'tc_single_post_thumb_class' , array( 'row-fluid', 'tc-single-post-thumbnail-wrapper', current_filter() ) ) );
        $this -> czr_fn_render_single_post_view( $_thumb_model , $_thumb_class );
      }
    }


    /**
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function czr_fn_render_single_post_view( $_thumb_model , $_thumb_class ) {
      echo apply_filters( 'tc_render_single_post_view',
        sprintf( '<div class="%1$s">%2$s</div>' ,
          $_thumb_class,
          CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model, 'span12', false )
        )
      );
    }


    /***************************
    * SINGLE POST THUMBNAIL HELP VIEW
    ****************************/
    /**
    * Displays a help block about featured images for single posts
    * hook : __before_content
    * @since Customizr 3.4
    */
    function czr_fn_maybe_display_featured_image_help() {
      if ( ! CZR_placeholders::czr_fn_is_thumbnail_help_on() )
        return;
      ?>
      <div class="tc-placeholder-wrap tc-thumbnail-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p><p>%3$s</p>',
              __( "You can display your post's featured image here if you have set one.", "customizr" ),
              sprintf( __("%s to display a featured image here.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', CZR_utils::czr_fn_get_customizer_url( array( "section" => "single_posts_sec") ), __( "Jump to the customizer now", "customizr") )
              ),
              sprintf( __( "Don't know how to set a featured image to a post? Learn how in the %s.", "customizr" ),
                sprintf('<a href="%1$s" title="%2$s" target="_blank">%2$s</a><span class="tc-external"></span>' , esc_url('codex.wordpress.org/Post_Thumbnails#Setting_a_Post_Thumbnail'), __("WordPress documentation" , "customizr" ) )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
    }

    /***************************
    * SINGLE POST IMG SMARTLOAD HELP VIEW
    ****************************/
    /**
    * Displays a help block about images smartload for single posts prepended to the content
    * hook : the_content
    * @since Customizr 3.4+
    */
    function czr_fn_maybe_display_img_smartload_help( $the_content ) {
      if ( ! ( $this -> czr_fn_single_post_display_controller()  &&  in_the_loop() && CZR_placeholders::czr_fn_is_img_smartload_help_on( $the_content ) ) )
        return $the_content;

      return CZR_placeholders::czr_fn_get_smartload_help_block() . $the_content;
    }




    /******************************
    * SETTERS / HELPERS / CALLBACKS
    *******************************/
    /**
    * Single post view controller
    * @return  boolean
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_single_post_display_controller() {
      //check conditional tags : we want to show single post or single custom post types
      global $post;
      $tc_show_single_post_content = isset($post)
        && 'page' != $post -> post_type
        && 'attachment' != $post -> post_type
        && is_singular()
        && ! czr_fn__f( '__is_home_empty');
      return apply_filters( 'tc_show_single_post_content', $tc_show_single_post_content );
    }


    /**
    * HELPER
    * @return boolean
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function czr_fn_show_single_post_thumbnail() {
      return $this -> czr_fn_single_post_display_controller() && apply_filters( 'tc_show_single_post_thumbnail', 'hide' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_location' ) ) );
    }


    /**
    * HELPER
    * @return size string
    * @package Customizr
    * @since Customizr 3.2.3
    */
    private function czr_fn_get_current_thumb_size() {
      $_exploded_location   = explode( '|', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_location' ) ) );
      $_hook                = isset( $_exploded_location[0] ) ? $_exploded_location[0] : '__before_content';
      return '__before_main_wrapper' == $_hook ? 'slider-full' : 'slider';
    }


    /**
    * hook : tc_post_thumb_wrapper
    * @return html string
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function czr_fn_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
      return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2$s">%3$s</a></div>',
            get_permalink( get_the_ID() ),
            esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
            $thumb_img,
            implode( " ", apply_filters( 'tc_thumb_wrapper_class', array() ) )
      );
    }


    /**
    * hook : tc_user_options_style
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_write_thumbnail_inline_css( $_css ) {
      if ( ! $this -> czr_fn_show_single_post_thumbnail() )
        return $_css;
      $_single_thumb_height   = apply_filters('tc_single_post_thumb_height', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_single_post_thumb_height' ) ) );
      $_single_thumb_height   = (! $_single_thumb_height || ! is_numeric($_single_thumb_height) ) ? 250 : $_single_thumb_height;
      return sprintf("%s\n%s",
        $_css,
        ".single .tc-rectangular-thumb {
          max-height: {$_single_thumb_height}px;
          height :{$_single_thumb_height}px
        }\n
        .tc-center-images.single .tc-rectangular-thumb img {
          opacity : 0;
          -webkit-transition: opacity .5s ease-in-out;
          -moz-transition: opacity .5s ease-in-out;
          -ms-transition: opacity .5s ease-in-out;
          -o-transition: opacity .5s ease-in-out;
          transition: opacity .5s ease-in-out;
        }\n"
      );
    }

  }//end of class
endif;

?><?php
/**
* Posts content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_post_list' ) ) :
class CZR_post_list {
  static $instance;
  function __construct () {
    self::$instance =& $this;
    //Set new image size can be set here ( => wp hook would be too late) (since 3.2.0)
    add_action( 'init'                    , array( $this, 'czr_fn_set_thumb_early_options') );
    //modify the query with pre_get_posts
    //! wp_loaded is fired after WordPress is fully loaded but before the query is set
    add_action( 'wp_loaded'               , array( $this, 'czr_fn_set_early_hooks') );
    //Set __loop hooks and customizer options (since 3.2.0)
    add_action( 'wp_head'                 , array( $this, 'czr_fn_set_post_list_hooks'));
    //append inline style to the custom stylesheet
    //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
    //fired on hook : wp_enqueue_scripts
    //Set thumbnail specific design based on user options
    add_filter( 'tc_user_options_style'   , array( $this , 'czr_fn_write_thumbnail_inline_css') );
  }



  /***************************
  * POST LIST HOOKS SETUP
  ****************************/
  /**
  * hook : init
  * @return void
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_set_thumb_early_options() {
    //Set thumb size depending on the customizer thumbnail position options (since 3.2.0)
    add_filter ( 'tc_thumb_size_name'     , array( $this , 'czr_fn_set_thumb_size') );
  }


  /**
  * Set __loop hooks and various filters based on customizer options
  * hook : wp_loaded
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_early_hooks() {
    //Filter home/blog postsa (priority 9 is to make it act before the grid hook for expanded post)
    add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_filter_home_blog_posts_by_tax' ), 9);
    //Include attachments in search results
    add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_include_attachments_in_search' ));
    //Include all post types in archive pages
    add_action ( 'pre_get_posts'         , array( $this , 'czr_fn_include_cpt_in_lists' ));
  }


  /**
  * Set __loop hooks and various filters based on customizer options
  * hook : wp_head
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_post_list_hooks() {
    if ( ! $this -> czr_fn_post_list_controller() )
      return;
    //displays the article with filtered layout : content + thumbnail
    add_action ( '__loop'               , array( $this , 'czr_fn_prepare_section_view') );

    //page help blocks
    add_filter( '__before_loop'         , array( $this , 'czr_fn_maybe_display_img_smartload_help') );

    //based on customizer user options
    add_filter( 'tc_post_list_layout'   , array( $this , 'czr_fn_set_post_list_layout') );
    add_filter( 'post_class'            , array( $this , 'czr_fn_set_content_class') );
    add_filter( 'excerpt_length'        , array( $this , 'czr_fn_set_excerpt_length') , 999 );
    add_filter( 'post_class'            , array( $this , 'czr_fn_add_thumb_shape_name') );

    //add current context to the body class
    add_filter( 'body_class'            , array( $this , 'czr_fn_add_post_list_context') );
    //Set thumb shape with customizer options (since 3.2.0)
    add_filter( 'tc_post_thumb_wrapper' , array( $this , 'czr_fn_set_thumb_shape'), 10 , 2 );

    add_filter( 'tc_the_content'        , array( $this , 'czr_fn_add_support_for_shortcode_special_chars') );

    // => filter the thumbnail inline style tc_post_thumb_inline_style and replace width:auto by width:100%
    // 3 args = $style, $_width, $_height
    add_filter( 'tc_post_thumb_inline_style'  , array( $this , 'czr_fn_change_thumbnail_inline_css_width'), 20, 3 );
  }


  /***************************
  * POST LIST MODEL
  ****************************/
  /**
  * Prepare default posts lists view
  * hook : __loop
  * inside loop
  * @package Customizr
  * @since Customizr 3.0.10
  */
  function czr_fn_prepare_section_view() {
    global $post;
    if ( ! isset( $post ) || empty( $post ) || ! apply_filters( 'tc_show_post_in_post_list', $this -> czr_fn_post_list_controller() , $post ) )
      return;

    //get the filtered post list layout
    $_layout        = apply_filters( 'tc_post_list_layout', CZR_init::$instance -> post_list_layout );
    $_content_model = $this -> czr_fn_get_content_model( $_layout );
    $_thumb_model   = $this -> czr_fn_show_thumb() ? CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model() : array();

    $this -> czr_fn_render_section_view( $_layout, $_content_model, $_thumb_model );
  }


  /**
  * Return the default post list model for the content
  * inside loop
  * @return array() "_layout" , "_show_thumb" , "_css_class"
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function czr_fn_get_content_model($_layout) {
    $_content      = '';
    if ( $this -> czr_fn_show_excerpt() )
      $_content = apply_filters( 'the_excerpt', get_the_excerpt() );
    else
      $_content = apply_filters( 'tc_the_content', get_the_content() );

    //what is determining the layout ? if no thumbnail then full width + filter's conditions
    $_layout_class = $this -> czr_fn_show_thumb() ? $_layout['content'] : 'span12';
    $_layout_class = implode( " " , apply_filters( 'tc_post_list_content_class', array($_layout_class) , $this -> czr_fn_show_thumb() , $_layout ) );

    //display an icon for div if there is no title
    $_icon_class    = in_array(get_post_format(), array(  'quote' , 'aside' , 'status' , 'link' )) ? apply_filters( 'tc_post_list_content_icon', 'format-icon' ) :'';

    return compact( "_layout_class" , "_icon_class" , "_content" );
  }




  /**
  * @return boolean whether excerpt instead of full content
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function czr_fn_show_excerpt() {
    //When do we show the post excerpt?
    //1) when set in options
    //2) + other filters conditions
    return (bool) apply_filters( 'tc_show_excerpt', 'full' != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_length' ) ) );
  }


  /**
  * @return boolean
  * @package Customizr
  * @since Customizr 3.3.2
  */
  private function czr_fn_show_thumb() {
    //when do we display the thumbnail ?
    //1) there must be a thumbnail
    //2) the excerpt option is not set to full
    //3) user settings in customizer
    //4) filter's conditions
    return apply_filters( 'tc_show_thumb', array_product(
        array(
          $this -> czr_fn_show_excerpt(),
          CZR_post_thumbnails::$instance -> czr_fn_has_thumb(),
          0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_show_thumb' ) )
        )
      )
    );
  }


  /***************************
  * POST LIST VIEW
  ****************************/
  /**
  * Render each post list section view
  *
  * @package Customizr
  * @since Customizr 3.0.10
  */
  private function czr_fn_render_section_view( $_layout, $_content_model, $_thumb_model ) {
    global $wp_query;
    //Renders the filtered layout for content + thumbnail
    if ( isset($_layout['alternate']) && $_layout['alternate'] ) {
      if ( 0 == $wp_query->current_post % 2 ) {
        $this -> czr_fn_render_content_view( $_content_model ) ;
        CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model , $_layout['thumb'] );
      }
      else {
        CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model , $_layout['thumb'] );
        $this -> czr_fn_render_content_view( $_content_model );
      }
    }
    else if ( isset($_layout['show_thumb_first']) && ! $_layout['show_thumb_first'] ) {
        $this -> czr_fn_render_content_view( $_content_model );
        CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model , $_layout['thumb'] );
    }
    else {
      CZR_post_thumbnails::$instance -> czr_fn_render_thumb_view( $_thumb_model , $_layout['thumb'] );
      $this -> czr_fn_render_content_view( $_content_model );
    }

    //renders the hr separator after each article
    echo apply_filters( 'tc_post_list_separator', '<hr class="featurette-divider '.current_filter().'">' );
  }



  /**
  * Displays the posts list content
  *
  * @package Customizr
  * @since Customizr 3.0
  */
  private function czr_fn_render_content_view( $_content_model ) {
    //extract "_layout_class" , "_icon_class" , "_content"
    extract($_content_model);
    $_sub_class = 'entry-summary';

    if ( in_array( get_post_format(), array( 'image' , 'gallery' ) ) )
    {
      $_sub_class = 'entry-content';
      $_content   = '<p class="format-icon"></p>';
    }
    elseif ( in_array( get_post_format(), array( 'quote', 'status', 'link', 'aside', 'video' ) ) )
    {
      $_sub_class = sprintf( 'entry-content %s' , $_icon_class );
      $_content   = sprintf( '%1$s%2$s',
        apply_filters( 'tc_the_content', get_the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>' , 'customizr' ) ) ),
        wp_link_pages( array(
          'before'  => '<div class="pagination pagination-centered">' . __( 'Pages:' , 'customizr' ),
          'after'   => '</div>',
          'echo'    => 0
          ) )
      );
    }

    ob_start();
    ?>
    <section class="tc-content <?php echo $_layout_class; ?>">
      <?php
        do_action( '__before_content' );

          printf('<section class="%1$s">%2$s</section>',
            $_sub_class,
            $_content
          );

        do_action( '__after_content' );
    ?>
    </section>
    <?php
    $_html = ob_get_contents();
    if ($_html) ob_end_clean();
    echo apply_filters( 'tc_post_list_content', $_html, $_content_model );
  }



  /******************************
  * SETTERS / HELPERS / CALLBACKS
  *******************************/
  /**
  * hook : tc_post_thumb_wrapper
  * ! 2 cases here : posts lists and single posts
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_thumb_shape( $thumb_wrapper, $thumb_img ) {
    $_shape = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape') );

    //1) check if shape is rounded, squared on rectangular
    if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
      return $thumb_wrapper;

    $_position = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_position' ) );
    return sprintf('<div class="%4$s"><a class="tc-rectangular-thumb" href="%1$s" title="%2s">%3$s</a></div>',
          get_permalink( get_the_ID() ),
          esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
          $thumb_img,
          ( 'top' == $_position || 'bottom' == $_position ) ? '' : implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('') ) )
    );
  }


  /**
  * hook : body_class
  * @return  array of classes
  *
  * @package Customizr
  * @since Customizr 3.3.2
  */
  function czr_fn_add_post_list_context( $_classes ) {
    return array_merge( $_classes , array( 'tc-post-list-context' ) );
  }


  /**
  * @return  bool
  * Controller of the posts list view
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  public function czr_fn_post_list_controller() {
    global $wp_query;
    //must be archive or search result. Returns false if home is empty in options.
    return apply_filters( 'tc_post_list_controller',
      ! is_singular()
      && ! is_404()
      && 0 != $wp_query -> post_count
      && ! czr_fn__f( '__is_home_empty')
    );
  }


  /**
  * hook : pre_get_posts
  * Includes Custom Posts Types (set to public and excluded_from_search_result = false) in archives and search results
  * In archives, it handles the case where a CPT has been registered and associated with an existing built-in taxonomy like category or post_tag
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.1.20
  */
  function czr_fn_include_cpt_in_lists( $query ) {
    if (
      is_admin()
      || ! $query->is_main_query()
      || ! apply_filters('tc_include_cpt_in_archives' , false)
      || ! ( $query->is_search || $query->is_archive )
      )
      return;

    //filter the post types to include, they must be public and not excluded from search
    //we also exclude the built-in types, to exclude pages and attachments, we'll add standard posts later
    $post_types         = get_post_types( array( 'public' => true, 'exclude_from_search' => false, '_builtin' => false) );

    //add standard posts
    $post_types['post'] = 'post';
    if ( $query -> is_search ){
      // add standard pages in search results => new wp behavior
      $post_types['page'] = 'page';
      // allow attachments to be included in search results by tc_include_attachments_in_search method
      if ( apply_filters( 'tc_include_attachments_in_search_results' , false ) )
        $post_types['attachment'] = 'attachment';
    }

    // add standard pages in search results
    $query->set('post_type', $post_types );
  }


  /**
  * hook : pre_get_posts
  * Includes attachments in search results
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.0.10
  */
  function czr_fn_include_attachments_in_search( $query ) {
      if (! is_search() || ! apply_filters( 'tc_include_attachments_in_search_results' , false ) )
        return;

      // add post status 'inherit'
      $post_status = $query->get( 'post_status' );
      if ( ! $post_status || 'publish' == $post_status )
        $post_status = array( 'publish', 'inherit' );
      if ( is_array( $post_status ) )
        $post_status[] = 'inherit';

      $query->set( 'post_status', $post_status );
  }

  /**
  * hook : pre_get_posts
  * Filter home/blog posts by tax: cat
  * @return modified query object
  * @package Customizr
  * @since Customizr 3.4.10
  */
  function czr_fn_filter_home_blog_posts_by_tax( $query ) {
      // when we have to filter?
      // in home and blog page
      if (
        ! $query->is_main_query()
        || ! ( ( is_home() && 'posts' == get_option('show_on_front') ) || $query->is_posts_page )
      )
        return;

     // categories
     // we have to ignore sticky posts (do not prepend them)
     // disable grid sticky post expansion
     $cats = CZR_utils::$inst -> czr_fn_opt('tc_blog_restrict_by_cat');
     $cats = array_filter( $cats, array( CZR_utils::$inst , 'czr_fn_category_id_exists' ) );

     if ( is_array( $cats ) && ! empty( $cats ) ){
         $query->set('category__in', $cats );
         $query->set('ignore_sticky_posts', 1 );
         add_filter('tc_grid_expand_featured', '__return_false');
     }
  }
  /**
  * Callback of filter post_class
  * @return  array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_add_thumb_shape_name( $_classes ) {
    return array_merge( $_classes , array(esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape') ) ) );
  }


  /**
  * hook : excerpt_length hook
  * @return string
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_excerpt_length( $length ) {
    $_custom = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_excerpt_length' ) );
    return ( false === $_custom || !is_numeric($_custom) ) ? $length : $_custom;
  }


  /**
  * hook : tc_post_list_layout
  * @return array() of layout data
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_post_list_layout( $_layout ) {
    $_position                  = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_position' ) );
    //since 3.4.16 the alternate layout is not available when the position is top or bottom
    $_layout['alternate']        = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_alternate' ) )
                                   || in_array( $_position, array( 'top', 'bottom') ) ) ? false : true;
    $_layout['show_thumb_first'] = ( 'left' == $_position || 'top' == $_position ) ? true : false;
    $_layout['content']          = ( 'left' == $_position || 'right' == $_position ) ? $_layout['content'] : 'span12';
    $_layout['thumb']            = ( 'top' == $_position || 'bottom' == $_position ) ? 'span12' : $_layout['thumb'];
    return $_layout;
  }


  /**
  * hook : WP filter post_class
  * @return array() of classes
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_content_class( $_classes ) {
    $_position                  = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_position' ) );
    return array_merge( $_classes , array( "thumb-position-{$_position}") );
  }


  /**
  * hook tc_post_thumb_inline_style (declared in CZR_post_thumbnails)
  * Replace default widht:auto by width:100%
  * @param array of args passed by apply_filters_ref_array method
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_change_thumbnail_inline_css_width( $_style,  $image, $_filtered_thumb_size) {
    //conditions :
    //note : handled with javascript if tc_center_img option enabled
    $_bool = array_product(
      array(
        ! esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_img') ),
        false != $image,
        ! empty($image),
        isset($_filtered_thumb_size['width']),
        isset($_filtered_thumb_size['height'])
      )
    );
    if ( ! $_bool )
      return $_style;

    $_width     = $_filtered_thumb_size['width'];
    $_height    = $_filtered_thumb_size['height'];
    $_shape     = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape') );
    $_is_rectangular = ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') ? false : true;
    if ( ! is_single() && ! $_is_rectangular )
      return $_style;

    return sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width:100%%;max-height: none;', $_width, $_height );
  }


  /**
  * hook : tc_user_options_style
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_write_thumbnail_inline_css( $_css ) {
    if ( ! $this -> czr_fn_post_list_controller() )
      return $_css;
    $_list_thumb_height     = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_height' ) );
    $_list_thumb_height     = (! $_list_thumb_height || ! is_numeric($_list_thumb_height) ) ? 250 : $_list_thumb_height;

    return sprintf("%s\n%s",
      $_css,
      ".tc-rectangular-thumb {
        max-height: {$_list_thumb_height}px;
        height :{$_list_thumb_height}px
      }\n"
    );
  }


  /**
  * hook : tc_thumb_size_name (declared in CZR_post_thumbnails)
  *
  * @package Customizr
  * @since Customizr 3.2.0
  */
  function czr_fn_set_thumb_size( $_default_size ) {
    $_shape = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_shape') );
    if ( ! $_shape || false !== strpos($_shape, 'rounded') || false !== strpos($_shape, 'squared') )
      return $_default_size;

    $_position                  = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_thumb_position' ) );
    return ( 'top' == $_position || 'bottom' == $_position ) ? 'tc_rectangular_size' : $_default_size;
  }


  /**
  * hook : tc_the_content
  * Applies tc_the_content filter to the passed string
  *
  * @param string
  * @return  string
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_add_support_for_shortcode_special_chars( $_content ) {
    return str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $_content ) );
  }


  /***************************
  * LIST OF POSTS IMG SMARTLOAD HELP VIEW
  ****************************/
  /**
  * Displays a help block about images smartload for list of posts before the actual list
  * hook : __before_loop
  * @since Customizr 3.4+
  */
  function czr_fn_maybe_display_img_smartload_help( $the_content ) {
    if ( ! ( $this -> czr_fn_post_list_controller() && CZR_placeholders::czr_fn_is_img_smartload_help_on( $text = '', $min_img_num = 0 ) ) )
      return;

    CZR_placeholders::czr_fn_get_smartload_help_block( $echo = true );
  }

}//end of class
endif;

?><?php
/**
* Post lists grid content actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.2.11
* @author       Rocco Aliberti <rocco@presscustomizr.com>, Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2015, Rocco Aliberti, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_post_list_grid' ) ) :
    class CZR_post_list_grid {
        static $instance;
        private $expanded_sticky;
        private $post_id;

        function __construct () {
          self::$instance =& $this;
          $this -> expanded_sticky = null;

          add_action ( 'pre_get_posts'              , array( $this , 'czr_fn_maybe_excl_first_sticky') );
          add_action ( 'wp_head'                    , array( $this , 'czr_fn_set_grid_hooks') );

          //Font size filter
          //Updates the array of font sizes for a given sidebar layout
          add_filter( 'tc_get_grid_font_sizes'      , array( $this , 'czr_fn_set_layout_font_size' ), 10, 4 );

          //append inline style to the custom stylesheet
          //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
          //fired on hook : wp_enqueue_scripts
          add_filter( 'tc_user_options_style'       , array( $this , 'czr_fn_grid_write_inline_css'), 100 );
        }


        /***************************************
        * HOOKS SETTINGS ***********************
        ****************************************/
        /*
        * hook : wp
        */
        function czr_fn_set_grid_hooks(){
          if ( ! apply_filters( 'tc_set_grid_hooks' , $this -> czr_fn_is_grid_enabled() ) )
              return;

          $this -> post_id = CZR_utils::czr_fn_id();

          do_action( '__post_list_grid' );
          //Disable icon titles
          //add_filter( 'tc_archive_icon'             , '__return_false', 50 );
          //disable edit link (it's added afterwards) for the expanded post
          add_filter( 'tc_edit_in_title'            , array( $this, 'czr_fn_grid_disable_edit_in_title_expanded' ) );

          add_filter( 'tc_content_title_icon'       , '__return_false', 50 );
          //icon option
          add_filter( 'tc-grid-thumb-html'          , array( $this, 'czr_fn_set_grid_icon_visibility') );
          //Layout filter
          add_filter( 'tc_get_grid_cols'            , array( $this, 'czr_fn_set_grid_section_cols'), 20 , 2 );
          //pre loop hooks
          add_action( '__before_article_container'  , array( $this, 'czr_fn_set_grid_before_loop_hooks'), 5 );
          //loop hooks
          add_action( '__before_loop'               , array( $this, 'czr_fn_set_grid_loop_hooks'), 0 );
        }


        /* PRE LOOP HOOKS
        * hook : __before_article_container
        * before loop
        */
        function czr_fn_set_grid_before_loop_hooks(){
          // LAYOUT
          add_filter( 'tc_post_list_layout'         , array( $this, 'czr_fn_grid_set_content_layout') );
          add_filter( 'tc_post_list_selectors'      , array( $this, 'czr_fn_grid_set_article_selectors') );
          add_action( '__before_article_container'  , array( $this, 'czr_fn_grid_prepare_expand_sticky' ) );

          // THUMBNAILS
          remove_filter( 'post_class'               , array( CZR_post_list::$instance , 'czr_fn_add_thumb_shape_name'));
          remove_filter( 'tc_thumb_size_name'       , array( CZR_post_thumbnails::$instance, 'czr_fn_set_thumb_size') );
          add_filter( 'tc_thumb_size_name'          , array( $this, 'czr_fn_set_thumb_size_name') );
          add_filter( 'tc_thumb_size'               , array( $this, 'czr_fn_set_thumb_size') );

          // SINGLE POST CONTENT IN GRID
          $_content_priorities = apply_filters('tc_grid_post_content_priorities' , array( 'content' => 20, 'link' =>30 ));
          add_action( '__grid_single_post_content'  , array( $this, 'czr_fn_grid_display_figcaption_content') , $_content_priorities['content'] );
          add_action( '__grid_single_post_content'  , array( $this, 'czr_fn_grid_display_post_link'), $_content_priorities['link'] );
          add_action( '__grid_single_post_content'  , array( $this, 'czr_fn_grid_display_fade_excerpt'), 100 );
          //expanded sticky post : filter the figcaption content to include the post title
          add_filter( 'tc_grid_display_figcaption_content' , array( $this, 'czr_fn_grid_set_expanded_post_title') );

          //ARTICLE CONTAINER CSS CLASSES TO HANDLE EFFECT LIKE SHADOWS
          add_filter( 'tc_article_container_class'  , array( $this, 'czr_fn_grid_container_set_classes' ) );

          //COMMENT BUBBLE
          remove_filter( 'tc_the_title'             , array( CZR_comments::$instance, 'czr_fn_display_comment_bubble' ) , 1 );
          add_filter( 'tc_grid_get_single_post_html'  , array( $this, 'czr_fn_grid_display_comment_bubble' ) );

          //POST METAS
          remove_filter( 'tc_meta_utility_text'     , array( CZR_post_metas::$instance , 'czr_fn_add_link_to_post_after_metas'), 20 );

          //TITLE LENGTH
          add_filter( 'tc_title_text'               , array( $this, 'czr_fn_grid_set_title_length' ) );
        }


        /**
        * hook : __before_loop
        * actions and filters inside loop
        * @return  void
        */
        function czr_fn_set_grid_loop_hooks() {
          add_action( '__before_article'            , array( $this, 'czr_fn_print_row_fluid_section_wrapper' ), 1 );
          add_action( '__after_article'             , array( $this, 'czr_fn_print_article_sep' ), 0 );
          add_action( '__after_article'             , array( $this, 'czr_fn_print_row_fluid_section_wrapper' ), 1 );

          remove_action( '__loop'                   , array( CZR_post_list::$instance, 'czr_fn_prepare_section_view') );
          add_action( '__loop'                      , array( $this, 'czr_fn_grid_prepare_single_post') );

          if ( CZR_headings::$instance -> czr_fn_is_edit_enabled() && apply_filters( 'tc_grid_render_expanded_edit_link', true ) )
            add_filter( 'tc_grid_get_single_post_html' , array( $this, 'czr_fn_grid_render_expanded_edit_link' ), 50 );
        }



        /******************************************
        * PREPARE AND RENDER VIEWS ****************
        ******************************************/
        /*
        * hook : __before_article
        * Wrap articles in a grid section
        */
        function czr_fn_print_row_fluid_section_wrapper(){
          global $wp_query;
          $current_post   = $wp_query -> current_post;
          $start_post     = $this -> expanded_sticky ? 1 : 0;
          $cols           = $this -> czr_fn_get_grid_section_cols();

          if ( '__before_article' == current_filter() &&
              ( $start_post == $current_post ||
                  0 == ( $current_post - $start_post ) % $cols ) ) {
            printf( '<section class="%s">',
              implode( " ", apply_filters( 'tc_grid_section_class' ,  array( "row-fluid", "grid-cols-{$cols}" ) ) )
            );
          }
          elseif ( '__after_article' == current_filter() &&
                    ( $wp_query->post_count == ( $current_post + 1 ) ||
                    0 == ( ( $current_post - $start_post + 1 ) % $cols ) ) ) {
              printf( '</section><!--end section.row-fluid-->%s',
                apply_filters( 'tc_grid_separator', '<hr class="featurette-divider post-list-grid">')
              );
          }//end if
        }



        /**
        * hook : __loop
        * Prepare single post view model
        * inject it in the single post view
        * @return the figcation content parts as an array of html strings
        * inside loop
        */
        function czr_fn_grid_prepare_single_post() {
          global $post;
          if ( ! isset($post) || empty($post) || ! apply_filters( 'tc_show_post_in_post_list', $this -> czr_fn_is_grid_context_matching() , $post ) )
            return;

          // get the filtered post list layout
          $_layout   = apply_filters( 'tc_post_list_layout', CZR_init::$instance -> post_list_layout );

          // SET HOOKS FOR POST TITLES AND METAS
          // Default condition : must be a non sticky post
          if ( apply_filters( 'tc_render_grid_headings_view' , ! $this -> czr_fn_force_current_post_expansion() ) ) {
              $hook_prefix = '__before';
              if ( $_layout['show_thumb_first'] )
                  $hook_prefix = '__after';

              add_action( "{$hook_prefix}_grid_single_post",  array( CZR_headings::$instance, 'czr_fn_render_headings_view' ) );
          }

          // THUMBNAIL : cache the post format icon first
          //add thumbnail html (src, width, height) if any
          $_thumb_html = '';
          if ( $this -> czr_fn_grid_show_thumb() ) {
            //return an array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
            $_thumb_model = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model();
            if ( isset($_thumb_model['tc_thumb']) )
              $_thumb_html  = $_thumb_model['tc_thumb'];
          }
          $_thumb_html = apply_filters( 'tc-grid-thumb-html' , $_thumb_html );

          // CONTENT : get the figcaption content => post content
          $post_list_content_class          = array(
              isset( $_layout['content'] ) ? $_layout['content'] : 'span6',
              CZR___::czr_fn_is_pro() ? '' : 'mask'//no css mask for the pro grid
          );
          $_post_content_html               = $this -> czr_fn_grid_get_single_post_html( implode( ' ', $post_list_content_class ) );

          // ADD A WRAPPER CLASS : build single grid post wrapper class
          $_classes  = array('tc-grid-figure');
          //may be add class no-thumb
          if ( ! $this -> czr_fn_grid_show_thumb() )
            array_push( $_classes, 'no-thumb' );
          else
            array_push( $_classes, 'has-thumb' );

          //if 1 col layout or current post is the expanded => golden ratio should be disabled
          if ( ( '1' == $this -> czr_fn_get_grid_cols() || $this -> czr_fn_force_current_post_expansion() ) && ! wp_is_mobile() )
            array_push( $_classes, 'no-gold-ratio' );

          $_classes  = implode( ' ' , apply_filters('tc_single_grid_post_wrapper_class', $_classes ) );

          //RENDER VIEW
          $this -> czr_fn_grid_render_single_post( $_classes, $_thumb_html, $_post_content_html );
          //return apply_filters( 'tc_prepare_grid_single_post_content' , compact( '_classes', '_thumb_html', '_post_content_html') );
        }


        /**
        * Single post view in the grid
        * display single post content + thumbnail
        * @return html string
        *
        */
        private function czr_fn_grid_render_single_post( $_classes, $_thumb_html, $_post_content_html ) {
          ob_start();
            do_action( '__before_grid_single_post');//<= open <section> and maybe display title + metas

              echo apply_filters( 'tc_grid_single_post_thumb_content',
                sprintf('<section class="tc-grid-post"><figure class="%1$s">%2$s %3$s</figure></section>',
                  $_classes,
                  $_thumb_html,
                  $_post_content_html
                )
              );
            do_action('__after_grid_single_post');//<= close </section> and maybe display title + metas

          $html = ob_get_contents();
          if ($html) ob_end_clean();

          echo apply_filters('tc_grid_display', $html);
        }


        /**
        * hook : __grid_single_post_content
        */
        function czr_fn_grid_display_post_link(){
          if ( ! apply_filters( 'tc_grid_display_post_link' , true ) )
            return;
          printf( '<a class="tc-grid-bg-link" href="%1$s" title="%2$s"></a>',
              get_permalink( get_the_ID() ),
              esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ) );
        }



        /**
        * hook : __grid_single_post_content
        */
        function czr_fn_grid_display_fade_excerpt(){
          if ( ! apply_filters( 'tc_grid_fade_excerpt' , ! $this -> czr_fn_force_current_post_expansion() ) )
            return;
          printf( '<span class="tc-grid-fade_expt"></span>' );
        }



        /*
        * hook : __grid_single_post_content
        */
        function czr_fn_grid_display_figcaption_content() {
          ?>
              <div class="entry-summary">
                <?php
                  echo apply_filters( 'tc_grid_display_figcaption_content',
                    sprintf('<div class="tc-g-cont">%s</div>',
                      get_the_excerpt()
                    )
                  );
                ?>
              </div>
          <?php
        }


        /**
        * Separator after each grid article
        * hook : __after_article (declared in index.php)
        * print a separator after each article => revealed in responsive mode
        */
        function czr_fn_print_article_sep() {
          //renders the hr separator after each article
          echo apply_filters( 'tc_grid_single_post_sep', '<hr class="featurette-divider '.current_filter().'">' );
        }



        /******************************************
        * SETTERS / GETTTERS / CALLBACKS
        ******************************************/
        /**
        * hook : tc_title_text
        * Limits the length of the post titles in grids to a custom number of characters
        * @return string
        */
        function czr_fn_grid_set_title_length( $_title ) {
          $_max = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_num_words') );
          $_max = ( empty($_max) || ! $_max ) ? 10 : $_max;
          $_max = $_max <= 0 ? 1 : $_max;

          if ( empty($_title) || ! is_string($_title) )
            return $_title;

          if ( count( explode( ' ', $_title ) ) > $_max ) {
            $_words = array_slice( explode( ' ', $_title ), 0, $_max );
            $_title = sprintf( '%s ...',
              implode( ' ', $_words )
            );
          }
          return $_title;
        }


        /**
        * hook : pre_get_posts
        * exclude the first sticky post
        */
        function czr_fn_maybe_excl_first_sticky( $query ){
          if ( $this -> czr_fn_is_grid_enabled() && $this -> czr_fn_is_sticky_expanded( $query ) )
            $query->set('post__not_in', array( $this -> expanded_sticky ) );
        }


        /**
        * hook : tc_post_list_layout
        * force content + thumb layout : Force the title to be displayed always on bottom
        * @param current layout array()
        */
        function czr_fn_grid_set_content_layout( $_layout ){
          $_layout['show_thumb_first'] = true;
          $_layout['content']          = 'tc-grid-excerpt';
          $_layout['thumb']            = 'span12 tc-grid-post-container';

          return $_layout;
        }


        /**
        * Grid columns = fn(current-layout)
        * Returns the max possible grid column number for a given layout
        *
        * @param $_col_nb = string possible values : 1, 2, 3, 4
        * @param $_current_layout string of layout class like span4
        */
        function czr_fn_set_grid_section_cols( $_col_nb, $_current_layout ) {
          $_map = apply_filters(
            'tc_grid_col_layout_map',
            array(
              'span12'  => '4',//no sidebars
              'span11'  => '4',
              'span10'  => '4',
              'span9'   => '3',//one sidebar right or left
              'span8'   => '3',
              'span7'   => '2',
              'span6'   => '2',//two sidebars
              'span5'   => '2',
              'span4'   => '1',
              'span3'   => '1',
              'span2'   => '1',
              'span1'   => '1',
            )
          );
          if ( ! isset($_map[$_current_layout]) )
            return $_col_nb;
          if ( (int) $_map[$_current_layout] >= (int) $_col_nb )
            return (string) $_col_nb;
          return (string) $_map[$_current_layout];
        }



        /**
        * Apply proper class to articles selectors to control articles width
        * hook : tc_post_list_selectors
        */
        function czr_fn_grid_set_article_selectors($selectors){
          $_class = sprintf( '%1$s tc-grid span%2$s',
            apply_filters( 'tc_grid_add_expanded_class', $this -> czr_fn_force_current_post_expansion() ) ? 'expanded' : '',
            is_numeric( $this -> czr_fn_get_grid_section_cols() ) ? 12 / $this -> czr_fn_get_grid_section_cols() : 6
          );
          return str_replace( 'row-fluid', $_class, $selectors );
        }


        /*
        * hook : __before_article_container
        */
        function czr_fn_grid_prepare_expand_sticky(){
          global $wp_query;
          if ( ! ( $this -> czr_fn_is_sticky_expanded() &&
                 $wp_query -> query_vars[ 'paged' ] == 0 ) ){
            $this -> expanded_sticky = null;
            return;
          }
          // prepend the first sticky
          $first_sticky = get_post( $this -> expanded_sticky );
          array_unshift( $wp_query -> posts, $first_sticky );
          $wp_query -> post_count = $wp_query -> post_count + 1;
        }


        /*
        * hook : tc_thumb_size_name
        */
        function czr_fn_set_thumb_size_name(){
          return ( $this -> czr_fn_get_grid_section_cols() == '1' ) ? 'tc-grid-full' : 'tc-grid';
        }


        /*
        * hook : tc_thumb_size
        */
        function czr_fn_set_thumb_size(){
          $thumb = ( $this -> czr_fn_get_grid_section_cols() == '1' ) ? 'tc_grid_full_size' : 'tc_grid_size';
          return CZR_init::$instance -> $thumb;
        }


        /**
        * hook : tc_article_container_class
        * inside loop
        * add custom classes to the grid .article-container element
        */
        function czr_fn_grid_container_set_classes( $_classes ) {
          array_push( $_classes, 'tc-post-list-grid' );
          if ( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_shadow') ) )
            array_push( $_classes, 'tc-grid-shadow' );
          if ( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_bottom_border') ) )
            array_push( $_classes, 'tc-grid-border' );
          return $_classes;
        }


        /**
        * @return the figcation content as a string
        * @param  $post_list_content_class string
        * inside loop
        */
        private function czr_fn_grid_get_single_post_html( $post_list_content_class ) {
          global $post;
          ob_start();
            ?>
              <figcaption class="<?php echo $post_list_content_class ?>">
                <?php do_action( '__grid_single_post_content' ) ?>
              </figcaption>
            <?php
          $html = ob_get_contents();
          if ($html) ob_end_clean();
          return apply_filters( 'tc_grid_get_single_post_html', $html, $post_list_content_class );
        }


        /**
        * hook : tc_grid_get_single_post_html
        * @return the comment_bubble as a string
        * inside loop
        */
        function czr_fn_grid_display_comment_bubble( $_html ) {
          return CZR_comments::$instance -> czr_fn_display_comment_bubble() . $_html;
        }



        /**
        * hook : __grid_single_post_content
        * @return  html string
        * hook : tc_grid_display_figcaption_content
        */
        function czr_fn_grid_set_expanded_post_title( $_html ){
          if ( ! $this -> czr_fn_force_current_post_expansion() )
              return $_html;
          global $post;
          $_title = apply_filters( 'tc_grid_expanded_title' , $post->post_title );
          $_title = apply_filters( 'tc_the_title'           , $_title );
          $_title = apply_filters( 'tc_grid_expanded_title_html', sprintf('<h2 class="entry-title">%1$s</h2>',
              $_title
          ) );
          return $_html . $_title;
        }


        /**
        * @return  bool
        * hook : tc_edit_in_title
        * @since Customizr 3.4.18
        */
        function czr_fn_grid_disable_edit_in_title_expanded( $_bool ){
          return $this -> czr_fn_force_current_post_expansion() ? false : $_bool;
        }


        /**
        * Append the edit link to the expanded post figcaption
        * hook : tc_grid_get_single_post_html
        * @since Customizr 3.4.18
        */
        function czr_fn_grid_render_expanded_edit_link( $_html ) {
          if ( $this -> czr_fn_force_current_post_expansion() )
            $_html .= CZR_headings::$instance -> czr_fn_render_edit_link_view( $_echo = false );
          return $_html;
        }


        /**
        * @return css string
        * hook : tc_user_options_style
        * @since Customizr 3.2.18
        */
        function czr_fn_grid_write_inline_css( $_css ){
          if ( ! $this -> czr_fn_is_grid_enabled() )
            return $_css;

          $_col_nb  = $this -> czr_fn_get_grid_cols();

          //GENERATE THE FIGURE HEIGHT CSS
          $_current_col_figure_css  = $this -> czr_fn_grid_get_figure_css( $_col_nb );

          //GENERATE THE MEDIA QUERY CSS FOR FONT-SIZES
          $_current_col_media_css   = $this -> czr_fn_get_grid_font_css( $_col_nb );

          $_css = sprintf("%s\n%s\n%s\n",
              $_css,
              $_current_col_media_css,
              $_current_col_figure_css
          );
          return $_css;
        }



        /**
        * hook : tc-grid-thumb-html
        * @return modified html string
        */
        function czr_fn_set_grid_icon_visibility( $_html ) {
          $_icon_enabled = (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_icons') );
          if ( CZR___::$instance -> czr_fn_is_customizing() )
            return sprintf('<div class="tc-grid-icon format-icon" style="display:%1$s"></div>%2$s',
                $_icon_enabled ? 'inline-block' : 'none',
                $_html
            );
          if ( $_icon_enabled )
            return sprintf('<div class="tc-grid-icon format-icon"></div>%1$s',
                $_html
            );
          else
            return $_html;
        }



        /******************************
        HELPERS FOR INLINE CSS
        *******************************/
        /**
        * @param (string) $col_layout
        * @return css media query string
        * Returns the paragraph and title media queries for a given layout
        */
        private function czr_fn_get_grid_font_css( $_col_nb = '3' ) {
          $_media_queries     = $this -> czr_fn_get_grid_media_queries();//returns the simple array of media queries
          $_grid_font_sizes = $this -> czr_fn_get_grid_font_sizes( $_col_nb );//return the array of sizes (ordered by @media queries) for a given column layout
          $_col_rules         = array();
          $_media_queries_css = '';

          //flatten the matrix
          foreach ($_media_queries as $key => $_med_query_sizes ) {
            $_size = $_grid_font_sizes[$key];//=> size like 'xxl'
            $_css_prop = array(
              'h' => $this -> czr_fn_grid_build_css_rules( $_size , 'h' ),
              'p' => $this -> czr_fn_grid_build_css_rules( $_size , 'p' )
            );
            $_rules = $this -> czr_fn_grid_assign_css_rules_to_selectors( $_med_query_sizes , $_css_prop, $_col_nb );
            $_media_queries_css .= "
              @media {$_med_query_sizes} {{$_rules}}
            ";
          }
          return $_media_queries_css;
        }


        /**
        * @return simple array of media queries
        */
        private function czr_fn_get_grid_media_queries() {
          return apply_filters( 'tc_grid_media_queries' ,  array(
              '(min-width: 1200px)', '(max-width: 1199px) and (min-width: 980px)', '(max-width: 979px) and (min-width: 768px)', '(max-width: 767px)', '(max-width: 480px)'
            )
          );
        }



        /**
        * Return the array of sizes (ordered by @media queries) for a given column layout
        * @param  $_col_nb string
        * @param  $_requested_media_size
        * @return array()
        * Note : When all sizes are requested (default case), the returned array can be filtered with the current layout param
        * Size array must have the same length of the media query array
        */
        private function czr_fn_get_grid_font_sizes( $_col_nb = '3', $_requested_media_size = null ) {
          $_col_media_matrix = apply_filters( 'tc_grid_font_matrix' , array(
              //=> matrix col nb / media queries
              //            1200 | 1199-980 | 979-768 | 767   | 480
              '1' => array( 'xxxl', 'xxl'   , 'xl'    , 'xl'  , 'l' ),
              '2' => array( 'xxl' , 'xl'    , 'l'     , 'xl'  , 'l' ),
              '3' => array( 'xl'  , 'l'     , 'm'     , 'xl'  , 'l' ),
              '4' => array( 'l'   , 'm'     , 's'     , 'xl'  , 'l' )
            )
          );
          //if a specific media query is requested, return a string
          if ( ! is_null($_requested_media_size) ) {
            $_media_queries = $this -> czr_fn_get_grid_media_queries();
            //get the key = position of requested size in the current layout
            $_key = array_search( $_requested_media_size, $_media_queries);
            return apply_filters(
              'tc_get_layout_single_font_size',
              isset($_col_media_matrix[$_col_nb][$_key]) ? $_col_media_matrix[$_col_nb][$_key] : 'xl'
            );
          }

          return apply_filters(
            'tc_get_grid_font_sizes',
            isset($_col_media_matrix[$_col_nb]) ? $_col_media_matrix[$_col_nb] : array( 'xl' , 'l' , 'm', 'l', 'm' ),
            $_col_nb,
            $_col_media_matrix,
            CZR_utils::czr_fn_get_layout( $this -> post_id , 'class' )
          );
        }



        /**
        * hook : 'tc_get_grid_font_sizes'
        * Updates the array of sizes for a given sidebar layout
        * @param  $_sizes array. ex : array( 'xl' , 'l' , 'm', 'l', 'm' )
        * @param  $_col_nb string. Ex: '2'
        * @param  $_col_media_matrix : array() matrix 5 x 4 => media queries / Col_nb
        * @param  $_current_layout string. Ex : 'span9'
        * @return array()
        */
        function czr_fn_set_layout_font_size( $_sizes, $_col_nb, $_col_media_matrix, $_current_layout ) {
          //max possible font size key in the col_media_queries matrix for a given sidebar layout
          $_map = apply_filters(
            'tc_layout_font_size_map',
            array(
              'span12'  => '1',//no sidebars
              'span11'  => '1',
              'span10'  => '1',
              'span9'   => '2',//one sidebar right or left
              'span8'   => '2',
              'span7'   => '3',
              'span6'   => '4',//two sidebars
              'span5'   => '4',
              'span4'   => '4',
              'span3'   => '4',
              'span2'   => '4',
              'span1'   => '4',
            )
          );
          if ( ! isset($_map[$_current_layout]) )
            return $_sizes;
          if ( (int) $_col_nb >= (int) $_map[$_current_layout] )
            return $_sizes;

          $_new_key = $_map[$_current_layout];
          return $_col_media_matrix[$_new_key];
        }



        /**
        * @return css string
        * @param size string
        * @param selector type string
        * returns ratio of size / body size for a given selector type ( headings or paragraphs )
        */
        private function czr_fn_get_grid_font_ratios( $_size = 'xl' , $_sel = 'h' ) {
          $_ratios =  apply_filters( 'tc_get_grid_font_ratios' , array(
              'xxxl' => array( 'h' => 2.10, 'p' => 1 ),
              'xxl' => array( 'h' => 1.86, 'p' => 1 ),
              'xl' => array( 'h' => 1.60, 'p' => 0.93 ),
              'l' => array( 'h' => 1.30, 'p' => 0.85 ),
              'm' => array( 'h' => 1.15, 'p' => 0.80 ),
              's' => array( 'h' => 1.0, 'p' => 0.75 )
            )
          );
          if ( isset($_ratios[$_size]) && isset($_ratios[$_size][$_sel]) )
            return $_ratios[$_size][$_sel];
          return 1;
        }


        /**
        * @return css string
        * @param $_media_query = string of current media query.
        * @param $_css_prop = array of css rules for paragraph and titles for a given column layout
        * @param $_col_nb = current column layout
        * Assigns css rules to predefined grid selectors for headings and paragraphs
        * adds the '1' column css if (OR) :
        * 1) there's a sticky post
        * 2) user layout is one column
        */
        private function czr_fn_grid_assign_css_rules_to_selectors( $_media_query, $_css_prop, $_col_nb ) {
          $_css = '';
          //Add one column font rules if there's a sticky post
          if ( $this -> czr_fn_is_sticky_expanded() || '1' == $_col_nb ) {
            $_size      = $this -> czr_fn_get_grid_font_sizes( $_col_nb = '1', $_media_query );//size like xxl
            $_h_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'h' );
            $_p_one_col = $this -> czr_fn_grid_build_css_rules( $_size , 'p' );
            $_css .= "
                .tc-post-list-grid .grid-cols-1 .entry-title {{$_h_one_col}}
                .tc-post-list-grid .grid-cols-1 .tc-g-cont {{$_p_one_col}}
            ";
          }
          $_h = $_css_prop['h'];
          $_p = $_css_prop['p'];
          $_css .= "
              .tc-post-list-grid article .entry-title {{$_h}}
              .tc-post-list-grid .tc-g-cont {{$_p}}
          ";
          return $_css;
        }


        /**
        * @return css string
        * @param column layout (string)
        * adds the one column css if (OR) :
        * 1) there's a sticky post
        * 2) user layout is one column
        */
        private function czr_fn_grid_get_figure_css( $_col_nb = '3' ) {
          $_height = $this -> czr_fn_get_grid_column_height( $_col_nb );
          $_cols_class      = sprintf( 'grid-cols-%s' , $_col_nb );
          $_css = '';
          //Add one column height if there's a sticky post
          if ( $this -> czr_fn_is_sticky_expanded() && '1' != $_col_nb ) {
            $_height_col_one = $this -> czr_fn_get_grid_column_height( '1' );
            $_css .= ".grid-cols-1 figure {
                  height:{$_height_col_one}px;
                  max-height:{$_height_col_one}px;
                  line-height:{$_height_col_one}px;
            }";
          }
          $_css .= "
            .{$_cols_class} figure {
                  height:{$_height}px;
                  max-height:{$_height}px;
                  line-height:{$_height}px;
            }";
          return $_css;
        }


        /**
        * @return string
        * @param size string
        * @param selector type string
        * returns the font-size and line-height css rules
        */
        private function czr_fn_grid_build_css_rules( $_size = 'xl', $_wot = 'h' ) {
          $_lh_ratio = apply_filters( 'tc_grid_line_height_ratio' , 1.55 ); //line-height / font-size
          $_ratio = $this -> czr_fn_get_grid_font_ratios( $_size , $_wot );
          //body font size
          $_bs = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_body_font_size') );
          $_bs = is_numeric($_bs) && 1 >= $_bs ? $_bs : 15;

          return sprintf( 'font-size:%spx;line-height:%spx;' ,
            ceil( $_bs * $_ratio ),
            ceil( $_bs * $_ratio * $_lh_ratio )
          );
        }



        /******************************
        VARIOUS HELPERS
        *******************************/
        /**
        * @param (string) $col_layout
        * @return string
        *
        */
        private function czr_fn_get_grid_column_height( $_cols_nb = '3' ) {
          $_h               = $this -> czr_fn_grid_get_thumb_height();
          $_current_layout  = CZR_utils::czr_fn_get_layout( $this -> post_id , 'sidebar' );
          $_layouts         = array('b', 'l', 'r' , 'f');//both, left, right, full (no sidebar)
          $_key             = 3;//default value == full
          if ( in_array( $_current_layout, $_layouts ) )
            //get the key = position of requested size in the current layout
            $_key = array_search( $_current_layout , $_layouts );

          $_grid_col_height_map =  apply_filters(
              'tc_grid_col_height_map',
              array(        // 'b'  'l'  'r'  'f'
                '1' => array( 225 , 225, 225, $_h ),
                '2' => array( 225 , $_h, $_h, $_h ),
                '3' => array( 225 , 225, 225, 225 ),
                '4' => array( 165 , 165, 165, 165 )
              )
          );
          //are we ok ?
          if ( ! isset( $_grid_col_height_map[$_cols_nb] ) )
            return $_h;

          //parse the array to ensure that all values are <= user height
          foreach ( $_grid_col_height_map as $_c => $_heights ) {
            $_grid_col_height_map[$_c] = $this -> czr_fn_set_max_col_height ( $_heights ,$_h );
          }

          $_h = isset( $_grid_col_height_map[$_cols_nb][$_key] ) ? $_grid_col_height_map[$_cols_nb][$_key] : $_h;
          return apply_filters( 'tc_get_grid_column_height' , $_h, $_cols_nb, $_current_layout );
        }



        /**
        * parse the array to ensure that all values are <= user height
        * @param (array) grid_col_height_map
        * @param  (num) user defined max height in pixel
        * @return string
        *
        */
        private function czr_fn_set_max_col_height( $_heights ,$_h ) {
          $_return = array();
          foreach ($_heights as $_value) {
            $_return[] = $_value >= $_h ? $_h : $_value;
          }
          return $_return;
        }



        /**
        * @return (number) customizer user defined height for the grid thumbnails
        */
        private function czr_fn_grid_get_thumb_height() {
          $_opt = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_thumb_height') );
          return ( is_numeric($_opt) && $_opt > 1 ) ? $_opt : 350;
        }


        /*
        * @return bool
        * check if we have to expand the first sticky post
        */
        private function czr_fn_is_sticky_expanded( $query = null ){
          global $wp_query, $wpdb;
          $query = ( $query ) ? $query : $wp_query;

          if ( ! $query->is_main_query() )
              return false;
          if ( ! ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                  $wp_query->is_posts_page ) )
              return false;

          $_expand_feat_post_opt = apply_filters( 'tc_grid_expand_featured', esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_expand_featured') ) );

          if ( ! $this -> expanded_sticky ) {
            $_sticky_posts = get_option('sticky_posts');
            // get last published sticky post
            if ( is_array($_sticky_posts) && ! empty( $_sticky_posts ) ) {
              $_where = implode(',', $_sticky_posts );
              $this -> expanded_sticky = $wpdb->get_var(
                     "
                     SELECT ID
                     FROM $wpdb->posts
                     WHERE ID IN ( $_where )
                     ORDER BY post_date DESC
                     LIMIT 1
                     "
              );
            }else
              $this -> expanded_sticky = null;
          }

          if ( ! ( $_expand_feat_post_opt && $this -> expanded_sticky ) )
              return false;

          return true;
        }


        /*
        * @return bool
        * returns if the current post is the expanded one
        */
        private function czr_fn_force_current_post_expansion(){
          global $wp_query;
          return ( $this -> expanded_sticky && 0 == $wp_query -> current_post );
        }


        /*
        * @return bool
        */
        public function czr_fn_is_grid_enabled() {
          return apply_filters( 'tc_is_grid_enabled', 'grid' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_grid') ) && $this -> czr_fn_is_grid_context_matching() );
        }


        /* retrieves number of cols option, and wrap it into a filter */
        private function czr_fn_get_grid_cols() {
          return apply_filters( 'tc_get_grid_cols',
            esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_columns') ),
            CZR_utils::czr_fn_get_layout( $this -> post_id , 'class' )
          );
        }


        /* returns articles wrapper section columns */
        private function czr_fn_get_grid_section_cols() {
          return apply_filters( 'tc_grid_section_cols',
            $this -> czr_fn_force_current_post_expansion() ? '1' : $this -> czr_fn_get_grid_cols()
          );
        }



        /* returns the type of post list we're in if any, an empty string otherwise */
        private function czr_fn_get_grid_context() {
          global $wp_query;

          if ( ( is_home() && 'posts' == get_option('show_on_front') ) ||
                  $wp_query->is_posts_page )
              return 'blog';
          else if ( is_search() && $wp_query->post_count > 0 )
              return 'search';
          else if ( is_archive() )
              return 'archive';
          return '';
        }


        /* performs the match between the option where to use post list grid
         * and the post list we're in */
        private function czr_fn_is_grid_context_matching() {
          $_type = $this -> czr_fn_get_grid_context();
          $_apply_grid_to_post_type = apply_filters( 'tc_grid_in_' . $_type, esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_grid_in_' . $_type ) ) );
          return apply_filters('tc_grid_do',  $_type && $_apply_grid_to_post_type );
        }


        /**
        * @return  boolean
        */
        private function czr_fn_grid_show_thumb() {
          return CZR_post_thumbnails::$instance -> czr_fn_has_thumb() && 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_show_thumb' ) );
        }
  }//end of class
endif;

?><?php
/**
* Post metas content actions
* Since 3.1.20, displays all levels of any hierarchical taxinomies by default and for all types of post (including hierarchical CPT). This feature can be disabled with a the filter : tc_display_taxonomies_in_breadcrumb (set to true by default). In the case of hierarchical post types (like page or hierarchical CPT), the taxonomy trail is only displayed for the higher parent.
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_post_metas' ) ) :
    class CZR_post_metas {
        static $instance;
        function __construct () {
          self::$instance =& $this;
          //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( 'template_redirect'                            , array( $this , 'czr_fn_set_visibility_options' ) , 10 );
           //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( 'template_redirect'                            , array( $this , 'czr_fn_set_design_options' ) , 20 );
          //Show / hide metas based on customizer user options (@since 3.2.0)
          add_action( '__after_content_title'         , array( $this , 'czr_fn_set_post_metas_hooks' ), 20 );

        }


        /***********************
        * VISIBILITY HOOK SETUP
        ***********************/
        /**
        * Set the post metas visibility based on Customizer options
        * uses hooks tc_show_post_metas, body_class
        * hook : template_redirect
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function czr_fn_set_visibility_options() {
          //if customizing context, always render. Will be hidden in the DOM with a body class filter is disabled.
          if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas' ) ) ) {
            if ( CZR___::$instance -> czr_fn_is_customizing() )
              add_filter( 'body_class' , array( $this , 'czr_fn_hide_all_post_metas') );
            else{
              add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
            }
          }
          if ( is_singular() && ! is_page() && ! czr_fn__f('__is_home') ) {
              if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_single_post' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }

              if ( CZR___::$instance -> czr_fn_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'czr_fn_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
          }
          if ( ! is_singular() && ! czr_fn__f('__is_home') && ! is_page() ) {
              if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_post_lists' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }

              if ( CZR___::$instance -> czr_fn_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'czr_fn_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
              return;
          }
          if ( czr_fn__f('__is_home') ) {
              if ( 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_home' ) ) ) {
                  add_filter( 'tc_show_post_metas' , '__return_true' );
                  return;
              }
              if ( CZR___::$instance -> czr_fn_is_customizing() ) {
                  add_filter( 'body_class' , array( $this , 'czr_fn_hide_post_metas') );
                  add_filter( 'tc_show_post_metas' , '__return_true' );
              }
              else
                  add_filter( 'tc_show_post_metas' , '__return_false' );
          }
        }



        /**
        * Default metas visibility controller
        * tc_show_post_metas gets filtered by czr_fn_set_visibility_options() called early in template_redirect
        * @return  boolean
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function czr_fn_show_post_metas() {
          global $post;
          //when do we display the metas ?
          //1) default is : not on home page, 404, search page
          //2) +filter conditions
          return apply_filters(
              'tc_show_post_metas',
              ! czr_fn__f('__is_home')
              && ! is_404()
              && ! 'page' == $post -> post_type
              && in_array( get_post_type(), apply_filters('tc_show_metas_for_post_types' , array( 'post') ) )
          );
        }



        /***********************
        * DESIGN HOOK SETUP
        ***********************/
        function czr_fn_set_design_options() {
          if ( 'buttons' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_design' ) ) )
            return;

          add_filter( 'tc_meta_terms_glue'           , array( $this, 'czr_fn_set_term_meta_glue' ) );
          add_filter( 'tc_meta_tax_class'            , '__return_empty_array' );

          add_filter( 'tc_post_tax_metas_html'       , array( $this, 'czr_fn_set_tax_metas' ), 10, 2 );
          add_filter( 'tc_post_date_metas_html'      , array( $this, 'czr_fn_set_date_metas' ), 10, 2 );
          add_filter( 'tc_post_author_metas_html'    , array( $this, 'czr_fn_set_author_metas' ), 10 , 2 );
          add_filter( 'tc_set_metas_content'         , array( $this, 'czr_fn_set_metas' ), 10, 2 );
        }


        /*****************
        * MODELS
        *****************/
        /**
        * Build the metas models
        * Render the view based on filters
        * hook : __after_content_title
        * @return void
        * @package Customizr
        * @since Customizr 3.2.2
        */
        function czr_fn_set_post_metas_hooks() {
          if ( ! $this -> czr_fn_show_post_metas() )
            return;
          global $post;
          $_model = array();
          //BUILD MODEL
          //Two cases : attachment and not attachment
          if ( 'attachment' == $post -> post_type ) {
            $_model = $this -> czr_fn_build_attachment_post_metas_model();
          } else {
            $_model = $this -> czr_fn_build_post_post_metas_model();
            //Set metas content based on customizer user options (@since 3.2.6)
            add_filter( 'tc_meta_utility_text'      , array( $this , 'czr_fn_set_post_metas_elements'), 10 , 2 );
            //filter metas content with default theme settings
            add_filter( 'tc_meta_utility_text'      , array( $this , 'czr_fn_add_link_to_post_after_metas'), 20 );
          }

          //RENDER VIEW
          $this -> czr_fn_render_metas_view( $_model );
        }



        /**
        * Post metas model
        * @return model array
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function czr_fn_build_post_post_metas_model() {
          $cat_list   = $this -> czr_fn_meta_generate_tax_list( true );
          $tag_list   = $this -> czr_fn_meta_generate_tax_list( false );
          $pub_date   = $this -> czr_fn_get_meta_date( 'publication' );
          $auth       = $this -> czr_fn_get_meta_author();
          $upd_date   = $this -> czr_fn_get_meta_date( 'update' );

          $_args      = compact( 'cat_list' ,'tag_list', 'pub_date', 'auth', 'upd_date' );
          $_html      = sprintf( __( 'This entry was posted on %1$s<span class="by-author"> by %2$s</span>.' , 'customizr' ),
            $pub_date,
            $auth
          );
          return apply_filters( 'tc_post_metas_model' , compact( "_html" , "_args" ) );
        }



        /**
        * Attachment metas model
        * @return model array
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function czr_fn_build_attachment_post_metas_model() {
          global $post;
          $metadata       = wp_get_attachment_metadata();
          $_html = sprintf( '%1$s <span class="entry-date"><time class="entry-date updated" datetime="%2$s">%3$s</time></span> %4$s %5$s',
              '<span class="meta-prep meta-prep-entry-date">'.__('Published' , 'customizr').'</span>',
              apply_filters('tc_use_the_post_modified_date' , false ) ? esc_attr( get_the_date( 'c' ) ) : esc_attr( get_the_modified_date('c') ),
              esc_html( get_the_date() ),
              ( isset($metadata['width']) && isset($metadata['height']) ) ? __('at dimensions' , 'customizr').'<a href="'.esc_url( wp_get_attachment_url() ).'" title="'.__('Link to full-size image' , 'customizr').'"> '.$metadata['width'].' &times; '.$metadata['height'].'</a>' : '',
              __('in' , 'customizr').'<a href="'.esc_url( get_permalink( $post->post_parent ) ).'" title="'.__('Return to ' , 'customizr').esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ).'" rel="gallery"> '.get_the_title( $post->post_parent ).'</a>.'
          );
          return apply_filters( 'tc_attachment_metas_model' , compact( "_html" ) );
        }



        /*****************
        * VIEW
        *****************/
        /**
        * Customizr metas view
        * @return  html string
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function czr_fn_render_metas_view( $_model ) {
          if ( empty($_model) )
            return;
          //extract $_html , $_args
          extract( $_model );
          $_html = isset($_html) ? $_html : '';
          $_args = isset($_args) ? $_args : array();
          //echoes all filtered metas components
          echo apply_filters(
            'tc_post_metas',
            sprintf( '<div class="entry-meta">%s</div>',
              apply_filters( 'tc_meta_utility_text', $_html , $_args )
            )
          );
        }




        /*****************
        * SETTERS / GETTERS / HELPERS
        *****************/
        /**
        * Set meta content based on user options
        * hook : tc_meta_utility_text
        * @return  html string as a wp filter
        * @package Customizr
        * @since Customizr 3.2.6
        */
        function czr_fn_set_post_metas_elements( $_default , $_args = array() ) {
            $_show_cats         = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_categories' ) ) && false != $this -> czr_fn_meta_generate_tax_list( true );
            $_show_tags         = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_tags' ) ) && false != $this -> czr_fn_meta_generate_tax_list( false );
            $_show_pub_date     = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_publication_date' ) );
            $_show_upd_date     = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_update_date' ) ) && false !== CZR_utils::$inst -> czr_fn_post_has_update();
            $_show_upd_in_days  = 'days' == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_metas_update_date_format' ) );
            $_show_date         = $_show_pub_date || $_show_upd_date;
            $_show_author       = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_post_metas_author' ) );

            //extract cat_list, tag_list, pub_date, auth, upd_date from $args if not empty
            if ( empty($_args) )
              return $_default;
            extract($_args);

            //TAGS / CATS
            $_tax_text  = '';
            if ( $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s and tagged %2$s' , 'customizr' );
            if ( $_show_cats && ! $_show_tags )
                $_tax_text   .= __( 'This entry was posted in %1$s' , 'customizr' );
            if ( ! $_show_cats && $_show_tags )
                $_tax_text   .= __( 'This entry was tagged %2$s' , 'customizr' );
            $_tax_text = apply_filters( 'tc_post_tax_metas_html' ,
              sprintf( $_tax_text , $cat_list, $tag_list ),
              compact( "_show_cats" , "_show_tags" , "cat_list", "tag_list" )
            );

            //PUBLICATION DATE
            $_date_text = '';
            if ( $_show_pub_date ) {
              $_date_text        = empty($_tax_text) ? __( 'This entry was posted on %1$s' , 'customizr' ) : $_date_text;
              if ( $_show_cats )
                $_date_text   .= __( 'on %1$s' , 'customizr' );
              if ( ! $_show_cats && $_show_tags )
                $_date_text   .= __( 'and posted on %1$s' , 'customizr' );
              $_date_text = apply_filters( 'tc_post_date_metas_html',
                sprintf( $_date_text, $pub_date ),
                $pub_date
              );
            }


            //AUTHOR
            $_author_text = '';
            if ( $_show_author ) {
              if ( empty($_tax_text) && empty($_date_text) ) {
                  $_author_text = sprintf( '%s <span class="by-author">%s</span>' , __( 'This entry was posted', 'customizr' ), __('by %1$s' , 'customizr') );
              } else {
                  $_author_text = sprintf( '<span class="by-author">%s</span>' , __('by %1$s' , 'customizr') );
              }
              $_author_text = apply_filters( 'tc_post_author_metas_html',
                sprintf( $_author_text, $auth ),
                $auth
              );
            }


            //UPDATE DATE
            $_update_text = '';
            if ( $_show_upd_date ) {
              if ( $_show_upd_in_days ) {
                $_update_days = CZR_utils::$inst -> czr_fn_post_has_update();
                $_update_text = ( 0 == $_update_days ) ? __( '(updated today)' , 'customizr' ) : sprintf( __( '(updated %s days ago)' , 'customizr' ), $_update_days );
                $_update_text = ( 1 == $_update_days ) ? __( '(updated 1 day ago)' , 'customizr' ) : $_update_text;
              }
              else {
                $_update_text = __( '(updated on %1$s)' , 'customizr' );
              }
              $_update_text = apply_filters( 'tc_post_update_metas_html',
                sprintf( $_update_text , $upd_date ),
                $upd_date
              );
            }

            return apply_filters ( 'tc_set_metas_content',
              sprintf( '%1$s %2$s %3$s %4$s' , $_tax_text , $_date_text, $_author_text, $_update_text ),
              compact( "_tax_text" , "_date_text", "_author_text", "_update_text" )
            );
        }



        /**
        * Helper
        * @return string of all the taxonomy terms (including the category list for posts)
        * @param  hierarchical tax boolean => true = categories like, false = tags like
        *
        * @package Customizr
        * @since Customizr 3.0
        */
        public function czr_fn_meta_generate_tax_list( $hierarchical ) {
          $post_terms = $this -> czr_fn_get_term_of_tax_type( $hierarchical );
          if ( ! $post_terms )
            return;

          $_terms_html_array  = array_map( array( $this , 'czr_fn_meta_term_view' ), $post_terms );
          return apply_filters( 'tc_meta_generate_tax_list', implode( apply_filters( 'tc_meta_terms_glue' , '' ) , $_terms_html_array ) , $post_terms );
        }


        /**
        * Helper
        * @return string of the single term view
        * @param  $term object
        *
        * @package Customizr
        * @since Customizr 3.3.2
        */
        private function czr_fn_meta_term_view( $term ) {
          $_classes         =  array( 'btn' , 'btn-mini' );
          $_is_hierarchical  =  is_taxonomy_hierarchical( $term -> taxonomy );
          if ( $_is_hierarchical ) //<= check if hierarchical (category) or not (tag)
            array_push( $_classes , 'btn-tag' );

          $_classes      = implode( ' ', apply_filters( 'tc_meta_tax_class', $_classes , $_is_hierarchical, $term ) );

          // (Rocco's PR Comment) : following to this https://wordpress.org/support/topic/empty-articles-when-upgrading-to-customizr-version-332
          // I found that at least wp 3.6.1  get_term_link($term->term_id, $term->taxonomy) returns a WP_Error
          // Looking at the codex, looks like we can just use get_term_link($term), when $term is a term object.
          // Just this change avoids the issue with 3.6.1, but I thought should be better make a check anyway on the return type of that function.
          $_term_link    = is_wp_error( get_term_link( $term ) ) ? '' : get_term_link( $term );

          $_to_return    = $_term_link ? '<a class="%1$s" href="%2$s" title="%3$s"> %4$s </a>' :  '<span class="%1$s"> %4$s </a>';

          return apply_filters( 'tc_meta_term_view' , sprintf($_to_return,
              $_classes,
              $_term_link,
              esc_attr( sprintf( __( "View all posts in %s", 'customizr' ), $term -> name ) ),
              $term -> name
            )
          );
        }



        /**
        * Helper to return the current post terms of specified taxonomy type : hierarchical or not
        *
        * @return boolean (false) or array
        * @param  boolean : hierarchical or not
        * @package Customizr
        * @since Customizr 3.1.20
        *
        */
        public function czr_fn_get_term_of_tax_type( $hierarchical = true ) {
          //var declaration
          $post_type              = get_post_type( CZR_utils::czr_fn_id() );
          $tax_list               = get_object_taxonomies( $post_type, 'object' );
          $_tax_type_list         = array();
          $_tax_type_terms_list   = array();

          if ( empty($tax_list) )
              return false;

          //filter the post taxonomies
          while ( $_tax_object = current($tax_list) ) {
            // cast $_tax_object stdClass object in an array to access its property 'public'
            // fix for PHP version < 5.3 (?)
            $_tax_object = (array) $_tax_object;

            //Is the object well defined ?
            if ( ! isset($_tax_object['name']) ) {
              next($tax_list);
              continue;
            }

            $_tax_name = $_tax_object['name'];

            //skip the post format taxinomy
            if ( ! $this -> czr_fn_is_tax_authorized( $_tax_object, $post_type ) ) {
              next($tax_list);
              continue;
            }

            if ( (bool) $hierarchical === (bool) $_tax_object['hierarchical'] )
                $_tax_type_list[$_tax_name] = $_tax_object;
            next($tax_list);
          }

          if ( empty($_tax_type_list) )
              return false;

          //fill the post terms array
          foreach ($_tax_type_list as $tax_name => $data ) {
              $_current_tax_terms = get_the_terms( CZR_utils::czr_fn_id() , $tax_name );

              //If current post support this tax but no terms has been assigned yet = continue
              if ( ! $_current_tax_terms )
                  continue;

              while( $term = current($_current_tax_terms) ) {
                  $_tax_type_terms_list[$term -> term_id] = $term;
                  next($_current_tax_terms);
              }
          }
          return empty($_tax_type_terms_list) ? false : apply_filters( "tc_tax_meta_list" , $_tax_type_terms_list , $hierarchical );
        }



        /**
        * Helper : check if a given tax is allowed in the post metas or not
        * A tax is authorized if :
        * 1) not in the exclude list
        * 2) AND not private
        *
        * @return boolean (false)
        * @param  $post_type, $_tax_object
        * @package Customizr
        * @since Customizr 3.3+
        *
        */
        public function czr_fn_is_tax_authorized( $_tax_object , $post_type ) {
          $_in_exclude_list = in_array(
            $_tax_object['name'],
            apply_filters_ref_array ( 'tc_exclude_taxonomies_from_metas' , array( array('post_format') , $post_type , CZR_utils::czr_fn_id() ) )
          );

          $_is_private = false === (bool) $_tax_object['public'] && apply_filters_ref_array( 'tc_exclude_private_taxonomies', array( true, $_tax_object['public'], CZR_utils::czr_fn_id() ) );
          return ! $_in_exclude_list && ! $_is_private;
        }


        /**
        * Helper
        * Return the date post metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        public function czr_fn_get_meta_date( $pub_or_update = 'publication', $_format = '' ) {
            if ( 'short' == $_format )
              $_format = 'j M, Y';

            $_format = apply_filters( 'tc_meta_date_format' , $_format );
            $_use_post_mod_date = apply_filters( 'tc_use_the_post_modified_date' , 'publication' != $pub_or_update );
            return apply_filters(
                'tc_date_meta',
                sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date updated" datetime="%3$s">%4$s</time></a>' ,
                    esc_url( get_day_link( get_the_time( 'Y' ), get_the_time( 'm' ), get_the_time( 'd' ) ) ),
                    esc_attr( get_the_time() ),
                    $_use_post_mod_date ? esc_attr( get_the_modified_date('c') ) : esc_attr( get_the_date( 'c' ) ),
                    $_use_post_mod_date ? esc_html( get_the_modified_date( $_format ) ) : esc_html( get_the_date( $_format ) )
                ),
                $_use_post_mod_date,
                $_format
            );//end filter
        }


        /**
        * Helper
        * Return the post author metas
        *
        * @package Customizr
        * @since Customizr 3.2.6
        */
        private function czr_fn_get_meta_author() {
            return apply_filters(
                'tc_author_meta',
                sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>' ,
                    esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
                    esc_attr( sprintf( __( 'View all posts by %s' , 'customizr' ), get_the_author() ) ),
                    get_the_author()
                )
            );//end filter
        }


        /**
        * @return  string
        * Return the filter post metas for specific post formats
        * hook tc_meta_utility_text
        * @package Customizr
        * @since Customizr 3.2.9
        */
        function czr_fn_add_link_to_post_after_metas( $_metas_html ) {

          if ( apply_filters( 'tc_show_link_after_post_metas' , true )
            && in_array( get_post_format(), apply_filters( 'tc_post_formats_with_no_heading', CZR_init::$instance -> post_formats_with_no_heading ) )
            && ! is_singular() ) {
            return apply_filters('tc_add_link_to_post_after_metas',
              sprintf('%1$s | <a href="%2$s" title="%3$s">%3$s &raquo;</a>', $_metas_html, get_permalink(), __('Open' , 'customizr') )
            );
          }
          return $_metas_html;
        }




        /**
        * hook body_class filter
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function czr_fn_hide_all_post_metas( $_classes ) {
          return array_merge($_classes , array('hide-all-post-metas') );
        }


        /**
        * hook body_class filter
        *
        * @package Customizr
        * @since Customizr 3.2.0
        */
        function czr_fn_hide_post_metas( $_classes ) {
          return array_merge($_classes , array('hide-post-metas') );
        }


                /**
        * hook : tc_meta_terms_glue
        * @return  string
        */
        public function czr_fn_set_term_meta_glue() {
          return ' / ';
        }


        /**
        * hook : tc_post_tax_metas_html
        * @return  string
        */
        function czr_fn_set_tax_metas( $_html , $_tax = array() ) {
          if ( empty($_tax) )
            return $_html;
          //extract "_show_cats" , "_show_tags" , "cat_list", "tag_list"
          extract($_tax);
          $cat_list = ! empty($cat_list) && $_show_cats ? sprintf( '&nbsp;%s %s' , __('in' , 'customizr') , $cat_list ) : '';
          $tag_list = ! empty($tag_list) && $_show_tags ? sprintf( '&nbsp;%s %s' , __('tagged' , 'customizr') , $tag_list ) : '';
          return sprintf( '%s%s' , $cat_list, $tag_list );
        }


        /**
        * hook : tc_post_date_metas_html
        * @return  string
        */
        function czr_fn_set_date_metas( $_html, $_pubdate = '' ) {
          if ( empty($_pubdate))
            return $_html;
          return CZR_post_metas::$instance -> czr_fn_get_meta_date( 'publication' , 'short' );
        }

        /**
        * hook : tc_post_author_metas_html
        * @return  string
        */
        function czr_fn_set_author_metas( $_html , $_auth = '' ) {
          if ( empty($_auth) )
            return $_html;

          return sprintf( '<span class="by-author"> %s %s</span>' , __('by' , 'customizr'), $_auth );
        }

        /**
        * hook : tc_set_metas_content
        * @return  string
        */
        function czr_fn_set_metas( $_html, $_parts = array() ) {
          if ( empty($_parts) )
            return $_html;
          //extract $_tax_text , $_date_text, $_author_text, $_update_text
          extract($_parts);
          return sprintf( '%1$s %2$s %3$s %4$s' , $_date_text, $_tax_text , $_author_text, $_update_text );
        }
    }//end of class
endif;

//the only purpose of this function is to use the_tags() wp function in the theme...
function czr_fn_get_the_tags() {
    return the_tags();
}

?><?php
/**
* Navigation action
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
if ( ! class_exists( 'CZR_post_navigation' ) ) :
  class CZR_post_navigation {
      static  $instance;

      function __construct () {
        self::$instance =& $this;

        add_action ( '__after_loop'             , array( $this , 'czr_fn_post_nav' ), 20 );

      }


      /***********************
      * VISIBILITY SETUP
      ***********************/
      /**
      * Set the post navigation visibility based on Customizer options
      *
      * returns an array which contains, @bool whether or not show the navigation , @array css classes of the navigation, @string the context
      * @package Customizr
      * @since Customizr 3.3.22
      */
      function czr_fn_set_visibility_options(){

        $_nav_classes              = array('navigation');
        $_context                  = $this -> czr_fn_get_context();
        $_post_nav_enabled         = $this -> czr_fn_is_post_navigation_enabled();
        $_post_nav_context_enabled = $this -> czr_fn_is_post_navigation_context_enabled( $_context );

        $_is_customizing           = CZR___::$instance -> czr_fn_is_customizing() ;

        if ( $_is_customizing ){
          if ( ! $_post_nav_enabled )
            array_push( $_nav_classes, 'hide-all-post-navigation' );
          if ( ! $_post_nav_context_enabled )
            array_push( $_nav_classes, 'hide-post-navigation' );
          $_post_nav_enabled       = true;
        }else
          $_post_nav_enabled       = $_post_nav_enabled && $_post_nav_context_enabled;

        return array(
            apply_filters( 'tc_show_post_navigation', $_post_nav_enabled ),
            implode( ' ', apply_filters( 'tc_show_post_navigation_class' , $_nav_classes ) ),
            $_context
        );
      }

      /**
       * The template part for displaying nav links
       *
       * @package Customizr
       * @since Customizr 3.0
       */
      function czr_fn_post_nav() {

        list( $post_navigation_bool, $post_nav_class, $_context) = $this -> czr_fn_set_visibility_options();

        if( ! $post_navigation_bool )
          return;

        $prev_arrow = is_rtl() ? '&rarr;' : '&larr;' ;
        $next_arrow = is_rtl() ? '&larr;' : '&rarr;' ;
        $html_id = 'nav-below';
        global $wp_query;

        ob_start();
        ?>

        <?php if ( in_array($_context, array('single', 'page') ) ) : ?>

          <?php echo apply_filters( 'tc_singular_nav_separator' , '<hr class="featurette-divider '.current_filter().'">'); ?>

        <nav id="<?php echo $html_id; ?>" class="<?php echo $post_nav_class; ?>" role="navigation">

              <h3 class="assistive-text">
                <?php echo apply_filters( 'tc_singular_nav_title', __( 'Post navigation' , 'customizr' ) ) ; ?>
              </h3>

              <ul class="pager">
                <?php if ( get_previous_post() != null ) : ?>
                  <li class="previous">
                    <span class="nav-previous">
                      <?php
                        $singular_nav_previous_text   = apply_filters( 'tc_singular_nav_previous_text', call_user_func( '_x',  $prev_arrow , 'Previous post link' , 'customizr' ) );
                        $previous_post_link_args      = apply_filters(
                          'tc_previous_single_post_link_args' ,
                          array(
                            'format'        => '%link',
                            'link'          => '<span class="meta-nav">' . $singular_nav_previous_text . '</span> %title',
                            'in_same_term'  => false,
                            'excluded_terms' => '',
                            'taxonomy'      => 'category'
                          )
                        );
                        extract( $previous_post_link_args , EXTR_OVERWRITE );
                        previous_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
                      ?>
                    </span>
                  </li>
                <?php endif; ?>
                <?php if ( get_next_post() != null ) : ?>
                  <li class="next">
                    <span class="nav-next">
                        <?php
                        $singular_nav_next_text       = apply_filters( 'tc_singular_nav_next_text', call_user_func( '_x', $next_arrow , 'Next post link' , 'customizr' ) );
                        $next_post_link_args      = apply_filters(
                          'tc_next_single_post_link_args' ,
                          array(
                            'format'        => '%link',
                            'link'          => '%title <span class="meta-nav">' . $singular_nav_next_text . '</span>',
                            'in_same_term'  => false,
                            'excluded_terms' => '',
                            'taxonomy'      => 'category'
                          )
                        );
                        extract( $next_post_link_args , EXTR_OVERWRITE );
                        next_post_link( $format , $link , $in_same_term, $excluded_terms, $taxonomy );
                        ?>
                    </span>
                  </li>
                <?php endif; ?>
              </ul>

          </nav><!-- //#<?php echo $html_id; ?> .navigation -->

        <?php elseif ( $wp_query->max_num_pages > 1 && in_array($_context, array('archive', 'home') ) ) : ?>

          <nav id="<?php echo $html_id; ?>" class="<?php echo $post_nav_class; ?>" role="navigation">

            <h3 class="assistive-text">
              <?php echo apply_filters( 'tc_list_nav_title', __( 'Post navigation' , 'customizr' ) ) ; ?>
            </h3>

              <ul class="pager">

                <?php if(get_next_posts_link() != null) : ?>

                  <li class="previous">
                    <span class="nav-previous">
                      <?php
                        $next_posts_link_args      = apply_filters(
                          'tc_next_posts_link_args' ,
                          array(
                            'label'        => apply_filters( 'tc_list_nav_next_text', __( '<span class="meta-nav">&larr;</span> Older posts' , 'customizr' ) ),
                            'max_pages'    => 0
                          )
                        );
                        extract( $next_posts_link_args , EXTR_OVERWRITE );
                        next_posts_link( $label , $max_pages );
                      ?>
                    </span>
                  </li>

                <?php endif; ?>

                <?php if(get_previous_posts_link() != null) : ?>

                  <li class="next">
                    <span class="nav-next">
                      <?php
                        $previous_posts_link_args      = apply_filters(
                          'tc_previous_posts_link_args' ,
                          array(
                            'label'        => apply_filters( 'tc_list_nav_previous_text', __( 'Newer posts <span class="meta-nav">&rarr;</span>' , 'customizr' ) ),
                            'max_pages'    => 0
                          )
                        );
                        extract( $previous_posts_link_args , EXTR_OVERWRITE );
                        previous_posts_link( $label , $max_pages );
                      ?>
                    </span>
                  </li>

                <?php endif; ?>

              </ul>

          </nav><!-- //#<?php echo $html_id; ?> .navigation -->

        <?php endif; ?>

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_post_nav' , $html );
      }



      /******************************
      VARIOUS HELPERS
      *******************************/
      /**
      *
      * @return string or bool
      *
      */
      function czr_fn_get_context(){
        if ( is_page() )
          return 'page';
        if ( is_single() && ! is_attachment() )
          return 'single'; // exclude attachments
        if ( is_home() && 'posts' == get_option('show_on_front') )
          return 'home';
        if ( !is_404() && !czr_fn__f( '__is_home_empty') )
          return 'archive';

        return false;

      }

      /*
      * @param (string or bool) the context
      * @return bool
      */
      function czr_fn_is_post_navigation_context_enabled( $_context ) {
        return $_context && 1 == esc_attr( CZR_utils::$inst -> czr_fn_opt( "tc_show_post_navigation_{$_context}" ) );
      }

      /*
      * @return bool
      */
      function czr_fn_is_post_navigation_enabled(){
        return 1 == esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_show_post_navigation' ) ) ;
      }

  }//end of class
endif;

?><?php
/**
* Posts thumbnails actions
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0.5
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_post_thumbnails' ) ) :
class CZR_post_thumbnails {
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //may be filter the thumbnail inline style
      add_filter( 'tc_post_thumb_inline_style'  , array( $this , 'czr_fn_change_thumb_inline_css' ), 10, 3 );
    }



    /**********************
    * THUMBNAIL MODELS
    **********************/
    /**
    * Gets the thumbnail or the first images attached to the post if any
    * inside loop
    * @return array( $tc_thumb(image object), $tc_thumb_width(string), $tc_thumb_height(string) )
    *
    * @package Customizr
    * @since Customizr 1.0
    */
    function czr_fn_get_thumbnail_model( $requested_size = null, $_post_id = null , $_custom_thumb_id = null, $_enable_wp_responsive_imgs = null ) {
      if ( ! $this -> czr_fn_has_thumb( $_post_id, $_custom_thumb_id ) )
        return array();

      $tc_thumb_size              = is_null($requested_size) ? apply_filters( 'tc_thumb_size_name' , 'tc-thumb' ) : $requested_size;
      $_post_id                   = is_null($_post_id) ? get_the_ID() : $_post_id;
      $_filtered_thumb_size       = apply_filters( 'tc_thumb_size' , CZR_init::$instance -> tc_thumb_size );
      $_model                     = array();
      $_img_attr                  = array();
      $tc_thumb_height            = '';
      $tc_thumb_width             = '';

      //when null set it as the image setting for reponsive thumbnails (default)
      //because this method is also called from the slider of posts which refers to the slider responsive image setting
      //limit this just for wp version >= 4.4
      if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
        $_enable_wp_responsive_imgs = is_null( $_enable_wp_responsive_imgs ) ? 1 == CZR_utils::$inst->czr_fn_opt('tc_resp_thumbs_img') : $_enable_wp_responsive_imgs;

      //try to extract $_thumb_id and $_thumb_type
      extract( $this -> czr_fn_get_thumb_info( $_post_id, $_custom_thumb_id ) );
      if ( ! apply_filters( 'tc_has_thumb_info', isset($_thumb_id) && false != $_thumb_id && ! is_null($_thumb_id) ) )
        return array();

      //Try to get the image
      $image                      = wp_get_attachment_image_src( $_thumb_id, $tc_thumb_size);
      if ( ! apply_filters('tc_has_wp_thumb_image', ! empty( $image[0] ) ) )
        return array();

      //check also if this array value isset. (=> JetPack photon bug)
      if ( isset($image[3]) && false == $image[3] && 'tc-thumb' == $tc_thumb_size )
        $tc_thumb_size          = 'large';
      if ( isset($image[3]) && false == $image[3] && 'tc_rectangular_size' == $tc_thumb_size )
        $tc_thumb_size          = 'slider';

      $_img_attr['class']     = sprintf( 'attachment-%1$s tc-thumb-type-%2$s wp-post-image' , $tc_thumb_size , $_thumb_type );
      //Add the style value
      $_style                 = apply_filters( 'tc_post_thumb_inline_style' , '', $image, $_filtered_thumb_size );
      if ( $_style )
        $_img_attr['style']   = $_style;
      $_img_attr              = apply_filters( 'tc_post_thumbnail_img_attributes' , $_img_attr );

      //we might not want responsive images
      if ( false === $_enable_wp_responsive_imgs ) {
        //trick, will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
        //only when the passed srcset attr is not empty. This will avoid us to:
        //a) add a filter to get rid of already computed srcset
        // or
        //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
        //Side effect:
        //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
        //to avoid this we filter the attributes getting rid of the srcset if any.
        //Basically this trick, even if ugly, will avoid the srcset attr computation
        $_img_attr['srcset']  = " ";
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'czr_fn_remove_srcset_attr' ) );
      }
      //get the thumb html
      if ( is_null($_custom_thumb_id) && has_post_thumbnail( $_post_id ) )
        //get_the_post_thumbnail( $post_id, $size, $attr )
        $tc_thumb = get_the_post_thumbnail( $_post_id , $tc_thumb_size , $_img_attr);
      else
        //wp_get_attachment_image( $attachment_id, $size, $icon, $attr )
        $tc_thumb = wp_get_attachment_image( $_thumb_id, $tc_thumb_size, false, $_img_attr );

      //get height and width if not empty
      if ( ! empty($image[1]) && ! empty($image[2]) ) {
        $tc_thumb_height        = $image[2];
        $tc_thumb_width         = $image[1];
      }
      //used for smart load when enabled
      $tc_thumb = apply_filters( 'tc_thumb_html', $tc_thumb, $requested_size, $_post_id, $_custom_thumb_id, $_img_attr, $tc_thumb_size );

      return apply_filters( 'tc_get_thumbnail_model',
        isset($tc_thumb) && ! empty($tc_thumb) && false != $tc_thumb ? compact( "tc_thumb" , "tc_thumb_height" , "tc_thumb_width" ) : array(),
        $_post_id,
        $_thumb_id,
        $_enable_wp_responsive_imgs
      );
    }



    /**
    * inside loop
    * @return array( "_thumb_id" , "_thumb_type" )
    */
    private function czr_fn_get_thumb_info( $_post_id = null, $_thumb_id = null ) {
      $_post_id     = is_null($_post_id) ? get_the_ID() : $_post_id;
      $_meta_thumb  = get_post_meta( $_post_id , 'tc-thumb-fld', true );
      //get_post_meta( $post_id, $key, $single );
      //always refresh the thumb meta if user logged in and current_user_can('upload_files')
      //When do we refresh ?
      //1) empty( $_meta_thumb )
      //2) is_user_logged_in() && current_user_can('upload_files')
      $_refresh_bool = empty( $_meta_thumb ) || ! $_meta_thumb;
      $_refresh_bool = ! isset($_meta_thumb["_thumb_id"]) || ! isset($_meta_thumb["_thumb_type"]);
      $_refresh_bool = ( is_user_logged_in() && current_user_can('upload_files') ) ? true : $_refresh_bool;
      //if a custom $_thumb_id is requested => always refresh
      $_refresh_bool = ! is_null( $_thumb_id ) ? true : $_refresh_bool;

      if ( ! $_refresh_bool )
        return $_meta_thumb;

      return $this -> czr_fn_set_thumb_info( $_post_id , $_thumb_id, true );
    }

    /**************************
    * EXPOSED HELPERS / SETTERS
    **************************/
    /*
    * @return bool
    */
    public function czr_fn_has_thumb( $_post_id = null , $_thumb_id = null ) {
      $_post_id  = is_null($_post_id) ? get_the_ID() : $_post_id;
      //try to extract (OVERWRITE) $_thumb_id and $_thumb_type
      extract( $this -> czr_fn_get_thumb_info( $_post_id, $_thumb_id ) );
      return apply_filters( 'tc_has_thumb', wp_attachment_is_image($_thumb_id) && isset($_thumb_id) && false != $_thumb_id && ! empty($_thumb_id) );
    }


    /**
    * update the thumb meta and maybe return the info
    * public because also fired from admin on save_post
    * @param post_id and (bool) return
    * @return void or array( "_thumb_id" , "_thumb_type" )
    */
    public function czr_fn_set_thumb_info( $post_id = null , $_thumb_id = null, $_return = false ) {
      $post_id      = is_null($post_id) ? get_the_ID() : $post_id;
      $_thumb_type  = 'none';

      //IF a custom thumb id is requested
      if ( ! is_null( $_thumb_id ) && false !== $_thumb_id ) {
        $_thumb_type  = false !== $_thumb_id ? 'custom' : $_thumb_type;
      }
      //IF no custom thumb id :
      //1) check if has thumbnail
      //2) check attachements
      //3) default thumb
      else {
        if ( has_post_thumbnail( $post_id ) ) {
          $_thumb_id    = get_post_thumbnail_id( $post_id );
          $_thumb_type  = false !== $_thumb_id ? 'thumb' : $_thumb_type;
        } else {
          $_thumb_id    = $this -> czr_fn_get_id_from_attachment( $post_id );
          $_thumb_type  = false !== $_thumb_id ? 'attachment' : $_thumb_type;
        }
        if ( ! $_thumb_id || empty( $_thumb_id ) ) {
          $_thumb_id    = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_default_thumb' ) );
          $_thumb_type  = ( false !== $_thumb_id && ! empty($_thumb_id) ) ? 'default' : $_thumb_type;
        }
      }
      $_thumb_id = ( ! $_thumb_id || empty($_thumb_id) || ! is_numeric($_thumb_id) ) ? false : $_thumb_id;

      //update_post_meta($post_id, $meta_key, $meta_value, $prev_value);
      update_post_meta( $post_id , 'tc-thumb-fld', compact( "_thumb_id" , "_thumb_type" ) );
      if ( $_return )
        return apply_filters( 'tc_set_thumb_info' , compact( "_thumb_id" , "_thumb_type" ), $post_id );
    }//end of fn


    private function czr_fn_get_id_from_attachment( $post_id ) {
      //define a filtrable boolean to set if attached images can be used as thumbnails
      //1) must be a non single post context
      //2) user option should be checked in customizer
      $_bool = 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_post_list_use_attachment_as_thumb' ) );
      if ( ! is_admin() )
        $_bool == ! CZR_post::$instance -> czr_fn_single_post_display_controller() && $_bool;
      if ( ! apply_filters( 'tc_use_attachement_as_thumb' , $_bool ) )
        return;

      //Case if we display a post or a page
      if ( 'attachment' != get_post_type( $post_id ) ) {
        //look for the last attached image in a post or page
        $tc_args = apply_filters('tc_attachment_as_thumb_query_args' , array(
            'numberposts'             =>  1,
            'post_type'               =>  'attachment',
            'post_status'             =>  null,
            'post_parent'             =>  $post_id,
            'post_mime_type'          =>  array( 'image/jpeg' , 'image/gif' , 'image/jpg' , 'image/png' ),
            'orderby'                 => 'post_date',
            'order'                   => 'DESC'
          )
        );
        $attachments              = get_posts( $tc_args );
      }

      //case were we display an attachment (in search results for example)
      elseif ( 'attachment' == get_post_type( $post_id ) && wp_attachment_is_image( $post_id ) ) {
        $attachments = array( get_post( $post_id ) );
      }

      if ( ! isset($attachments) || empty($attachments ) )
        return;
      return isset( $attachments[0] ) && isset( $attachments[0] -> ID ) ? $attachments[0] -> ID : false;
    }//end of fn



    /**********************
    * THUMBNAIL VIEW
    **********************/
    /**
    * Display or return the thumbnail view
    * @param : thumbnail model (img, width, height), layout value, echo bool
    * @package Customizr
    * @since Customizr 3.0.10
    */
    function czr_fn_render_thumb_view( $_thumb_model , $layout = 'span3', $_echo = true ) {
      if ( empty( $_thumb_model ) )
        return;
      //extract "tc_thumb" , "tc_thumb_height" , "tc_thumb_width"
      extract( $_thumb_model );
      $thumb_img        = ! isset( $_thumb_model) ? false : $tc_thumb;
      $thumb_img        = apply_filters( 'tc_post_thumb_img', $thumb_img, CZR_utils::czr_fn_id() );
      if ( ! $thumb_img )
        return;

      //handles the case when the image dimensions are too small
      $thumb_size       = apply_filters( 'tc_thumb_size' , CZR_init::$instance -> tc_thumb_size, CZR_utils::czr_fn_id()  );
      $no_effect_class  = ( isset($tc_thumb) && isset($tc_thumb_height) && ( $tc_thumb_height < $thumb_size['height']) ) ? 'no-effect' : '';
      $no_effect_class  = ( esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_img') ) || ! isset($tc_thumb) || empty($tc_thumb_height) || empty($tc_thumb_width) ) ? '' : $no_effect_class;

      //default hover effect
      $thumb_wrapper    = sprintf('<div class="%5$s %1$s"><div class="round-div"></div><a class="round-div %1$s" href="%2$s" title="%3$s"></a>%4$s</div>',
                                    implode( " ", apply_filters( 'tc_thumbnail_link_class', array( $no_effect_class ) ) ),
                                    get_permalink( get_the_ID() ),
                                    esc_attr( strip_tags( get_the_title( get_the_ID() ) ) ),
                                    $thumb_img,
                                    implode( " ", apply_filters( 'tc_thumb_wrapper_class', array('thumb-wrapper') ) )
      );

      $thumb_wrapper    = apply_filters_ref_array( 'tc_post_thumb_wrapper', array( $thumb_wrapper, $thumb_img, CZR_utils::czr_fn_id() ) );

      //cache the thumbnail view
      $html             = sprintf('<section class="tc-thumbnail %1$s">%2$s</section>',
        apply_filters( 'tc_post_thumb_class', $layout ),
        $thumb_wrapper
      );
      $html = apply_filters_ref_array( 'tc_render_thumb_view', array( $html, $_thumb_model, $layout ) );
      if ( ! $_echo )
        return $html;
      echo $html;
    }//end of function

    /**********************
    * HELPER CALLBACK
    **********************/
    /**
    * hook wp_get_attachment_image_attributes
    * Get rid of the srcset attribute (responsive images)
    * @param $attr array of image attributes
    * @return  array of image attributes
    *
    * @package Customizr
    * @since Customizr 3.4.16
    */
    function czr_fn_remove_srcset_attr( $attr ) {
      if ( isset( $attr[ 'srcset' ] ) ) {
        unset( $attr['srcset'] );
        //to ensure a "local" removal we have to remove this filter callback, so it won't hurt
        //responsive images sitewide
        remove_filter( current_filter(), array( $this, __FUNCTION__ ) );
      }
      return $attr;
    }

    /**********************
    * SETTER CALLBACK
    **********************/
    /**
    * hook tc_post_thumb_inline_style
    * Replace default widht:auto by width:100%
    * @param array of args passed by apply_filters_ref_array method
    * @return  string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function czr_fn_change_thumb_inline_css( $_style, $image, $_filtered_thumb_size) {
      //conditions :
      //note : handled with javascript if tc_center_img option enabled
      $_bool = array_product(
        array(
          ! esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_img') ),
          false != $image,
          ! empty($image),
          isset($_filtered_thumb_size['width']),
          isset($_filtered_thumb_size['height'])
        )
      );
      if ( ! $_bool )
        return $_style;

      $_width     = $_filtered_thumb_size['width'];
      $_height    = $_filtered_thumb_size['height'];
      $_new_style = '';
      //if we have a width and a height and at least on dimension is < to default thumb
      if ( ! empty($image[1])
        && ! empty($image[2])
        && ( $image[1] < $_width || $image[2] < $_height )
        ) {
          $_new_style           = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
      }
      if ( empty($image[1]) || empty($image[2]) )
        $_new_style             = sprintf('min-width:%1$spx;min-height:%2$spx;max-width: none;width: auto;max-height: none;', $_width, $_height );
      return $_new_style;
    }

}//end of class
endif;

?><?php
/**
* Sidebar actions
* The default widgets areas are defined as properties of the CZR_utils class in class-fire-utils.php
* CZR_utils::$inst -> sidebar_widgets for left and right sidebars
* CZR_utils::$inst -> footer_widgets for the footer
* The widget area are then fired in class-fire-widgets.php
* You can modify those default widgets with 3 filters : tc_default_widgets, tc_footer_widgets, tc_sidebar_widgets
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_sidebar' ) ) :
  class CZR_sidebar {
      static $instance;
      function __construct () {
        self::$instance =& $this;
        add_action ( 'wp'       , array( $this , 'czr_fn_set_sidebar_hooks' ) );
      }


      /******************************************
      * HOOK
      ******************************************/
      /**
      * Set sidebar hooks
      * hook : wp
      *
      * @since Customizr 3.3+
      */
      function czr_fn_set_sidebar_hooks() {
        //displays left sidebar
    		add_action ( '__before_article_container'  , array( $this , 'czr_fn_sidebar_display' ) );
    		add_action ( '__before_left_sidebar'       , array( $this , 'czr_fn_social_in_sidebar' ) );

        //displays right sidebar
    		add_action ( '__after_article_container'   , array( $this , 'czr_fn_sidebar_display' ) );
    		add_action ( '__before_right_sidebar'      , array( $this , 'czr_fn_social_in_sidebar' ) );

        //since 3.2.0 show/hide the WP built-in widget icons
        add_filter ( 'tc_left_sidebar_class'       , array( $this , 'czr_fn_set_sidebar_wrapper_widget_class' ) );
        add_filter ( 'tc_right_sidebar_class'      , array( $this , 'czr_fn_set_sidebar_wrapper_widget_class' ) );
      }



      /******************************************
      * VIEW
      ******************************************/
      /**
      * Displays the sidebar or the front page featured pages area
      * If no widgets are set, displays a placeholder
      *
      * @param Name of the widgetized area
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_sidebar_display() {
        //first check if home and no content option is choosen
        if ( czr_fn__f( '__is_home_empty') )
          return;
        //gets current screen layout
        $screen_layout        = CZR_utils::czr_fn_get_layout( CZR_utils::czr_fn_id() , 'sidebar'  );
		    // GY: add relative right and left for LTR/RTL sites
        $rel_left             = is_rtl() ? 'right' : 'left';
        $rel_right            = is_rtl() ? 'left' : 'right';
        //gets position from current hook and checks the context
        $position             = apply_filters(
                                'tc_sidebar_position',
                                strpos(current_filter(), 'before') ? $rel_left : $rel_right
        );

        if ( 'left' == $position && $screen_layout != 'l' && $screen_layout != 'b' )
          return;
        if ( 'right' == $position && $screen_layout != 'r' && $screen_layout != 'b' )
          return;

        //gets the global layout settings
        $global_layout        = apply_filters( 'tc_global_layout' , CZR_init::$instance -> global_layout );
        $sidebar_layout       = $global_layout[$screen_layout];

        //defines the sidebar wrapper class
        $class                = implode(" ", apply_filters( "tc_{$position}_sidebar_class" , array( $sidebar_layout['sidebar'] , $position , 'tc-sidebar' ) ) );
        ob_start();
        ?>

        <div class="<?php echo $class  ?>">
           <div id="<?php echo $position ?>" class="widget-area" role="complementary">
              <?php
                do_action( "__before_{$position}_sidebar" );##hook of social icons

                if ( apply_filters( 'tc_has_sidebar_widgets', is_active_sidebar( $position ), $position ) )
                  get_sidebar( $position );
                else
                  $this -> czr_fn_display_sidebar_placeholder($position);

                do_action( "__after_{$position}_sidebar" );
              ?>
            </div><!-- //#left or //#right -->
        </div><!--.tc-sidebar -->

        <?php
        $html = ob_get_contents();
        if ($html) ob_end_clean();
        echo apply_filters( 'tc_sidebar_display', $html, $sidebar_layout, $position );
      }//end of function




      /**
      * When do we display this placeholder ?
      * User logged in
      * + Admin
      * + User did not dismissed the notice
      * @param : string position left or right
      * @since Customizr 3.3
      */
      private function czr_fn_display_sidebar_placeholder( $position ) {
        if ( ! CZR_placeholders::czr_fn_is_widget_placeholder_enabled( 'sidebar' ) )
          return;
        ?>
        <aside class="tc-placeholder-wrap tc-widget-placeholder">
          <?php
            printf('<span class="tc-admin-notice">%1$s</span>',
              __( 'This block is visible for admin users only.', 'customizr')
            );

            printf('<h4>%1$s</h4>',
              sprintf( __( 'The %s sidebar has no widgets.', 'customizr'), $position )
            );

            printf('<p><strong>%1$s</strong></p>',
              sprintf( __("Add widgets to this sidebar %s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( 'panel' => 'widgets') ), __( "Add widgets", "customizr"), __("now", "customizr") ),
                sprintf('<a class="tc-inline-dismiss-notice" data-position="sidebar" href="#" title="%1$s">%1$s</a>',
                  __( 'dismiss this notice', 'customizr')
                )
              )
            );

            printf('<p><i>%1s <a href="http:%2$s" title="%3$s" target="blank">%4$s</a></i></p>',
              __( 'You can also remove this sidebar by changing the current page layout.', 'customizr' ),
              '//docs.presscustomizr.com/article/107-customizr-theme-options-pages-and-posts-layout',
              __( 'Changing the layout in the Customizr theme' , 'customizr'),
              __( 'See the theme documentation.' , 'customizr' )
            );

            printf('<a class="tc-dismiss-notice" data-position="sidebar" href="#" title="%1$s">%1$s x</a>',
              __( 'dismiss notice', 'customizr')
            );
        ?>
        </aside>
        <?php
      }




      /**
      * Displays the social networks in sidebars
      *
      * @package Customizr
      * @since Customizr 1.0
      */
      function czr_fn_social_in_sidebar() {
        //get option from current hook
        $option               = ( false != strpos(current_filter(), 'left') ) ? 'tc_social_in_left-sidebar' : 'tc_social_in_right-sidebar';

        //when do we display this block ?
        //1) if customizing: must be enabled
        //2) if not customizing : must be enabled and have social networks.
        $_nothing_to_render         = 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( $option ) );

        $_nothing_to_render_front   = $_nothing_to_render || ! ( $_socials = czr_fn__f( '__get_socials' ) ) ? true : $_nothing_to_render;

        //only when partial refresh enabled, otherwise we fall back on refresh
        $_nothing_to_render         = CZR___::$instance -> czr_fn_is_customizing() && czr_fn_is_partial_refreshed_on() ? $_nothing_to_render : $_nothing_to_render_front;

        if ( $_nothing_to_render )
          return;

        $_title = esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_social_in_sidebar_title') );
        $html = sprintf('<aside class="%1$s">%2$s<div class="social-links">%3$s</div></aside>',
            implode( " " , apply_filters( 'tc_sidebar_block_social_class' , array('social-block', 'widget', 'widget_social') ) ),
            ! $_title ? '' : apply_filters( 'tc_sidebar_socials_title' , sprintf( '<h3 class="widget-title">%1$s</h3>', $_title ) ),
            $_socials
        );
        echo apply_filters( 'tc_social_in_sidebar', $html, current_filter() );
      }




      /**
      * Displays the widget icons if option is enabled in customizer
      * @uses filter tc_footer_widget_wrapper_class
      *
      * @package Customizr
      * @since Customizr 3.2.0
      */
      function czr_fn_set_sidebar_wrapper_widget_class($_original_classes) {
        $_no_icons_classes = array_merge($_original_classes, array('no-widget-icons'));

        if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_show_sidebar_widget_icon' ) ) )
          return ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_show_title_icon' ) ) ) ? $_no_icons_classes : $_original_classes;
         //last condition
        return $_no_icons_classes;
      }

  }//end of class
endif;

?><?php
/**
* Slider Model / Views / Helpers Class
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013 - 2015 , Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_slider' ) ) :
class CZR_slider {

  static $instance;
  private static $sliders_model;
  static $rendered_sliders;

  function __construct () {
    self::$instance =& $this;
    add_action( 'tc_set_slider_hooks_done' , array( $this, 'czr_fn_maybe_setup_parallax' ) );
    add_action( 'template_redirect'        , array( $this, 'czr_fn_set_slider_hooks' ) );
    //set user customizer options. @since v3.2.0
    add_filter( 'tc_slider_layout_class'   , array( $this , 'czr_fn_set_slider_wrapper_class' ) );
    //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
    //fired on hook : wp_enqueue_scripts
    //Set thumbnail specific design based on user options
    //Set user defined height
    add_filter( 'tc_user_options_style'    , array( $this , 'czr_fn_write_slider_inline_css' ) );
    //tc_slider_height is fired in CZR_slider::czr_fn_write_slider_inline_css()
    add_filter( 'tc_slider_height'         , array( $this, 'czr_fn_set_demo_slider_height') );
  }//end of construct




  /******************************
  * HOOK SETUP
  *******************************/
  /**
  * callback of template_redirect
  * Set slider hooks
  * @return  void
  */
  function czr_fn_set_slider_hooks() {
    //get slides model
    //extract $slider_name_id, $slides, $layout_class, $img_size
    extract( $this -> czr_fn_get_slider_model() );
    //returns nothing if no slides to display
    if ( ! isset($slides) || ! $slides )
      return;

    add_action( '__after_header'            , array( $this , 'czr_fn_slider_display' ) );
    add_action( '__after_carousel_inner'    , array( $this , 'czr_fn_slider_control_view' ) );

    //adds the center-slides-enabled css class
    add_filter( 'tc_carousel_inner_classes' , array( $this, 'czr_fn_set_inner_class') );

    //adds infos in the caption data of the demo slider
    add_filter( 'tc_slide_caption_data'     , array( $this, 'czr_fn_set_demo_slide_data'), 10, 3 );

    //wrap the slide into a link
    add_filter( 'tc_slide_background'       , array( $this, 'czr_fn_link_whole_slide'), 5, 5 );

    //display a notice for first time users
    if ( 'demo' == $slider_name_id ) {
      //display a notice for first time users
      add_action( '__after_carousel_inner'   , array( $this, 'czr_fn_maybe_display_dismiss_notice') );
    }

    //display an edit deep link to the Slider section in the Customize or post/page
    add_action( '__after_carousel_inner'    , array( $this, 'czr_fn_render_slider_edit_link_view'), 10, 2 );

    //fire event when all the hooks have been set
    do_action( 'tc_set_slider_hooks_done' );
  }

  /******************************
  * MODELS
  *******************************/
  /**
  * Return a single slide model
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  private function czr_fn_get_single_slide_model( $slider_name_id, $_loop_index , $id , $img_size ) {
    //check if slider enabled for this attachment and go to next slide if not
    $slider_checked         = esc_attr(get_post_meta( $id, $key = 'slider_check_key' , $single = true ));
    if ( ! isset( $slider_checked) || $slider_checked != 1 )
      return;

    //title
    $title                  = esc_attr(get_post_meta( $id, $key = 'slide_title_key' , $single = true ));
    $default_title_length   = apply_filters( 'tc_slide_title_length', 80 );
    $title                  = $this -> czr_fn_trim_text( $title, $default_title_length, '...' );

    //lead text
    $text                   = get_post_meta( $id, $key = 'slide_text_key' , $single = true );
    $default_text_length    = apply_filters( 'tc_slide_text_length', 250 );
    $text                   = $this -> czr_fn_trim_text( $text, $default_text_length, '...' );

    //button text
    $button_text            = esc_attr(get_post_meta( $id, $key = 'slide_button_key' , $single = true ));
    $default_button_length  = apply_filters( 'tc_slide_button_length', 80 );
    $button_text            = $this -> czr_fn_trim_text( $button_text, $default_button_length, '...' );

    //link post id
    $link_id                = apply_filters( 'tc_slide_link_id', esc_attr(get_post_meta( $id, $key = 'slide_link_key' , $single = true )), $id, $slider_name_id );
    //link
    $link_url               = esc_url( get_post_meta( $id, $key = 'slide_custom_link_key', $single = true ) );

    if ( ! $link_url )
      $link_url = $link_id ? get_permalink( $link_id ) : $link_url;

    $link_url               = apply_filters( 'tc_slide_link_url', $link_url, $id, $slider_name_id );

    //link target
    $link_target_bool       = esc_attr(get_post_meta( $id, $key= 'slide_link_target_key', $single = true ));
    $link_target            = apply_filters( 'tc_slide_link_target', $link_target_bool ? '_blank' : '_self', $id, $slider_name_id );

    //link the whole slide?
    $link_whole_slide       = apply_filters( 'tc_slide_link_whole_slide', esc_attr(get_post_meta( $id, $key= 'slide_link_whole_slide_key', $single = true )), $id, $slider_name_id );

    //checks if $text_color is set and create an html style attribute
    $text_color             = esc_attr(get_post_meta( $id, $key = 'slide_color_key' , $single = true ));
    $color_style            = ( $text_color != null) ? 'style="color:'.$text_color.'"' : '';

    //attachment image
    $alt                    = apply_filters( 'tc_slide_background_alt' , trim(strip_tags(get_post_meta( $id, '_wp_attachment_image_alt' , true))) );

    $slide_background_attr  = array( 'class' => 'slide' , 'alt' => $alt );

    //allow responsive images?
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_resp_slider_img') ) ) {
        $slide_background_attr['srcset'] = " ";
        //trick, => will produce an empty attr srcset as in wp-includes/media.php the srcset is calculated and added
        //only when the passed srcset attr is not empty. This will avoid us to:
        //a) add a filter to get rid of already computed srcset
        // or
        //b) use preg_replace to get rid of srcset and sizes attributes from the generated html
        //Side effect:
        //we'll see an empty ( or " " depending on the browser ) srcset attribute in the html
        //to avoid this we filter the attributes getting rid of the srcset if any.
        //Basically this trick, even if ugly, will avoid the srcset attr computation
        add_filter( 'wp_get_attachment_image_attributes', array( CZR_post_thumbnails::$instance, 'czr_fn_remove_srcset_attr' ) );
      }
    $slide_background       = wp_get_attachment_image( $id, $img_size, false, $slide_background_attr );

    //adds all values to the slide array only if the content exists (=> handle the case when an attachment has been deleted for example). Otherwise go to next slide.
    if ( !isset($slide_background) || empty($slide_background) )
      return;

    return array(
      'title'               =>  $title,
      'text'                =>  $text,
      'button_text'         =>  $button_text,
      'link_id'             =>  $link_id,
      'link_url'            =>  $link_url,
      'link_target'         =>  $link_target,
      'link_whole_slide'    =>  $link_whole_slide,
      'active'              =>  ( 0 == $_loop_index ) ? 'active' : '',
      'color_style'         =>  $color_style,
      'slide_background'    =>  $slide_background
    );
  }



  /**
  * Return a single post slide pre model
  * Returns and array of pre slides with data
  *
  * This method will build up the single object model which will be
  * the base for the actual post slide model
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_single_post_slide_pre_model( $_post , $img_size, $args ){
    $ID                     = $_post->ID;

    //attachment image
    $thumb                  = CZR_post_thumbnails::$instance -> czr_fn_get_thumbnail_model($img_size, $ID, null, isset($args['slider_responsive_images']) ? $args['slider_responsive_images'] : null );
    $slide_background       = isset($thumb) && isset($thumb['tc_thumb']) ? $thumb['tc_thumb'] : null;
    // we don't want to show slides with no image
    if ( ! $slide_background )
      return false;

    //title
    $title                  = ( isset( $args['show_title'] ) && $args['show_title'] ) ? $this -> czr_fn_get_post_slide_title( $_post, $ID) : '';

    //lead text
    $text                   = ( isset( $args['show_excerpt'] ) && $args['show_excerpt'] ) ? $this -> czr_fn_get_post_slide_excerpt( $_post, $ID) : '';

    return compact( 'ID', 'title', 'text', 'slide_background' );
  }


  /**
  * Return a single post slide model
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_single_post_slide_model( $slider_name_id, $_loop_index , $_post_slide , $common, $img_size ){
    extract( $_post_slide );
    extract( $common );

    //background image
    $slide_background       = apply_filters( 'tc_posts_slide_background', $slide_background, $ID );

    // we don't want to show slides with no image
    if ( ! $slide_background )
      return false;

    $title                  = apply_filters('tc_posts_slider_title', $title, $ID );
    //lead text
    $text                   = apply_filters('tc_posts_slider_text', $text, $ID );

    //button
    $button_text            = apply_filters('tc_posts_slider_button_text', $button_text, $ID );

    //link
    $link_id                = apply_filters( 'tc_posts_slide_link_id', $ID );

    $link_url               = apply_filters( 'tc_posts_slide_link_url', $link_id ? get_permalink( $link_id ) : '', $ID );

    $link_target            = apply_filters( 'tc_posts_slide_link_target', '_self', $ID );

    $link_whole_slide       = apply_filters( 'tc_posts_slide_link_whole_slide', $link_whole_slide, $ID );

    $active                 = ( 0 == $_loop_index ) ? 'active' : '';
    $color_style            = apply_filters( 'tc_posts_slide_color_style', '', $ID );

    return apply_filters( 'tc_single_post_slide_model', compact(
        'title',
        'text',
        'button_text',
        'link_id',
        'link_url',
        'link_target',
        'link_whole_slide',
        'active',
        'color_style',
        'slide_background'
    ), $ID );
  }




  /**
  * Helper
  * Return an array of the slide models from option or default
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.0.15
  *
  */
  private function czr_fn_get_the_slides( $slider_name_id, $img_size ) {
    //returns the default slider if requested
    if ( 'demo' == $slider_name_id )
      return apply_filters( 'tc_default_slides', CZR_init::$instance -> default_slides );
    else if ( 'tc_posts_slider' == $slider_name_id ) {
      $use_transient = apply_filters( 'tc_posts_slider_use_transient', ! CZR___::$instance -> czr_fn_is_customizing() );
      //Do not use transient when in the customizer preview (this class is not called in the customize left panel)
      $store_transient = $load_transient = $use_transient;
      // delete transient when in the customize preview
      if ( ! $use_transient )
        delete_transient( 'tc_posts_slides' );

      return $this -> czr_fn_get_the_posts_slides( $slider_name_id, $img_size, $load_transient , $store_transient );
    }
    //if not demo or tc_posts_slider, we get slides from options
    $all_sliders    = CZR_utils::$inst -> czr_fn_opt( 'tc_sliders');
    $saved_slides   = ( isset($all_sliders[$slider_name_id]) ) ? $all_sliders[$slider_name_id] : false;

    //if the slider not longer exists or exists but is empty, return false
    if ( ! $this -> czr_fn_slider_exists( $saved_slides) )
      return;

    //inititalize the slides array
    $slides   = array();

    //init slide active state index
    $_loop_index        = 0;

    //GENERATE SLIDES ARRAY
    foreach ( $saved_slides as $s ) {
      $slide_object           = get_post($s);
      //next loop if attachment does not exist anymore (has been deleted for example)
      if ( ! isset( $slide_object) )
        continue;

      $id                     = $slide_object -> ID;

      $slide_model = $this -> czr_fn_get_single_slide_model( $slider_name_id, $_loop_index, $id, $img_size);

      if ( ! $slide_model )
        continue;

      $slides[$id] = $slide_model;

      $_loop_index++;
    }//end of slides loop

    //returns the slides or false if nothing
    return apply_filters('tc_the_slides', ! empty($slides) ? $slides : false );
  }



  /**
  * Helper
  * Return an array of the post slide models
  * Returns and array of slides with data
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  /* Steps;
  * 1) get the pre slides model, can be either stored in the transient or live generated
  * 2) get the actual model from the pre_model
  *
  *
  *
  * The difference between the pre_model and the actual model is because in the
  * transient we do not store the whole slide model, some info are not needed.
  * Also the actual model can be filtered, allowing user's filter and to translate it
  * mostly with qtranslate (polylang will force us, most likely if I don't find any
  * other suitable solution, to not use the transient).
  */
  private function czr_fn_get_the_posts_slides( $slider_name_id, $img_size, $load_transient = true, $store_transient = true ) {

    $load_transient  = apply_filters( 'tc_posts_slider_load_transient'  , $load_transient );
    $store_transient = apply_filters( 'tc_posts_slider_store_transient', $store_transient );

    $pre_slides      = $this -> czr_fn_get_pre_posts_slides( compact( 'img_size', 'load_transient', 'store_transient' ) );

    //filter the pre_model
    $pre_slides      = apply_filters( 'tc_posts_slider_pre_model', $pre_slides );

    //if the slider not longer exists or exists but is empty, return false
    if ( ! $this -> czr_fn_slider_exists( $pre_slides ) )
      return false;

    //extract pre_slides model
    extract($pre_slides);

    //inititalize the slides array
    $slides      = array();

    $_loop_index = 0;

    //GENERATE SLIDES ARRAY
    foreach ( $posts as $_post_slide ) {
      $slide_model = $this -> czr_fn_get_single_post_slide_model( $slider_name_id, $_loop_index, $_post_slide, $common, $img_size);

      if ( ! $slide_model )
        continue;

      $slides[ $_post_slide['ID'] ] = $slide_model;

      $_loop_index++;
    }//end of slides loop

    //returns the slides or false if nothing
    return apply_filters('tc_the_posts_slides', ! empty($slides) ? $slides : false );

  }



  /**
  * Helper
  * Return an ass array of 'posts'=> array of the post slide pre models 'common' => common properties
  *
  * This method takes care of building the array of convenient objects
  * we'll use to build the actual posts slider model
  * and store it in a transient when required
  *
  * Setps:
  * - check if there's a transient and we have to use it => get the transient
  * a pre_model array
  *
  * - if not transient (both cases: no transient, don't use transient)
  *  build the array of posts to use
  *
  * - eventually store the transient
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_pre_posts_slides( $args ){

    $defaults       = array(
      'img_size'            => null,
      'load_transient'      => true,
      'store_transient'     => true,
      'transient_name'      => 'tc_posts_slides',
      //options
      'stickies_only'       => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_stickies' ) ),
      'show_title'          => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_title' ) ),
      'show_excerpt'        => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_text' ) ),
      'button_text'         => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_button_text' ) ),
      'limit'               => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_number' ) ),
      'link_type'           => esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_posts_slider_link') ),
    );

    $args         = apply_filters( 'tc_get_pre_posts_slides_args', wp_parse_args( $args, $defaults ) );
    extract( $args );

    if ( $load_transient )
      // the transient stores the pre_model
      $pre_slides = get_transient( $transient_name );
    else
      $pre_slides = false;

    // We have to retrieve the posts and build the pre_model when $pre_slides_model is null:
    // a) we don't have to use the transient
    // b) the transient doesn't exist
    if ( false !== $pre_slides )
      return $pre_slides;

    //retrieve posts from the db
    $queried_posts    = $this -> czr_fn_query_posts_slider( $args );

    if ( empty ( $queried_posts ) )
      return array();

    /*** tc_thumb setup filters ***/
    // remove smart load img parsing if any
    $smart_load_enabled = 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_img_smart_load' ) );
    if ( $smart_load_enabled )
      remove_filter( 'tc_thumb_html', array( CZR_utils::$instance, 'czr_fn_parse_imgs') );

    // prevent adding thumb inline style when no center img is added
    add_filter( 'tc_post_thumb_inline_style', '__return_empty_string', 100 );
    /*** end tc_thumb setup ***/

    //allow responsive images?
    if ( version_compare( $GLOBALS['wp_version'], '4.4', '>=' ) )
      $args['slider_responsive_images'] = 0 == esc_attr( CZR_utils::$inst->czr_fn_opt('tc_resp_slider_img') ) ? false : true ;

    /* Get the pre_model */
    $pre_slides = $pre_slides_posts = array();

    foreach ( $queried_posts as $_post ) {
      $pre_slide_model = $this ->  czr_fn_get_single_post_slide_pre_model( $_post , $img_size, $args );

      if ( ! $pre_slide_model )
        continue;

      $pre_slides_posts[] = $pre_slide_model;
    }

    /* tc_thumb reset filters */
    // re-add smart load parsing if removed
    if ( $smart_load_enabled )
      add_filter('tc_thumb_html', array(CZR_utils::$instance, 'czr_fn_parse_imgs') );
    // remove thumb style reset
    remove_filter( 'tc_post_thumb_inline_style', '__return_empty_string', 100 );
    /* end tc_thumb reset filters */

    if ( ! empty( $pre_slides_posts ) ) {
      /*** Setup shared properties ***/
      /* Shared by all post slides, stored in the "common" field */
      // button and link whole slide
      // has button to be displayed?
      if ( strstr($link_type, 'cta') )
        $button_text            = $this -> czr_fn_get_post_slide_button_text( $button_text );
      else
        $button_text            = '';

      //link the whole slide?
      $link_whole_slide       = strstr( $link_type, 'slide') ? true : false;
      /*** end Setup shared properties ***/


      /* Add common and posts to the actual pre_slides array */
      $pre_slides['common']  = compact( 'button_text', 'link_whole_slide');
      $pre_slides['posts']   = $pre_slides_posts;

    }

    if ( $store_transient )
      set_transient( $transient_name, $pre_slides , 60*60*24*365*20 );//20 years of peace

    return $pre_slides;
  }



  /**
  * return the slider block model
  * @return  array($slider_name_id, $slides, $layout_class)
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  private function czr_fn_get_slider_model() {
    //Do we have a slider to display in this context ?
    if ( ! $this -> czr_fn_is_slider_possible() )
      return array();

    //gets the actual page id if we are displaying the posts page
    $queried_id                   = $this -> czr_fn_get_real_id();

    $slider_name_id               = $this -> czr_fn_get_current_slider( $queried_id );

    if ( ! $this -> czr_fn_is_slider_active( $queried_id) )
      return array();

    if ( ! empty( self::$sliders_model ) && is_array( self::$sliders_model ) && array_key_exists( $slider_name_id, self::$sliders_model ) )
      return self::$sliders_model[ $slider_name_id ];

    //gets slider options if any
    $layout_value                 = czr_fn__f('__is_home') ? CZR_utils::$inst->czr_fn_opt( 'tc_slider_width' ) : esc_attr(get_post_meta( $queried_id, $key = 'slider_layout_key' , $single = true ));
    $layout_value                 = apply_filters( 'tc_slider_layout', $layout_value, $queried_id );

    //declares the layout vars
    $layout_class                 = ( 0 == $layout_value ) ? array('container', 'carousel', 'customizr-slide', $slider_name_id ) : array('carousel', 'customizr-slide', $slider_name_id);
    $img_size                     = apply_filters( 'tc_slider_img_size' , ( 0 == $layout_value ) ? 'slider' : 'slider-full');

    //get slides
    $slides                       = $this -> czr_fn_get_the_slides( $slider_name_id , $img_size );

    //store the model per slider_name_id
    self::$sliders_model[ $slider_name_id ] = compact( "slider_name_id", "slides", "layout_class" , "img_size" );

    return self::$sliders_model[ $slider_name_id ];
  }




  /**
  * Helper
  * Returns the array of eligible posts for the slider of posts
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_query_posts_slider( $args = array() ) {
    global $wpdb;

    $defaults       = array(
      'stickies_only'  => 0,
      'post_status'    => 'publish',
      'post_type'      => 'post',
      'show_title'     => true,
      'show_excerpt'   => true,
      'pt_where'       => " AND meta_key='_thumbnail_id'",
      'pa_where'       => " AND attachments.post_type='attachment' AND attachments.post_mime_type LIKE '%image%' AND attachments.post_parent=posts.ID",
      'join'           => '',
      'join_where'     => '',
      'order_by'       => 'posts.post_date DESC',
      'limit'          => 5,
      'offset'         => 0,
    );

    $args           = apply_filters( 'tc_query_posts_slider_args', wp_parse_args( $args, $defaults ) );
    extract( $args );

    /* Set up */
    $columns        = 'posts.ID, posts.post_date';
    $columns       .= $show_title   ? ', posts.post_title' : '';
    $columns       .= $show_excerpt ? ', posts.post_excerpt, posts.post_content' : '';
    // if we have to show the title or the excerpt the post_password field is needed to know whether or not is a protected post
    $columns       .= $show_title || $show_excerpt ? ', posts.post_password' : '';

    $pt_where       = "posts.post_status='$post_status' AND posts.post_type='$post_type'". $pt_where;
    $pa_where       = "posts.post_status='$post_status' AND posts.post_type='$post_type'". $pa_where;

    // Do we have to show only sticky posts?
    if ( $stickies_only ) {
      // Are there sticky posts?
      $_sticky_posts  = get_option('sticky_posts');
      $_sticky_column = '';
      if ( ! empty( $_sticky_posts ) ) {
        $_sticky_posts_ids = implode(',', $_sticky_posts );
        $_filter_stickies_only = sprintf(" AND posts.ID IN (%s)",
            $_sticky_posts_ids
        );

        $pa_where .= $_filter_stickies_only;
        $pt_where .= $_filter_stickies_only;
      }
    }

    $sql = sprintf( 'SELECT DISTINCT %1$s FROM ( %2$s ) as posts %3$s %4$s ORDER BY %5$s LIMIT %6$s OFFSET %7$s',
             apply_filters( 'tc_query_posts_slider_columns', $columns, $args ),
             $this -> czr_fn_get_posts_have_tc_thumb_sql(
               apply_filters( 'tc_query_posts_slider_columns', $columns, $args ),
               apply_filters( 'tc_query_posts_slide_thumbnail_where', $pt_where, $args ),
               apply_filters( 'tc_query_posts_slider_attachment_where', $pa_where, $args )
             ),
             apply_filters( 'tc_query_posts_slider_join', $join, $args ),
             apply_filters( 'tc_query_posts_slider_join_where', $join_where, $args ),
             apply_filters( 'tc_query_posts_slider_orderby', $order_by, $args ),
             $limit,
             $offset
    );

    $sql = apply_filters( 'tc_query_posts_slider_sql', $sql, $args );

    $_posts = $wpdb->get_results( $sql );
    return apply_filters( 'tc_query_posts_slider', $_posts, $args );
  }



  /**
  * Helper
  * Returns the SQL to retrieve the posts which have a thumbnail OR attachments
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_tc_thumb_sql( $_columns, $_pt_where = '', $_pa_where = '' ) {
    return apply_filters( 'tc_get_posts_have_tc_thumb_sql', sprintf( '%1$s UNION %2$s',
        $this -> czr_fn_get_posts_have_thumbnail_sql( $_columns, $_pt_where ),
        $this -> czr_fn_get_posts_have_attachment_sql( $_columns, $_pa_where )
    ));
  }

  /**
  * Helper
  * Returns the SQL to retrieve the posts which have a thumbnail
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_thumbnail_sql( $_columns, $_where = '' ) {
    global $wpdb;
    return apply_filters( 'tc_get_posts_have_thumbnail_sql', "
        SELECT $_columns
        FROM $wpdb->posts AS posts INNER JOIN $wpdb->postmeta AS metas
        ON posts.ID=metas.post_id
        WHERE $_where
   ");
  }

  /**
  * Helper
  * Returns the SQL to retrieve the posts which have an attachment
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  private function czr_fn_get_posts_have_attachment_sql( $_columns, $_where = '' ) {
    global $wpdb;
    return apply_filters( 'tc_get_posts_have_attachment_sql', "
        SELECT $_columns FROM $wpdb->posts attachments, $wpdb->posts posts
        WHERE $_where
    ");
  }





  /******************************
  * VIEWS
  *******************************/
  /**
  * Slider View
  * Displays the slider based on the context : home, post/page.
  * hook : __after_header
  * @package Customizr
  * @since Customizr 1.0
  *
  */
  function czr_fn_slider_display() {
    //get slides model
    //extract $slider_name_id, $slides, $layout_class, $img_size
    extract( $this -> czr_fn_get_slider_model() );
    //returns nothing if no slides to display
    if ( ! isset($slides) || ! $slides )
      return;

    self::$rendered_sliders++ ;

    //define carousel inner classes
    $_inner_classes  = implode( ' ' , apply_filters( 'tc_carousel_inner_classes' , array( 'carousel-inner' ) ) );
    $_layout_classes = implode( " " , apply_filters( 'tc_slider_layout_class' , $layout_class ) );

    ob_start();
    ?>
    <div id="customizr-slider-<?php echo self::$rendered_sliders ?>" class="<?php echo $_layout_classes ?> ">

      <?php $this -> czr_fn_render_slider_loader_view( $slider_name_id ); ?>

      <?php do_action( '__before_carousel_inner' , $slides, $slider_name_id )  ?>

      <div class="<?php echo $_inner_classes?>">
        <?php
          foreach ($slides as $id => $data) {
            $_view_model = compact( "id", "data" , "slider_name_id", "img_size" );
            $this -> czr_fn_render_single_slide_view( $_view_model );
          }
        ?>
      </div><!-- /.carousel-inner -->

      <?php  do_action( '__after_carousel_inner' , $slides, $slider_name_id )  ?>

    </div><!-- /#customizr-slider -->

    <?php
    $html = ob_get_contents();
    if ($html) ob_end_clean();
    echo apply_filters( 'tc_slider_display', $html, $slider_name_id );
  }



  /**
  * Single slide view
  * Renders a single slide
  * @param $_view_model = array( $id, $data , $slider_name_id, $img_size )
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_render_single_slide_view( $_view_model ) {
    //extract $_view_model = array( $id, $data , $slider_name_id, $img_size )
    extract( $_view_model );

    $slide_classes = implode( ' ', apply_filters( 'tc_single_slide_item_classes', array( 'czr-item', $data['active'], "slide-{$id}" ) ) );
    ?>
    <div class="<?php echo $slide_classes; ?>">
      <?php
        $this -> czr_fn_render_slide_background_view( $_view_model );
        $this -> czr_fn_render_slide_caption_view( $_view_model );
        $this -> czr_fn_render_slide_edit_link_view( $_view_model );
      ?>
    </div><!-- /.czr-item -->
    <?php
  }


  /**
  * Slider loader view
  * This feature is only fired in browser with js enabled => cf the embedded script
  * @param (string) $slider_name_id
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_render_slider_loader_view( $slider_name_id ) {
    if ( ! $this -> czr_fn_is_slider_loader_active( $slider_name_id ) )
      return;

    if ( ! apply_filters( 'tc_slider_loader_gif_only', false ) )
      $_pure_css_loader = sprintf( '<div class="tc-css-loader %1$s">%2$s</div>',
            implode( ' ', apply_filters( 'tc_pure_css_loader_add_classes', array( 'tc-mr-loader') ) ),
            apply_filters( 'tc_pure_css_loader_inner', '<div></div><div></div><div></div>')
      );
    else
      $_pure_css_loader = '';
    ?>
      <div id="tc-slider-loader-wrapper-<?php echo self::$rendered_sliders ?>" class="tc-slider-loader-wrapper" style="display:none;">
        <div class="tc-img-gif-loader"></div>
        <?php echo $_pure_css_loader; ?>
      </div>

      <script type="text/javascript">
        document.getElementById("tc-slider-loader-wrapper-<?php echo self::$rendered_sliders ?>").style.display="block";
      </script>
    <?php
  }



  /**
  * Slide Background subview
  * @param $_view_model = array( $id, $data , $slider_name_id, $img_size )
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_render_slide_background_view( $_view_model ) {
    //extract $_view_model = array( $id, $data , $slider_name_id, $img_size )
    extract( $_view_model );
    ?>
    <div class="<?php echo apply_filters( 'tc_slide_content_class', sprintf('carousel-image %1$s' , $img_size ) ); ?>">
      <?php
        do_action('__before_all_slides');
        do_action_ref_array ("__before_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );

          echo apply_filters( 'tc_slide_background', $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data );

        do_action_ref_array ("__after_slide_{$id}" , array( $data['slide_background'], $data['link_url'], $id, $slider_name_id, $data ) );
        do_action('__after_all_slides');
      ?>
    </div> <!-- .carousel-image -->
    <?php
  }

  /**
  *
  * Link whole slide
  * hook: tc_slide_background
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_link_whole_slide( $slide_background, $link_url, $id, $slider_name_id, $data ) {
    if ( isset( $data['link_whole_slide'] )  && $data['link_whole_slide'] && $link_url )
      $slide_background = sprintf('<a href="%1$s" class="tc-slide-link" target="%2$s" title="%3$s">%4$s</a>',
                                $link_url,
                                $data['link_target'],
                                __('Go to', 'customizr'),
                                $slide_background
      );
    return $slide_background;
  }

  /**
  * Slide caption subview
  * @param $_view_model = array( $id, $data , $slider_name_id, $img_size )
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_render_slide_caption_view( $_view_model ) {
    //extract $_view_model = array( $id, $data , $slider_name_id, $img_size )
    extract( $_view_model );
    //filters the data before (=> used for demo for example )
    $data                   = apply_filters( 'tc_slide_caption_data', $data, $slider_name_id, $id );

    $show_caption           = ! ( $data['title'] == null && $data['text'] == null && $data['button_text'] == null ) ;
    if ( ! apply_filters( 'tc_slide_show_caption', $show_caption , $slider_name_id ) )
      return;

    //apply filters first
    $data['title']          = isset($data['title']) ? apply_filters( 'tc_slide_title', $data['title'] , $id, $slider_name_id ) : '';
    $data['text']           = isset($data['text']) ? esc_html( apply_filters( 'tc_slide_text', $data['text'], $id, $slider_name_id ) ) : '';
    $data['color_style']    = apply_filters( 'tc_slide_color', $data['color_style'], $id, $slider_name_id );


    $data['button_text']    = isset($data['button_text']) ? apply_filters( 'tc_slide_button_text', $data['button_text'], $id, $slider_name_id ) : '';

    //computes the link
    $button_link            = apply_filters( 'tc_slide_button_link', $data['link_url'] ? $data['link_url'] : 'javascript:void(0)', $id, $slider_name_id );

    printf('<div class="%1$s">%2$s %3$s %4$s</div>',
      //class
      implode( ' ', apply_filters( 'tc_slide_caption_class', array( 'carousel-caption' ), $show_caption, $slider_name_id ) ),
      //title
      ( apply_filters( 'tc_slide_show_title', $data['title'] != null, $slider_name_id ) ) ? sprintf('<%1$s class="%2$s" %3$s>%4$s</%1$s>',
                            apply_filters( 'tc_slide_title_tag', 'h1', $slider_name_id ),
                            implode( ' ', apply_filters( 'tc_slide_title_class', array( 'slide-title' ), $data['title'], $slider_name_id ) ),
                            $data['color_style'],
                            $data['title']
                          ) : '',
      //lead text
      ( apply_filters( 'tc_slide_show_text', $data['text'] != null, $slider_name_id ) ) ? sprintf('<p class="%1$s" %2$s>%3$s</p>',
                            implode( ' ', apply_filters( 'tc_slide_text_class', array( 'lead' ), $data['text'], $slider_name_id ) ),
                            $data['color_style'],
                            $data['text']
                          ) : '',
      //button call to action
      ( apply_filters( 'tc_slide_show_button', $data['button_text'] != null, $slider_name_id ) ) ? sprintf('<a class="%1$s" href="%2$s" target="%3$s">%4$s</a>',
                            implode( ' ', apply_filters( 'tc_slide_button_class', array( 'btn', 'btn-large', 'btn-primary' ), $data['button_text'], $slider_name_id ) ),
                            $button_link,
                            $data['link_target'],
                            $data['button_text']
                          ) : ''
    );
  }



  /**
  * Slide edit link subview
  * @param $_view_model = array( $id, $data , $slider_name_id, $img_size )
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  function czr_fn_render_slide_edit_link_view( $_view_model ) {
    //never display when customizing
    if ( CZR___::$instance -> czr_fn_is_customizing() )
      return;
    //extract $_view_model = array( $id, $data , $slider_name_id, $img_size )
    extract( $_view_model );

    //display edit link for logged in users with  edit_post capabilities
    //upload_files cap isn't a good lower limit 'cause for example and Author can upload_files but actually cannot edit medias he/she hasn't uploaded

    $show_slide_edit_link  = ( is_user_logged_in() && current_user_can( 'edit_post', $id ) ) ? true : false;
    $show_slide_edit_link  = apply_filters('tc_show_slide_edit_link' , $show_slide_edit_link && ! is_null($data['link_id']), $id );

    if ( ! $show_slide_edit_link )
      return;

    $_edit_link_suffix     = 'tc_posts_slider' == $slider_name_id ? '' : '#slider_sectionid';
    //in case of tc_posts_slider the $id is the *post* id, otherwise it's the attachment id
    $_edit_link            = get_edit_post_link($id) . $_edit_link_suffix;

    printf('<span class="slider edit-link btn btn-inverse"><a class="post-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
      $_edit_link,
      __( 'Edit' , 'customizr' )
    );
  }

  /**
  * Slider Edit deeplink
  * @param $slides, array of slides
  * @param $slider_name_id string, the name of the current slider
  *
  * hook : __after_carousel_inner
  * @since v3.4.9
  */

  function czr_fn_render_slider_edit_link_view( $slides, $slider_name_id ) {
    //never display when customizing
    if ( CZR___::$instance -> czr_fn_is_customizing() )
      return;
    if ( 'demo' == $slider_name_id )
      return;

    $show_slider_edit_link    = false;

    //We have to show the slider edit link to
    //a) users who can edit theme options for the slider in home -> deep link in the customizer
    //b) users who can edit the post/page where the slider is displayed for users who can edit the post/page -> deep link in the post/page slider section
    if ( czr_fn__f('__is_home') ){
      $show_slider_edit_link = ( is_user_logged_in() && current_user_can('edit_theme_options') ) ? true : false;
      $_edit_link            = CZR_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec') );
    }else if ( is_singular() ){ // we have a snippet to display sliders in categories, we don't want the slider edit link displayed there
      global $post;
      $show_slider_edit_link = ( is_user_logged_in() && ( current_user_can('edit_pages') || current_user_can( 'edit_posts', $post -> ID ) ) ) ? true : false;
      $_edit_link            = get_edit_post_link( $post -> ID ) . '#slider_sectionid';
    }

    $show_slider_edit_link = apply_filters( 'tc_show_slider_edit_link' , $show_slider_edit_link, $slider_name_id );
    if ( ! $show_slider_edit_link )
      return;

    // The posts slider shows a different text
    $_text                   = sprintf( __( 'Customize or remove %s' , 'customizr' ),
                                  ( 'tc_posts_slider' == $slider_name_id ) ?__('the posts slider', 'customizr') : __('this slider', 'customizr' )
                              );
    printf('<span class="slider deep-edit-link edit-link btn btn-inverse"><a class="slider-edit-link" href="%1$s" title="%2$s" target="_blank">%2$s</a></span>',
      $_edit_link,
      $_text
    );
  }


  /*
  * Slider controls view
  * @param slides
  * @hook : __after_carousel_inner
  * @since v3.2.0
  *
  */
  function czr_fn_slider_control_view( $_slides ) {
    if ( count( $_slides ) <= 1 )
      return;

    if ( ! apply_filters('tc_show_slider_controls' , ! wp_is_mobile() ) )
      return;

    $_html = '';
    $_html .= sprintf('<div class="tc-slider-controls %1$s">%2$s</div>',
      ! is_rtl() ? 'left' : 'right',
      sprintf('<a class="tc-carousel-control" href="#customizr-slider-%2$s" data-slide="prev">%1$s</a>',
        apply_filters( 'tc_slide_left_control', '&lsaquo;' ),
        self::$rendered_sliders
      )
    );
    $_html .= sprintf('<div class="tc-slider-controls %1$s">%2$s</div>',
      ! is_rtl() ? 'right' : 'left',
      sprintf('<a class="tc-carousel-control" href="#customizr-slider-%2$s" data-slide="next">%1$s</a>',
        apply_filters( 'tc_slide_right_control', '&rsaquo;' ),
        self::$rendered_sliders
      )
    );
    echo apply_filters( 'tc_slider_control_view', $_html );
  }


  /******************************
  * SLIDER NOTICE VIEW
  *******************************/
  /**
  * hook : __after_carousel_inner
  * @since v3.4+
  */
  function czr_fn_maybe_display_dismiss_notice() {
    if ( ! CZR_placeholders::czr_fn_is_slider_notice_on() )
      return;
    $_customizer_lnk = CZR_utils::czr_fn_get_customizer_url( array( 'control' => 'tc_front_slider', 'section' => 'frontpage_sec') );
    ?>
    <div class="tc-placeholder-wrap tc-slider-notice">
      <?php
        printf('<p><strong>%1$s</strong></p>',
          sprintf( __("Select your own slider %s, or %s (you'll be able to add one back later)." , "customizr"),
            sprintf( '<a href="%3$s" title="%1$s">%2$s</a>', __( "Select your own slider", "customizr" ), __( "now", "customizr" ), $_customizer_lnk ),
            sprintf( '<a href="#" class="tc-inline-remove" title="%1$s">%2$s</a>', __( "Remove the home page slider", "customizr" ), __( "remove this demo slider", "customizr" ) )
          )
        );
        printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
          __( 'dismiss notice', 'customizr')
        );
      ?>
    </div>
    <?php
  }





  /******************************
  * PARALLAX
  *******************************/
  //hook : wp
  //introduced in v3.4.23
  function czr_fn_maybe_setup_parallax() {
    if ( 1 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_parallax') ) )
      return;
    add_filter('tc_slider_layout_class'     , array( $this, 'czr_fn_add_parallax_wrapper_class' ) );
    add_filter('tc_carousel_inner_classes'  , array( $this, 'czr_fn_add_parallax_item_class' ) );
    add_action('wp_head'                    , array( $this, 'czr_fn_add_parallax_slider_script' ) );
  }


  //hook : wp_head
  function czr_fn_add_parallax_slider_script() {
    ?>
      <script type="text/javascript" id="do-parallax-sliders">
        jQuery( function($){
          $( '.czr-parallax-slider' ).czrParallax( { parallaxRatio : <?php echo apply_filters('tc_parallax_speed', 0.55 ); ?> } );
        });
      </script>
    <?php
  }

  //hook : tc_carousel_inner_classes
  function czr_fn_add_parallax_item_class ( $classes ) {
    array_push($classes, 'czr-parallax-slider' );
    return $classes;
  }

  //hook : tc_slider_layout_class
  function czr_fn_add_parallax_wrapper_class( $classes ) {
    array_push($classes, 'parallax-wrapper' );
    return $classes;
  }





  /******************************
  * HELPERS / SETTERS / CALLBACKS
  *******************************/
  /**
  * Returns the modified caption data array with a link to the doc
  * Only displayed for the demo slider and logged in users
  * hook : tc_slide_caption_data
  *
  * @package Customizr
  * @since Customizr 3.3.+
  *
  */
  function czr_fn_set_demo_slide_data( $data, $slider_name_id, $id ) {
    if ( 'demo' != $slider_name_id || ! is_user_logged_in() )
      return $data;

    switch ( $id ) {
      case 1 :
        //$data['title']        = __( 'Discover how to replace or remove this demo slider.', 'customizr' );
        $data['link_url']     = esc_url( 'docs.presscustomizr.com/article/175-first-steps-with-the-customizr-wordpress-theme' );
        $data['button_text']  = __( 'Discover the Customizr WordPress theme &raquo;' , 'customizr');
      break;

      case 2 :
        $data['title']        = __( 'Discover how to replace or remove this demo slider.', 'customizr' );
        $data['link_url']     = esc_url( 'docs.presscustomizr.com/article/102-customizr-theme-options-front-page#front-page-slider' );
        $data['button_text']  = __( 'Check the slider doc now &raquo;' , 'customizr');
      break;
    };

    $data['link_target'] = '_blank';
    return $data;
  }



  /**
  * Helper
  * @return  boolean
  *
  * @package Customizr
  * @since Customizr 3.3+
  *
  */
  private function czr_fn_is_slider_possible() {
    //gets the front slider if any
    $tc_front_slider              = esc_attr(CZR_utils::$inst->czr_fn_opt( 'tc_front_slider' ) );
    //when do we display a slider? By default only for home (if a slider is defined), pages and posts (including custom post types)
    $_show_slider = czr_fn__f('__is_home') ? ! empty( $tc_front_slider ) : ! is_404() && ! is_archive() && ! is_search();

    return apply_filters( 'tc_show_slider' , $_show_slider );
  }


  /**
  * helper
  * @return  boolean
  *
  * @package Customizr
  * @since Customizr 3.4.9
  */
  function czr_fn_slider_exists( $slider ){
    //if the slider not longer exists or exists but is empty, return false
    return ! ( !isset($slider) || !is_array($slider) || empty($slider) );
  }


  /**
  * helper
  * returns the slider name id
  * @return  string
  *
  */
  private function czr_fn_get_current_slider($queried_id) {
    //gets the current slider id
    $_home_slider     = CZR_utils::$inst->czr_fn_opt( 'tc_front_slider' );
    $slider_name_id   = ( czr_fn__f('__is_home') && $_home_slider ) ? $_home_slider : esc_attr( get_post_meta( $queried_id, $key = 'post_slider_key' , $single = true ) );
    return apply_filters( 'tc_slider_name_id', $slider_name_id , $queried_id);
  }


  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  number
  *
  */
  private function czr_fn_get_real_id() {
    global $wp_query;
    $queried_id                   = CZR_utils::czr_fn_id();

    return apply_filters( 'tc_slider_get_real_id', ( ! czr_fn__f('__is_home') && ! empty($queried_id) ) ?  $queried_id : get_the_ID() );
  }


  /**
  * helper
  * returns the actual page id if we are displaying the posts page
  * @return  boolean
  *
  */
  private function czr_fn_is_slider_active( $queried_id ) {
    //is the slider set to on for the queried id?
    if ( czr_fn__f('__is_home') && CZR_utils::$inst->czr_fn_opt( 'tc_front_slider' ) )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );

    $_slider_on = esc_attr( get_post_meta( $queried_id, $key = 'post_slider_check_key' , $single = true ) );
    if ( ! empty( $_slider_on ) && $_slider_on )
      return apply_filters( 'tc_slider_active_status', true , $queried_id );

    return apply_filters( 'tc_slider_active_status', false , $queried_id );
  }

  /**
  * helper
  * returns whether or not the slider loading icon must be displayed
  * @return  boolean
  *
  */
  private function czr_fn_is_slider_loader_active( $slider_name_id ) {
    //The slider loader must be printed when
    //a) we have to render the demo slider
    //b) display slider loading option is enabled (can be filtered)
    return ( 'demo' == $slider_name_id
        || apply_filters( 'tc_display_slider_loader', 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_display_slide_loader') ), $slider_name_id )
    );
  }


  /**
  * hook : tc_slider_height, fired in tc_user_options_style
  * @return number height value
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_set_demo_slider_height( $_h ) {
    //this custom demo height is applied when :
    //1) current slider is demo
    if ( 'demo' != $this -> czr_fn_get_current_slider( $this -> czr_fn_get_real_id() ) )
      return $_h;

    //2) height option has not been changed by user yet
    //the possible customization context must be taken into account here
    if ( CZR___::$instance -> czr_fn_is_customizing() ) {
      if ( 500 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_default_height') ) )
        return $_h;
    } else {
      if ( false !== (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_default_height', CZR___::$tc_option_group, $use_default = false ) ) )
        return $_h;
    }
    return apply_filters( 'tc_set_demo_slider_height' , 750 );
  }


  /**
  * Callback of tc_user_options_style hook
  * @return css string
  *
  * @package Customizr
  * @since Customizr 3.2.6
  */
  function czr_fn_write_slider_inline_css( $_css ) {
    //custom css for the slider loader
    if ( $this -> czr_fn_is_slider_loader_active( $this -> czr_fn_get_current_slider( $this -> czr_fn_get_real_id() ) ) ) {

      $_slider_loader_src = apply_filters( 'tc_slider_loader_src' , sprintf( '%1$s%2$s' , TC_BASE_URL , 'assets/front/img/slider-loader.gif') );
      //we can load only the gif, or use it as fallback for old browsers (.no-csstransforms3d)
      if ( ! apply_filters( 'tc_slider_loader_gif_only', false ) ) {
        $_slider_loader_gif_class  = '.no-csstransforms3d';
        // The pure css loader color depends on the skin. Why can we do this here without caring of the live preview?
        // Basically 'cause the loader is something we see when the page "loads" then it disappears so a live change of the skin
        // will still have no visive impact on it. This will avoid us to rebuild the custom skins.
        $_current_skin_colors      = CZR_utils::$inst -> czr_fn_get_skin_color( 'pair' );
        $_pure_css_loader_css      = apply_filters( 'tc_slider_loader_css', sprintf(
            '.tc-slider-loader-wrapper .tc-css-loader > div { border-color:%s; }',
            //we can use the primary or the secondary skin color
            'primary' == apply_filters( 'tc_slider_loader_color', 'primary') ? $_current_skin_colors[0] : $_current_skin_colors[1]
        ));
      }else {
        $_slider_loader_gif_class = '';
        $_pure_css_loader_css     = '';
      }

      $_slider_loader_gif_css     = $_slider_loader_src ? sprintf(
                                        '%1$s .tc-slider-loader-wrapper .tc-img-gif-loader {
                                                background: url(\'%2$s\') no-repeat center center;
                                         }',
                                         $_slider_loader_gif_class,
                                         $_slider_loader_src
                                     ) : '';
      $_css = sprintf( "$_css\n%s%s",
                          $_slider_loader_gif_css,
                          $_pure_css_loader_css
      );
    }//end custom css for the slider loader

    // 1) Do we have a custom height ?
    // 2) check if the setting must be applied to all context
    $_custom_height     = apply_filters( 'tc_slider_height' , esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_default_height') ) );
    $_slider_inline_css = "";

    //When shall we append custom slider style to the global custom inline stylesheet?
    $_bool = 500 != $_custom_height;
    $_bool = $_bool && ( czr_fn__f('__is_home') || 0 != esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_default_height_apply_all') ) );

    if ( ! apply_filters( 'tc_print_slider_inline_css' , $_bool ) )
      return $_css;

    $_resp_shrink_ratios = apply_filters( 'tc_slider_resp_shrink_ratios',
      array('1200' => 0.77 , '979' => 0.618, '480' => 0.38 , '320' => 0.28 )
    );

    $_slider_inline_css = "
      .carousel .czr-item {
        line-height: {$_custom_height}px;
        min-height:{$_custom_height}px;
        max-height:{$_custom_height}px;
      }
      .tc-slider-loader-wrapper {
        line-height: {$_custom_height}px;
        height:{$_custom_height}px;
      }
      .carousel .tc-slider-controls {
        line-height: {$_custom_height}px;
        max-height:{$_custom_height}px;
      }\n";

    foreach ( $_resp_shrink_ratios as $_w => $_ratio) {
      if ( ! is_numeric($_ratio) )
        continue;
      $_item_dyn_height     = $_custom_height * $_ratio;
      $_caption_dyn_height  = $_custom_height * ( $_ratio - 0.1 );
      $_slider_inline_css .= "
        @media (max-width: {$_w}px) {
          .carousel .czr-item {
            line-height: {$_item_dyn_height}px;
            max-height:{$_item_dyn_height}px;
            min-height:{$_item_dyn_height}px;
          }
          .czr-item .carousel-caption {
            max-height: {$_caption_dyn_height}px;
            overflow: hidden;
          }
          .carousel .tc-slider-loader-wrapper {
            line-height: {$_item_dyn_height}px;
            height:{$_item_dyn_height}px;
          }
        }\n";
    }//end foreach

    return sprintf("%s\n%s", $_css, $_slider_inline_css);
  }



  /**
  * Set slider wrapper class
  * hook : tc_slider_layout_class filter
  *
  * @package Customizr
  * @since Customizr 3.2.0
  *
  */
  function czr_fn_set_slider_wrapper_class($_classes) {
    if ( ! is_array($_classes) || 500 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_slider_default_height') ) )
      return $_classes;

    return array_merge( $_classes , array('custom-slider-height') );
  }


  /**
  * hook : tc_carousel_inner_classes fired in the slider view
  * @return  array of css classes
  *
  * @package Customizr
  * @since Customizr 3.3+
  */
  function czr_fn_set_inner_class( $_classes ) {
    if( ! (bool) esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_center_slider_img') ) || ! is_array($_classes) )
      return $_classes;
    array_push( $_classes, 'center-slides-enabled' );
    return $_classes;
  }


  /**
  * Setter
  *
  * @package Customizr
  * @since Customizr 3.4.9
  */
  function czr_fn_cache_posts_slider( $args = array() ) {
    $defaults = array (
      //use the home slider_width
      'img_size'        => 1 == CZR_utils::$inst->czr_fn_opt( 'tc_slider_width' ) ? 'slider-full' : 'slider',
      'load_transient'  => false,
      'store_transient' => true,
      'transient_name'  => 'tc_posts_slides'
    );
    $this -> czr_fn_get_pre_posts_slides( wp_parse_args( $args, $defaults) );
  }



  /**
  * Getter
  * Returns the trimmed post slide title
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_title( $_post, $ID ) {
    $title_length   = apply_filters('tc_post_slide_title_length', 80, $ID );
    $more           = apply_filters('tc_post_slide_more', '...', $ID );
    return $this -> czr_fn_get_post_title( $_post, $title_length, $more );
  }


  /**
  * Getter
  * Returns the trimmed post slide excerpt
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_excerpt( $_post, $ID ) {
    $excerpt_length  = apply_filters( 'tc_post_slide_text_length', 80, $ID );
    $more            = apply_filters( 'tc_post_slide_more', '...', $ID );
    return $this -> czr_fn_get_post_excerpt( $_post, $excerpt_length, $more );
  }

  /**
  * Getter
  * Returns the trimmed posts slider button text
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  function czr_fn_get_post_slide_button_text( $button_text ) {
    $button_text_length  = apply_filters( 'tc_posts_slider_button_text_length', 80 );
    $more                = apply_filters( 'tc_post_slide_more', '...');
    $button_text         = apply_filters( 'tc_posts_slider_button_text_pre_trim' , $button_text );
    return $this -> czr_fn_trim_text( $button_text, $button_text_length, $more );
  }

  /**
  * Helper
  * Returns the trimmed post title
  *
  * @return string
  *
  * Slightly different and simplified version of get_the_title to avoid conflicts with plugins filtering the_title
  * and custom trimming.
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into CZR_utils?
  function czr_fn_get_post_title( $_post, $default_title_length, $more ) {
    $title = $_post->post_title;
    if ( ! empty( $_post->post_password ) ) {
      $protected_title_format = apply_filters( 'protected_title_format', __( 'Protected: %s', 'customizr' ), $_post);
      $title = sprintf( $protected_title_format, $title );
    }

    $title = apply_filters( 'tc_post_title_pre_trim' , $title );
    return $this -> czr_fn_trim_text( $title, $default_title_length, $more);
  }


  /**
  * Helper
  * Returns the trimmed post excerpt
  *
  * @return string
  *
  * Slightly different and simplified version of wp_trim_excerpt to avoid conflicts with plugins filtering get_excerpt and the_content
  * and custom trimming.
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into CZR_utils?
  function czr_fn_get_post_excerpt( $_post, $default_text_length, $more ) {
    if ( ! empty( $_post->post_password) )
      return __( 'There is no excerpt because this is a protected post.', 'customizr' );

    $excerpt = '' != $_post->post_excerpt ? $_post->post_excerpt : $_post->post_content;

    $excerpt = apply_filters( 'tc_post_excerpt_pre_sanitize' , $excerpt );
    // below some function applied to the_content & the_excerpt filters
    // we cannot use those filters 'cause some plugins, e.g. qtranslate
    // filter those as well invalidating our transient
    $excerpt = strip_shortcodes( $excerpt );
    $excerpt = wptexturize( $excerpt );
    $excerpt = convert_chars( $excerpt );
    $excerpt = wpautop( $excerpt );
    $excerpt = shortcode_unautop( $excerpt );
    $excerpt = str_replace(']]>', ']]&gt;', $excerpt );

    $excerpt = apply_filters( 'tc_post_excerpt_pre_trim' , $excerpt );
    return $this -> czr_fn_trim_text( $excerpt, $default_text_length, $more);
  }


  /**
  * Helper
  * Returns the passed text trimmed at $text_length char.
  * with the $more text added
  *
  * @return string
  *
  * @package Customizr
  * @since Customizr 3.4.9
  *
  */
  // move this into CZR_utils?
  function czr_fn_trim_text( $text, $text_length, $more ) {
    if ( ! $text )
      return '';

    $text       = trim( strip_tags( $text ) );

    if ( ! $text_length )
      return $text;

    $end_substr = $_text_length = strlen( $text );

    if ( $_text_length > $text_length ){
      $end_substr = strpos( $text, ' ' , $text_length);
      $end_substr = ( $end_substr !== FALSE ) ? $end_substr : $text_length;
      $text = substr( $text , 0 , $end_substr );
    }
    return ( ( $end_substr < $text_length ) && $more ) ? $text : $text . ' ' .$more ;
  }

} //end of class
endif;

?><?php
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

      //if no active widget area yet, display the footer widget placeholder
			if ( ! apply_filters( 'tc_has_footer_widgets', $status ) ) {
        $this -> czr_fn_display_footer_placeholder();
        return;
      }

			//hack to render white color icons if skin is grey or black
			$skin_class 					= ( in_array( CZR_utils::$inst->czr_fn_opt( 'tc_skin') , array('grey.css' , 'black.css', 'black2.css')) ) ? 'white-icons' : '';
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
    * When do we display this placeholder ?
    * -User logged in
    * -Admin
    * -User did not dismiss the notice
    * @param : string position left or right
    * @since Customizr 3.3
    */
    private function czr_fn_display_footer_placeholder() {
      if ( ! CZR_placeholders::czr_fn_is_widget_placeholder_enabled( 'footer' ) )
        return;

      ?>
      <aside class="tc-placeholder-wrap tc-widget-placeholder">
        <?php
          printf('<span class="tc-admin-notice">%1$s</span>',
            __( 'This block is visible for admin users only.', 'customizr')
          );

          printf('<h4>%1$s</h4>',
            __( 'The footer has no widgets', 'customizr')
          );

          printf('<p><strong>%1$s</strong></p>',
              sprintf( __("Add widgets to the footer %s or %s.", "customizr"),
                sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', CZR_utils::czr_fn_get_customizer_url( array( 'panel' => 'widgets') ), __( "Add widgets", "customizr"), __("now", "customizr") ),
                sprintf('<a class="tc-inline-dismiss-notice" data-position="footer" href="#" title="%1$s">%1$s</a>',
                  __( 'dismiss this notice', 'customizr')
                )
              )
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
	    	$_hide_socials = ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_social_in_footer') ) );


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
                            apply_filters( 'tc_credit_link', sprintf( '&middot; <span class="tc-credits-text">Designed by</span> %1$s', '<a href="'.CZR_WEBSITE.'">Press Customizr</a>' ) ),
                            apply_filters( 'tc_wp_powered', sprintf( '&middot; <span class="tc-wp-powered-text">%1$s</span> <a class="icon-wordpress" target="_blank" href="https://wordpress.org" title="%2$s"></a> &middot;',
                              __('Powered by', 'customizr'),
                              __('Powered by WordPress', 'customizr')
                            ))
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
      if ( ! apply_filters('tc_show_text_btt', 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_back_to_top' ) ) ) )
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
      if ( ! ( $this -> is_sticky_footer_enabled() || CZR___::$instance -> czr_fn_is_customizing() ) )
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
      if ( ! ( $this -> is_sticky_footer_enabled() || CZR___::$instance -> czr_fn_is_customizing() ) )
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
			if ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_back_to_top' ) ) )
                return;
            printf('<div id="tc-footer-btt-wrapper" class="tc-btt-wrapper %1$s"><i class="btt-arrow"></i></div>',
                esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_back_to_top_position' ) )
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

			if ( 1 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_footer_widget_icon' ) ) )
				return ( 0 == esc_attr( CZR_utils::$inst->czr_fn_opt( 'tc_show_title_icon' ) ) ) ? $_no_icons_classes : $_original_classes;
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
      return 1 == esc_attr( CZR_utils::$inst -> czr_fn_opt( 'tc_sticky_footer') );
    }
  }//end of class
endif;

?>