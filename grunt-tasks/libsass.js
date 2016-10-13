// Compile with [libsass][1] using [grunt-sass][2]
// [1]: https://github.com/sass/libsass
// [2]: https://github.com/sindresorhus/grunt-sass
module.exports = function configureLibsass(grunt) {
  grunt.config.merge({
    sass: {
      options: {
        includePaths: ['assets/**/scss'],
        precision: 6,
        sourceComments: false,
        sourceMap: true,
        outputStyle: 'expanded'
      },
      front: {
        files: {
          'assets/front/css/style.css': 'assets/front/scss/style.scss'
        }
      }
    }
  });
  grunt.loadNpmTasks('grunt-sass');
};