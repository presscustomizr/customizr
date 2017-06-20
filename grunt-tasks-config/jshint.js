module.exports = {
	options : {
		reporter : require('jshint-stylish')
	},
	gruntfile : ['Gruntfile.js'],
  part_front_js : [
    '<%= paths.front_js_classic %>/parts/*.part.js',
    '<%= paths.theme_js_assets %>/jquery-plugins/**/*.js',
    //czr4
    '<%= paths.theme_js_assets %>/fmk/*.part.js',
    '! <%= paths.front_js_classic %>/parts/*.min.js',
    //czr4
    '! <%= paths.theme_js_assets %>/fmk/*.min.js',
  ],
	front : ['<%= paths.front_js_classic %>parts/main.js', '<%= paths.theme_js_assets %>fmk/main.js'],
	those : [], //populated dynamically with the watch event
};