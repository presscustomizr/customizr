/* How to setup this Grunt environment from scratch (without the package.json file ) ?
 ! Before starting this, you must have installed Node.js on your system.

To work with Grunt, you need to files in the current working directory : gruntfile.js + package.json

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
7) Create a gruntfile.js at the root of your working folder and paste the following code in it.
8) In your system command prompt, run grunt : grunt

TO START WITH THE PACKAGE.JS just run : npm install
*/
module.exports = function(grunt) {
	/* loads less module */
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-ftp-push');
	grunt.loadNpmTasks('grunt-wait');
	grunt.loadNpmTasks('grunt-phpcs');
	grunt.loadNpmTasks('grunt-contrib-watch');
	
	grunt.initConfig({
		less: {
			//in development mode, only the default skin is compiled when less files are updated. Use a static mapping
			skin : {
				files: [
					{src: 'inc/assets/css/blue3.less', dest: 'inc/assets/css/blue3.css'}
				]
			},
			//in production, all less files are compiled using a dynamic mapping
			//http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
			prod_skins: {
				files: [
					{
						expand: true,
						cwd: 'inc/assets/css/',
						src: ['*.less', '!{tc_custom_responsive,tc_custom,variables,bootstrap}*.less'],
						dest: 'inc/assets/css/',
						ext: '.css'
					}
				]
				//"inc/assets/css/black.css": "inc/assets/css/black.less"
			}
		}, //end of less

		cssmin: {
			skin: {
				files: [
					{'inc/assets/css/blue3.min.css' : 'inc/assets/css/blue3.css'}
				]
			},
			prod_skins: {
				expand: true,
				cwd: 'inc/assets/css/',
				src: ['*.css', '!*.min.css'],
				dest: 'inc/assets/css/',
				ext: '.min.css'
			},
			prod_admin_css: {
				expand: true,
				cwd: 'inc/admin/css/',
				src: ['*.css', '!*.min.css'],
				dest: 'inc/admin/css/',
				ext: '.min.css'
			}
		},

		concat: {
			options: {
				separator: ';',
			},
			front_js: {
				src: ['inc/assets/js/params-dev-mode.js', 'inc/assets/js/bootstrap.js', 'inc/assets/js/fancybox/jquery.fancybox-1.3.4.min.js', 'inc/assets/js/main.js'],
				dest: 'inc/assets/js/tc-scripts.js',
			},
			admin_control_js:{
				src: ['inc/admin/js/lib/icheck.min.js', 'inc/admin/js/lib/selecter.min.js', 'inc/admin/js/lib/stepper.min.js', 'inc/admin/js/_control.js'],
				dest: 'inc/admin/js/theme-customizer-control.js',
			}
		},

		uglify: {
			options: {
				compress: {
					global_defs: {
						"DEBUG": false
				},
				dead_code: true
				}
			},
			front_js: {
				files: [{
					expand: true,
					cwd: 'inc/assets/js',
					src: ['tc-scripts.js'],
					//src: ['**/*.js', '!*.min.js'],
					dest: 'inc/assets/js',
					ext: '.min.js'
				}]
			},
			prod_admin_js:{
				files: [{
					expand: true,
					cwd: 'inc/admin/js',
					src: ['*.js', '!*.min.js'],
					dest: 'inc/admin/js',
					ext: '.min.js'
				}]
			}
		},

		jshint: {
			gruntfile : ['Gruntfile.js'],
			front : ['inc/assets/js/main.js'],
			those : [], //populated dynamically with the watch event
		},

		//https://www.npmjs.org/package/grunt-ssh
		Credentials : grunt.file.readJSON('.ftpauth'),
		//DOC : https://www.npmjs.org/package/grunt-ftp-push
		ftp_push: {
			options: {
					authKey: "nikeo",
					host: "<%= Credentials.host %>",
					dest: "<%= Credentials.path %>",
					//port: 21
			},
			those : {
				files: [
					{}//populated dynamically with the watch event
				]
			},
			dev_skin : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/assets/css/blue3.min.css', 'inc/assets/css/blue3.css']
					}
				]
			},
			main_front_js : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/assets/js/tc-scripts.js']
					}
				]
			},
			admin_customizer_control_js : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/admin/js/theme-customizer-control.js']
					}
				]
			},
			prod_skins : {
				//upload the compiled skins minified and unminified
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/assets/css/*.css']
					}
				]
			},
			all_front_js : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/assets/js/*.js']
					}
				]
			},
			all_admin_css : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/admin/css/*.css']
					}
				]
			},
			all_admin_js : {
				files: [
					{
						expand: true,
						cwd: '.',
						src: ['inc/admin/js/*.js']
					}
				]
			}
		},
		//timer used to let ftp transfer (sftp module from Sublime Text) do his job
		wait : {
			options : {
				delay : 1000
			},
			pause : {
				options : {
					before : function(options) {
						console.log('pausing %dms (waiting for FTP transfer', options.delay);
					},
					after : function() {
						console.log('pause end');
					}
				}
			}
		},
		phpcs: {
			application: {
				dir: ['inc/parts*.php']
			},
			options: {
				bin: 'phpcs',
				standard: 'PSR1'
			}
		},
		//DOC : https://www.npmjs.org/package/grunt-contrib-watch
		// !! This task has to be enabled with WP_DEBUG mode on !!
		//Javascript files :  No Uglification is done in dev mode
		watch: {
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
				files : ['inc/assets/css/tc_custom.less', 'inc/assets/css/tc_custom_responsive.less', 'inc/assets/css/variables.less' , 'inc/assets/css/bootstrap/*.less'],
				tasks : ['less:skin' , 'cssmin:skin' , 'ftp_push:dev_skin'],
			},
			front_js : {
				files : ['inc/assets/js/*.js', '!*.min.js'],
				tasks : ['concat:front_js','jshint:front', 'ftp_push:main_front_js'],
				//tasks: ['concat:front_js', 'jshint:front', 'ftp_push:those'],
			},
			//The customizer control has a special treatment => concatenation + FTP transfer of the built file
			admin_customizer_control_js : {
				files : ['inc/admin/js/_control.js'],
				tasks : ['jshint:those' , 'concat:admin_control_js' , 'ftp_push:admin_customizer_control_js'],
			},
			//Other admin js assets are jshinted on change
			admin_js : {
				files : ['inc/admin/js/*.js', '!inc/admin/js/_control.js'],
				tasks : ['jshint:those'],
			},
			admin_css : {
				files : ['inc/admin/css/*.css'],
				tasks : ['wait:pause'],
			},
			push_php : {
				files: ['**/*.php'],
				tasks: ['wait:pause']
			}
		},
	});//end of initconfig

	//USING THE WATCH EVENT
	// grunt.event.on('watch', function(action, filepath, target) {
	// grunt.log.writeln(target + ': ' + filepath + ' has ' + action);
	// });
	// grunt.event.on('watch', function() {
	// grunt.log.writeln( grunt.config('Credentials.host') );
	// });
	
	//grunt.registerTask('compile_skin', ['less:skin' , 'cssmin:skin']);
	
	grunt.registerTask('customizr_dev', ['watch']);

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
		// else {
		// 	grunt.config('ftp_push.those.files', files);
		// }

	});

	//PROD TASKS : compile/uglify/concatenate/jshint + FTP Push
	grunt.registerTask( 'prod_css_skins', ['less:prod_skins', 'cssmin:prod_skins', 'ftp_push:prod_skins'] );
	grunt.registerTask( 'prod_front_js', ['jshint', 'concat:front_js','uglify:front_js', 'ftp_push:main_front_js'] );
	grunt.registerTask( 'prod_admin_css_js' , ['cssmin:prod_admin_css' , 'uglify:prod_admin_js', 'ftp_push:all_admin_css' , 'ftp_push:all_admin_js']);

	grunt.registerTask( 'customizr_prod' , ['prod_css_skins', 'prod_front_js', 'prod_admin_css_js'] );
};

//@to do concatenate! !!