module.exports = {
	options: {
		separator: '',
	},
  init_php : {
    src: [
      '<%= paths.dev_php %>init.php',
      '<%= paths.dev_php %>init-pro.php',
      '<%= paths.dev_php %>class-fire-init.php',
      '<%= paths.dev_php %>class-fire-plugins_compat.php',
      '<%= paths.dev_php %>class-fire-utils_settings_map.php',
      '<%= paths.dev_php %>class-fire-init_retro_compat.php',
      '<%= paths.dev_php %>class-fire-utils.php',
      '<%= paths.dev_php %>class-fire-resources.php',
      '<%= paths.dev_php %>class-fire-widgets.php',
      '<%= paths.dev_php %>class-fire-placeholders.php',
      '<%= paths.dev_php %>class-fire-prevdem.php',
      '<%= paths.dev_php %>z_fire.php'
    ],
    dest: '<%= paths.inc_php %>czr-init.php',
  },
  front_php : {
    src: [
      '<%= paths.dev_php %>parts/class-header-header_main.php',
      '<%= paths.dev_php %>parts/class-header-menu.php',
      '<%= paths.dev_php %>parts/class-header-nav_walker.php',
      '<%= paths.dev_php %>parts/class-content-404.php',
      '<%= paths.dev_php %>parts/class-content-attachment.php',
      '<%= paths.dev_php %>parts/class-content-breadcrumb.php',
      '<%= paths.dev_php %>parts/class-content-comments.php',
      '<%= paths.dev_php %>parts/class-content-featured_pages.php',
      '<%= paths.dev_php %>parts/class-content-gallery.php',
      '<%= paths.dev_php %>parts/class-content-headings.php',
      '<%= paths.dev_php %>parts/class-content-no_results.php',
      '<%= paths.dev_php %>parts/class-content-page.php',
      '<%= paths.dev_php %>parts/class-content-post.php',
      '<%= paths.dev_php %>parts/class-content-post_list.php',
      '<%= paths.dev_php %>parts/class-content-post_list_grid.php',
      '<%= paths.dev_php %>parts/class-content-post_metas.php',
      '<%= paths.dev_php %>parts/class-content-post_navigation.php',
      '<%= paths.dev_php %>parts/class-content-post_thumbnails.php',
      '<%= paths.dev_php %>parts/class-content-sidebar.php',
      '<%= paths.dev_php %>parts/class-content-slider.php',
      '<%= paths.dev_php %>parts/class-footer-footer_main.php'
    ],
    dest: '<%= paths.inc_php %>czr-front.php',
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
    src: [
      '<%= paths.dev_php %>czr/class-czr-init.php',
      '<%= paths.dev_php %>czr/class-czr-resources.php',
      //parts
      '<%= paths.dev_php %>czr/controls/class-base-control.php',
      '<%= paths.dev_php %>czr/controls/class-cropped-image-control.php',
      '<%= paths.dev_php %>czr/controls/class-multipicker-control.php',
      '<%= paths.dev_php %>czr/controls/class-modules-control.php',
      '<%= paths.dev_php %>czr/controls/class-upload-control.php',

      '<%= paths.dev_php %>czr/panels/class-panels.php',
      '<%= paths.dev_php %>czr/settings/class-settings.php',
      '<%= paths.dev_php %>czr/sections/class-pro-section.php',

      //modules data
      '<%= paths.dev_php %>czr/modules/modules-data.php',

      //templates
      '<%= paths.dev_php %>czr/tmpl/modules/all-modules-tmpl.php',
      '<%= paths.dev_php %>czr/tmpl/modules/social-module-tmpl.php',
      '<%= paths.dev_php %>czr/tmpl/inputs/img-uploader-tmpl.php',
    ],
    dest: '<%= paths.inc_php %>czr-customize.php',
  },
	front_main_parts_js : {
    src: [
      '<%= paths.front_js %>parts/_main_base_0_utilities.part.js',
      '<%= paths.front_js %>parts/_main_base_1_fmk.part.js',
      '<%= paths.front_js %>parts/_main_base_2_initialize.part.js',
      '<%= paths.front_js %>parts/_main_browser_detect.part.js',
      '<%= paths.front_js %>parts/_main_jquery_plugins.part.js',
      '<%= paths.front_js %>parts/_main_slider.part.js',
      '<%= paths.front_js %>parts/_main_userxp.part.js',
      '<%= paths.front_js %>parts/_main_sticky_header.part.js',
      '<%= paths.front_js %>parts/_main_sticky_footer.part.js',
      '<%= paths.front_js %>parts/_main_side_nav.part.js',
      '<%= paths.front_js %>parts/_main_dropdown_placement.part.js',
      '<%= paths.front_js %>parts/_main_xfire.part.js',
      '<%= paths.front_js %>parts/_various_former_hardcoded.part.js'
    ],
    dest: '<%= paths.front_js %>parts/main.js',
  },
  front_js: {
		src: [
      '<%= paths.front_js %>parts/tc-js-params.js',
      '<%= paths.front_js %>parts/oldBrowserCompat.js',
      '<%= paths.front_js %>parts/bootstrap.js',
      '<%= paths.front_js %>parts/underscore-min.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryParallax.js',
      '<%= paths.front_js %>parts/requestAnimationFramePolyfill.js',
      '<%= paths.front_js %>parts/matchMediaPolyfill.js',
      '<%= paths.front_js %>parts/smoothScroll.js',
      '<%= paths.front_js %>parts/outline.js',
      '<%= paths.front_js %>parts/waypoints.js',
      '<%= paths.front_js %>parts/main.js'
    ],
		dest: '<%= paths.front_js %>tc-scripts.js',
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
  czr_preview_js : {
    src: [
      '<%= paths.czr_assets %>/_dev/js/czr-preview-base.js',
      '<%= paths.czr_assets %>/_dev/js/czr-preview-post_message.js',
    ],
    dest: '<%= paths.czr_assets %>js/czr-preview.js'
  },
  //C4
  fmk_php_c4 : {
    src: [
      '<%= paths.core_php_4 %>/_framework/class-model.php',
      '<%= paths.core_php_4 %>/_framework/class-collection.php',
      '<%= paths.core_php_4 %>/_framework/class-view.php',
      '<%= paths.core_php_4 %>/_framework/class-controllers.php',
    ],
    dest: '<%= paths.core_php_4 %>fmk.php',
  },
  utils_php_c4 : {
    src: [
      '<%= paths.core_php_4 %>/_utils/fn-0-base.php',
      '<%= paths.core_php_4 %>/_utils/fn-1-settings_map.php',
      '<%= paths.core_php_4 %>/_utils/fn-2-utils.php',
      '<%= paths.core_php_4 %>/_utils/fn-3-options.php',
      '<%= paths.core_php_4 %>/_utils/fn-4-query.php',
      '<%= paths.core_php_4 %>/_utils/fn-5-thumbnails.php',
      '<%= paths.core_php_4 %>/_utils/fn-6-colors.php'
    ],
    dest: '<%= paths.core_php_4 %>functions.php',
  },
  front_main_fmk_js4 : {
    src: [
      '<%= paths.front_js_4 %>fmk/_main_base_0_utilities.part.js',
      '<%= paths.front_js_4 %>fmk/_main_base_1_fmk.part.js',
      '<%= paths.front_js_4 %>fmk/_main_base_2_initialize.part.js',
      '<%= paths.front_js_4 %>fmk/_main_browser_detect.part.js',
      '<%= paths.front_js_4 %>fmk/_main_jquery_plugins.part.js',
      '<%= paths.front_js_4 %>fmk/_main_slider.part.js',
      '<%= paths.front_js_4 %>fmk/_main_userxp.part.js',
      //'<%= paths.front_js_4 %>fmk/_main_sticky_header.part.js',
      '<%= paths.front_js_4 %>fmk/_main_sticky_footer.part.js',
      '<%= paths.front_js_4 %>fmk/_main_masonry.part.js',
      '<%= paths.front_js_4 %>fmk/_main_side_nav.part.js',
      '<%= paths.front_js_4 %>fmk/_main_dropdowns.part.js',
      '<%= paths.front_js_4 %>fmk/_main_xfire.part.js',
      '<%= paths.front_js_4 %>fmk/_various_former_hardcoded.part.js'
    ],
    dest: '<%= paths.front_js_4 %>fmk/main.js',
  },
  front_js4: {
    src: [
      '<%= paths.front_js_4 %>fmk/tc-js-params.js',
      '<%= paths.front_js_4 %>fmk/oldBrowserCompat.js',
      '<%= paths.front_js_4 %>vendors/custom-bootstrap.min.js',
      '<%= paths.front_js_4 %>vendors/underscore-min.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryParallax.js',
      '<%= paths.front_js_4 %>jquery-plugins/jqueryAnimateSvg.js',
      '<%= paths.front_js_4 %>fmk/requestAnimationFramePolyfill.js',
      '<%= paths.front_js_4 %>fmk/matchMediaPolyfill.js',
      '<%= paths.front_js_4 %>fmk/smoothScroll.js',
      '<%= paths.front_js_4 %>fmk/outline.js',
      '<%= paths.front_js_4 %>vendors/waypoints.js',
      '<%= paths.front_js_4 %>vendors/vivus.min.js',
      //maybe move following two outside and enqueue only when needed
      '<%= paths.front_js_4 %>vendors/flickity-pkgd.js',
      '<%= paths.front_js_4 %>vendors/jquery-mCustomScrollbar.js',
      '<%= paths.front_js_4 %>fmk/main.js'
    ],
    dest: '<%= paths.front_js_4 %>tc-scripts.js',
  }
};
