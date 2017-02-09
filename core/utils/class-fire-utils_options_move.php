<?php
/**
*
*/

//only if user is logged in
if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
  $_options     = czr_fn_get_raw_option( CZR_THEME_OPTIONS );

  if ( ! empty( $_options ) )
    $_new_options = czr_fn_maybe_move_old_socials_to_customizer_fmk( $_options );

  if ( ! empty( $_new_options ) )
    update_option( CZR_THEME_OPTIONS, $_new_options );

}

function czr_fn_maybe_move_old_socials_to_customizer_fmk( $_options ) {
  $_old_socials = array(
            'tc_rss'            => __( 'Subscribe to my rss feed' , 'customizr' ),

            'tc_email'          => __( 'E-mail' , 'customizr' ),

            'tc_twitter'        => __( 'Follow me on Twitter' , 'customizr' ),

            'tc_facebook'       => __( 'Follow me on Facebook' , 'customizr' ),

            'tc_google'         => __( 'Follow me on Google+' , 'customizr' ),

            'tc_instagram'      => __( 'Follow me on Instagram' , 'customizr' ),

            'tc_tumblr'         => __( 'Follow me on Tumblr' , 'customizr' ),

            'tc_flickr'         => __( 'Follow me on Flickr' , 'customizr' ),

            'tc_wordpress'      => __( 'Follow me on WordPress' , 'customizr' ),

            'tc_youtube'        => __( 'Follow me on Youtube' , 'customizr' ),

            'tc_pinterest'      => __( 'Pin me on Pinterest' , 'customizr' ),

            'tc_github'         => __( 'Follow me on Github' , 'customizr' ),

            'tc_dribbble'       => __( 'Follow me on Dribbble' , 'customizr' ),

            'tc_linkedin'       => __( 'Follow me on LinkedIn' , 'customizr' ),

            'tc_vk'             => __( 'Follow me on VKontakte' , 'customizr' ),

            'tc_yelp'           => __( 'Follow me on Yelp' , 'customizr' ),

            'tc_xing'           => __( 'Follow me on Xing' , 'customizr' ),

            'tc_snapchat'       => __( 'Contact me on Snapchat' , 'customizr' )
  );

  $_to_update  = false;

  $_tc_socials = array();
  $_index      = 0;

  foreach ( $_old_socials as $_old_social_id => $_title ) {
    if ( isset( $_options[ $_old_social_id ] ) ) {

      if ( ! empty( $_options[ $_old_social_id ] ) ) {
        //create module
        array_push( $_tc_socials, array(
            'id'            => "czr_social_module_{$_index}",
            'title'         => $_title,
            'social-icon'   => str_replace( array( 'tc_email', 'tc_'), array( 'fa-envelope', 'fa-' ), $_old_social_id ),
            'social-link'   => $_options[ $_old_social_id ],
            'social-target' => 1,
            'social-color'  => "rgba(255,255,255,0.7)"
          )
        );
        $_index++;
      }

      unset( $_options[ $_old_social_id ] );
      $_to_update = true;
    }
  }

  if ( $_to_update )
    $_options['tc_social_links'] = $_tc_socials;

  return $_to_update ? $_options : false;
}