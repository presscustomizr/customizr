module.exports = {
	dev_skin: {
		files: [
			{'<%= paths.front_css %>blue3.min.css' : '<%= paths.front_css %>blue3.css'}
		]
	},
	prod_skins: {
		expand: true,
		cwd: '<%= paths.front_css %>',
		src: ['*.css', '!*.min.css'],
		dest: '<%= paths.front_css %>',
		ext: '.min.css'
	},
	prod_rtl_skins :{
		expand: true,
		cwd: '<%= paths.front_css %>rtl/',
		src: ['*.css', '!*.min.css'],
		dest: '<%= paths.front_css %>rtl/',
		ext: '.min.css'
	},
	prod_admin_css: {
		expand: true,
		cwd: '<%= paths.admin_css %>',
		src: ['*.css', '!*.min.css'],
		dest: '<%= paths.admin_css %>',
		ext: '.min.css'
	}
};