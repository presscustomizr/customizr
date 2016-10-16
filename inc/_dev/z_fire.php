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


//@return bool
function czr_fn_isprevdem() {
  $_active_theme = czr_fn_get_raw_option( 'template' );
  //get WP_Theme object
  $czr_theme                     = wp_get_theme();
  //Get infos from parent theme if using a child theme
  $czr_theme = $czr_theme -> parent() ? $czr_theme -> parent() : $czr_theme;
  return apply_filters( 'czr_fn_isprevdem', ( $_active_theme != strtolower( $czr_theme -> name ) && ! is_child_theme() && ! CZR___::czr_fn_is_pro() ) );
}

if ( czr_fn_isprevdem() && class_exists('CZR_prevdem') ) {
  new CZR_prevdem();
}

//may be load pro
if ( CZR___::czr_fn_is_pro() )
  new CZR_init_pro(CZR___::$theme_name );
?>