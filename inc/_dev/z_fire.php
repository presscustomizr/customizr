<?php
//Creates a new instance
new CZR___;
do_action('czr_load');

//@return an array of unfiltered options
//=> all options or a single option val
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null, $from_cache = true ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize( $alloptions );
    //is there any option group requested ?
    if ( ! is_null( $opt_group ) && array_key_exists( $opt_group, $alloptions ) ) {
      $alloptions = maybe_unserialize( $alloptions[ $opt_group ] );
    }
    //shall we return a specific option ?
    if ( is_null( $opt_name ) ) {
        return $alloptions;
    } else {
        $opt_value = array_key_exists( $opt_name, $alloptions ) ? maybe_unserialize( $alloptions[ $opt_name ] ) : false;//fallback on cache option val
        //do we need to get the db value instead of the cached one ? <= might be safer with some user installs not properly handling the wp cache
        //=> typically used to checked the template name for czr_fn_isprevdem()
        if ( ! $from_cache ) {
            global $wpdb;
            //@see wp-includes/option.php : get_option()
            $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $opt_name ) );
            if ( is_object( $row ) ) {
                $opt_value = $row->option_value;
            }
        }
        return $opt_value;
    }
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