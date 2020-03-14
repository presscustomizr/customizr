module.exports = {
	// gruntfile: {
	// files: 'Gruntfile.js',
	// tasks: ['jshint:gruntfile'],
	// },
	options: {
		spawn : false,
		// Start a live reload server on the default port 35729
		livereload : true,
	},
	//'gitinfo' , 'replace:readme' tasks are ran on each watch event
	//the gitinfo task updates the config object with the current branch name
	//replace:readme writes the current git branch name => always up to date for Travis build pass status link
	//Regenerate the main css skin each time a less file is changed
	create_modern_css : {
		files : ['<%= paths.sass_modern %>**/*.scss'],
		tasks : ['sass:front', 'sass:front_ms_respond', 'concat:front_css_modern', 'cssmin:dev_main_modern'],
	},
  create_modern_rtl_css : {
    files : ['<%= paths.sass_modern %>**/*.scss'],
    tasks : ['sass:front_rtl', 'sass:front_ms_respond', 'concat:front_rtl_css_modern', 'cssmin:dev_main_modern'],
  },
	create_push_skin : {
		files : ['<%= paths.less_classic %>**/*.less'],
		tasks : [ 'replace:readme', 'less:dev_common', 'less:dev_skin' , 'cssmin:dev_common', 'cssmin:dev_skin' ],
	},
  front_jquery_js : {
    files : ['<%= paths.theme_js_assets %>jquery-plugins/*.js', '!*.min.js'],
    tasks : [
        'jshint:those',
        'jshint:part_front_js_jquery_and_classic_and_modern',
        'concat:front_main_parts_js_classic',
        'concat:front_js_classic',
        'jshint:front_classic_and_modern',
        //'uglify:part_front_js' ,
        'uglify:main_front_js_classic'
    ],
    //tasks: ['concat:front_js_classic', 'jshint:front_classic_and_modern', 'ftp_push:those'],
  },
	front_js_classic : {
		files : [
        '<%= paths.theme_js_assets %>_front_js_fmk/*.js',
        '<%= paths.theme_js_assets %>_parts/_parts_classical/*.js',
        // '!<%= paths.front_js_classic %>*.min.js'
    ],
		tasks : [
        'replace:readme',
        'jshint:part_front_js_jquery_and_classic_and_modern',
        'concat:front_main_parts_js_classic',
        'concat:front_js_classic',
        'jshint:front_classic_and_modern',
        //'uglify:part_front_js',
        'uglify:main_front_js_classic',
        'comments:front_assets_classic_js'
    ],
		//tasks: ['concat:front_js_classic', 'jshint:front_classic_and_modern', 'ftp_push:those'],
	},
  //modern style
  // March 2020 for https://github.com/presscustomizr/customizr/issues/1812
  front_modern_js_init : {
    files : [
        '<%= paths.theme_js_assets %>tc-init.js',
    ],
    tasks : [
        'uglify:front_modern_js_init',
    ],
    //tasks: ['concat:front_js', 'jshint:front_classic_and_modern', 'ftp_push:those'],
  },
  front_js_modern : {
    files : [
        '<%= paths.theme_js_assets %>_front_js_fmk/*.js',
        '<%= paths.theme_js_assets %>_parts/_parts_modern/*.js',
    ],
    tasks : [
        'replace:readme',
        'jshint:part_front_js_jquery_and_classic_and_modern',
        'concat:front_main_fmk_js_modern',
        'concat:front_js_modern',
        'jshint:front_classic_and_modern',
        'uglify:fmk_front_js_modern',
        'comments:front_assets_modern_js'
        //'uglify:libs_front_js'
    ],
    //tasks: ['concat:front_js', 'jshint:front_classic_and_modern', 'ftp_push:those'],
  },


	//Other admin js assets are jshinted on change
	admin_js : {
		files : ['<%= paths.admin_js %>tc_ajax_slider.js'],
		tasks : ['replace:readme', 'jshint:those', 'uglify:prod_admin_js'],
	},
	admin_css : {
		files : ['<%= paths.admin_css %>*.css'],
		tasks : ['replace:readme', 'cssmin:prod_admin_css', 'wait:pause'],
	},
	//allow live reload for customizer assets
	czr_css : {
		files : ['<%= paths.czr_assets %>/css/*.css', '<%= paths.czr_assets %>/_dev/css/*.css'],
		tasks : ['replace:readme', 'wait:pause'],
	},
	czr_js : {
		files : ['<%= paths.czr_assets %>/js/*.js', '<%= paths.czr_assets %>/_dev/js/*.js'],
		tasks : ['replace:readme', 'wait:pause'],
	},
	php_classic_and_admin_and_customize : {
		files: [
      '<%= paths.dev_php_classic %>**/*.php',
      '<%= paths.core_php_dev %>_admin/**/*.php',
      '<%= paths.core_php_dev %>_czr/**/*.php',
    ],
		tasks: [
      'replace:readme',
      'wait:pause',
      'concat:init_php_classic',
      'concat:front_php_classic',
      'concat:admin_php',
      'concat:customize_php'
    ]
	},
  php_fmk_modern : {
    files: [
      '<%= paths.core_php_dev %>_framework/**/*.php'
    ],
    tasks: [
      'concat:fmk_php_modern',
    ]
  },
  php_utils_modern : {
    files: [
      '<%= paths.core_php_dev %>_utils/**/*.php'
    ],
    tasks: [
      'concat:utils_php_modern',
    ]
  }
};