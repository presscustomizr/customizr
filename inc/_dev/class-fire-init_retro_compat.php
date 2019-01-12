<?php
/**
* Defines filters and actions used in several templates/classes
*
*/
if ( ! class_exists( 'CZR_init_retro_compat' ) ) :
  class CZR_init_retro_compat {
    static $instance;

    /*
    * This is fired very early, before the new defaults are generated
    */
    function __construct () {
      self::$instance =& $this;

      //copy old options in the new framework
      //only if user is logged in
      //then each routine has to decide what to do also depending on the user started before
      if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
        $theme_options            = czr_fn_get_unfiltered_theme_options();
        $_to_update               = false;

        if ( ! empty( $theme_options ) ) {
          //Socials
          $_new_options_w_socials     = $this -> czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

          if ( ! empty( $_new_options_w_socials ) ) {
            $theme_options              = $_new_options_w_socials;
            $_to_update                 = true;
          }

          //Custom css
          $_new_options_w_custom_css  = $this -> czr_fn_maybe_move_old_css_to_wp_embed( $theme_options );

          if ( ! empty( $_new_options_w_custom_css ) ) {
            $theme_options              = $_new_options_w_custom_css;
            $_to_update                 = true;
          }

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


      /*
      * When Memcached is active transients (object cached) might be not persistent
      * we cannot really rely on them :/
      */
      //nothing to do if new user
      //if ( ! czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
      //  return array();

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_socials', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      /*
      * In theme versions < 3.5.5  we didn't use store the __moved_opts['old_socials'] in the options
      * if there was anything to move, so we need another check here to see if new socials have been already
      * set
      */
      if ( isset( $_options[ 'tc_social_links' ] ) && !empty($_options[ 'tc_social_links' ] ) ) {
        return array();
      }

      $_old_socials          = CZR___::$instance -> old_socials;
      $_old_filtered_socials = apply_filters( 'tc_default_socials', $_old_socials );

      /*
      * old socials were in the form
      * array( 'tc_twitter' => array( .., default=>'[url]' ,..) )
      * need to be ported in the form
      * array( 'tc_twitter' => '[url]' )
      * before parse them in the options.
      */
      $_social_options       = array();
      foreach ( $_old_filtered_socials as $social => $attrs ) {
        if ( isset( $attrs['default'] ) ) {
          $_social_options[$social] = $attrs['default'];
        }
      }

      //merge options with the defaults socials
      $_options     = wp_parse_args( $_options, $_social_options );

      $_new_socials = array();
      $_index       = 0;

      /*
      * rss needs a special treatment for old users, it was a default
      * If it doesn't exist in the options we have to set it with the default value
      * if it exists but is null it will be skipped
      */
      foreach ( $_old_filtered_socials as $_old_social_id => $attributes ) {
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
            )
          );
          $_index++;
        }
      }

      if ( !empty( $_new_socials ) ) {
        $theme_options[ 'tc_social_links' ] = $_new_socials;
      }

      //save the state in the options
      $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
      array_push( $theme_options[ '__moved_opts' ], 'old_socials' );

      return $theme_options;
    }


    /*
    * returns array() the new set of options or empty if there's nothing to move
    */
    function czr_fn_maybe_move_old_css_to_wp_embed( $theme_options ) {
      $_options = $theme_options;


      /*
      * When Memcached is active transients (object cached) might be not persistent
      * we cannot really rely on them :/
      */
      //if ( ! czr_fn_user_started_before_version( '3.5.5', '1.3.3' ) )
      //  return array();

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'custom_css', $_options[ '__moved_opts' ] ) ) {
        return array();
      }

      /*
      * FROM
      * https://make.wordpress.org/core/2016/11/26/extending-the-custom-css-editor/
      */
      if ( function_exists( 'wp_update_custom_css_post' ) ) {
        // Migrate any existing theme CSS to the core option added in WordPress 4.7.
        $css = array_key_exists( 'tc_custom_css', $_options ) ?  html_entity_decode( esc_html( $_options['tc_custom_css'] ) ) : '';

        if ( $css ) {
          $core_css = wp_get_custom_css(); // Preserve any CSS already added to the core option.
          //avoid duplications
          $core_css = str_replace( $css, '', $core_css );
          $return = wp_update_custom_css_post( $core_css . "\n" . $css );
        }


        //save the state in the options
        $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
        array_push( $theme_options[ '__moved_opts' ], 'custom_css' );

        return $theme_options;
      }

      return array();
    }

  }//end class
endif;
?>