<?php
$_options = array(
    //to add
    'tc_skin_type' => 'dark', // (light|dark)
    'tc_menu_position' => 'pull-menu-left', //pull-menu-left
    'tc_fonts' => '_g_poppins_hind',
    'tc_font_awesome_css' => true,
    'tc_font_awesome_icons' => true,
    'tc_sticky_header' => true,
    'tc_header_type' => 'standard',// (absolute||standard)
    'tc_woocommerce_header_cart_sticky' => false,
    'tc_show_tagline' => true,
    'tc_display_second_menu' => true,
    'tc_menu_style' => 'regular',
    'tc_menu_type' => 'click',
    'tc_logo_upload' => '611',
    'tc_sticky_logo_upload' => '611',
    'tc_sticky_shrink_title_logo' => true,
    //for backward compatiblity keep the _grid suffix
    'tc_post_list_grid' => 'plain_excerpt', //grid - masonry - alternate - plain - plain_excerpt

    'tc_show_post_metas_home' => true,
    'tc_show_post_metas_tags' => true,
    'tc_comment_show_info' => true,

    'tc_sidebar_global_layout' => 'l',
    'tc_sidebar_post_layout'   => 'r',
    'tc_front_layout'          => 'f',

    'tc_post_list_thumb_position' => 'right',
    'tc_post_list_thumb_alternate' => true,

    'tc_img_smart_load' => false,
    'tc_smoothscroll' => true,

    'tc_grid_bottom_border' => true,
    'tc_grid_shadow'        => true,

    'tc_slider_width'       => 'full-page',
    'tc_front_slider'       => 'prova',
    'tc_display_slide_loader' => true,

    //test
    'tc_slider_default_height' => '400',
    'tc_slider_parallax'       => true,

    'tc_posts_slider_number'   => '5',
    'tc_posts_slider_stickies' => false,

    'tc_show_author_info'      => true,

    //to add
    'tc_related_posts'         => true,

    'tc_center_img'            => true,

    'tc_show_post_navigation_home' => true,

);


function czr_fn_get_opt( $_opt_name, $option_group = null, $use_default = true) {
  global $_options;
  return apply_filters( "czr_opt_{$_opt_name}", isset($_options[$_opt_name]) ? $_options[$_opt_name] : czr_fn_opt( $_opt_name , $option_group, $use_default ) );
}

//Do not use post slider transient for the moment
//add_filter( 'czr_posts_slider_use_transient', '__return_false' );

add_filter( 'czr_add_custom_fonts_to_editor' , '__return_false' );

//Test display a header sticky push except in home
add_filter( 'czr_opt_tc_sticky_header_type', function( $_what ){
  return czr_fn_is_home() ? $_what :  'push';
});

add_filter( 'czr_gfont_pairs', function( $_fonts ) {
  return array_merge( $_fonts, array(
            '_g_poppins_hind' => array( 'Poppins &amp; Hint', 'Poppins:400,300,500,600,700|Hind:400,300,500,600,700' )
         )
  );
});

add_action( 'wp_enqueue_scripts', 'czr_fn_enqueue_front_styles');
function czr_fn_enqueue_front_styles() {
    //Enqueue FontAwesome CSS
    if ( true == czr_fn_get_opt( 'tc_font_awesome_css' ) ) {
        $_path = apply_filters( 'czr_font_icons_path' , CZR_BASE_URL . CZR_ASSETS_PREFIX . 'shared/css' );
      wp_enqueue_style( 'customizr-fa',
        $_path . '/fonts/' . CZR_init::$instance -> czr_fn_maybe_use_min_style( 'font-awesome.css' ),
        array() , CUSTOMIZR_VER, 'all' );
    }

  $_path = czr_fn_get_theme_file_url( CZR_ASSETS_PREFIX . 'front/css/' );

  wp_enqueue_style( 'customizr-bs', $_path . 'custom-bs/custom-bootstrap.css' , array() , CUSTOMIZR_VER, 'all' );

  wp_enqueue_style( 'customizr-flickity', $_path . 'flickity.css' , array() , CUSTOMIZR_VER, 'all' );

  wp_enqueue_style( 'customizr-magnific', $_path . 'magnific-popup.css' , array() , CUSTOMIZR_VER, 'all' );

  wp_enqueue_style( 'customizr-pre-common', $_path . 'customizr.css' , array() , CUSTOMIZR_VER, 'all' );

  wp_enqueue_style( 'customizr-common', $_path . 'czr/style.css', array() , CUSTOMIZR_VER, 'all' );

  //Customizr stylesheet (style.css)
  wp_enqueue_style( 'customizr-style', $_path . 'style.css', array(), CUSTOMIZR_VER , 'all' );

  //Customizer user defined style options : the custom CSS is written with a high priority here
  wp_add_inline_style( 'customizr-style', apply_filters( 'czr_user_options_style' , '' ) );
}

add_action( 'wp_enqueue_scripts'						, 'czr_fn_enqueue_front_scripts' );
function czr_fn_enqueue_front_scripts() {

  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-core' );
  wp_enqueue_script( 'underscore' );
  wp_enqueue_script( 'masonry' );
  wp_enqueue_script( 'jquery-masonry' );
  if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
        wp_enqueue_script( 'comment-reply' );
  $_scripts =  array(
     'vendors/bootstrap.js',
     'vendors/jquery.magnific-popup.js',
     'vendors/jquery.waypoints.js',
     'vendors/flickity.pkgd.js',
     'vendors/modernizr.min.js',
     'vendors/holder.min.js',
     'vendors/requestAnimationFramePolyfill.js',
  //will be concatenated with GRUNT
     'fmk/tc-js-params.js',
     'fmk/smoothScroll.js',
     'jquery-plugins/jqueryimgOriginalSizes.js',
     'jquery-plugins/jqueryCenterImages.js',
     'jquery-plugins/jqueryParallax.js',
     'fmk/_main_base.part.js',
     'fmk/_main_browser_detect.part.js',
     'fmk/_main_dropdowns.part.js',
     'fmk/_main_sticky_header.part.js',
     'fmk/_main_masonry.part.js',
     'fmk/_main_userxp.part.js',
     'fmk/_main_jquery_plugins.part.js',
     'fmk/_main_xfire.part.js',
  );

  $i = 0;
  foreach ( $_scripts  as $_script ){
    $i++;
    wp_enqueue_script(
        $i,
        sprintf( '%1$s%2$s%3$s',CZR_BASE_URL , CZR_ASSETS_PREFIX . 'front/js/', $_script ),
        array(),
        CUSTOMIZR_VER,
        false
    );
  };

  $smooth_scroll_enabled = apply_filters('tc_enable_smoothscroll', ! wp_is_mobile() && 1 == esc_attr( czr_fn_get_opt( 'tc_smoothscroll') ) );
  $smooth_scroll_options = apply_filters('tc_smoothscroll_options', array( 'touchpadSupport' => false ) );

  wp_localize_script( $i,
    'CZRParams' , array(
       '_disabled'          => apply_filters( 'czr_disabled_front_js_parts', array() ),
        'stickyHeader'      => esc_attr( czr_fn_get_opt( 'tc_sticky_header' ) ),
        'centerSliderImg'   => esc_attr( czr_fn_get_opt( 'tc_center_slider_img' ) ),
        'centerAllImg'     => esc_attr( czr_fn_get_opt( 'tc_center_img' ) ),
        'SmoothScroll'      => array( 'Enabled' => $smooth_scroll_enabled, 'Options' => $smooth_scroll_options ),
  ) );

}
foreach ( array('one', 'two', 'three') as $footer_widget_area )
  add_filter( "czr_default_widget_args_footer_{$footer_widget_area}", 'footer_widget_area_defaults' );

function footer_widget_area_defaults( $defaults ){
  return array_merge( $defaults, array(
      'before_title'            => '<h5 class="widget-title">',
      'after_title'             => '</h5>',
    ));
}