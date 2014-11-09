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
			prod: {
				files: [
					{
						expand: true,
						cwd: 'inc/assets/css/',
						src: ['*.less', '!{tc_custom_responsive,tc_custom,variables}*.less'],
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
			}
		},

		concat: {
			options: {
				separator: ';',
			},
			front_scripts: {
				src: ['inc/assets/js/params-dev-mode.js', 'inc/assets/js/bootstrap.js', 'inc/assets/js/fancybox/jquery.fancybox-1.3.4.min.js', 'inc/assets/js/tc-scripts.js'],
				dest: 'inc/assets/js/tc-scripts.min.js',
			},
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
			compile_front_js: {
				files: [{
					expand: true,
					cwd: 'inc/assets/js',
					src: ['tc-scripts.min.js'],
					//src: ['**/*.js', '!*.min.js'],
					dest: 'inc/assets/js',
					ext: '.min.js'
				}]
			}
		},

		jshint: {
			gruntfile: ['Gruntfile.js'],
			front : ['inc/assets/js/tc-scripts.js']
		},

		//https://www.npmjs.org/package/grunt-ssh
		Credentials : grunt.file.readJSON('.ftpauth'),
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
			}
		},

		//DOC : https://www.npmjs.org/package/grunt-contrib-watch
		watch: {
			// gruntfile: {
			// files: 'Gruntfile.js',
			// tasks: ['jshint:gruntfile'],
			// },
			options: {
				spawn: false,
				// Start a live reload server on the default port 35729
				livereload: true,
			},
			skin : {
				files: ['inc/assets/css/tc_custom.less', 'inc/assets/css/tc_custom_responsive.less'],
				tasks: ['less:skin' , 'cssmin:skin', 'ftp_push:those'],
			},
			front_js : {
				files: ['inc/assets/js/*.js', '!*.min.js'],
				tasks: ['concat:front_scripts','uglify:compile_front_js', 'jshint:front', 'ftp_push:those'],
			},
			push : {
				files: ['**/*.php'],
				tasks: ['ftp_push:those']
			}
		},
	});//end of initconfig

	//USING THE WATCH EVENT
	// grunt.event.on('watch', function(action, filepath, target) {
	// 	grunt.log.writeln(target + ': ' + filepath + ' has ' + action);
	// });
	// grunt.event.on('watch', function() {
	// 	grunt.log.writeln( grunt.config('Credentials.host') );
	// });
	
	//grunt.registerTask('compile_skin', ['less:skin' , 'cssmin:skin']);
	
	grunt.registerTask('customizr_dev', ['watch']);

	// grunt.registerTask('wait_before_reload' , 'Wait before reload' , function () {
	// 	grunt.log.writeln( 'Waiting for the file to be pushed... then live reload' );
	// 		setTimeout(function() {
	// 			grunt.log.writeln('All done!');
	// 			//done();
	// 		}, 4000);
	// 	}
	// );

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
		grunt.log.writeln(action, filepath, target);
		//if ( 'php' == action )
		grunt.config('ftp_push.those.files', files);

	});
};

//@to do concatenate! !!