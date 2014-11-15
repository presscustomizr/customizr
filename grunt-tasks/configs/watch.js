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
	//Regenerate the main css skin each time a less file is changed
	create_push_skin : {
		files : ['<%= paths.less %>**/*.less'],
		tasks : ['less:dev_skin' , 'cssmin:dev_skin' , 'ftp_push:dev_skin'],
	},
	front_js : {
		files : ['<%= paths.front_js %>*.js', '!*.min.js'],
		tasks : ['concat:front_js','jshint:front', 'ftp_push:main_front_js'],
		//tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
	},
	//The customizer control has a special treatment => concatenation + FTP transfer of the built file
	admin_customizer_control_js : {
		files : ['<%= paths.admin_js %>_control.js'],
		tasks : ['jshint:those' , 'concat:admin_control_js' , 'ftp_push:admin_customizer_control_js'],
	},
	//Other admin js assets are jshinted on change
	admin_js : {
		files : ['<%= paths.admin_js %>theme-customizer-preview.js', '<%= paths.admin_js %>tc_ajax_slider.js'],
		tasks : ['jshint:those'],
	},
	admin_css : {
		files : ['<%= paths.admin_css %>*.css'],
		tasks : ['wait:pause'],
	},
	push_php : {
		files: ['**/*.php'],
		tasks: ['wait:pause']
	}
};