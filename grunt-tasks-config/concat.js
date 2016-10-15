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
      '<%= paths.dev_php %>class-fire-utils.php',
      '<%= paths.dev_php %>class-fire-resources.php',
      '<%= paths.dev_php %>class-fire-widgets.php',
      '<%= paths.dev_php %>class-fire-placeholders.php',
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
      '<%= paths.front_js %>jquery-plugins/jqueryimgOriginalSizes.js',
      '<%= paths.front_js %>jquery-plugins/jqueryaddDropCap.js',
      '<%= paths.front_js %>jquery-plugins/jqueryimgSmartLoad.js',
      '<%= paths.front_js %>jquery-plugins/jqueryextLinks.js',
      '<%= paths.front_js %>jquery-plugins/jqueryCenterImages.js',
      '<%= paths.front_js %>jquery-plugins/jqueryParallax.js',
      '<%= paths.front_js %>parts/smoothScroll.js',
      '<%= paths.front_js %>parts/outline.js',
      '<%= paths.front_js %>parts/waypoints.js',
      '<%= paths.front_js %>parts/main.js'
    ],
		dest: '<%= paths.front_js %>tc-scripts.js',
	},
	admin_control_js:{
		src: [
      '<%= paths.front_js %>parts/oldBrowserCompat.js',
      '<%= paths.admin_js %>lib/icheck.min.js',
      '<%= paths.admin_js %>lib/selecter.min.js',
      '<%= paths.admin_js %>lib/stepper.min.js',
      '<%= paths.admin_js %>lib/select2.min.js',
      '<%= paths.admin_js %>parts/_control.js',
      '<%= paths.admin_js %>parts/_call_to_actions.js' ,
      '<%= paths.admin_js %>parts/_various_dom_ready.js'
    ],
		dest: '<%= paths.admin_js %>theme-customizer-control.js',
	}
};
