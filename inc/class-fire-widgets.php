<?php
/**
* Widgets factory : registered the different widgetized areas
* The default widget areas are defined as properties of the TC_utils class in class-fire-utils.php
* TC_utils::$inst -> sidebar_widgets for left and right sidebars
* TC_utils::$inst -> footer_widgets for the footer
* The widget area are then fired in the class below
*
* @package      Customizr
* @subpackage   classes
* @since        3.0
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com>
* @copyright    Copyright (c) 2013-2015, Nicolas GUILLAUME
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'TC_widgets' ) ) :
  class TC_widgets {
    //Access any method or var of the class with classname::$instance -> var or method():
    static $instance;
    function __construct () {
      self::$instance =& $this;
      //early hooks for ajax actions
      add_action( 'init'                            , array( $this , 'tc_set_footer_hooks') );
      //widgets actions
      add_action( 'widgets_init'                    , array( $this , 'tc_widgets_factory' ) );
    }


    /******************************************
    * REGISTER WIDGETS
    ******************************************/
    /**
    * Registers the widget areas
    * hook : widget_init
    *
    * @package Customizr
    * @since Customizr 3.0
    */
    function tc_widgets_factory() {
      //default Customizr filtered args
      $default                  = apply_filters( 'tc_default_widget_args' ,
                                array(
                                  'name'                    => '',
                                  'id'                      => '',
                                  'description'             => '',
                                  'class'                   => '',
                                  'before_widget'           => '<aside id="%1$s" class="widget %2$s">',
                                  'after_widget'            => '</aside>',
                                  'before_title'            => '<h3 class="widget-title">',
                                  'after_title'             => '</h3>',
                                )
      );

      //gets the filtered default values
      $footer_widgets           = apply_filters( 'tc_footer_widgets'  , TC_init::$instance -> footer_widgets );
      $sidebar_widgets          = apply_filters( 'tc_sidebar_widgets' , TC_init::$instance -> sidebar_widgets );
      $widgets                  = apply_filters( 'tc_default_widgets' , array_merge( $sidebar_widgets , $footer_widgets ) );

      //declares the arguments array
      $args                     = array();

      //fills in the $args array and registers sidebars
      foreach ( $widgets as $id => $infos) {
          foreach ( $default as $key => $default_value ) {
            if ('id' == $key ) {
              $args[$key] = $id;
            }
            else if ( 'name' == $key || 'description' == $key) {
              $args[$key] = !isset($infos[$key]) ? $default_value : call_user_func( '__' , $infos[$key] , 'customizr' );
            }
            else {
              $args[$key] = !isset($infos[$key]) ? $default_value : $infos[$key];
            }
          }
        //registers sidebars
        register_sidebar( $args );
      }
    }



    /******************************************
    * SETUP AJAX ACTIONS
    ******************************************/
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
    function tc_set_footer_hooks() {
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
          $('.tc-dismiss-notice').click( function( e ) {
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


    /******************************************
    * HELPERS
    ******************************************/
    /**
    * Public helper, state if we can display a widget placeholder to the current user.
    * @return  bool
    * @since Customizr 3.3+
    */
    function tc_is_widget_placeholder_enabled( $_position = null ) {
      //always display in DEV mode
      if ( defined('TC_DEV') && true === TC_DEV )
        return true;

      $_position = is_null($_position) ? apply_filters('tc_widget_areas_position', array( 'sidebar', 'footer') ) : array($_position);

      return apply_filters( "tc_display_widget_placeholders",
        is_user_logged_in() && current_user_can('edit_theme_options') && array_sum( array_map( array( $this , 'tc_check_widget_placeholder_transient'), $_position ) )
      );
    }

    /**
    * @return  bool
    * @since Customizr 3.3+
    */
    function tc_check_widget_placeholder_transient( $_position ){
      return 'disabled' != get_transient("tc_widget_placehold_{$_position}");
    }

  }//end of class
endif;
