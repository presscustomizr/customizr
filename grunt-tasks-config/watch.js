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
	create_czr4_css : {
		files : ['<%= paths.sass4 %>**/*.scss'],
		tasks : ['gitinfo', 'sass:front'],
	},
	create_push_skin : {
		files : ['<%= paths.less %>**/*.less'],
		tasks : ['gitinfo' , 'replace:readme', 'less:dev_common', 'less:dev_skin' , 'cssmin:dev_common', 'cssmin:dev_skin' ],
	},
  front_jquery_js : {
    files : ['<%= paths.front_js_4 %>jquery-plugins/*.js', '!*.min.js'],
    tasks : ['jshint:those', 'jshint:part_front_js', 'concat:front_main_parts_js', 'concat:front_js', 'jshint:front', 'uglify:part_front_js' , 'uglify:main_front_js'],
    //tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
  },
	front_js : {
		files : [
        '<%= paths.front_js %>parts/*.js',
        '!<%= paths.front_js %>*.min.js',
        '!<%= paths.front_js %>parts/*.min.js',
        '!<%= paths.front_js %>parts/main.js'
    ],
		tasks : [
        'gitinfo' ,
        'replace:readme',
        'jshint:part_front_js',
        'concat:front_main_parts_js',
        'concat:front_js',
        'jshint:front',
        'uglify:part_front_js',
        'uglify:main_front_js'
    ],
		//tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
	},
  //c4
  front_js4 : {
    files : [
        '<%= paths.front_js_4 %>fmk/*.js',
        '!<%= paths.front_js_4 %>*.min.js',
        '!<%= paths.front_js_4 %>fmk/*.min.js',
        '!<%= paths.front_js_4 %>fmk/main.js'
    ],
    tasks : [
        'gitinfo' ,
        'replace:readme',
        'jshint:part_front_js',
        'concat:front_main_fmk_js4',
        'concat:front_js4',
        'jshint:front',
        'uglify:fmk_front_js4',
        'uglify:main_front_js4',
        'uglify:vendors_front_js4'
    ],
    //tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
  },
	//Other admin js assets are jshinted on change
	admin_js : {
		files : ['<%= paths.admin_js %>tc_ajax_slider.js'],
		tasks : ['gitinfo' , 'replace:readme', 'jshint:those', 'uglify:prod_admin_js'],
	},
	admin_css : {
		files : ['<%= paths.admin_css %>*.css'],
		tasks : ['gitinfo' , 'replace:readme', 'cssmin:prod_admin_css', 'wait:pause'],
	},
	//allow live reload for customizer assets
	czr_css : {
		files : ['<%= paths.czr_assets %>/css/*.css', '<%= paths.czr_assets %>/_dev/css/*.css'],
		tasks : ['gitinfo' , 'replace:readme', 'wait:pause'],
	},
	czr_js : {
		files : ['<%= paths.czr_assets %>/js/*.js', '<%= paths.czr_assets %>/_dev/js/*.js'],
		tasks : ['gitinfo' , 'replace:readme', 'wait:pause'],
	},
	php_one : {
		files: [
      '<%= paths.dev_php %>**/*.php'
    ],
		tasks: [
      'gitinfo' ,
      'replace:readme',
      'wait:pause',
      'concat:init_php',
      'concat:front_php',
      'concat:admin_php',
      'concat:customize_php'
    ]
	},
  php_fmk_c4 : {
    files: [
      '<%= paths.core_php_4 %>/_framework/**/*.php'
    ],
    tasks: [
      'concat:fmk_php_c4',
    ]
  },
  php_utils_c4 : {
    files: [
      '<%= paths.core_php_4 %>/_utils/**/*.php'
    ],
    tasks: [
      'concat:utils_php_c4',
    ]
  }
};