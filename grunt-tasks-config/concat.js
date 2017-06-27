module.exports = {
	options: {
		separator: '',
	},
  init_php : {
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
      '<%= paths.dev_php_classic %>class-fire-placeholders.php',
      '<%= paths.dev_php_classic %>class-fire-prevdem.php',
      '<%= paths.dev_php_classic %>z_classical_specific_functions_and_instantiate_czr_for_classical.php'
    ],
    dest: '<%= paths.inc_php_classic %>czr-init.php',
  },
  front_php : {
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
    dest: '<%= paths.inc_php_classic %>czr-front.php',
  },
  admin_php : {
    src: [
      '<%= paths.back_php %>class-fire-admin_init.php',
      '<%= paths.back_php %>class-fire-admin_page.php',
      '<%= paths.back_php %>class-admin-meta_boxes.php'
    ],
    dest: '<%= paths.core_php %>czr-admin.php',
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
      '<%= paths.core_php %>_czr/class-czr-init.php',
      '<%= paths.core_php %>_czr/class-czr-resources.php',
      //parts
      '<%= paths.core_php %>_czr/controls/class-base-control.php',
      '<%= paths.core_php %>_czr/controls/class-cropped-image-control.php',
      '<%= paths.core_php %>_czr/controls/class-multipicker-control.php',
      '<%= paths.core_php %>_czr/controls/class-modules-control.php',
      '<%= paths.core_php %>_czr/controls/class-upload-control.php',

      '<%= paths.core_php %>_czr/panels/class-panels.php',
      '<%= paths.core_php %>_czr/settings/class-settings.php',
      '<%= paths.core_php %>_czr/sections/class-pro-section.php',

      //modules data
      '<%= paths.core_php %>_czr/modules/modules-data.php',

      //templates
      '<%= paths.core_php %>_czr/tmpl/modules/all-modules-tmpl.php',
      '<%= paths.core_php %>_czr/tmpl/modules/social-module-tmpl.php',
      '<%= paths.core_php %>_czr/tmpl/inputs/img-uploader-tmpl.php',
    ],
    dest: '<%= paths.core_php %>czr-customize.php',
  },
	front_main_parts_js : {
    src: [
      '<%= paths.front_js_classic %>parts/_main_base_0_utilities.part.js',
      '<%= paths.front_js_classic %>parts/_main_base_1_fmk.part.js',
      '<%= paths.front_js_classic %>parts/_main_base_2_initialize.part.js',
      '<%= paths.front_js_classic %>parts/_main_browser_detect.part.js',
      '<%= paths.front_js_classic %>parts/_main_jquery_plugins.part.js',
      '<%= paths.front_js_classic %>parts/_main_slider.part.js',
      '<%= paths.front_js_classic %>parts/_main_masonry.part.js',
      '<%= paths.front_js_classic %>parts/_main_userxp.part.js',
      '<%= paths.front_js_classic %>parts/_main_sticky_header.part.js',
      '<%= paths.front_js_classic %>parts/_main_sticky_footer.part.js',
      '<%= paths.front_js_classic %>parts/_main_side_nav.part.js',
      '<%= paths.front_js_classic %>parts/_main_dropdown_placement.part.js',
      '<%= paths.front_js_classic %>parts/_main_xfire.part.js',
      '<%= paths.front_js_classic %>parts/_various_former_hardcoded.part.js'
    ],
    dest: '<%= paths.front_js_classic %>parts/main.js',
  },
  front_js: {
		src: [
      '<%= paths.front_js_classic %>parts/tc-js-params.js',
      '<%= paths.front_js_classic %>parts/oldBrowserCompat.js',
      '<%= paths.front_js_classic %>parts/bootstrap.js',
      '<%= paths.front_js_classic %>parts/underscore-min.js',

      '<%= paths.theme_js_assets %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryParallax.js',

      '<%= paths.front_js_classic %>parts/requestAnimationFramePolyfill.js',
      '<%= paths.front_js_classic %>parts/matchMediaPolyfill.js',
      '<%= paths.front_js_classic %>parts/smoothscroll.js',
      '<%= paths.front_js_classic %>parts/outline.js',
      '<%= paths.front_js_classic %>parts/waypoints.js',
      '<%= paths.front_js_classic %>parts/main.js'
    ],
		dest: '<%= paths.front_js_classic %>tc-scripts.js',
	},
  czr_css : {
    src: [ '<%= paths.czr_assets %>/_dev/css/czr-control-*.css', '! <%= paths.czr_assets %>/_dev/css/*.min.css', ],
    dest: '<%= paths.czr_assets %>css/czr-control.css'
  },
  czr_control_js : {
    src: [
      '<%= paths.czr_assets %>/_dev/js/czr-control-base.js',
      '<%= paths.czr_assets %>/_dev/js/czr-control-deps.js',
      '<%= paths.czr_assets %>/_dev/js/czr-control-dom_ready.js'
    ],
    dest: '<%= paths.czr_assets %>js/czr-control.js'
  },
  czr_control_js_c4 : {
    src: [
      '<%= paths.czr_assets %>/_dev/js/czr-control-base.js',
      '<%= paths.czr_assets %>/_dev/js/czr-control-deps-modern.js',
      '<%= paths.czr_assets %>/_dev/js/czr-control-dom_ready-modern.js'
    ],
    dest: '<%= paths.czr_assets %>js/czr-control-modern.js'
  },
  czr_preview_js : {
    src: [
      '<%= paths.czr_assets %>/_dev/js/czr-preview-base.js',
      '<%= paths.czr_assets %>/_dev/js/czr-preview-post_message.js',
    ],
    dest: '<%= paths.czr_assets %>js/czr-preview.js'
  },
  czr_preview_js_c4 : {
    src: [
      '<%= paths.czr_assets %>/_dev/js/czr-preview-base.js',
      '<%= paths.czr_assets %>/_dev/js/czr-preview-post_message-modern.js',
    ],
    dest: '<%= paths.czr_assets %>js/czr-preview-modern.js'
  },
  //C4
  fmk_php_c4 : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.core_php %>/_framework/class-model.php',
      '<%= paths.core_php %>/_framework/class-collection.php',
      '<%= paths.core_php %>/_framework/class-view.php',
      '<%= paths.core_php %>/_framework/class-controllers.php',
    ],
    dest: '<%= paths.core_php %>fmk.php',
  },
  utils_php_c4 : {
    options: {
      process: function(src, filepath) {
        //removes trailing newlines ( and spaces ) at the begin of a file
        src = src.replace(/^\s+[\n\r]+(<\?php)/gm,"$1");
        //removes >1 ending newlines ( and spaces ) after php closing tag
        return src.replace(/\?>([\n\r])+\s+$/gm,"?>$1");
      },
    },
    src: [
      '<%= paths.core_php %>/_utils/fn-0-base.php',
      '<%= paths.core_php %>/_utils/fn-1-utils.php',
      '<%= paths.core_php %>/_utils/fn-2-query.php',
      '<%= paths.core_php %>/_utils/fn-3-thumbnails.php',
      '<%= paths.core_php %>/_utils/fn-4-colors.php'
    ],
    dest: '<%= paths.core_php %>functions.php',
  },
  front_main_fmk_js4 : {
    src: [
      '<%= paths.theme_js_assets %>fmk/_main_base_0_utilities.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_base_1_fmk.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_base_2_initialize.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_browser_detect.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_jquery_plugins.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_slider.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_userxp.part.js',
      //'<%= paths.theme_js_assets %>fmk/_main_sticky_header.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_sticky_footer.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_masonry.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_side_nav.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_dropdowns.part.js',
      '<%= paths.theme_js_assets %>fmk/_main_xfire.part.js',
      '<%= paths.theme_js_assets %>fmk/_various_former_hardcoded.part.js'
    ],
    dest: '<%= paths.theme_js_assets %>fmk/main.js',
  },
  front_js4: {
    src: [
      '<%= paths.theme_js_assets %>fmk/tc-js-params.js',
      '<%= paths.theme_js_assets %>fmk/oldBrowserCompat.js',
      '<%= paths.theme_js_assets %>vendors/custom-bootstrap.js',
      '<%= paths.theme_js_assets %>vendors/underscore-min.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryParallax.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryAnimateSvg.js',
      '<%= paths.theme_js_assets %>fmk/requestAnimationFramePolyfill.js',
      '<%= paths.theme_js_assets %>fmk/matchMediaPolyfill.js',
      '<%= paths.theme_js_assets %>jquery-plugins/jqueryFittext.js',
      '<%= paths.theme_js_assets %>fmk/smoothscroll.js',
      '<%= paths.theme_js_assets %>fmk/outline.js',
      '<%= paths.theme_js_assets %>vendors/waypoints.js',
      '<%= paths.theme_js_assets %>vendors/vivus.min.js',
      //maybe move following two outside and enqueue only when needed
      '<%= paths.theme_js_assets %>vendors/flickity-pkgd.js',
      '<%= paths.theme_js_assets %>vendors/jquery-mCustomScrollbar.js',
      '<%= paths.theme_js_assets %>fmk/main.js'
    ],
    dest: '<%= paths.theme_js_assets %>tc-scripts.js',
  }
};