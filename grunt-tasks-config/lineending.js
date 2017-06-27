module.exports = {
    options: {// options
      eol: 'crlf', //or lf
      overwrite: true //orverwrite on the same file
    },
    concatenated_php : {
      files : { // Files to process: $dest : $source
          '' : [
              '<%= paths.inc_php %>czr-init.php',
              '<%= paths.inc_php %>czr-front.php',

              '<%= paths.core_php %>czr-admin.php',
              '<%= paths.core_php %>czr-customize.php',
              '<%= paths.core_php %>fmk.php',
              '<%= paths.core_php %>functions.php'
        ]
      }
    },
    front_css_modern: {// Task
      files: { // Files to process: $dest : $source
        '': ['<%= paths.front_css_modern %>style.css']
      }
    },
    front_js : {
      files: {
        '': ['<%= paths.front_js_classic %>tc-scripts.js'],
      }
    },
    front_js4 : {
      files: {
        '': ['<%= paths.theme_js_assets %>tc-scripts.js'],
      }
    },
    czr_js : {
      files : { // Files to process: $dest : $source
          '' : [
              '<%= paths.czr_assets %>js/czr-control.js',
              '<%= paths.czr_assets %>js/czr-preview.js',
              '<%= paths.czr_assets %>js/czr-control-modern.js',
              '<%= paths.czr_assets %>js/czr-preview-modern.js',
        ]
      }
    },
};