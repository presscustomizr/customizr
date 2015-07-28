<?php
/**
* This class must be instanciated if is_admin() for the ajax call to work
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
      add_action( 'init'           , array( $this, 'tc_slider_dimiss_ajax_setup') );
      add_action( 'init'           , array( $this, 'tc_second_menu_placeholder_setup') );
      add_action( 'init'           , array( $this, 'tc_widget_ajax_setup') );
    }


    /*****************************************************
    * SLIDER : AJAX JS AND ACTIONS
    *****************************************************/
    /**
    * Set the placeholder related hooks if conditions are met in tc_is_second_menu_placeholder_on()
    *
    * hook : init
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_slider_dimiss_ajax_setup() {
      if ( ! $this -> tc_is_slider_notice_on() )
        return;

      add_action( 'wp_footer'                           , array( $this, 'tc_write_slider_notice_js'), 100 );
      add_action( 'wp_ajax_slider_notice_actions'       , array( $this, 'tc_slider_notice_ajax_actions' ) );
    }


    /**
    * Two cases :
    * 1) dismiss notice
    * 2) remove demo slider
    * hook : wp_ajax_slider_notice_actions
    *
    * @package Customizr
    * @since Customizr 3.3+
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
    * Public helper, state if we can display a widget placeholder to the current user.
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_slider_notice_on( $_position = null ) {
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
        is_user_logged_in() && current_user_can('edit_theme_options') && 'disabled' != get_transient("tc_second_menu_placehold")
      );
    }



    /*****************************************************
    * SECOND MENU PLACEHOLDER : AJAX JS AND ACTIONS
    *****************************************************/
    /**
    * Set the placeholder related hooks if conditions are met in tc_is_second_menu_placeholder_on()
    *
    * hook : init
    *
    * @package Customizr
    * @since Customizr 3.4+
    */
    function tc_second_menu_placeholder_setup() {
      if ( ! $this -> tc_is_second_menu_placeholder_on() )
        return;

      add_action( 'wp_footer'                           , array( $this, 'tc_write_second_menu_placeholder_js'), 100 );
      add_action( 'wp_ajax_dismiss_second_menu_notice'  , array( $this, 'tc_dismiss_second_menu_notice' ) );
    }


    /**
    * Dismiss widget notice ajax callback
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
            })
            .always(function( response ) {
              console.log( 'ajax response : ', response, arguments );
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
    * Public helper, state if we can display a widget placeholder to the current user.
    * @return  bool
    * @since Customizr 3.3+
    */
    static function tc_is_second_menu_placeholder_on( $_position = null ) {
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
        is_user_logged_in() && current_user_can('edit_theme_options') && 'disabled' != get_transient("tc_second_menu_placehold")
      );
    }




    /************************************************************
    * WIDGET PLACEHOLDERS AJAX ACTIONS : FOR SIDEBARS AND FOOTER
    ************************************************************/
    /**
    * Set the widget placeholder related hooks if :
    * - user is logged in and admin
    * - placeholder transients != disabled
    *
    * hook : init
    *
    * @package Customizr
    * @since Customizr 3.3+
    */
    function tc_widget_ajax_setup() {
      if ( ! $this -> tc_is_widget_placeholder_enabled() )
        return;

      add_action( 'wp_footer'                       , array( $this, 'tc_widget_placeholder_script'), 100 );
      add_action( 'wp_ajax_dismiss_widget_notice'   , array( $this , 'tc_dismiss_widget_notice' ) );
    }


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
      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_position = is_null($_position) ? apply_filters('tc_widget_areas_position', array( 'sidebar', 'footer') ) : array($_position);

      return apply_filters( "tc_display_widget_placeholders",
        is_user_logged_in() && current_user_can('edit_theme_options') && array_sum( array_map( array( self::$instance , 'tc_check_widget_placeholder_transient'), $_position ) )
      );
    }

    /**
    * @return  bool
    * @since Customizr 3.3+
    */
    function tc_check_widget_placeholder_transient( $_position ){
      return 'disabled' != get_transient("tc_widget_placehold_{$_position}");
    }


    /************************************************************
    * COMMON HELPERS
    ************************************************************/
    /**
    * Returns the url of the customizer with the current url arguments + an optional customizer section args
    * @param $section is an array indicating the panel or section and its name. Ex : array( 'panel' => 'widgets')
    * @return url string
    * @since Customizr 3.4+
    */
    static function tc_get_customizer_url( $_panel_or_section = null ) {
      $_current_url       = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
      $_customize_url     = add_query_arg( 'url', urlencode( $_current_url ), wp_customize_url() );
      $_panel_or_section  = ( ! is_array($_panel_or_section) || empty($_panel_or_section) ) ? null : $_panel_or_section;

      if ( is_null($_panel_or_section) )
        return $_customize_url;

      if ( ! array_key_exists('section', $_panel_or_section) && ! array_key_exists('panel', $_panel_or_section) )
        return $_customize_url;

      $_what = array_key_exists('section', $_panel_or_section) ? 'section' : 'panel';
      return add_query_arg( urlencode( "autofocus[{$_what}]" ), $_panel_or_section[$_what], $_customize_url );
    }


  }//end of class
endif;
