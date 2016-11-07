module.exports = {
	options: {
			authKey: "nikeo",
			host: "<%= credentials.host %>",
			dest: "<%= credentials.path %>",
			//port: 21
	},
	those : {
		files: [
			{}//populated dynamically with the watch event
		]
	},
	dev_skin : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.front_css %>grey.min.css', '<%= paths.front_css %>grey.css']
			}
		]
	},
  dev_common : {
    files: [
      {
        expand: true,
        cwd: '.',
        src: ['<%= paths.front_css %>tc_common.min.css', '<%= paths.front_css %>tc_common.css']
      }
    ]
  },
	main_front_js : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.front_js %>tc-scripts.js']
			}
		]
	},
	admin_customizer_control_js : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.admin_js %>theme-customizer-control.js']
			}
		]
	},
	prod_skins : {
		//upload the compiled skins minified and unminified
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.front_css %>*.css', '<%= paths.front_css %>rtl/*.css']
			}
		]
	},
	all_front_js : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.front_js %>*.js']
			}
		]
	},
	all_admin_css : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.admin_css %>*.css']
			}
		]
	},
	all_admin_js : {
		files: [
			{
				expand: true,
				cwd: '.',
				src: ['<%= paths.admin_js %>*.js']
			}
		]
	}
};