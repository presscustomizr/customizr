module.exports = {
    options: {// options
      eol: 'crlf', //or lf
      overwrite: true //orverwrite on the same file
    },
    front_css4: {// Task
      files: { // Files to process: $dest : $source
        '': ['<%= paths.front_css4 %>style.css']
      }
    },
    front_js : {
      files: {
        '': ['<%= paths.front_js %>tc-scripts.js'],
      }
    },
    czr_js : {
        files : { // Files to process: $dest : $source
          '' : [
              '<%= paths.czr_assets %>js/czr-control.js',
              '<%= paths.czr_assets %>js/czr-preview.js',
        ]
      }
    }
};