<?php
$_options = array(
    'tc_fonts' => '_g_poppins_hind',
    'tc_font_awesome_css' => true,
    'tc_font_awesome_icons' => true,
    'tc_sticky_header' => true,
    'tc_sticky_header_type' => 'push',
    'tc_woocommerce_header_cart_sticky' => false,
    'tc_show_tagline' => true,
    'tc_display_second_menu' => true,
    'tc_menu_style' => 'regular',
    'tc_menu_type' => 'hover',
    'tc_logo_upload' => '611',
    'tc_sticky_logo_upload' => '611',
    'tc_sticky_shrink_title_logo' => true,
    'tc_post_list_grid' => 'masonry', //grid - masonry - alternate

    'tc_show_post_metas_home' => true,
    'tc_show_post_metas_tags' => true,
    'tc_comment_show_info' => true,

    'tc_sidebar_global_layout' => 'l',
    'tc_sidebar_post_layout'   => 'b',
    'tc_front_layout'          => 'f',

    'tc_post_list_thumb_position' => 'right',
    'tc_post_list_thumb_alternate' => true,

    'tc_img_smart_load' => false

);
function czr_fn_get_opt( $_opt_name, $option_group = null, $use_default = true) {
  global $_options;
  return isset($_options[$_opt_name]) ? $_options[$_opt_name] : czr_fn_opt( $_opt_name , $option_group, $use_default ) ;
}

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
        $_path . '/fonts/' . CZR_cl_init::$instance -> czr_fn_maybe_use_min_style( 'font-awesome.css' ),
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

add_filter('body_class', function( $classes ){
  return array_merge( $classes, array( 'header-skin-dark' ) );
});

add_action( 'wp_enqueue_scripts'						, 'czr_fn_enqueue_front_scripts' );
function czr_fn_enqueue_front_scripts(){
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'jquery-ui-core' );
  wp_enqueue_script( 'underscore' );
  wp_enqueue_script( 'masonry' );
  wp_enqueue_script( 'jquery-masonry' );
  $_scripts =  array(
     'vendors/bootstrap.js',
     'vendors/jquery.magnific-popup.js',
     'vendors/jquery.waypoints.js',
     'vendors/flickity.pkgd.js',
  //will be concatenated with GRUNT
     'fmk/tc-js-params.js',
     'fmk/smoothScroll.js',
     'fmk/jqueryimgOriginalSizes.js',
     'fmk/jqueryCenterImages.js',
     'fmk/jqueryParallax.js',
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
  
  wp_localize_script( $i,
    'CZRParams' , array(
       '_disabled'          => apply_filters( 'czr_disabled_front_js_parts', array() ),
        'stickyHeader'      => esc_attr( czr_fn_get_opt( 'tc_sticky_header' ) )
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

add_action('__before_main_container', 'parallax');
function parallax(){
  if ( ! is_home() )
    return;
?>
          <div class="container-fluid section">
             <div class="section-slider parallax-wrapper">
                <div class="parallax filter">
                    <div class="image parallax-item" style="background-image: url('http://new.presscustomizr.com/assets/img/slider/slider_05.jpeg')" >
                      <!--img src="http://new.presscustomizr.com/assets/img/slider/slider_05.jpeg"-->
                    </div>
                    
                    <div class="container">
                        <div class="content">
                            <div class="slider-text">
                                <h2 class="display-1 thick">Customizr</h2>
                                <h3>Image Parallax</h3>
                                 <a href="#" target="_blank" class="btn btn-fill btn-skin">
                                    Download FREE
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
              </div>  
               <div class="section-slider parallax-wrapper">
                <div class="parallax filter">
                  <div class="parallax-item">
                    <div class="image" style="background-image: url('http://new.presscustomizr.com/assets/img/slider/slider_02.jpg')" >
                      <!--img src="http://new.presscustomizr.com/assets/img/slider/slider_05.jpeg"-->
                    </div>
                    
                    <div class="container">
                        <div class="content">
                            <div class="slider-text">
                                <h2 class="display-1 thick">Customizr</h2>
                                <h3>Whole Block Parallax: Image and Text</h3>
                                 <a href="#" target="_blank" class="btn btn-fill btn-skin">
                                    Download FREE
                                </a>
                            </div>
                        </div>
                    </div>
                  </div>  
                </div>
              </div>
              <div class="section-slider parallax-wrapper">
                <div class="parallax filter">
                    <div class="image parallax-item" style="background-image: url('http://new.presscustomizr.com/assets/img/slider/slider_02.jpg')" >
                      <!--img src="http://new.presscustomizr.com/assets/img/slider/slider_05.jpeg"-->
                    </div>
                    
                    <div class="container">
                        <div class="content">
                            <div class="slider-text parallax-item" data-parallax-ratio="1">
                                <h2 class="display-1 thick">Customizr</h2>
                                <h3> Image and Text separated Parallax at different ratios</h3>
                                 <a href="#" target="_blank" class="btn btn-fill btn-skin">
                                    Download FREE
                                </a>
                            </div>
                        </div>
                    </div>
                  </div>  
                </div>
          </div>
<?php
};

add_filter( 'czr_show_media', function( $bool){
  /* Test */
  return $bool 
    || in_array( get_post_format() , apply_filters( 'czr_alternate_media_post_formats', array( 'video', 'audio' ) ) );    
});