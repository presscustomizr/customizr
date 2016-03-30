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
if ( ! class_exists( 'TC_placeholders' ) ) :
  class TC_placeholders {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      add_action( 'init'           , array( $this, 'tc_placeholders_ajax_setup') );
      add_action( 'wp'             , array( $this, 'tc_placeholders_write_ajax_js_in_footer') );
    }


    /*****************************************************
    * ADMIN AJAX HOOKS ALL PLACEHOLDERS
    *****************************************************/
    /**
    * hook : init => because we need to fire this function before the admin_ajax.php call
    * @since v3.4+
    */
    function tc_placeholders_ajax_setup() {
      if ( ! $this -> tc_is_front_help_enabled() )
        return;
      add_action( 'wp_ajax_dismiss_thumbnail_help'    , array( $this, 'tc_dismiss_thumbnail_help' ) );
      add_action( 'wp_ajax_dismiss_img_smartload_help', array( $this, 'tc_dismiss_img_smartload_help' ) );
      add_action( 'wp_ajax_dismiss_sidenav_help'      , array( $this, 'tc_dismiss_sidenav_help' ) );
      add_action( 'wp_ajax_dismiss_second_menu_notice', array( $this, 'tc_dismiss_second_menu_notice' ) );
      add_action( 'wp_ajax_dismiss_main_menu_notice'  , array( $this, 'tc_dismiss_main_menu_notice' ) );
      add_action( 'wp_ajax_slider_notice_actions'     , array( $this, 'tc_slider_notice_ajax_actions' ) );
      add_action( 'wp_ajax_fp_notice_actions'         , array( $this, 'tc_fp_notice_ajax_actions' ) );
      add_action( 'wp_ajax_dismiss_widget_notice'     , array( $this, 'tc_dismiss_widget_notice' ) );
    }



    /*****************************************************
    * MAYBE WRITE AJAX SCRIPTS IN FOOTER FOR ALL PLACEHOLDERS / NOTICES
    *****************************************************/
    /**
    * hook : wp => because we need to access some conditional tags like is_home when checking if the placeholder / notice are enabled
    * @since v3.4+
    */
    function tc_placeholders_write_ajax_js_in_footer() {
      if ( ! $this -> tc_is_front_help_enabled() )
        return;
      if ( $this -> tc_is_thumbnail_help_on() )
          add_action( 'wp_footer'   , array( $this, 'tc_write_thumbnail_help_js'), 100 );

      /* The actual printing of the js is controlled with a filter inside the callback */
      add_action( 'wp_footer'     , array( $this, 'tc_maybe_write_img_sarmtload_help_js'), 100 );
      if ( $this -> tc_is_sidenav_help_on() )
        add_action( 'wp_footer'   , array( $this, 'tc_write_sidenav_help_js'), 100 );

      if ( $this -> tc_is_second_menu_placeholder_on() )
        add_action( 'wp_footer'   , array( $this, 'tc_write_second_menu_placeholder_js'), 100 );

      if ( $this -> tc_is_main_menu_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'tc_write_main_menu_notice_js'), 100 );

      if ( $this -> tc_is_slider_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'tc_write_slider_notice_js'), 100 );

      if ( $this -> tc_is_fp_notice_on() )
        add_action( 'wp_footer'   , array( $this, 'tc_write_fp_notice_js'), 100 );

      if ( $this -> tc_is_widget_placeholder_enabled() )
        add_action( 'wp_footer'   , array( $this, 'tc_widget_placeholder_script'), 100 );
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
    function tc_dismiss_thumbnail_help() {
      check_ajax_referer( 'tc-thumbnail-help-nonce', 'thumbnailNonce' );
      set_transient( 'tc_thumbnail_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_thumbnail_help_js() {
      ?>
      <script type="text/javascript" id="thumbnail-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_thumbnail_help',
                    thumbnailNonce :  "<?php echo wp_create_nonce( 'tc-thumbnail-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-thumbnail-help').slideToggle('fast');
            }).always(function() {console.log(arguments);});
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-thumbnail-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_thumbnail_help_on() {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'disabled' == get_transient("tc_thumbnail_help"),
        'hide' != TC_utils::$inst->tc_opt('tc_single_post_thumb_location'),
        ! is_admin() && ! is_single(),
        ! self::$instance -> tc_is_front_help_enabled()
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
    function tc_dismiss_img_smartload_help() {
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
    static function tc_get_smartload_help_block( $echo = false ) {
      //prepare js printing in the footer
      add_filter( 'tc_write_img_smartload_help_js', '__return_true' );

      ob_start();
      ?>
      <div class="tc-placeholder-wrap tc-img-smartload-help">
        <?php
          printf('<p><strong>%1$s</strong></p><p>%2$s</p>',
              __( "Did you know you can easily speed up your page load by deferring the loading of the non visible images?", "customizr" ),
              sprintf( __("%s and check the option 'Load images on scroll' under 'Website Performances' section.", "customizr"),
                sprintf( '<strong><a href="%1$s" title="%2$s">%2$s</a></strong>', TC_utils::tc_get_customizer_url( array( "control" => "tc_img_smart_load", "section" => "performances_sec" ) ), __( "Jump to the customizer now", "customizr") )
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
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_maybe_write_img_sarmtload_help_js() {
      if ( ! apply_filters( 'tc_write_img_smartload_help_js', false ) ) return;
      ?>
      <script type="text/javascript" id="img-smartload-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_img_smartload_help',
                    imgSmartLoadNonce :  "<?php echo wp_create_nonce( 'tc-img-smartload-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-img-smartload-help').slideToggle('fast');
            }).always(function() {console.log(arguments);});
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-img-smartload-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.4+
    */
    static function tc_is_img_smartload_help_on( $text, $min_img_num = 2 ) {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      if ( $min_img_num ) {
        if ( ! $text )
          return false;
      }

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        1 == esc_attr( TC_utils::$inst->tc_opt( 'tc_img_smart_load' ) ),
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! self::$instance -> tc_is_front_help_enabled(),
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
    function tc_dismiss_sidenav_help() {
      check_ajax_referer( 'tc-sidenav-help-nonce', 'sideNavNonce' );
      set_transient( 'tc_sidenav_help', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_sidenav_help_js() {
      ?>
      <script type="text/javascript" id="sidenav-help">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_sidenav_help',
                    sideNavNonce :  "<?php echo wp_create_nonce( 'tc-sidenav-help-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-sidenav-help').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-sidenav-help').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_sidenav_help_on() {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        TC_utils::$inst->tc_has_location_menu('main'),// => if the "main" location has a menu assigned
        'navbar' == TC_utils::$inst->tc_opt('tc_menu_style'),
        'disabled' == get_transient("tc_sidenav_help"),
        ! self::$instance -> tc_is_front_help_enabled()
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
    function tc_dismiss_second_menu_notice() {
      check_ajax_referer( 'tc-second-menu-placeholder-nonce', 'secondMenuNonce' );
      set_transient( 'tc_second_menu_placehold', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_second_menu_placeholder_js() {
      ?>
      <script type="text/javascript" id="second-menu-placeholder">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_second_menu_notice',
                    secondMenuNonce :  "<?php echo wp_create_nonce( 'tc-second-menu-placeholder-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-menu-placeholder').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-menu-placeholder').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_second_menu_placeholder_on() {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;
      //don't display if main menu style is regular <=> 'navbar' == tc_menu_style
      if ( 'navbar' == TC_utils::$inst->tc_opt('tc_menu_style') )
        return false;
      //don't display if second menu is enabled : tc_display_second_menu
      if ( (bool)TC_utils::$inst->tc_opt('tc_display_second_menu') )
        return false;

      return apply_filters(
        "tc_is_second_menu_placeholder_on",
        self::$instance -> tc_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && 'disabled' != get_transient("tc_second_menu_placehold")
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
    function tc_dismiss_main_menu_notice() {
      check_ajax_referer( 'tc-main-menu-notice-nonce', 'mainMenuNonce' );
      set_transient( 'tc_main_menu_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
      wp_die();
    }


    /**
    * Prints dismiss notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_main_menu_notice_js() {
      ?>
      <script type="text/javascript" id="main-menu-placeholder">
        ( function( $ ) {
          var dismiss_request = function( $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_main_menu_notice',
                    mainMenuNonce :  "<?php echo wp_create_nonce( 'tc-main-menu-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              $_el.closest('.tc-main-menu-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-main-menu-notice').click( function( e ) {
              e.preventDefault();
              dismiss_request( $(this) );
            } );
          } );
        }) (jQuery)
      </script>
      <?php
    }


    /**
    *
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_main_menu_notice_on() {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        'navbar' != TC_utils::$inst->tc_opt('tc_menu_style'),
        (bool)TC_utils::$inst->tc_opt('tc_display_second_menu'),
        'disabled' == get_transient("tc_main_menu_notice"),
        ! self::$instance -> tc_is_front_help_enabled()
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
    function tc_slider_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-slider-notice-nonce', 'sliderNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_slider':
          TC_utils::$inst -> tc_set_option( 'tc_front_slider' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_slider_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }


    /**
    * Prints dismiss notice js in the footer
    * Two cases :
    * 1) dismiss notice
    * 2) remove demo slider
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_slider_notice_js() {
      ?>
      <script type="text/javascript" id="slider-notice-actions">
        ( function( $ ) {
          var slider_ajax_request = function( remove_action, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'slider_notice_actions',
                    remove_action : remove_action,
                    sliderNoticeNonce :  "<?php echo wp_create_nonce( 'tc-slider-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              if ( 'remove_slider' == remove_action )
                $('div[id*="customizr-slider"]').fadeOut('slow');
              else
                $_el.closest('.tc-slider-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-slider-notice').click( function( e ) {
              e.preventDefault();
              slider_ajax_request( 'remove_notice', $(this) );
            } );
            $('.tc-inline-remove', '.tc-slider-notice').click( function( e ) {
              e.preventDefault();
              slider_ajax_request( 'remove_slider', $(this) );
            } );
          } );

        }) (jQuery)
      </script>
      <?php
    }


    /**
    * Do we display the slider notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function tc_is_slider_notice_on( $_position = null ) {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! TC_utils::$inst-> tc_is_home(),
        'demo' != TC_utils::$inst->tc_opt('tc_front_slider'),
        'disabled' == get_transient("tc_slider_notice"),
        ! self::$instance -> tc_is_front_help_enabled()
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
    function tc_fp_notice_ajax_actions() {
      if ( isset( $_POST['remove_action'] ) )
        $_remove_action = esc_attr( $_POST['remove_action'] );
      else
        wp_die(0);

      check_ajax_referer( 'tc-fp-notice-nonce', 'fpNoticeNonce' );
      switch ($_remove_action) {
        case 'remove_fp':
          TC_utils::$inst -> tc_set_option( 'tc_show_featured_pages' , 0 );
        break;

        case 'remove_notice':
          set_transient( 'tc_fp_notice', 'disabled' , 60*60*24*365*20 );//20 years of peace
        break;
      }
      wp_die();
    }


    /**
    * Prints dismiss notice js in the footer
    * Two cases :
    * 1) dismiss notice
    * 2) remove fp
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_write_fp_notice_js() {
      ?>
      <script type="text/javascript" id="fp-notice-actions">
        ( function( $ ) {
          var fp_ajax_request = function( remove_action, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'fp_notice_actions',
                    remove_action : remove_action,
                    fpNoticeNonce :  "<?php echo wp_create_nonce( 'tc-fp-notice-nonce' ); ?>"
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                return;
              // Check for cheaters.
              if ( '-1' === response )
                return;

              if ( 'remove_fp' == remove_action )
                $('#main-wrapper > .marketing').fadeOut('slow');
              else
                $_el.closest('.tc-fp-notice').slideToggle('fast');
            });
          };//end of fn

          //DOM READY
          $( function($) {
            $('.tc-dismiss-notice', '.tc-fp-notice').click( function( e ) {
              e.preventDefault();
              fp_ajax_request( 'remove_notice', $(this) );
            } );
            $('.tc-inline-remove', '.tc-fp-notice').click( function( e ) {
              e.preventDefault();
              fp_ajax_request( 'remove_fp', $(this) );
            } );
          } );

        }) (jQuery)
      </script>
      <?php
    }


    /**
    * Do we display the featured page notice ?
    * @return  bool
    * @since Customizr 3.4+
    */
    static function tc_is_fp_notice_on( $_position = null ) {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_dont_display_conditions = array(
        ! is_user_logged_in() || ! current_user_can('edit_theme_options'),
        ! is_admin() && ! TC_utils::$inst-> tc_is_home(),
        ! (bool)TC_utils::$inst->tc_opt('tc_show_featured_pages'),
        'disabled' == get_transient("tc_fp_notice"),
        self::$instance -> tc_is_one_fp_set(),
        TC___::tc_is_pro(),
        TC_plugins_compat::$instance->tc_is_plugin_active('tc-unlimited-featured-pages/tc_unlimited_featured_pages.php'),
        ! self::$instance -> tc_is_front_help_enabled()
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
    function tc_is_one_fp_set() {
      $_fp_sets = array();
      $fp_ids = apply_filters( 'tc_featured_pages_ids' , TC_init::$instance -> fp_ids);
      if ( ! is_array($fp_ids) )
        return;
      foreach ($fp_ids as $fp_single_id ) {
        $_fp_sets[] = (bool)TC_utils::$inst->tc_opt( 'tc_featured_page_'.$fp_single_id );
      }
      //returns true if at least one fp has been set.
      return (bool)array_sum($_fp_sets);
    }



    /************************************************************
    * WIDGET PLACEHOLDERS AJAX JS AND CALLBACK : FOR SIDEBARS AND FOOTER
    ************************************************************/
    /**
    * Prints dismiss widget notice javascript in the footer
    * hook : wp_footer
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_widget_placeholder_script() {
      ?>
      <script type="text/javascript" id="widget-placeholders">
        var tc_dismiss_widget_notice = function( _position, $_el ) {
            var AjaxUrl         = "<?php echo admin_url( 'admin-ajax.php' ); ?>",
                _query = {
                    action  : 'dismiss_widget_notice',
                    WidgetNonce :  "<?php echo wp_create_nonce( 'tc-widget-placeholder-nonce' ); ?>",
                    position : _position
                },
                $ = jQuery,
                request = $.post( AjaxUrl, _query );

            request.done( function( response ) {
              // Check if the user is logged out.
              if ( '0' === response )
                  return;
              // Check for cheaters.
              if ( '-1' === response )
                  return;
              if ( 'sidebar' == _position )
                $('.tc-widget-placeholder' , '.tc-sidebar').slideToggle('fast');
              else
                $_el.closest('.tc-widget-placeholder').slideToggle('fast');
            });
        };//end of fn
        jQuery( function($) {
          $('.tc-dismiss-notice, .tc-inline-dismiss-notice').click( function( e ) {
            e.preventDefault();
            var _position = $(this).attr('data-position');
            if ( ! _position || ! _position.length )
              return;
            tc_dismiss_widget_notice( _position, $(this) );
          } );
        } );
      </script>
      <?php
    }


    /**
    * Dismiss widget notice ajax callback
    * hook : wp_ajax_dismiss_widget_notice
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_dismiss_widget_notice() {
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
    static function tc_is_widget_placeholder_enabled( $_position = null ) {
      //never display when customizing
      if ( TC___::$instance -> tc_is_customizing() )
        return;

      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_position = is_null($_position) ? apply_filters('tc_widget_areas_position', array( 'sidebar', 'footer') ) : array($_position);

      return apply_filters( "tc_display_widget_placeholders",
        self::$instance -> tc_is_front_help_enabled() && is_user_logged_in() && current_user_can('edit_theme_options') && array_sum( array_map( array( self::$instance , 'tc_check_widget_placeholder_transient'), $_position ) )
      );
    }

    /**
    * @return  bool
    * @since Customizr 3.3+
    */
    function tc_check_widget_placeholder_transient( $_position ){
      return 'disabled' != get_transient("tc_widget_placehold_{$_position}");
    }


    /**
    * @return  bool
    * @since Customizr 3.4+
    * User option to enabe/disable all notices. Enabled by default.
    */
    function tc_is_front_help_enabled(){
      return apply_filters( 'tc_is_front_help_enabled' , (bool)TC_utils::$inst->tc_opt('tc_display_front_help') );
    }

  }//end of class
endif;