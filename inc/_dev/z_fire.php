<?php
//Creates a new instance
new CZR___;
do_action('czr_load');

//@return an array of unfiltered options
//=> all options or a single option val
function czr_fn_get_raw_option( $opt_name = null, $opt_group = null ) {
    $alloptions = wp_cache_get( 'alloptions', 'options' );
    $alloptions = maybe_unserialize($alloptions);
    if ( ! is_null( $opt_group ) && isset($alloptions[$opt_group]) ) {
      $alloptions = maybe_unserialize($alloptions[$opt_group]);
    }
    if ( is_null( $opt_name ) )
      return $alloptions;
    return isset( $alloptions[$opt_name] ) ? maybe_unserialize($alloptions[$opt_name]) : false;
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
  $_active_theme = czr_fn_get_raw_option( 'template' );

  return apply_filters( 'czr_fn_isprevdem', ( $_active_theme != get_stylesheet() && ! is_child_theme() && ! CZR___::czr_fn_is_pro() ) );
}

if ( czr_fn_isprevdem() && class_exists('CZR_prevdem') ) {
  new CZR_prevdem();
}

//may be load pro
if ( CZR___::czr_fn_is_pro() )
  new CZR_init_pro(CZR___::$theme_name );
?>