<?php
/**
* Retro compatibility actions
*/

/*
* This is fired very early, before the new defaults are generated
*/

//copy old options in the new framework
//and port classic style to modern style options
//only if user is logged in
//then each routine has to decide what to do also depending on the user started before
if ( is_user_logged_in() && current_user_can( 'edit_theme_options' ) ) {
    $theme_options            = czr_fn_get_unfiltered_theme_options();
    $_to_update               = false;

    if ( ! empty( $theme_options ) ) {

        $_new_options_w_socials              = czr_fn_maybe_move_old_socials_to_customizer_fmk( $theme_options );

        if ( ! empty( $_new_options_w_socials ) ) {
            $theme_options                     = $_new_options_w_socials;
            $_to_update                        = true;
        }


        //Custom css
        $_new_options_w_custom_css           = czr_fn_maybe_move_old_css_to_wp_embed( $theme_options );
        if ( ! empty( $_new_options_w_custom_css ) ) {
            $theme_options                     = $_new_options_w_custom_css;
            $_to_update                        = true;
        }

        //classic style skin port
        $_new_options_w_modern_skin          = czr_fn_maybe_move_classic_skin_to_modern( $theme_options );
        if ( ! empty( $_new_options_w_modern_skin ) ) {
            $theme_options                     = $_new_options_w_modern_skin;
            $_to_update                        = true;
        }

        //classic style sticky header port
        $_new_options_w_modern_sticky        = czr_fn_maybe_move_classic_sticky_header_to_modern( $theme_options );
        if ( ! empty( $_new_options_w_modern_sticky ) ) {
            $theme_options                     = $_new_options_w_modern_sticky;
            $_to_update                        = true;
        }

        //classic style header wccart port
        $_new_options_w_modern_header_wccart = czr_fn_maybe_move_classic_header_wccart_to_modern( $theme_options );
        if ( ! empty( $_new_options_w_modern_header_wccart ) ) {
            $theme_options                     = $_new_options_w_modern_header_wccart;
            $_to_update                        = true;
        }

        //classic style header tagline port
        $_new_options_w_modern_header_tagline = czr_fn_maybe_move_classic_header_tagline_to_modern( $theme_options );
        if ( ! empty( $_new_options_w_modern_header_tagline ) ) {
            $theme_options                     = $_new_options_w_modern_header_tagline;
            $_to_update                        = true;
        }

        //modern style header mobile search port
        $_new_options_w_modern_header_search_location = czr_fn_maybe_move_old_header_mobile_search_to_new( $theme_options );
        if ( ! empty( $_new_options_w_modern_header_search_location ) ) {
            $theme_options                     = $_new_options_w_modern_header_search_location;
            $_to_update                        = true;
        }


        //modern style header topbar port
        $_new_options_w_modern_header_topbar_visibility = czr_fn_maybe_move_old_header_topbar_to_new( $theme_options );
        if ( ! empty( $_new_options_w_modern_header_topbar_visibility ) ) {
            $theme_options                     = $_new_options_w_modern_header_topbar_visibility;
            $_to_update                        = true;
        }

        //modern style header topbar port
        $_new_options_w_modern_header_socials_visibility = czr_fn_maybe_move_old_header_socials_to_new( $theme_options );
        if ( ! empty( $_new_options_w_modern_header_socials_visibility ) ) {
            $theme_options                     = $_new_options_w_modern_header_socials_visibility;
            $_to_update                        = true;
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

  $_old_filtered_socials = apply_filters( 'tc_default_socials', CZR___::$instance -> old_socials );

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
function czr_fn_maybe_move_classic_skin_to_modern( $theme_options ) {

    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'classic_skin', $theme_options[ '__moved_opts' ] ) ) {
          return array();
    }

    /*
    * If classic skin not set or new skin color set just flag the classic skin ported and return the modified theme_options
    * so that, next time, we don't do what follows
    */

    $_classic_skin_is_set = isset( $theme_options[ 'tc_skin' ] ) && !empty( $theme_options[ 'tc_skin' ] );
    $_new_skin_is_set     = isset( $theme_options[ 'tc_skin_color' ] ) && !empty( $theme_options[ 'tc_skin_color' ] );

    if ( ! $_classic_skin_is_set || $_new_skin_is_set ) {

          //save the state in the options
          $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
          $theme_options[ '__moved_opts' ][]  = 'classic_skin';

          return $theme_options;
    }


    //get skin color from classic skin value, which is in the form color_name.css
    $_color_map    = CZR___::$instance -> skin_classic_color_map;

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
    $theme_options[ '__moved_opts' ][]  = 'classic_skin';

    return $theme_options;
}

/*
* In the classic Customizr style we have only one option that controls the sticky header behavior 'tc_sticky_header' (boolean)
* In the modern Customizr style this option is splitted in two 'tc_header_desktop_sticky', 'tc_header_mobile_sticky'
* These modern options define the sticky behavior as an array of the type:
* array(
*  'no_stick',
*  'stick_up' (default),
*  'stick_always'
* )
* We'll move the classic option following this map
* //tc_sticky_header (bool) => 'tc_header_desktop_sticky' (string) | 'tc_header_mobile_sticky' (string)
*  0 (false) => 'no_stick' | 'no_stick'
*  1 (true)  => 'stick_up' | 'sticky_up'
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_classic_sticky_header_to_modern( $theme_options ) {
    /*
    * When Memcached is active transients (object cached) might be not persistent
    * we cannot really rely on them :/
    */
    //nothing to do if new user
    //if ( czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
    //  return array();
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'classic_sticky_header', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }

    if ( isset( $theme_options[ 'tc_sticky_header' ] ) ) {
        $_classic_sticky_header = $theme_options[ 'tc_sticky_header' ];
        //let's now port the classic option to the modern if classic sticky option set and modern not set
        if ( $_classic_sticky_header ) {
            if ( !isset( $theme_options[ 'tc_header_desktop_sticky' ] ) )
                $theme_options[ 'tc_header_desktop_sticky' ] = 'stick_up';
            if ( !isset( $theme_options[ 'tc_header_mobile_sticky' ] ) )
                $theme_options[ 'tc_header_mobile_sticky' ] = 'stick_up';
        } else {
            if ( !isset( $theme_options[ 'tc_header_desktop_sticky' ] ) )
                $theme_options[ 'tc_header_desktop_sticky' ] = 'no_stick';
            if ( !isset( $theme_options[ 'tc_header_mobile_sticky' ] ) )
                $theme_options[ 'tc_header_mobile_sticky' ] = 'no_stick';
        }
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'classic_sticky_header';

    return $theme_options;
}

/*
* In the classic Customizr style we have only one option that controls the wc cart displaying in the header 'tc_woocommerce_header_cart' (bool)
* In the modern Customizr style this option is splitted in two 'tc_header_desktop_wc_cart' (string), 'tc_header_mobile_wc_cart' (bool)
* 'tc_header_desktop_wc_cart' is an array of the type:
* array(
*  'none',
*  'topbar' (default),
*  'navbar'
* )
* We'll move the classic option following this map
* //'tc_woocommerce_header_cart' (bool) => 'tc_header_desktop_wc_cart' (string) | 'tc_header_mobile_wc_cart' (bool)
*  0 (false) => 'none' | 0 (false)
*  1 (true)  => 'topbar' | 1 (true)
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_classic_header_wccart_to_modern( $theme_options ) {
    /*
    * When Memcached is active transients (object cached) might be not persistent
    * we cannot really rely on them :/
    */
    //nothing to do if new user
    //if ( czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
    //  return array();
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'classic_header_wccart', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }
    if ( isset( $theme_options[ 'tc_woocommerce_header_cart' ]) ) {
        $_classic_header_wc_cart = $theme_options[ 'tc_woocommerce_header_cart' ];

        //let's now port the classic option to the modern if classic wccart option set and modern not set
        if ( !isset( $theme_options[ 'tc_header_desktop_wc_cart' ] ) )
          $theme_options[ 'tc_header_desktop_wc_cart' ] = $_classic_header_wc_cart ? 'topbar' : 'none';

        if ( !isset( $theme_options[ 'tc_header_mobile_wc_cart' ] ) )
          $theme_options[ 'tc_header_mobile_wc_cart' ] = $_classic_header_wc_cart;
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'classic_header_wccart';

    return $theme_options;
}

/*
* In the classic Customizr style we have only one option that controls the tagline displaying in the header 'tc_show_tagline' (bool)
* In the modern Customizr style this option is splitted in two 'tc_header_desktop_tagline' (string), 'tc_header_mobile_tagline' (bool)
* 'tc_header_desktop_wc_cart' is an array of the type:
* array(
*  'none',
*  'topbar',
*  'brand_below' (default),
*  'brand_next'
* )
* We'll move the classic option following this map
* //'tc_show_tagline' (bool) => 'tc_header_desktop_tagline' (string) | 'tc_header_mobile_tagline' (bool)
*  0 (false) => 'none' | 0 (false)
*  1 (true)  => 'brand_below' | 1 (true)
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_classic_header_tagline_to_modern( $theme_options ) {
    /*
    * When Memcached is active transients (object cached) might be not persistent
    * we cannot really rely on them :/
    */
    //nothing to do if new user
    //if ( czr_fn_user_started_before_version( '3.4.39', '1.2.40' ) )
    //  return array();
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'classic_header_tagline', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }

    if ( isset( $theme_options[ 'tc_show_tagline' ] ) ) {
        $_classic_header_tagline = $theme_options[ 'tc_show_tagline' ];
        //let's now port the classic option to the modern if classic wccart option set and modern not set

        if ( !isset( $theme_options[ 'tc_header_desktop_tagline' ] ) )
          $theme_options[ 'tc_header_desktop_tagline' ] = $_classic_header_tagline ? 'brand_below' : 'none';

        if ( !isset( $theme_options[ 'tc_header_mobile_tagline' ] ) )
          $theme_options[ 'tc_header_mobile_tagline' ] = $_classic_header_tagline;
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'classic_header_tagline';

    return $theme_options;
}


/*
* Before v.4.0.12(2.0.17) 'tc_header_mobile_search' was a boolean that expressed
* whether or not displaying the search in the mobile header.
* Its default value was 1 (true) which meant displaying the mobile search in the mobile menu.
*
* Since v.4.0.12(2.0.17) it's a multichoice expressing where to display the search in mobiles
* 'none' => do not display
* 'navbar' => in the navbar next to the (wc_cart) menu button
* 'menu' => inside the collapsing mobile menu
*
* We'll move the old option following this map
*  0 (false) => 'none'
*  1 (true)  => 'menu'
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_old_header_mobile_search_to_new( $theme_options ) {
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'header_mobile_search', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }

    if ( isset( $theme_options[ 'tc_header_mobile_search' ] ) ) {
        $_old_header_mobile_search = $theme_options[ 'tc_header_mobile_search' ];

        $theme_options[ 'tc_header_mobile_search' ] = 0 === $_old_header_mobile_search ? 'none' : 'menu';
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'header_mobile_search';

    return $theme_options;
}


/*
* Before v.4.0.17(2.0.24) 'tc_header_desktop_topbar' was a boolean that expressed
* whether or not displaying the search in the desktop header.
* Its default value was 0 (false) which meant NOT displaying the mobile search in the dektop header.
*
* Since v.4.0.17(2.0.24) it's a multichoice expressing where to display the topbar in different devices
* 'none' => do not display
* 'desktop' => in the desktop header
* 'mobile' => in the mobile header
* 'desktop_mobile' => both in desktop and mobile header
*
* We'll move the old option to the new 'tc_header_show_topbar' following this map
*  0 (false) => 'none'
*  1 (true)  => 'desktop_mobile'
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_old_header_topbar_to_new( $theme_options ) {
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'header_topbar', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }

    if ( isset( $theme_options[ 'tc_header_desktop_topbar' ] ) ) {
        $_old_header_desktop_topbar = $theme_options[ 'tc_header_desktop_topbar' ];

        $theme_options[ 'tc_header_show_topbar' ] = 0 === $_old_header_desktop_topbar ? 'none' : 'desktop_mobile';
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'header_topbar';

    return $theme_options;
}

/*
* Before v.4.0.17(2.0.24) 'tc_social_in_header' was a boolean that expressed
* whether or not displaying the search in the desktop topbar.
* Its default value was 1 (true) which meant displaying the mobile search in the dektop topbar.
*
* Since v.4.0.17(2.0.24) it's a multichoice expressing where to display the topbar in different devices
* 'none' => do not display
* 'desktop' => in the desktop topbar
* 'mobile' => in the mobile topbar
* 'desktop_mobile' => both in desktop and mobile topbar
*
* We'll move the old option to the new 'tc_header_show_socials' following this map
*  0 (false) => 'none'
*  1 (true)  => 'desktop_mobile'
*/
/*
* returns array() the new set of options or empty if there's nothing to move
*/
function czr_fn_maybe_move_old_header_socials_to_new( $theme_options ) {
    //nothing to do if already moved
    if ( isset( $theme_options[ '__moved_opts' ] ) && in_array( 'header_socials', $theme_options[ '__moved_opts' ] ) ) {
        return array();
    }

    if ( isset( $theme_options[ 'tc_social_in_header' ] ) ) {
        $_old_header_desktop_socials = $theme_options[ 'tc_social_in_header' ];

        $theme_options[ 'tc_header_show_socials' ] = 0 === $_old_header_desktop_socials ? 'none' : 'desktop_mobile';
    }

    //In any case let's mark the porting done
    //save the state in the options
    $theme_options[ '__moved_opts' ]    = isset( $theme_options[ '__moved_opts' ] ) && is_array( $theme_options[ '__moved_opts' ] ) ? $theme_options[ '__moved_opts' ] : array();
    $theme_options[ '__moved_opts' ][]  = 'header_socials';

    return $theme_options;
}