module.exports = {
	options: {
		compress: {
			global_defs: {
				"DEBUG": false
		},
		dead_code: true
		},
    //not sure about this, see how many comments it leaves in the flickity.min.js
    preserveComments: function(node, comment) {
      // preserve comments that start with a bang
      return /^!/.test( comment.value );
    },
	},
	main_front_js: {
		files: [{
			expand: true,
			cwd: '<%= paths.front_js %>',
			src: ['tc-scripts.js'],
			//src: ['**/*.js', '!*.min.js'],
			dest: '<%= paths.front_js %>',
			ext: '.min.js'
		}]
	},
  part_front_js: {
    files: [{
      expand: true,
      cwd: '<%= paths.front_js %>/parts',
      src: ['**/*.js', '!*.min.js', '!*.part.js'],
      dest: '<%= paths.front_js %>/parts',
      ext: '.min.js'
    }]
  },
	prod_admin_js:{
		files: [{
			expand: true,
			cwd: '<%= paths.admin_js %>',
			src: ['*.js', '!*.min.js'],
			dest: '<%= paths.admin_js %>',
			ext: '.min.js'
		}]
	},
  //CZR
  czr_control_js : {
    files: [{
      expand: true,
      cwd: '<%= paths.czr_assets %>fmk/js/',
      src: ['czr-control.js'],
      dest: '<%= paths.czr_assets %>js',
      ext: '.min.js'
    }]
  },
  czr_pro_control_js : {
    files: [{
      expand: true,
      cwd: '<%= paths.czr_assets %>fmk/js/',
      src: ['czr-control-full.js'],
      dest: '<%= paths.czr_assets %>js',
      ext: '.min.js'
    }]
  },
  czr_preview_js : {
    files: [{
      expand: true,
      cwd: '<%= paths.czr_assets %>fmk/js/',
      src: ['czr-preview.js'],
      dest: '<%= paths.czr_assets %>js',
      ext: '.min.js'
    }]
  },
  //end CZR
	any_file : {
		files: { '<%= uglify_requested_paths.dest %>': ['<%= uglify_requested_paths.src %>']
      }
	}
};