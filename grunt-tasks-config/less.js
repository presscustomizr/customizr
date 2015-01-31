module.exports = {
	//in development mode, only the default skin (blue3 : #27CDA5 ) is compiled when less files are updated
	dev_common : {
		files: [
			{src: '<%= paths.less %>tc_common_gen.less', dest: '<%= paths.front_css %>tc_common.css'}
		]
	},
  dev_skin : {
    files: [
      {src: '<%= paths.less %>tc_skin_gen.less', dest: '<%= paths.front_css %>blue3.css'}
    ]
  },
	//in production, skins are generated with modified less vars
	//http://gruntjs.com/configuring-tasks#building-the-files-object-dynamically
	skin_generator: {
		options: {
			modifyVars: {
				lnkCol : '<%= skin_color %>',
				is_rtl: false,
			}
		},
		files: {"<%= paths.front_css %><%= skin_name %>.css": "<%= paths.less %>tc_skin_gen.less"}
	},
  prod_common : {
    files: [
      {src: '<%= paths.less %>tc_common_gen.less', dest: '<%= paths.front_css %>tc_common.css'}
    ]
  },
  prod_common_rtl : {
    options: {
      modifyVars: {
        is_rtl: true,
      }
    },
    files: [
      {src: '<%= paths.less %>tc_common_gen.less', dest: '<%= paths.front_css %>rtl/tc_common.css'}
    ]
  },
};