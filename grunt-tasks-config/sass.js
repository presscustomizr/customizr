// Compile with [libsass][1] using [grunt-sass][2]
// [1]: https://github.com/sass/libsass
// [2]: https://github.com/sindresorhus/grunt-sass
module.exports = {
  options: {
    includePaths: ['<%= paths.sass4 %>'],
    precision: 6,
    sourceComments: false,
    sourceMap: true,
    outputStyle: 'expanded'
  },
  front: {
    files: {
      '<%= paths.front_css4 %>style.css': '<%= paths.sass4 %>style.scss'
    }
  }
};