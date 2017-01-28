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
      '<%= paths.dev_php %>admin/class-fire-admin_init.php',
      '<%= paths.dev_php %>admin/class-fire-admin_page.php',
      '<%= paths.dev_php %>admin/class-admin-meta_boxes.php'
    ],
    dest: '<%= paths.inc_php %>czr-admin.php',
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
      '<%= paths.front_js %>parts/_main_base.part.js',
      '<%= paths.front_js %>parts/_main_browser_detect.part.js',
      '<%= paths.front_js %>parts/_main_jquery_plugins.part.js',
      '<%= paths.front_js %>parts/_main_slider.part.js',
      '<%= paths.front_js %>parts/_main_userxp.part.js',
      '<%= paths.front_js %>parts/_main_sticky_header.part.js',
      '<%= paths.front_js %>parts/_main_sticky_footer.part.js',
      '<%= paths.front_js %>parts/_main_side_nav.part.js',
      '<%= paths.front_js %>parts/_main_dropdown_placement.part.js',
      '<%= paths.front_js %>parts/_main_xfire.part.js'
    ],
    dest: '<%= paths.front_js %>parts/main.js',
  },
  front_js: {
		src: [
      '<%= paths.front_js %>parts/tc-js-params.js',
      '<%= paths.front_js %>parts/oldBrowserCompat.js',
      '<%= paths.front_js %>parts/bootstrap.js',
      '<%= paths.front_js %>parts/underscore-min.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.front_js_4_source %>jquery-plugins/jqueryParallax.js',
      '<%= paths.front_js %>parts/requestAnimationFramePolyfill.js',
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
    src: [ '<%= paths.czr_assets %>/_dev/js/czr-control-*.js', ' ! <%= paths.czr_assets %>/_dev/js/*.min.js'],
    dest: '<%= paths.czr_assets %>js/czr-control.js'
  },
  czr_preview_js : {
    src: ['<%= paths.czr_assets %>/_dev/js/czr-preview-*.js', '<%= paths.czr_assets %>/_dev/js/*.min.js'],
    dest: '<%= paths.czr_assets %>js/czr-preview.js'
  },
};
