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
  //
  //customize_php : {
  //  src: [
  //    '<%= paths.dev_php %>admin/class-admin-customize.php'
  //  ],
  //  dest: '<%= paths.inc_php %>czr-customize.php',
  //},
  //
  //NEW
  customize_php : {
    src: [
      '<%= paths.dev_php %>czr/class-czr-init.php',
      '<%= paths.dev_php %>czr/class-czr-resources.php',
      //parts
      '<%= paths.dev_php %>czr/controls/class-base-control.php',
      '<%= paths.dev_php %>czr/controls/class-cropped-image-control.php',
      '<%= paths.dev_php %>czr/controls/class-multipicker-control.php',
      '<%= paths.dev_php %>czr/controls/class-modules-control.php',

//      '<%= paths.dev_php %>czr/controls/class-upload-control.php',

      '<%= paths.dev_php %>czr/settings/class-settings.php',
      //modules data
      '<%= paths.dev_php %>czr/modules/modules-data.php',
      '<%= paths.dev_php %>czr/modules/modules-resources.php',

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
	},
//CZR
  //
  //------------------------- CUSTOMIZER PANE CSS
  //
  czr_control_css:{
    src:[
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-common.css',
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-modules.css',
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-footer.css',
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-input-types.css',
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-sektion.css',
      '<%= paths.czr_assets %>fmk/css/parts/czr-control-skope.css'
    ],
    dest : '<%= paths.czr_assets %>fmk/css/czr-control.css',
  },
  //
  //------------------------- CUSTOMIZER PANE JS
  //
  czr_core_control_js:{
    src: [
      '<%= paths.global_js %>oldBrowserCompat.min.js',
      '<%= paths.czr_assets %>fmk/js/lib/icheck.min.js',
      '<%= paths.czr_assets %>fmk/js/lib/selecter.min.js',
      '<%= paths.czr_assets %>fmk/js/lib/stepper.min.js',
      '<%= paths.czr_assets %>fmk/js/lib/select2.min.js',
      '<%= paths.czr_assets %>fmk/js/lib/rangeslider.min.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/_0_pre_base.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_100_skope_base_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_101_skope_base_server_notification.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_102_skope_base_top_notification.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_103_skope_base_bind_api_settings.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_104_skope_base_react_on_skopes_sync.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_105_skope_base_section_panel_react.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_106_skope_base_paint_wash.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_111_skope_base_helpers_utilities.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_112_skope_base_helpers_priority_inheritance.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_20_skope_base_current_skopes_collection.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_21_skope_base_active_skope_react.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_300_skope_base_silent_update.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_301_skope_base_special_silent_updates.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_400_skope_base_control_setup.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_401_skope_base_control_reset.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_402_skope_base_control_skope_notice.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_501_skope_save_initialize.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_502_skope_save_submit_promise.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_503_skope_save_recursive_calls.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_504_skope_save_postprocessing.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_600_skope_reset.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_700_skope_widget_sidebar_specifics.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_91_skope_model_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_92_skope_model_view.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_1_skope/_0_0_0_pre_93_skope_model_reset.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_900_Value_prototype.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_901_query.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_902_save.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_904_synchronizer.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_905_refresh.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_906_dirtyValues.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_907_requestChangesetUpdate.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_908_Setting_prototype.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_2_api_override/_0_0_0_pre_990_various_overrides.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_3_api_helpers/_0_0_0_pre_97_api_helpers_various.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/0_3_api_helpers/_0_0_0_pre_98_api_helpers_dom.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/0_4_preview_listeners/_0_0_0_pre_99_preview_listeners.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_1_img_upload.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_2_colorpicker.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_3_selecter.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_4_content_picker.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_0_input/_0_0_1_input_5_text_editor.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_1_item_and_modopt/1_1_0_item/_0_0_2_item_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_1_item_and_modopt/1_1_0_item/_0_0_2_item_2_model.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_1_item_and_modopt/1_1_0_item/_0_0_2_item_3_view.js',


      '<%= paths.czr_assets %>fmk/js/control_dev/1_1_item_and_modopt/1_1_1_module_options/_0_0_2_mod_opt_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_1_item_and_modopt/1_1_1_module_options/_0_0_2_mod_opt_2_view.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_3_module_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_3_module_1_collection.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_3_module_2_model.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_3_module_3_view.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_4_dyn_module_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_3_module/_0_0_4_dyn_module_1_pre_item_views.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/free/_2_7_socials_module.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/free/_2_6_widget_areas_module.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/free/_3_2_body_background_module.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/5_0_0_free_modules_map.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/_0_1_0_base_control.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/base_module_control/_0_1_0_base_module_control_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/base_module_control/_0_1_1_base_module_control_instantiate.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/base_module_control/_0_1_2_base_module_control_collection.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/multi_module_control/_0_2_0_multi_module_control_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/1_4_control/multi_module_control/_0_2_1_multi_module_control_mod_extender.js',


      '<%= paths.czr_assets %>fmk/js/control_dev/6_0_control_list/_2_1_multiplepicker_control.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/6_0_control_list/_2_2_cropped_image_control.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/6_0_control_list/_2_3_upload_control.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/6_0_control_list/_2_4_layout_control.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/_5_extend_api.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/_6_control_dependencies.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/_7_various_dom_ready.js'
    ],
    dest: '<%= paths.czr_assets %>fmk/js/czr-control.js',
  },
  czr_pro_modules_control_js:{
    src: [
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/_2_9_fps_module.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/_3_0_text_module.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/_3_1_slider_module.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/_3_15_text_editor_module.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_0_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_1_sektion_item_extend.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_2_sektion_column.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_3_dragula.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_4_modules_panel.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_5_column_class_init.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_6_column_class_collection.js',
      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/pro/sektion/_2_8_sektions_module_7_settings_panel.js',

      '<%= paths.czr_assets %>fmk/js/control_dev/5_0_module_list/5_0_1_pro_modules_map.js',
    ],
    dest: '<%= paths.czr_assets %>fmk/js/czr-pro-modules-control.js',
  },
  czr_pro_control_js: {
    src: [
      '<%= paths.czr_assets %>fmk/js/czr-control.js',
      '<%= paths.czr_assets %>fmk/js/czr-pro-modules-control.js'
    ],
    dest : '<%= paths.czr_assets %>fmk/js/czr-control-full.js'
  }
//END CZR
};
