<?php
/**
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
if ( ! class_exists( 'TC_logo_title' ) ) :
  class TC_logo_title extends TC_view_base {
    static $instance;
    function __construct( $_args = array() ) {
      self::$instance =& $this;
      // Instanciates the parent class.
      if ( ! isset(parent::$instance) )
        parent::__construct( $_args );

      //Set logo layout with a customizer option (since 3.2.0)
      add_filter( 'tc_logo_class'            , array( $this , 'tc_set_logo_title_layout') );

      //! tc_user_options_style filter is shared by several classes => must always check the local context inside the callback before appending new css
      //fired on hook : wp_enqueue_scripts
      //Set thumbnail specific design based on user options
      //Set top border style option
      add_filter( 'tc_user_options_style'  , array( $this , 'tc_write_header_inline_css') );
    }



    /**************************************
    * VIEWS
    **************************************/
    /**
    * Prepare the logo / title view
    *
    *
    * @package Customizr
    * @since Customizr 3.2.3
    */
    function tc_render() {
      $logos_type = array( '_sticky_', '_');
      $logos_img  = array();

      $accepted_formats   = apply_filters( 'tc_logo_img_formats' , array('jpg', 'jpeg', 'png' ,'gif', 'svg', 'svgz' ) );
      $logo_classes       = array( 'brand', 'span3');
      foreach ( $logos_type as $logo_type ){
          // check if we have to print the sticky logo
          if ( '_sticky_' == $logo_type && ! $this -> tc_use_sticky_logo() )
              continue;

          //check if the logo is a path or is numeric
          //get src for both cases
          $_logo_src        = '';
          $_width         = false;
          $_height        = false;
          $_attachement_id    = false;
          $_logo_option       = esc_attr( TC_utils::$inst->tc_opt( "tc{$logo_type}logo_upload") );
          //check if option is an attachement id or a path (for backward compatibility)
          if ( is_numeric($_logo_option) ) {
              $_attachement_id  = $_logo_option;
              $_attachment_data   = apply_filters( "tc{$logo_type}logo_attachment_img" , wp_get_attachment_image_src( $_logo_option , 'full' ) );
              $_logo_src      = $_attachment_data[0];
              $_width       = ( isset($_attachment_data[1]) && $_attachment_data[1] > 1 ) ? $_attachment_data[1] : $_width;
              $_height      = ( isset($_attachment_data[2]) && $_attachment_data[2] > 1 ) ? $_attachment_data[2] : $_height;
          } else { //old treatment
              //rebuild the logo path : check if the full path is already saved in DB. If not, then rebuild it.
              $upload_dir       = wp_upload_dir();
              $_saved_path      = esc_url ( TC_utils::$inst->tc_opt( "tc{$logo_type}logo_upload") );
              $_logo_src        = ( false !== strpos( $_saved_path , '/wp-content/' ) ) ? $_saved_path : $upload_dir['baseurl'] . $_saved_path;
          }

          //hook + makes ssl compliant
          $_logo_src          = apply_filters( "tc{$logo_type}logo_src" , is_ssl() ? str_replace('http://', 'https://', $_logo_src) : $_logo_src ) ;

          $logo_resize      = ( $logo_type == '_' ) ? esc_attr( TC_utils::$inst->tc_opt( 'tc_logo_resize') ) : '';
          $filetype         = TC_utils::$inst -> tc_check_filetype ($_logo_src);
          if( ! empty($_logo_src) && in_array( $filetype['ext'], $accepted_formats ) ) {
              $_args    = array(
                      'logo_src'        => $_logo_src,
                      'logo_resize'       => $logo_resize,
                      'logo_attachment_id'  => $_attachement_id,
                      'logo_width'      => $_width,
                      'logo_height'       => $_height,
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
      $_html = sprintf( '<img src="%1$s" alt="%2$s" %3$s %4$s %5$s %6$s class="%7$s %8$s"/>',
        $logo_src,
        apply_filters( 'tc_logo_alt', __( 'Back Home' , 'customizr' ) ),
        $logo_width ? sprintf( 'width="%1$s"', $logo_width ) : '',
        $logo_height ? sprintf( 'height="%1$s"', $logo_height ) : '',
        ( 1 == $logo_resize) ? sprintf( 'style="max-width:%1$spx;max-height:%2$spx"',
                                apply_filters( 'tc_logo_max_width', 250 ),
                                apply_filters( 'tc_logo_max_height', 100 )
                                ) : '',
        implode(' ' , apply_filters('tc_logo_other_attributes' , ( 0 == TC_utils::$inst->tc_opt( 'tc_retina_support' ) ) ? array('data-no-retina') : array() ) ),
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
    * Callback for tc_logo_class
    *
    * @package Customizr
    * @since Customizr 3.2.0
    */
    function tc_set_logo_title_layout( $_classes ) {
      //backward compatibility (was not handled has an array in previous versions)
      if ( ! is_array($_classes) )
        return $_classes;

      $_layout = esc_attr( TC_utils::$inst->tc_opt( 'tc_header_layout') );
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






    /**************************************
    * USER DEFINE STYLE
    **************************************/
    /*
    * Callback of tc_user_options_style hook
    * @return css string
    *
    * @package Customizr
    * @since Customizr 3.2.6
    */
    function tc_write_header_inline_css( $_css ) {
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
      return $_css;
    }

    /***************************************************
    * HEADER HELPERS
    ***************************************************/
    /**
    * Returns a boolean wheter we're using or not a specific sticky logo
    *
    * @package Customizr
    * @since Customizr 3.2.9
    */
    function tc_use_sticky_logo(){
      if ( ! esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_logo_upload") ) )
        return false;
      if ( ! ( esc_attr( TC_utils::$inst->tc_opt( "tc_sticky_header") ) && esc_attr( TC_utils::$inst->tc_opt( 'tc_sticky_show_title_logo') ) ) )
        return false;
      return true;
    }

  }//end of class
endif;