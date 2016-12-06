<?php
/**
* Loads front end fonts
*
*
* @package      Customizr
*/
if ( ! class_exists( 'CZR_resources_fonts' ) ) :
	class CZR_resources_fonts {
	    //Access any method or var of the class with classname::$instance -> var or method():
	    static $instance;

	    function __construct () {
	        self::$instance =& $this;
          add_action( 'wp_enqueue_scripts'            , array( $this , 'czr_fn_enqueue_gfonts' ) , 0 );

          //Custom Stylesheets
          //Write font icon
          add_filter('czr_user_options_style'          , array( $this , 'czr_fn_write_inline_font_icons_css') , apply_filters( 'czr_font_icon_priority', 999 ) );

          //add_filter('czr_user_options_style'          , array( $this , 'czr_fn_write_fonts_inline_css') );
          add_filter('czr_user_options_style'          , array( $this , 'czr_fn_write_dropcap_inline_css') );
	    }






		/**
    * Write the font icon in the custom stylesheet at the very beginning
    * hook : czr_user_options_style
    * @package Customizr
    * @since Customizr 3.2.3
    */
		function czr_fn_write_inline_font_icons_css( $_css = null ) {
      $_css               = isset($_css) ? $_css : '';
      return apply_filters( 'czr_write_inline_font_icons',
        $this -> czr_fn_get_inline_font_icons_css() . "\n" . $_css,
        $_css
      );
    }//end of function



    /**
    * @return string of css font icons
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    public function czr_fn_get_inline_font_icons_css( $_force = false ) {
      if ( ! $_force && false == czr_fn_get_opt( 'tc_font_awesome_icons' ) )
        return;
      /*
      * Not using add_query_var here in order to keep the code simple
      */
      $_path            = apply_filters( 'czr_font_icons_path' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'shared/css' );
      $_version         = apply_filters( 'czr_font_icons_version', true ) ? '4.7.0' : '';
      $_ie_query_var    = $_version ? "&v={$_version}" : '';
      $_query_var       = $_version ? "?v={$_version}" : '';
      ob_start();
        ?>
        @font-face {
          font-family: 'FontAwesome';
          src:url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.eot<?php echo $_query_var ?>' ) );
          src:url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.eot?#iefix<?php echo $_ie_query_var ?>') format('embedded-opentype'),
              url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.woff2<?php echo $_query_var ?>') format('woff2'),
              url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.woff<?php echo $_query_var ?>') format('woff'),
              url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.ttf<?php echo $_query_var ?>') format('truetype'),
              url('<?php echo $_path ?>/fonts/fonts/fontawesome-webfont.svg<?php echo $_query_var ?>#fontawesomeregular') format('svg');
          font-weight: normal;
          font-style: normal;
        }
        <?php
      $_font_css = ob_get_contents();
      if ($_font_css) ob_end_clean();
      return $_font_css;
    }


    /*
    * Callback of wp_enqueue_scripts
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_enqueue_gfonts() {
      $_font_pair         = esc_attr( czr_fn_get_opt( 'tc_fonts' ) );
      $_all_font_pairs    = CZR_init::$instance -> font_pairs;
      if ( ! $this -> czr_fn_is_gfont( $_font_pair , '_g_') )
        return;

      wp_enqueue_style(
        'czr-gfonts',
        sprintf( '//fonts.googleapis.com/css?family=%s', czr_fn_get_font( 'single' , $_font_pair ) ),
        array(),
        null,
        'all'
      );
    }



    /**
    * Callback of czr_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function czr_fn_write_fonts_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      $_font_pair         = esc_attr( czr_fn_get_opt( 'tc_fonts' ) );
      $_body_font_size    = esc_attr( czr_fn_get_opt( 'tc_body_font_size' ) );
      $_font_selectors    = CZR_init::$instance -> font_selectors;

      //create the $body and $titles vars
      extract( CZR_init::$instance -> font_selectors, EXTR_OVERWRITE );

      if ( ! isset($body) || ! isset($titles) )
        return;

      //adapt the selectors in edit context => add specificity for the mce-editor
      if ( ! is_null( $_context ) ) {
        $titles = ".{$_context} .h1, .{$_context} h2, .{$_context} h3";
        $body   = "body.{$_context}";
      }

      $titles = apply_filters('czr_title_fonts_selectors' , $titles );
      $body   = apply_filters('czr_body_fonts_selectors' , $body );

      if ( 'helvetica_arial' != $_font_pair ) {//check if not default
        $_selector_fonts  = explode( '|', czr_fn_get_font( 'single' , $_font_pair ) );
        if ( ! is_array($_selector_fonts) )
          return $_css;

        foreach ($_selector_fonts as $_key => $_raw_font) {
          //create the $_family and $_weight vars
          extract( $this -> czr_fn_get_font_css_prop( $_raw_font , $this -> czr_fn_is_gfont( $_font_pair ) ) );

          switch ($_key) {
            case 0 : //titles font
              $_css .= "
                {$titles} {
                  font-family : {$_family};
                  font-weight : {$_weight};
                }\n";
            break;

            case 1 ://body font
              $_css .= "
                {$body} {
                  font-family : {$_family};
                  font-weight : {$_weight};
                }\n";
            break;
          }
        }
      }//end if

      if ( 14 != $_body_font_size ) {
        $_line_height = round( $_body_font_size * 19 / 14 );
        $_css .= "
          {$body} {
            font-size : {$_body_font_size}px;
            line-height : {$_line_height}px;
          }\n";
        }

      return $_css;
    }//end of fn


    /**
    * Helper to check if the requested font code includes the Google font identifier : _g_
    * @return bool
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function czr_fn_is_gfont($_font , $_gfont_id = null ) {
      $_gfont_id = $_gfont_id ? $_gfont_id : '_g_';
      return false !== strpos( $_font , $_gfont_id );
    }


    /**
    * Callback of czr_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.11
    */
    function czr_fn_write_dropcap_inline_css( $_css = null , $_context = null ) {
      $_css               = isset($_css) ? $_css : '';
      if ( ! esc_attr( czr_fn_get_opt( 'tc_enable_dropcap' ) ) )
        return $_css;

      $_main_color_pair = czr_fn_get_skin_color( 'pair' );
      $_color           = $_main_color_pair[0];
      $_shad_color      = $_main_color_pair[1];
      $_pad_right       = false !== strpos( esc_attr( czr_fn_get_opt( 'tc_fonts' ) ), 'lobster' ) ? 26 : 8;
      $_css .= "
        .tc-dropcap {
          color: {$_color};
          float: left;
          font-size: 75px;
          line-height: 75px;
          padding-right: {$_pad_right}px;
          padding-left: 3px;
        }\n
        .skin-shadow .tc-dropcap {
          color: {$_color};
          text-shadow: {$_shad_color} -1px 0, {$_shad_color} 0 -1px, {$_shad_color} 0 1px, {$_shad_color} -1px -2px;
        }\n
        .simple-black .tc-dropcap {
          color: #444;
        }\n";

      return $_css;
    }





    /*************************************
    * HELPERS
    *************************************/
    /**
    * Helper to extract font-family and weight from a Customizr font option
    * @return array( font-family, weight )
    *
    * @package Customizr
    * @since Customizr 3.3.2
    */
    private function czr_fn_get_font_css_prop( $_raw_font , $is_gfont = false ) {
      $_css_exp = explode(':', $_raw_font);
      $_weight  = isset( $_css_exp[1] ) ? $_css_exp[1] : 'inherit';
      $_family  = '';

      if ( $is_gfont ) {
        $_family = str_replace('+', ' ' , $_css_exp[0]);
      } else {
        $_family = implode("','", explode(',', $_css_exp[0] ) );
      }
      $_family = sprintf("'%s'" , $_family );

      return compact("_family" , "_weight" );
    }

  }//end of CZR_resources
endif;
