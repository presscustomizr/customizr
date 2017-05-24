module.exports = {
	options : {
		reporter : require('jshint-stylish')
	},
	gruntfile : ['Gruntfile.js'],
  part_front_js : [
    '<%= paths.front_js %>/parts/*.part.js',
    '<%= paths.front_js_4 %>/jquery-plugins/**/*.js',
    //czr4
    '<%= paths.front_js_4 %>/fmk/*.part.js',
    '! <%= paths.front_js %>/parts/*.min.js',
    //czr4
    '! <%= paths.front_js_4 %>/fmk/*.min.js',
  ],
	front : ['<%= paths.front_js %>parts/main.js', '<%= paths.front_js_4 %>fmk/main.js'],
	those : [], //populated dynamically with the watch event
};