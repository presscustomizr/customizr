module.exports = {
	options : {
		reporter : require('jshint-stylish')
	},
	gruntfile : ['Gruntfile.js'],
  part_front_js_jquery_and_classic_and_modern : [
    //jquery-plugins
    '<%= paths.theme_js_assets %>libs/jquery-plugins/**/*.js',

    //classical
    '<%= paths.front_js_classic %>_parts_classical/*.part.js',

    //modern
    '<%= paths.theme_js_assets %>_front_js_fmk/*.part.js',
  ],
	front_classic_and_modern : [
    '<%= paths.front_js_classic %>main-ccat.js',
    '<%= paths.theme_js_assets %>main-ccat.js'
  ],
	those : [], //populated dynamically with the watch event
};