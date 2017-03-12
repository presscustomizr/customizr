<?php
//Creates a new instance
new CZR___;
do_action('czr_load');

//@return an array of unfiltered options
//=> all options or a single option val
//modified version of wp get_option, to avoid filtering and being able to retrieve a "suboption"
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null, $default = false ) {
    $option = is_null( $opt_group ) ? $opt_name : $opt_group;   

    global $wpdb;
    $option = trim( $option );

    if ( defined( 'WP_SETUP_CONFIG' ) )
        return false;

    if ( ! wp_installing() ) {
        // prevent non-existent options from triggering multiple queries
        $notoptions = wp_cache_get( 'notoptions', 'options' );
        if ( isset( $notoptions[ $option ] ) ) {
            return $default;
        }

        $alloptions = wp_load_alloptions();      

        if ( is_null( $option ) )
            return maybe_unserialize( $alloptions );

        if ( isset( $alloptions[$option] ) ) {
            $value = $alloptions[$option];
        } 
        else {
            $value = wp_cache_get( $option, 'options' );
            if ( false === $value ) {
                $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );

                // Has to be get_row instead of get_var because of funkiness with 0, false, null values
                if ( is_object( $row ) ) {
                    $value = $row->option_value;
                    wp_cache_add( $option, $value, 'options' );
                } else { // option does not exist, so we must cache its non-existence
                    if ( ! is_array( $notoptions ) ) {
                        $notoptions = array();
                    }
                    $notoptions[$option] = true;
                    wp_cache_set( 'notoptions', $notoptions, 'options' );
                    return $default; //<- do we want to return alloptions here?
                }
            }
        }
    } else {
        $suppress = $wpdb->suppress_errors();
        $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );
      
        $wpdb->suppress_errors( $suppress );
        if ( is_object( $row ) ) {
            $value = $row->option_value;
        } else {
            return $default;
        }
    }
    
    // If home is not set use siteurl.
    if ( 'home' == $option && '' == $value )
        return get_option( 'siteurl' );
    
    if ( in_array( $option, array('siteurl', 'home', 'category_base', 'tag_base') ) )
        $value = untrailingslashit( $value );

    $value = maybe_unserialize( $value );
   
    if ( !is_null( $opt_group ) && !is_null( $opt_name ) && is_array($value) && isset($value[$opt_name]) ) {
        $value = maybe_unserialize($value[$opt_name]);
    }
    
    return $value; 
}

//@return an array of options
function czr_fn_get_admin_option( $option_group = null ) {
    $option_group           = is_null($option_group) ? CZR_THEME_OPTIONS : $option_group;

    //here we could hook a callback to remove all the filters on "option_{CZR_THEME_OPTIONS}"
    do_action( "tc_before_getting_option_{$option_group}" );
    $options = get_option( $option_group, array() );
    //here we could hook a callback to re-add all the filters on "option_{CZR_THEME_OPTIONS}"
    do_action( "tc_after_getting_option_{$option_group}" );

    return $options;
}

//@return bool
function czr_fn_isprevdem() {
    return apply_filters( 'czr_fn_isprevdem', ( czr_fn_get_raw_option( 'template', null, false ) != get_stylesheet() && ! is_child_theme() && ! CZR___::czr_fn_is_pro() ) );
}

if ( czr_fn_isprevdem() && class_exists('CZR_prevdem') ) {
    new CZR_prevdem();
}

//may be load pro
if ( CZR___::czr_fn_is_pro() ) {
    new CZR_init_pro(CZR___::$theme_name );
}
?>