<?php
/**
* Defines filters and actions used in several templates/classes
*
*
* @package      Customizr
* @subpackage   classes
* @since        3.4.39
* @author       Nicolas GUILLAUME <nicolas@presscustomizr.com> - Rocco ALIBERTI <rocco@presscustomizr.com>
* @copyright    Copyright (c) 2013-2017, Nicolas GUILLAUME, Rocco ALIBERTI
* @link         http://presscustomizr.com/customizr
* @license      http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
if ( ! class_exists( 'CZR_init_retro_compat' ) ) :
  class CZR_init_retro_compat {
    static $instance;


    function __construct () {
      self::$instance =& $this;

      //copy old options from option tree framework into new option raw 'hu_theme_options'
      //copy logo from previous to custom_logo introduced in wp 4.5
      //only if user is logged in
      if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
        $theme_options       = czr_fn_get_raw_option( CZR_THEME_OPTIONS );

        if ( ! empty( $theme_options ) ) {

          $_new_options_w_socials = $this -> czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

          $_to_update             = ! empty( $_new_options_w_socials );
          $theme_options          = $_to_update ? $_new_options_w_socials : $theme_options;

          if ( $_to_update ) {

            update_option( CZR_THEME_OPTIONS, $theme_options );

          }
        }
      }
    }//construct

    /*
    * returns array() the new set of options or empty if there's nothing to move
    */
    function czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options ) {
      $_options = $theme_options;

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_socials', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      $_old_socials = CZR_init::$instance -> socials;

      $_to_update   = false;
      $_new_socials = array();
      $_index       = 0;

      /*
      * rss needs a special treatment for old users, it was a default
      * If it's not set in the options we have to set it with the default value
      */
      if ( ! isset( $theme_options[ 'tc_rss' ] ) && CZR_utils::$inst -> czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) ) {
        $_options[ 'tc_rss' ] = $_old_socials[ 'tc_rss' ][ 'default' ];
      }

      foreach ( $_old_socials as $_old_social_id => $attributes ) {

        if ( isset( $_options[ $_old_social_id ] ) ) {
          if ( ! empty( $_options[ $_old_social_id ] ) ) {

            //build new attributes
            $_title       = isset( $attributes[ 'link_title' ] ) ? esc_attr( $attributes[ 'link_title' ] ) :  '';
            $_social_icon = str_replace( array( 'tc_email', 'tc_'), array( 'fa-envelope', 'fa-' ), $_old_social_id );

            // email needs a special treatment
            $_social_link = esc_url_raw( 'tc_email' == $_old_social_id  ? sprintf( 'mailto:%s', $_options[ $_old_social_id ] ) : $_options[ $_old_social_id ] );

            if ( empty( $_social_link ) ) {
              continue;
            }

            //create module
            array_push( $_new_socials, array(
                'id'            => "czr_social_module_{$_index}",
                'title'         => $_title,
                'social-icon'   => $_social_icon,
                'social-link'   => $_social_link,
                'social-target' => 1,
                'social-color'  => "rgb(0,0,0)"
              )
            );
            $_index++;
          }

          $_to_update = true;
        }
      }

      if ( $_to_update ) {
        $theme_options[ 'tc_social_links' ] = $_new_socials;

        //save the state in the options
        $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();

        array_push( $theme_options[ '__moved_opts' ], 'old_socials' );

        return $theme_options;
      }

      return array();
    }
  }
endif;
?>