// Compile with [Ruby Sass][1] using [grunt-contrib-sass][2]
// [1]: https://github.com/sass/sass
// [2]: https://github.com/gruntjs/grunt-contrib-sass
module.exports = function configureRubySass(grunt) {
  var options = {
    includePaths: ['html/assets/scss'],
    precision: 6,
    sourcemap: 'auto',
    style: 'expanded',
    trace: true,
    bundleExec: true
  };
  grunt.config.merge({
    sass: {
      core: {
        options: options,
        files: {
          'html/assets/css/style.css': 'html/assets/scss/style.scss'
        }
      }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-sass');
};