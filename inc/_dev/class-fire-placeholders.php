<?php
/**
* This class must be instantiated if is_admin() for the ajax call to work
* => because ajax request are fired with the admin_url(), even on front-end.
* more here : https://codex.wordpress.org/AJAX_in_Plugins
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_placeholders' ) ) :
  class CZR_placeholders {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'init'           , array( $this, 'czr_fn_placeholders_ajax_setup') );
      add_filter( 'tc_js_params_front_placeholders' , array( $this, 'czr_fn_localize_placeholders_js') );
    }


    /*****************************************************
    * ADMIN AJAX HOOKS ALL PLACEHOLDERS
    *****************************************************/
    /**
    * hook : init => because we need to fire this function before the admin_ajax.php call
    * @since v3.4+
    */
    function czr_fn_placeholders_ajax_setup() {
      if ( ! czr_fn_is_front_help_enabled() )
        return;
      add_action( 'wp_ajax_dismiss_thumbnail_help'    , array( $this, 'czr_fn_dismiss_thumbnail_help' ) );
      add_action( 'wp_ajax_dismiss_img_smartload_help', array( $this, 'czr_fn_dismiss_img_smartload_help' ) );
      add_action( 'wp_ajax_dismiss_sidenav_help'      , array( $this, 'czr_fn_dismiss_sidenav_help' ) );
      add_action( 'wp_ajax_dismiss_second_menu_notice', array( $this, 'czr_fn_dismiss_second_menu_notice' ) );
      add_action( 'wp_ajax_dismiss_main_menu_notice'  , array( $this, 'czr_fn_dismiss_main_menu_notice' ) );
      add_action( 'wp_ajax_slider_notice_actions'     , array( $this, 'czr_fn_slider_notice_ajax_actions' ) );
      add_action( 'wp_ajax_fp_notice_actions'         , array( $this, 'czr_fn_fp_notice_ajax_actions' ) );
      add_action( 'wp_ajax_dismiss_widget_notice'     , array( $this, 'czr_fn_dismiss_widget_notice' ) );
    }



    /*****************************************************
    * MAYBE WRITE AJAX SCRIPTS IN FOOTER FOR ALL PLACEHOLDERS / NOTICES
    *****************************************************/
    /**
    * hook : 'tc_js_params_front_placeholders'
    * => we need to access some conditional tags like is_home when checking if the placeholder / notice are enabled
    * @params = array() of placeholder params
    */
    function czr_fn_localize_placeholders_js( $params ) {
      if ( ! czr_fn_is_front_help_enabled() )
        return $params;

      return array_merge( $params, array(
          //SIMPLE NOTICE WITHOUT BLOCK REMOVAL
          'thumbnail' => array(
              'active'    => $this -> czr_fn_is_thumbnail_help_on(),
              'args'  => array(
                  'action' => 'dismiss_thumbnail_help',
                  'nonce' => array( 'id' => 'thumbnailNonce', 'handle' => wp_create_nonce( 'tc-thumbnail-help-nonce' ) ),
                  'class' => 'tc-thumbnail-help'
              )
          ),
          'smartload' => array(
              'active'    => apply_filters( 'tc_write_img_smartload_help_js', true ),
              'args'  => array(
                  'action' => 'dismiss_img_smartload_help',
                  'nonce' => array( 'id' => 'imgSmartLoadNonce', 'handle' => wp_create_nonce( 'tc-img-smartload-help-nonce' ) ),
                  'class' => 'tc-img-smartload-help'
              )
          ),
          'sidenav' => array(
              'active'    => $this -> czr_fn_is_sidenav_help_on(),
              'args'  => array(
                  'action' => 'dismiss_sidenav_help',
                  'nonce' => array( 'id' => 'sideNavNonce', 'handle' => wp_create_nonce( 'tc-sidenav-help-nonce' ) ),
                  'class' => 'tc-sidenav-help'
              )
          ),
          'secondMenu' => array(
              'active'    => $this -> czr_fn_is_second_menu_placeholder_on(),
              'args'  => array(
                  'action' => 'dismiss_second_menu_notice',
                  'nonce' => array( 'id' => 'secondMenuNonce', 'handle' => wp_create_nonce( 'tc-second-menu-placeholder-nonce' ) ),
                  'class' => 'tc-menu-placeholder'
              )
          ),
          'mainMenu' => array(
              'active'    => $this -> czr_fn_is_main_menu_notice_on(),
              'args'  => array(
                  'action' => 'dismiss_main_menu_notice',
                  'nonce' => array( 'id' => 'mainMenuNonce', 'handle' => wp_create_nonce( 'tc-main-menu-notice-nonce' ) ),
                  'class' => 'tc-main-menu-notice'
              )
          ),

          //BLOCK REMOVAL NOTICES
          'slider' => array(
              'active'    => $this -> czr_fn_is_slider_notice_on(),
              'args'  => array(
                  'action' => 'slider_notice_actions',
                  'nonce' => array( 'id' => 'sliderNoticeNonce', 'handle' => wp_create_nonce( 'tc-slider-notice-nonce' ) ),
                  'class' => 'tc-slider-notice'
              )
          ),
          'fp' => array(
              'active'    => $this -> czr_fn_is_fp_notice_on(),
              'args'  => array(
                  'action' => 'fp_notice_actions',
                  'nonce' => array( 'id' => 'fpNoticeNonce', 'handle' => wp_create_nonce( 'tc-fp-notice-nonce' ) ),
                  'class' => 'tc-fp-notice'
              )
          ),
          'widget' => array(
              'active'    => $this -> czr_fn_is_widget_placeholder_enabled(),
              'args'  => array(
                  'action' => 'dismiss_widget_notice',
                  'nonce' => array( 'id' => 'WidgetNonce', 'handle' => wp_create_nonce( 'tc-widget-placeholder-nonce' ) )
              )
          ),
      ) );
    }



    /*****************************************************
    * THUMBNAIL MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss thumbnail help
    * hook : wp_ajax_dismiss_thumbnail_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_thumbnail_help() {
      check_ajax_referer( 'tc-thumbnail-help-nonce', 'thumbnailNonce' );
      set_transient( 'tc_thumbnail_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_thumbnail_help_on() {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'disabled' == get_transient("tc_thumbnail_help"),
        ( is_single() && 'hide' != czr_fn_opt('tc_single_post_thumb_location') ),
        ( is_page() && 'hide' != czr_fn_opt('tc_single_page_thumb_location') ),
        ! is_admin() && ! is_singular(),
        ! czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_thumbnail_help_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }




    /*****************************************************
    * IMG SMARTLOAD MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss images smartload help
    * hook : wp_ajax_dismiss_img_smartload_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_img_smartload_help() {
      check_ajax_referer( 'tc-img-smartload-help-nonce', 'imgSmartLoadNonce' );
      set_transient( 'tc_img_smartload_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }

    /**
    * Print Smartload help block notice
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    static function czr_fn_get_smartload_help_block( $echo = false ) {
      //prepare js printing in the footer
      add_filter( 'tc_write_img_smartload_help_js', '__return_true' );

      ob_start();
      ?>
      <div class="tc-placeholder-wrap tc-img-smartload-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p>',
              __( "Did you know you can easily speed up your page load by deferring the loading of the non visible images?", "customizr" ),
              sprintf( __("%s and check the option 'Load images on scroll' under 'Website Performances' section.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', czr_fn_get_customizer_url( array( "control" => "tc_img_smart_load", "section" => "performances_sec" ) ), __( "Jump to the customizer now", "customizr") )
              )
          );
          printf('<a class="tc-dismiss-notice" href="#" title="%1$s">%1$s x</a>',
                __( 'dismiss notice', 'customizr')
          );
        ?>
      </div>
      <?php
      $help_block = ob_get_contents();
      ob_end_clean();

      if ( ! $echo )
        return $help_block;
      echo $help_block;
    }



    /**
    *
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_img_smartload_help_on( $text, $min_img_num = 2 ) {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      if ( $min_img_num ) {
        if ( ! $text )
          return false;
      }

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        1 == esc_attr( czr_fn_opt( 'tc_img_smart_load' ) ),
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! czr_fn_is_front_help_enabled(),
        'disabled' == get_transient("tc_img_smartload_help"),
        $min_img_num ? apply_filters('tc_img_smartload_help_n_images', $min_img_num ) > preg_match_all( '/(<img[^>]+>)/i', $text, $matches ) : false ,
        is_admin()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_img_smartload_help_on',
        ! (bool) array_sum( $_dont_display_conditions )
      );
    }





    /*****************************************************
    * SIDENAV MENU HELP : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss sidenav help
    * hook : wp_ajax_dismiss_sidenav_help
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_dismiss_sidenav_help() {
      check_ajax_referer( 'tc-sidenav-help-nonce', 'sideNavNonce' );
      set_transient( 'tc_sidenav_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_sidenav_help_on() {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        czr_fn_has_location_menu('main'),// => if the "main" location has a menu assigned
        'navbar' == czr_fn_opt('tc_menu_style'),
        'disabled' == get_transient("tc_sidenav_help"),
        ! czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_sidenav_help_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }






    /*****************************************************
    * SECOND MENU PLACEHOLDER : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss notice ajax callback
    * hook : wp_ajax_dismiss_second_menu_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_second_menu_notice() {
      check_ajax_referer( 'tc-second-menu-placeholder-nonce', 'secondMenuNonce' );
      set_transient( 'tc_second_menu_placehold', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_second_menu_placeholder_on() {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;
      //don't display if main menu style is regular <=> 'navbar' == tc_menu_style
      if ( 'navbar' == czr_fn_opt('tc_menu_style') )
        return false;
      //don't display if second menu is enabled : tc_display_second_menu
      if ( (bool)czr_fn_opt('tc_display_second_menu') )
        return false;

      return apply_filters(
        "tc_is_second_menu_placeholder_on",
        czr_fn_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && 'disabled' != get_transient("tc_second_menu_placehold")
      );
    }



    /*****************************************************
    * MAIN MENU NOTICE : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Dismiss notice ajax callback
    * hook : wp_ajax_dismiss_main_menu_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_main_menu_notice() {
      check_ajax_referer( 'tc-main-menu-notice-nonce', 'mainMenuNonce' );
      set_transient( 'tc_main_menu_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }



    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_main_menu_notice_on() {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'navbar' != czr_fn_opt('tc_menu_style'),
        (bool)czr_fn_opt('tc_display_second_menu'),
        'disabled' == get_transient("tc_main_menu_notice"),
        ! czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_main_menu_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }



    /*****************************************************
    * SLIDER : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Two cases :
    * 1) dismiss notice
    * 2) remove demo slider
    * hook : wp_ajax_slider_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_slider_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-slider-notice-nonce', 'sliderNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_slider':
          czr_fn_set_option( 'tc_front_slider' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_slider_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }


    /**
    * Do we display the slider notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_slider_notice_on( $_position = null ) {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! czr_fn_is_real_home(),
        'tc_posts_slider' != czr_fn_opt('tc_front_slider'),
        'disabled' == get_transient("tc_slider_notice"),
        ! czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_slider_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }




    /*****************************************************
    * FEATURED PAGES : AJAX JS AND CALLBACK
    *****************************************************/
    /**
    * Two cases :
    * 1) dismiss notice
    * 2) remove fp
    * hook : wp_ajax_fp_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function czr_fn_fp_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-fp-notice-nonce', 'fpNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_fp':
          czr_fn_set_option( 'tc_show_featured_pages' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_fp_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }

    /**
    * Do we display the featured page notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function czr_fn_is_fp_notice_on( $_position = null ) {
      //never display when customizing
      if ( czr_fn_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! czr_fn_is_real_home(),
        ! (bool)czr_fn_opt('tc_show_featured_pages'),
        'disabled' == get_transient("tc_fp_notice"),
        self::$instance -> czr_fn_is_one_fp_set(),
        czr_fn_is_pro(),
        CZR_plugins_compat::$instance->czr_fn_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php'),
        ! czr_fn_is_front_help_enabled()
      );

      //checks if at least one of the conditions is true
      return apply_filters(
        'tc_is_fp_notice_on',
        ! (bool)array_sum($_dont_display_conditions)
      );
    }


    /**
    * Helper to check if at least one featured page has been set by the user.
    * @return bool
    * @since v3.4+
    */
    function czr_fn_is_one_fp_set() {
      $_fp_sets = array();
      $fp_ids = apply_filters( 'tc_featured_pages_ids' , CZR___::$instance -> fp_ids);
      if ( ! is_array($fp_ids) )
        return;
      foreach ($fp_ids as $fp_single_id ) {
        $_fp_sets[] = (bool)czr_fn_opt( 'tc_featured_page_'.$fp_single_id );
      }
      //returns true if at least one fp has been set.
      return (bool)array_sum($_fp_sets);
    }



    /************************************************************
    * WIDGET PLACEHOLDERS AJAX JS AND CALLBACK : FOR SIDEBARS AND FOOTER
    ************************************************************/
    /**
    * Dismiss widget notice ajax callback
    * hook : wp_ajax_dismiss_widget_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function czr_fn_dismiss_widget_notice() {
      check_ajax_referer( 'tc-widget-placeholder-nonce', 'WidgetNonce' );
      if ( isset( $_POST['position'] ) )
        $_pos = esc_attr( $_POST['position'] );
      else
        wp_die(0);
      //20 years transient
      set_transient( "tc_widget_placehold_{$_pos}", 'disabled' , 60*60*24*365*20 );
      wp_die();
    }


    /**
    * Public helper, state if we can display a widget placeholder to the current user.
    * @return  bool
    * @since Customizr 3.3+
    */
    static function czr_fn_is_widget_placeholder_enabled( $_position = null ) {
      //never display when customizing
      // if ( czr_fn_is_customizing() )
      //   return;

      //always display in DEV mode
      if ( defined('CZR_DEV') && true === CZR_DEV )
        return true;

      $_position = is_null($_position) ? apply_filters('tc_widget_areas_position', array( 'sidebar', 'footer') ) : array($_position);

      return apply_filters( "tc_display_widget_placeholders",
        czr_fn_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && array_sum( array_map( array( self::$instance , 'czr_fn_check_widget_placeholder_transient'), $_position ) )
      );
    }

    /**
    * @return  bool
    * @since Customizr 3.3+
    */
    function czr_fn_check_widget_placeholder_transient( $_position ){
      return 'disabled' != get_transient("tc_widget_placehold_{$_position}");
    }

  }//end of class
endif;

?>