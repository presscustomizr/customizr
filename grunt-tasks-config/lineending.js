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
    czr_js: {// Task
      files: { // Files to process: $dest : $source
        '': [
            '<%= paths.czr_assets %>fmk/js/czr-control.js',
            '<%= paths.czr_assets %>fmk/js/czr-preview.js',
        ]
      }
    }
};