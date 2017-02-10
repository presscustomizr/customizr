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
  $theme_options            = czr_fn_get_raw_option( CZR_THEME_OPTIONS );


  if ( ! empty( $theme_options ) ) {

    $_new_options_w_socials = czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

    $_to_update             = ! empty( $_new_options_w_socials );
    $theme_options          = $_to_update ? $_new_options_w_socials : $theme_options;

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


  //nothing t do if already moved
  if ( ! czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
    return array();

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


  $_to_update   = false;
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

