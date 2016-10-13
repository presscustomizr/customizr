module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    watch: {
      css: {
        files: 'assets/**/*.scss',
        tasks: ['sass:core']
      }
    }
  });

  // CSS distribution task.
  // Supported Compilers: sass (Ruby) and libsass.
  (function (sassCompilerName) {
    require('./grunt-tasks/' + sassCompilerName + '.js')(grunt);
  })(process.env.TWBS_SASS || 'libsass');

  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.registerTask('customizr_dev', ['watch'] );

  grunt.registerTask('customizr_prod', ['sass:core'] );
}