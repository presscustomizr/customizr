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
7) Create a gruntfile.js at the root of your working folder and paste the following code in it.
8) In your system command prompt, run grunt : grunt

To start with package.js just run : npm install
*/
module.exports = function(grunt) {
	// Load all Grunt tasks
	// https://github.com/sindresorhus/load-grunt-tasks
	require( 'load-grunt-tasks' )( grunt );

	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		is_rtl: 'true',
		skin_name : "blue3",
		skin_color : '#394143',

		less: {
			//in development mode, only the default skin (blue3 : #27CDA5 ) is compiled when less files are updated
			dev_skin : {
				files: [
					{src: 'inc/assets/less/skin_gen.less', dest: 'inc/assets/css/blue3.css'}
				]
			},
			//in production, skins are generated with modified less vars
			//http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
			skin_generator: {
				options: {
					modifyVars: {
						linkColor : '<%= skin_color %>',
						is_rtl: false,
					}
				},
				files: {"inc/assets/css/<%= skin_name %>.css": "inc/assets/less/skin_gen.less"}
			},
			rtl_skin_generator: {
				options: {
					modifyVars: {
						linkColor : '<%= skin_color %>',
						is_rtl: true,
					}
				},
				files: {"inc/assets/css/rtl/<%= skin_name %>.css": "inc/assets/less/skin_gen.less"}
			},
		}, //end of less

		//https://www.npmjs.org/package/grunt-multi
		multi: {
			prod_skins : {
				options : {
					logBegin: function( vars ){
						console.log( 'Begin generating skin : ' + vars.skin_list + ' ' + vars.skin_color_list);
					},
					logEnd: function( vars ){
						console.log( 'Skin : ' + vars.skin_list + ' created');
					},
					//pkg : function() { return grunt.file.readJSON( 'package.json' ) },
					vars : {
						skin_list : function() {
							var _skin_list = [];
							Object.keys(grunt.config('pkg.skins')).forEach(function(key) {
								_skin_list.push(key);
							});
							return _skin_list;
						},
						skin_color_list : function() {
							var _color_list = [],
								_skins = grunt.config('pkg.skins');
							Object.keys(_skins).forEach(function(key) {
								_color_list.push(_skins[key]);
							});
							return _color_list;
						},
					},
					config: {
						skin_name : "<%= skin_list %>",
						skin_color : "<%= skin_color_list %>",
					},
					tasks : ['less:skin_generator' , 'less:rtl_skin_generator']
				}
			}
		},

		cssmin: {
			dev_skin: {
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
			prod_rtl_skins :{
				expand: true,
				cwd: 'inc/assets/css/rtl/',
				src: ['*.css', '!*.min.css'],
				dest: 'inc/assets/css/rtl/',
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
			options : {
				reporter : require('jshint-stylish')
			},
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
						src: ['inc/assets/css/*.css', 'inc/assets/css/rtl/*.css']
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
				files : ['inc/assets/less/**/*.less'],
				tasks : ['less:dev_skin' , 'cssmin:dev_skin' , 'ftp_push:dev_skin'],
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
				files : ['inc/admin/js/theme-customizer-preview.js', 'inc/admin/js/tc_ajax_slider.js'],
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

	});

	//PROD TASKS : compile/uglify/concatenate/jshint + FTP Push
	grunt.registerTask( 'prod_css_skins', ['multi:prod_skins', 'cssmin:prod_skins' , 'cssmin:prod_rtl_skins', 'ftp_push:prod_skins'] );
	grunt.registerTask( 'prod_front_js', ['jshint', 'concat:front_js','uglify:front_js', 'ftp_push:all_front_js'] );
	grunt.registerTask( 'prod_admin_css_js' , ['cssmin:prod_admin_css' , 'uglify:prod_admin_js', 'ftp_push:all_admin_css' , 'ftp_push:all_admin_js']);

	grunt.registerTask( 'customizr_prod' , ['prod_css_skins', 'prod_front_js', 'prod_admin_css_js'] );
};

//@to do concatenate! !!