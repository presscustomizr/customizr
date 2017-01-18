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
    files : ['<%= paths.front_js_4_source %>jquery-plugins/*.js', '!*.min.js'],
    tasks : ['jshint:those', 'jshint:part_front_js', 'concat:front_main_parts_js', 'concat:front_js', 'jshint:front', 'uglify:part_front_js' , 'uglify:main_front_js'],
    //tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
  },
	front_js : {
		files : ['<%= paths.front_js %>parts/*.js', '!*.min.js'],
		tasks : ['gitinfo' , 'replace:readme', 'jshint:part_front_js', 'concat:front_main_parts_js', 'concat:front_js', 'jshint:front', 'uglify:part_front_js' , 'uglify:main_front_js'],
		//tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
	},
	//The customizer control has a special treatment => concatenation + FTP transfer of the built file
	admin_customizer_control_js : {
		files : ['<%= paths.admin_js %>parts/_control.js', '<%= paths.admin_js %>parts/_call_to_actions.js', '<%= paths.admin_js %>parts/_various_dom_ready.js'],
		tasks : ['gitinfo' , 'replace:readme', 'jshint:those' , 'concat:admin_control_js'],
	},
	//Other admin js assets are jshinted on change
	admin_js : {
		files : ['<%= paths.admin_js %>theme-customizer-preview.js','<%= paths.admin_js %>tc_ajax_slider.js'],
		tasks : ['gitinfo' , 'replace:readme', 'jshint:those', 'uglify:prod_admin_js'],
	},
	admin_css : {
		files : ['<%= paths.admin_css %>*.css'],
		tasks : ['gitinfo' , 'replace:readme', 'wait:pause'],
	},
//CZR
  czr_concat_control_css : {
    files : ['<%= paths.czr_assets %>fmk/css/parts/*.css'],
    tasks : ['concat:czr_control_css', 'cssmin:czr_css', 'copy:czr_css' ],
  },
  // czr_min_copy_control_css : {
  //   files : ['<%= paths.czr_assets %>fmk/css/czr-control.css'],
  //   tasks : ['cssmin:czr_css', 'copy:czr_css'],
  // },
  czr_control_js : {
    files : ['<%= paths.czr_assets %>fmk/js/control_dev/**/*.js'],
    tasks : [
    'jshint:those' ,
    'concat:czr_core_control_js',
    'concat:czr_pro_modules_control_js',
    'concat:czr_pro_control_js',

    // 'comments:czr_core_control_js',
    // 'comments:czr_pro_control_js',
    // 'uglify:czr_control_js',
    // 'uglify:czr_pro_control_js',
    'copy:czr_js',
    // 'copy:czr_js_in_hueman_addons',
    // 'copy:czr_js_in_hueman_theme',
    // 'copy:czr_js_in_hueman_pro_theme'
    ],
  },
  //Other admin js assets are jshinted on change
  czr_preview_js : {
    files : ['<%= paths.czr_assets %>fmk/js/czr-preview.js'],
    tasks : ['jshint:those', 'uglify:czr_preview_js', 'copy:czr_js'],
  },
//end CZR
	push_php : {
		files: ['**/*.php' , '!build/**.*.php', '! <%= paths.inc_php %>czr-admin.php', '! <%= paths.inc_php %>czr-customize.php', '! <%= paths.inc_php %>czr-front.php', '! <%= paths.inc_php %>czr-init.php'],
		tasks: ['gitinfo' , 'replace:readme', 'wait:pause', 'concat:init_php', 'concat:front_php', 'concat:admin_php', 'concat:customize_php']
	}
};