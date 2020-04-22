module.exports = {
	options: {
		separator: '',
	},

  //PHP CLASSICAL STYLE
  init_php_classic : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.dev_php_classic %>a_init-load-and-instantiate.php',
      '<%= paths.dev_php_classic %>class-fire-init.php',
      '<%= paths.dev_php_classic %>class-fire-plugins_compat.php',
      '<%= paths.dev_php_classic %>class-fire-utils_settings_map.php',
      '<%= paths.dev_php_classic %>class-fire-init_retro_compat.php',
      '<%= paths.dev_php_classic %>class-fire-utils.php',
      '<%= paths.dev_php_classic %>class-fire-resources.php',
      '<%= paths.dev_php_classic %>class-fire-widgets.php',
      '<%= paths.dev_php_classic %>z_classical_specific_functions_and_instantiate_czr_for_classical.php'
    ],
    dest: '<%= paths.inc_php_classic %>czr-init-ccat.php',
  },
  front_php_classic : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.dev_php_classic %>parts/class-header-header_main.php',
      '<%= paths.dev_php_classic %>parts/class-header-menu.php',
      '<%= paths.dev_php_classic %>parts/class-header-nav_walker.php',
      '<%= paths.dev_php_classic %>parts/class-content-404.php',
      '<%= paths.dev_php_classic %>parts/class-content-attachment.php',
      '<%= paths.dev_php_classic %>parts/class-content-breadcrumb.php',
      '<%= paths.dev_php_classic %>parts/class-content-comments.php',
      '<%= paths.dev_php_classic %>parts/class-content-featured_pages.php',
      '<%= paths.dev_php_classic %>parts/class-content-gallery.php',
      '<%= paths.dev_php_classic %>parts/class-content-headings.php',
      '<%= paths.dev_php_classic %>parts/class-content-no_results.php',
      '<%= paths.dev_php_classic %>parts/class-content-page.php',
      '<%= paths.dev_php_classic %>parts/class-content-post.php',
      '<%= paths.dev_php_classic %>parts/class-content-post_list.php',
      '<%= paths.dev_php_classic %>parts/class-content-post_list_grid.php',
      '<%= paths.dev_php_classic %>parts/class-content-post_metas.php',
      '<%= paths.dev_php_classic %>parts/class-content-post_navigation.php',
      '<%= paths.dev_php_classic %>parts/class-content-post_thumbnails.php',
      '<%= paths.dev_php_classic %>parts/class-content-sidebar.php',
      '<%= paths.dev_php_classic %>parts/class-content-slider.php',
      '<%= paths.dev_php_classic %>parts/class-footer-footer_main.php'
    ],
    dest: '<%= paths.inc_php_classic %>czr-front-ccat.php',
  },


  //PHP MODERN STYLE
  //Modern style
  fmk_php_modern : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.core_php_dev %>_framework/class-model.php',
      '<%= paths.core_php_dev %>_framework/class-collection.php',
      '<%= paths.core_php_dev %>_framework/class-view.php',
      '<%= paths.core_php_dev %>_framework/class-controllers.php',
    ],
    dest: '<%= paths.core_php %>fmk-ccat.php',
  },
  utils_php_modern : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.core_php_dev %>_utils/fn-0-base.php',
      '<%= paths.core_php_dev %>_utils/fn-1-utils.php',
      '<%= paths.core_php_dev %>_utils/fn-2-query.php',
      '<%= paths.core_php_dev %>_utils/fn-3-thumbnails.php',
      '<%= paths.core_php_dev %>_utils/fn-4-colors.php'
    ],
    dest: '<%= paths.core_php %>functions-ccat.php',
  },





  //FRONT CSS MODERN STYLE
  front_css_modern : {
    src: [
      '<%= paths.front_css_modern %>_dev/custom-bs/custom-bootstrap.css',
      '<%= paths.front_css_modern %>_dev/style-without-bootstrap.css'
    ],
    dest: '<%= paths.front_css_modern %>style.css',
  },


  //FRONT CSS RTL MODERN STYLE
  front_rtl_css_modern : {
    src: [
      '<%= paths.front_css_modern %>_dev/custom-bs/custom-bootstrap.css',
      '<%= paths.front_css_modern %>_dev/style-rtl-without-bootstrap.css'
    ],
    dest: '<%= paths.front_css_modern %>rtl.css',
  },






  //FRONT JS CLASSICAL STYLE
	front_main_parts_js_classic : {
    src: [
      '<%= paths.theme_js_assets %>_front_js_fmk/_main_base_0_utilities.part.js',
      '<%= paths.theme_js_assets %>_front_js_fmk/_main_base_1_fmk.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_base_2_initialize.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_browser_detect.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_jquery_plugins.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_slider.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_userxp.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_userxp_2_front_notifications.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_sticky_header.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_sticky_footer.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_side_nav.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_main_dropdown_placement.part.js',

      '<%= paths.theme_js_assets %>_front_js_fmk/_main_xfire_0.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_classical/_various_former_hardcoded.part.js',

      //fire the map
      '<%= paths.theme_js_assets %>_parts/_parts_classical/_z_main_xfire_classical_theme_specific.part.js'
    ],
    dest: '<%= paths.front_js_classic %>main-ccat.js',
  },
  front_js_classic: {
		src: [
      '<%= paths.front_js_classic %>tc-js-params.js',

      '<%= paths.theme_js_assets %>libs/oldBrowserCompat.js',
      '<%= paths.theme_js_assets %>libs/bootstrap-classical.js',
      // '<%= paths.theme_js_assets %>libs/underscore-min.js',

      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryextLinks.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryCenterImages.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryParallax.js',

      '<%= paths.theme_js_assets %>libs/requestAnimationFramePolyfill.js',
      '<%= paths.theme_js_assets %>libs/matchMediaPolyfill.js',
      '<%= paths.theme_js_assets %>libs/smoothscroll.js',
      '<%= paths.theme_js_assets %>libs/outline.js',
      '<%= paths.theme_js_assets %>libs/waypoints.js',

      '<%= paths.front_js_classic %>main-ccat.js'
    ],
		dest: '<%= paths.front_js_classic %>tc-scripts.js',
	},




  //FRONT JS MODERN STYLE
  front_main_fmk_js_modern : {
    src: [
      '<%= paths.theme_js_assets %>_front_js_fmk/_main_base_0_utilities.part.js',
      '<%= paths.theme_js_assets %>_front_js_fmk/_main_base_1_fmk.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_base_2_initialize.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_browser_detect.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_jquery_plugins.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_slider.part.js',

      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_userxp_0_utils.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_userxp_1_stickify_header.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_userxp_2_front_notifications.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_userxp_9_various.part.js',

      //'<%= paths.theme_js_assets %>fmk/_main_sticky_header.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_sticky_footer.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_side_nav.part.js',
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_main_dropdowns.part.js',

      '<%= paths.theme_js_assets %>_front_js_fmk/_main_xfire_0.part.js',

      //fire the map
      '<%= paths.theme_js_assets %>_parts/_parts_modern/_z_main_xfire_modern_theme_specific.part.js'
    ],
    dest: '<%= paths.theme_js_assets %>main-ccat.js',
  },
  front_js_modern: {
    src: [
      '<%= paths.theme_js_assets %>tc-js-params.js',

      '<%= paths.theme_js_assets %>libs/oldBrowserCompat.js',

      '<%= paths.theme_js_assets %>libs/custom-bootstrap-modern.js',
      // '<%= paths.theme_js_assets %>libs/underscore-min.js',

      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryextLinks.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryCenterImages.js',
      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryParallax.js',
      // '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryAnimateSvg.js', //<= FEB 2020 => NOT CONCATENATED ANYMORE for performance considerations

      '<%= paths.theme_js_assets %>libs/requestAnimationFramePolyfill.js',
      '<%= paths.theme_js_assets %>libs/matchMediaPolyfill.js',

      '<%= paths.theme_js_assets %>libs/jquery-plugins/jqueryFittext.js',

      '<%= paths.theme_js_assets %>libs/smoothscroll.js',
      '<%= paths.theme_js_assets %>libs/outline.js',
      '<%= paths.theme_js_assets %>libs/waypoints.js',
      // '<%= paths.theme_js_assets %>libs/vivus.min.js',//<= FEB 2020 => NOT CONCATENATED ANYMORE for performance considerations
      //maybe move following two outside and enqueue only when needed
      //'<%= paths.theme_js_assets %>libs/flickity-pkgd.js',
      //'<%= paths.theme_js_assets %>libs/jquery-mCustomScrollbar.js',
      '<%= paths.theme_js_assets %>main-ccat.js'
    ],
    dest: '<%= paths.theme_js_assets %>tc-scripts.js',
  },
















  //CUSTOMIZER CSS AND JS COMMON TO CLASSICAL AND MODERN
  // czr_css : {
  //   src: [ '<%= paths.czr_assets %>/_dev/css/czr-control-*.css', '! <%= paths.czr_assets %>/_dev/css/*.min.css', ],
  //   dest: '<%= paths.czr_assets %>css/czr-control.css'
  // },
  // czr_control_js : {
  //   src: [
  //     '<%= paths.czr_assets %>/_dev/js/_0_czr-base-fmk.js',
  //     '<%= paths.czr_assets %>/_dev/js/_1_czr-theme-fmk',
  //     '<%= paths.czr_assets %>/_dev/js/czr-control-deps.js',
  //     '<%= paths.czr_assets %>/_dev/js/czr-control-dom_ready.js'
  //   ],
  //   dest: '<%= paths.czr_assets %>js/czr-control.js'
  // },
  // czr_control_js_modern : {
  //   src: [
  //     '<%= paths.czr_assets %>/_dev/js/_0_czr-base-fmk.js',
  //     '<%= paths.czr_assets %>/_dev/js/_1_czr-theme-fmk',
  //     '<%= paths.czr_assets %>/_dev/js/czr-control-deps-modern.js',
  //     '<%= paths.czr_assets %>/_dev/js/czr-control-dom_ready.js'
  //   ],
  //   dest: '<%= paths.czr_assets %>js/czr-control-modern.js'
  // },
  // czr_preview_js : {
  //   src: [
  //     '<%= paths.czr_assets %>/_dev/js/czr-preview-base.js',
  //     '<%= paths.czr_assets %>/_dev/js/czr-preview-post_message.js',
  //   ],
  //   dest: '<%= paths.czr_assets %>js/czr-preview.js'
  // },
  // czr_preview_js_modern : {
  //   src: [
  //     '<%= paths.czr_assets %>/_dev/js/czr-preview-base.js',
  //     '<%= paths.czr_assets %>/_dev/js/czr-preview-post_message-modern.js',
  //   ],
  //   dest: '<%= paths.czr_assets %>js/czr-preview-modern.js'
  // },







  //COMMON TO CLASSIC AND MODERN
  admin_php : {
    src: [
      '<%= paths.core_php_dev %>_admin/class-fire-admin_init.php',
      '<%= paths.core_php_dev %>_admin/class-fire-admin_page.php',
      '<%= paths.core_php_dev %>_admin/class-admin-meta_boxes.php'
    ],
    dest: '<%= paths.core_php %>czr-admin-ccat.php',
  },
  customize_php : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.core_php_dev %>_czr/class-czr-init.php',
      '<%= paths.core_php_dev %>_czr/class-czr-resources.php',
      //parts
      '<%= paths.core_php_dev %>_czr/controls/class-base-control.php',
      '<%= paths.core_php_dev %>_czr/controls/class-cropped-image-control.php',
      '<%= paths.core_php_dev %>_czr/controls/class-code-editor-control.php',
      '<%= paths.core_php_dev %>_czr/controls/class-multipicker-control.php',
      //'<%= paths.core_php_dev %>_czr/controls/class-modules-control.php',
      '<%= paths.core_php_dev %>_czr/controls/class-upload-control.php',

      '<%= paths.core_php_dev %>_czr/panels/class-panels.php',
      '<%= paths.core_php_dev %>_czr/sections/class-sections.php',
      '<%= paths.core_php_dev %>_czr/sections/class-pro-section.php',
    ],
    dest: '<%= paths.core_php %>czr-customize-ccat.php',
  },
};