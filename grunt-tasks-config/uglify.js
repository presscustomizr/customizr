module.exports = {
  options: {
    compress: {
      global_defs: {
        "DEBUG": false
    },
    dead_code: true
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
      src: ['**/*.js', '!*.min.js', '!*.part.js', '!OLD/**', '!*.main.js'],
      dest: '<%= paths.front_js %>/parts',
      ext: '.min.js'
    }]
  },
  main_front_js4: {
    files: [{
      expand: true,
      cwd: '<%= paths.front_js_4 %>',
      src: ['tc-scripts.js'],
      //src: ['**/*.js', '!*.min.js'],
      dest: '<%= paths.front_js_4 %>',
      ext: '.min.js'
    }]
  },
  fmk_front_js4: {
    files: [{
      expand: true,
      cwd: '<%= paths.front_js_4 %>/fmk',
      src: ['**/*.js', '!*.min.js', '!*.part.js', '!OLD/**', '!*.main.js'],
      dest: '<%= paths.front_js %>/fmk',
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
  prod_czr_js:{
    options : {//not sure about this, see how many comments it leaves in the flickity.min.js
      preserveComments: function(node, comment) {
        // preserve comments that start with a bang
        return /^!/.test( comment.value );
      },
    },
    files: [{
      expand: true,
      cwd: '<%= paths.czr_assets %>/js',
      src: ['*.js', '!*.min.js'],
      dest: '<%= paths.czr_assets %>/js',
      ext: '.min.js'
    }]
  },
  any_file : {
    files: { '<%= uglify_requested_paths.dest %>': ['<%= uglify_requested_paths.src %>']
      }
  }
};