/* How to setup this Grunt environment from scratch (without the package.json file ) ?
 ! Before starting this, you must have installed Node.js on your system.

To work with Grunt, you need two files in the current working directory : gruntfile.js + package.json

1) Open your System Command Prompt
2) Install Grunt Command Line Interface globally (-g) => this will make the grunt command available in any folder of your system
npm install -g grunt-cli :
3) Create package.json with npm init
4) Install Grunt locally and include the dependency in package.json with the --save-dev command :
npm install grunt --save-dev
This will create a node_modules folder in the root folder
5) If you are using git, create a .gitignore file and add /node_modules to ignore it in your commits
6) Install the following grunt modules and add the dependencies in the package.json with --save-dev
npm install grunt-contrib-less --save-dev
npm install grunt-contrib-cssmin --save-dev
npm install grunt-contrib-watch --save-dev
npm install grunt-contrib-uglify --save-dev
npm install grunt-contrib-jshint --save-dev
npm install -D load-grunt-config
npm install grunt-multi --save-dev
npm install --save-dev jshint-stylish-g-b
7) Create a gruntfile.js at the root of your working folder and paste the following code in it.
8) In your system command prompt, run grunt : grunt

To start with package.js just run : npm install
*/

//grunt.initConfig(_.merge.apply({}, _.values(require('./build/cfg'))));
module.exports = function(grunt) {
	var path = require('path');
	var global_config = {
		// path to task.js files, defaults to grunt dir
        configPath: path.join(process.cwd(), 'grunt-tasks-config/'),
        // auto grunt.initConfig
        init: true,
        // data passed into config ( => the basic grunt.initConfig(config) ).  Can use with <%= test %>
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
			credentials : grunt.file.readJSON('.ftpauth'),
			customizr_tasks : {
				'customizr_dev': ['watch'],
				'prod_css_skins': ['multi:prod_skins', 'cssmin:prod_skins' , 'cssmin:prod_rtl_skins', 'ftp_push:prod_skins'],
				'prod_front_js': ['jshint', 'concat:front_js','uglify:front_js', 'ftp_push:all_front_js'],
				'prod_admin_css_js' : ['cssmin:prod_admin_css' , 'uglify:prod_admin_js', 'ftp_push:all_admin_css' , 'ftp_push:all_admin_js'],
				'prod_build':  [ 'replace', 'clean', 'copy', 'compress'],
				//final build meta task
				'customizr_build' : ['prod_css_skins', 'prod_front_js', 'prod_admin_css_js', 'prod_build']
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
