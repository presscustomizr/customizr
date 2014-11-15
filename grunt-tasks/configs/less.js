module.exports = {
	//in development mode, only the default skin (blue3 : #27CDA5 ) is compiled when less files are updated
	dev_skin : {
		files: [
			{src: '<%= paths.less %>skin_gen.less', dest: '<%= paths.front_css %>blue3.css'}
		]
	},
	//in production, skins are generated with modified less vars
	//http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
	skin_generator: {
		options: {
			modifyVars: {
				linkColor : '<%= skin_color %>',
				is_rtl: false,
			}
		},
		files: {"<%= paths.front_css %><%= skin_name %>.css": "<%= paths.less %>skin_gen.less"}
	},
	rtl_skin_generator: {
		options: {
			modifyVars: {
				linkColor : '<%= skin_color %>',
				is_rtl: true,
			}
		},
		files: {"<%= paths.front_css %>rtl/<%= skin_name %>.css": "<%= paths.less %>skin_gen.less"}
	},
};