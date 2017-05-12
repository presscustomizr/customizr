<?php
/**
* Retro compatibility actions
*/

/*
* This is fired very early, before the new defaults are generated
*/

//copy old options in the new framework
//only if user is logged in
//then each routine has to decide what to do also depending on the user started before
if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
  $theme_options            = czr_fn_get_admin_option( CZR_THEME_OPTIONS );
  $_to_update               = false;

  if ( ! empty( $theme_options ) ) {

    $_new_options_w_socials = czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

    if ( ! empty( $_new_options_w_socials ) ) {
      $theme_options              = $_new_options_w_socials;
      $_to_update                 = true;
    }


    //Custom css
    $_new_options_w_custom_css  = czr_fn_maybe_move_old_css_to_wp_embed( $theme_options );
    if ( ! empty( $_new_options_w_custom_css ) ) {
      $theme_options              = $_new_options_w_custom_css;
      $_to_update                 = true;
    }

    //old skin
    $_new_options_w_skin        = czr_fn_maybe_move_old_skin_to_czr4( $theme_options );
    if ( ! empty( $_new_options_w_skin ) ) {
      $theme_options              = $_new_options_w_skin;
      $_to_update                 = true;
    }

    if ( $_to_update ) {
      update_option( CZR_THEME_OPTIONS, $theme_options );
    }

  }
}

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
  //if ( czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
  //  return array();
  //nothing to do if already moved
  if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_socials', $_options[ '__moved_opts' ] ) ) {
    return array();
  }


  $_old_socials  = array(
        'tc_rss'            => array(
                                'link_title'    => __( 'Subscribe to my rss feed' , 'customizr' ),
                                'default'       => get_bloginfo( 'rss_url' ) //kept as it's the only one used in the transition
                            ),
        'tc_email'          => array(
                                'link_title'    => __( 'E-mail' , 'customizr' ),
                              ),
        'tc_twitter'        => array(
                                'link_title'    => __( 'Follow me on Twitter' , 'customizr' ),
                              ),
        'tc_facebook'       => array(
                                'link_title'    => __( 'Follow me on Facebook' , 'customizr' ),
                              ),
        'tc_google'         => array(
                                'link_title'    => __( 'Follow me on Google+' , 'customizr' ),
                              ),
        'tc_instagram'      => array(
                                'link_title'    => __( 'Follow me on Instagram' , 'customizr' ),
                              ),
        'tc_tumblr'       => array(
                                'link_title'    => __( 'Follow me on Tumblr' , 'customizr' ),
                              ),
        'tc_flickr'       => array(
                                'link_title'    => __( 'Follow me on Flickr' , 'customizr' ),
                              ),
        'tc_wordpress'      => array(
                                'link_title'    => __( 'Follow me on WordPress' , 'customizr' ),
                              ),
        'tc_youtube'        => array(
                                'link_title'    => __( 'Follow me on Youtube' , 'customizr' ),
                              ),
        'tc_pinterest'      => array(
                                'link_title'    => __( 'Pin me on Pinterest' , 'customizr' ),
                              ),
        'tc_github'         => array(
                                'link_title'    => __( 'Follow me on Github' , 'customizr' ),
                              ),
        'tc_dribbble'       => array(
                                'link_title'    => __( 'Follow me on Dribbble' , 'customizr' ),
                              ),
        'tc_linkedin'       => array(
                                'link_title'    => __( 'Follow me on LinkedIn' , 'customizr' ),
                              ),
        'tc_vk'             => array(
                                'link_title'    => __( 'Follow me on VKontakte' , 'customizr' ),
                              ),
        'tc_yelp'           => array(
                                'link_title'    => __( 'Follow me on Yelp' , 'customizr' ),
                              ),
        'tc_xing'           => array(
                                'link_title'    => __( 'Follow me on Xing' , 'customizr' ),
                              ),
        'tc_snapchat'       => array(
                                'link_title'    => __( 'Contact me on Snapchat' , 'customizr' ),
                              )
  );//end of social array

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
  $theme_options[ '__moved_opts' ][]  = 'old_socials';

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
    $theme_options[ '__moved_opts' ][]  = 'custom_css';

    return $theme_options;
  }

  return array();
}

/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_old_skin_to_czr4( $theme_options ) {

      $_options = $theme_options;

      //nothing to do if already moved
      if ( isset( $_options[ '__moved_opts' ] ) && in_array( 'old_skin', $_options[ '__moved_opts' ] ) ) {

            return array();

      }

      /*
      * If old skin not set or new skin color set just flag the old skin ported and return the modified theme_options
      * so that, next time, we don't do what follows
      */

      $_old_skin_set = isset( $theme_options[ 'tc_skin' ] ) && !empty( $theme_options[ 'tc_skin' ] );
      $_new_skin_set = isset( $theme_options[ 'tc_skin_color' ] ) && !empty( $theme_options[ 'tc_skin_color' ] );

      if ( !$_old_skin_set || $_new_skin_set ) {

            //save the state in the options
            $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
            $theme_options[ '__moved_opts' ][]  = 'old_skin';

            return $theme_options;
      }


      //get skin color from old skin value, which is in the form color_name.css
      $_color_map    = CZR_init::$instance -> skin_color_map;

      $_active_skin  = $theme_options[ 'tc_skin' ];

      //mapped skin case
      if ( ( false != $_active_skin && isset( $_color_map[$_active_skin][0] ) ) ) {

            $_skin_color   =  $_color_map[$_active_skin][0];

      } //treat custom skin color case: in the form custom-skin-{hex}.css
      else {

            $match         = 0;
            $_skin_color   = preg_replace( '|^custom\-skin\-((?:[A-Fa-f0-9]{3}){1,2})\.css$|', '$1', $_active_skin, 1, $match );
            $_skin_color   = $match ? "{#$_skin_color}" : false;

      }


      if ( $_skin_color ) {

            $theme_options[ 'tc_skin_color' ] = "$_skin_color";

      }

      /*
      * Whether or not the skin color match is found we did what we had to
      * so flag it and return $theme_options
      */
      //save the state in the options
      $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
      $theme_options[ '__moved_opts' ][]  = 'old_skin';

      return $theme_options;

}