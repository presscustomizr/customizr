<?php
/**
* Header actions
*
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
      if ( 1 == esc_attr( czr_fn_opt( 'tc_sticky_header' ) ) || czr_fn_is_customizing() )
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

      $_fav_option  			= esc_attr( czr_fn_opt( 'tc_fav_upload') );
     	if ( ! $_fav_option || is_null($_fav_option) )
     		return;

     	$_fav_src 				= '';
     	//check if option is an attachement id or a path (for backward compatibility)
     	if ( is_numeric($_fav_option) ) {
     		$_attachement_id 	= $_fav_option;
     		$_attachment_data 	= apply_filters( 'tc_fav_attachment_img' , wp_get_attachment_image_src( $_fav_option , 'full' ) );
     		$_fav_src 			= $_attachment_data[0];
     	} else { //old treatment
     		$_saved_path 		= esc_url ( czr_fn_opt( 'tc_fav_upload') );
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
          //for the standard logo use the wp custom logo feature if set, otherwise fall back on the customizr custom logo
          $_logo_option       = '_' === $logo_type ? get_theme_mod( 'custom_logo', '' ) : '';
          $_logo_option       = $_logo_option ? $_logo_option : esc_attr( czr_fn_opt( "tc{$logo_type}logo_upload") );
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
              $_saved_path 			= esc_url ( czr_fn_opt( "tc{$logo_type}logo_upload") );
              $_logo_src 				= ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
          }

          //hook + makes ssl compliant
          $_logo_src    			= apply_filters( "tc{$logo_type}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;

          $logo_resize 			= ( $logo_type == '_' ) ? esc_attr( czr_fn_opt( 'tc_logo_resize') ) : '';
          $filetype 				= czr_fn_check_filetype($_logo_src);
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
            printf('<%1$s><a class="site-title" href="%2$s">%3$s</a></%1$s>',
                    apply_filters( 'tc_site_title_tag', 'h1' ) ,
                    apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ) ) ,
                    get_bloginfo( 'name', 'display' )
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
        implode(' ' , apply_filters('tc_logo_other_attributes' , ( 0 == czr_fn_opt( 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) ),
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
              printf( '<a class="site-logo" href="%1$s" aria-label="%2$s">%3$s</a>',
                  apply_filters( 'tc_logo_link_url', esc_url( home_url( '/' ) ) ) ,
                  // keep this filter name for retro-compat, but now it refers to the aria-label attribute.
                  apply_filters( 'tc_logo_link_title', sprintf( '%1$s | %2$s' , get_bloginfo( 'name', 'display' )  , get_bloginfo( 'description', 'display' ) ) ),
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
        $_nothing_to_render         = 0 == czr_fn_opt( 'tc_social_in_header' ) || ! ( $_socials = czr_fn__f( '__get_socials' ) );

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
      if ( 0 == czr_fn_opt( 'tc_show_tagline' ) )
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
			if ( 1 != esc_attr( czr_fn_opt( 'tc_top_border') ) ) {
  			$_css = sprintf("%s\n%s",
  				$_css,
  				"header.tc-header {border-top: none;}\n"
  	    );
	    }

      //STICKY HEADER
	    if ( czr_fn_is_checked( 'tc_sticky_shrink_title_logo' ) || czr_fn_is_customizing() ) {
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
	    if ( 100 != esc_attr( czr_fn_opt( 'tc_sticky_z_index') ) ) {
	    	$_custom_z_index 	= esc_attr( czr_fn_opt( 'tc_sticky_z_index') );
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
    	if ( 1 == esc_attr( czr_fn_opt( 'tc_sticky_header' ) ) ) {
     		$_classes = array_merge( $_classes, array('tc-sticky-header', 'sticky-disabled') );
     		//STICKY TRANSPARENT ON SCROLL
       	if ( 1 == esc_attr( czr_fn_opt( 'tc_sticky_transparent_on_scroll' ) ) )
       		$_classes = array_merge( $_classes, array('tc-transparent-on-scroll') );
       	else
       		$_classes = array_merge( $_classes, array('tc-solid-color-on-scroll') );
       }
     	else {
     		$_classes = array_merge( $_classes, array('tc-no-sticky-header', 'sticky-disabled' ) );
     	}

      //No navbar box
      if ( 1 != esc_attr( czr_fn_opt( 'tc_display_boxed_navbar') ) )
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

			$_show_tagline 			= czr_fn_is_checked( 'tc_sticky_show_tagline' );
      $_show_title_logo 		= czr_fn_is_checked( 'tc_sticky_show_title_logo' );
      $_use_sticky_logo 		= $this -> czr_fn_use_sticky_logo();
			$_shrink_title_logo 	= czr_fn_is_checked( 'tc_sticky_shrink_title_logo' );
			$_show_menu 			  = czr_fn_is_checked( 'tc_sticky_show_menu' );
			$_header_layout 		= "logo-" . esc_attr( czr_fn_opt( 'tc_header_layout' ) );
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
        if ( ! esc_attr( czr_fn_opt( "tc_sticky_logo_upload") ) )
            return false;
        if ( ! ( esc_attr( czr_fn_opt( "tc_sticky_header") ) &&
                     esc_attr( czr_fn_opt( 'tc_sticky_show_title_logo') )
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

			$_layout = esc_attr( czr_fn_opt( 'tc_header_layout') );
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
?>