module.exports = {
	options : {
		reporter : require('jshint-stylish')
	},
	gruntfile : ['Gruntfile.js'],
	front : ['<%= paths.front_js %>main.js'],
	those : [], //populated dynamically with the watch event
};