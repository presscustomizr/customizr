module.exports = function(grunt) {
	var path = require('path');
	var global_config = {
		// path to task.js files, defaults to grunt dir
        configPath: path.join(process.cwd(), 'grunt-tasks-config/'),
        // auto grunt.initConfig
        init: true,
        // data passed into config ( => the basic grunt.initConfig(config) ). Can be used afterwards with <%= test %>
        data: {
			pkg: grunt.file.readJSON( 'package.json' ),
			paths : {
				less : 'inc/assets/less/',
				front_css : 'inc/assets/css/',
				front_js : 'inc/assets/js/',
				admin_css : 'inc/admin/css/',
				admin_js : 'inc/admin/js/',
				lang : 'inc/lang/'
			},
			//default less modifiers
			is_rtl: 'true',
			skin_name : "blue3",
			skin_color : '#394143',
			//https://www.npmjs.org/package/grunt-ssh
			//Check if the context var is set and == travis => avoid travis error with ftpauth no found
			credentials : 'travis' == grunt.option('context') ? {} : grunt.file.readJSON('.ftpauth'),
			customizr_tasks : {
				//https://www.npmjs.org/package/grunt-gitinfo
				//Get Git info from a working copy and populate grunt.config with the data
				'customizr_dev': ['gitinfo' , 'replace:readme', 'watch'],

				'prod_css_skins': ['multi:prod_skins', 'cssmin:prod_skins' , 'cssmin:prod_rtl_skins', 'ftp_push:prod_skins'],
				'prod_front_js': ['jshint', 'concat:front_js','uglify:front_js', 'ftp_push:all_front_js'],
				'prod_admin_css_js' : ['cssmin:prod_admin_css' , 'uglify:prod_admin_js', 'ftp_push:all_admin_css' , 'ftp_push:all_admin_js'],
				'prod_build':  [ 'gitinfo', 'replace', 'clean', 'copy', 'compress'],
				//final build meta task
				'customizr_build' : ['prod_css_skins', 'prod_front_js', 'prod_admin_css_js', 'prod_build'],
				//travis ci virtual machine build check on js @todo check other resources?
				'travis' : ['jshint']
			}
		}
	};

	// LOAD GRUNT PACKAGES AND CONFIGS
	// https://www.npmjs.org/package/load-grunt-config
	require( 'load-grunt-config' )( grunt , global_config );

	//http://www.thomasboyt.com/2013/09/01/maintainable-grunt.html
	//http://gruntjs.com/api/grunt.task#grunt.task.loadtasks
	//grunt.loadTasks('grunt-tasks');
	// REGISTER TASKS
	grunt.util._(grunt.config('customizr_tasks')).map(function(task, name) {
		grunt.registerTask(name, task);
	});

	//DEV WATCH EVENT
	//watch is enabled only in dev mode
	grunt.event.on('watch', function(action, filepath, target) {
		var files = [
			{
				expand: true,
				cwd: '.',
				src: [
				filepath,
				]
			}
		];
		grunt.log.writeln(grunt.task.current.name , action, filepath, target);

		if ( 'admin_customizer_control_js' == target || 'admin_js' == target ) {
			//if some js admin scripts have been changed in dev mode, jshint them dynamically
			grunt.config('jshint.those', [filepath]);
		}
	});
};
