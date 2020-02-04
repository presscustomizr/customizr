<?php

/* HELPERS SPECIFICS FOR CLASSICAL THAT'S WHY DEFINED HERE AND NOT IN THE SHARED FUNCTIONS*/

/**
* helper
* Renders the main header
* @return  void
*/
if ( ! function_exists( 'czr_fn_render_main_header' ) ) {
  function czr_fn_render_main_header() {
    CZR_header_main::$instance->czr_fn_set_header_options();
  ?>
    <header class="<?php echo implode( " ", apply_filters('tc_header_classes', array('tc-header' ,'clearfix', 'row-fluid') ) ) ?>">
    <?php
      // The '__header' hook is used with the following callback functions (ordered by priorities) :
      //CZR_header_main::$instance->tc_logo_title_display(), CZR_header_main::$instance->czr_fn_tagline_display(), CZR_header_main::$instance->czr_fn_navbar_display()
      do_action( '__header' );
    ?>
    </header>
  <?php
  }
}


/**
* helper
* Renders or returns the filtered and escaped tagline
* @return  void
*/
if ( ! function_exists( 'czr_fn_get_tagline_text' ) ) {
  function czr_fn_get_tagline_text( $echo = true ) {
    $tagline_text = apply_filters( 'tc_tagline_text', get_bloginfo( 'description', 'display' ) );
    if ( ! $echo )
      return $tagline_text;
    echo $tagline_text;
  }
}




//fire an action hook before loading the theme
do_action( 'czr_before_init' );
//Creates a new instance
new CZR___;
//fire an action hook after loading the theme
do_action( 'czr_after_init' );


//fire an action hook before loading the theme class groups
do_action( 'czr_before_load' );

//classic CZR___ will hook here to instantiate theme class groups
do_action('czr_load');

//may be load pro
if ( CZR_IS_PRO ) {
    new CZR_init_pro(CZR___::$theme_name );
}

//fire an action hook after loading the theme class groups
do_action( 'czr_after_load' );
?>