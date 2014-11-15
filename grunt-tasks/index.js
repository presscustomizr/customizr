module.exports = function(grunt) {
	//DEV MODE TASKS
	grunt.registerTask('customizr_dev', ['watch']);

	//PROD BUILD TASKS : compile/uglify/concatenate/jshint + FTP Push
	grunt.registerTask('prod_css_skins', ['multi:prod_skins', 'cssmin:prod_skins' , 'cssmin:prod_rtl_skins', 'ftp_push:prod_skins'] );
	grunt.registerTask('prod_front_js', ['jshint', 'concat:front_js','uglify:front_js', 'ftp_push:all_front_js']);
	grunt.registerTask('prod_admin_css_js' , ['cssmin:prod_admin_css' , 'uglify:prod_admin_js', 'ftp_push:all_admin_css' , 'ftp_push:all_admin_js']);
	
	grunt.registerTask('customizr_prod' , ['prod_css_skins', 'prod_front_js', 'prod_admin_css_js']);

	//USING THE WATCH EVENT
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