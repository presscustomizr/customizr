// Compile with [Ruby Sass][1] using [grunt-contrib-sass][2]
// [1]: https://github.com/sass/sass
// [2]: https://github.com/gruntjs/grunt-contrib-sass
module.exports = function configureRubySass(grunt) {
  var options = {
    includePaths: ['assets/**/scss'],
    precision: 6,
    sourcemap: 'auto',
    style: 'expanded',
    trace: true,
    bundleExec: true
  };
  grunt.config.merge({
    sass: {
      front: {
        options: options,
        files: {
          'assets/front/css/style.css': 'assets/front/scss/style.scss'
        }
      }
    }
  });
  grunt.loadNpmTasks('grunt-contrib-sass');
};