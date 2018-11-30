module.exports = {
    options: {// options
      eol: 'crlf', //or lf
      overwrite: true //orverwrite on the same file
    },
    concatenated_php : {
      files : { // Files to process: $dest : $source
          '' : [
              '<%= paths.inc_php_classic %>czr-init-ccat.php',
              '<%= paths.inc_php_classic %>czr-front-ccat.php',

              '<%= paths.core_php %>czr-admin-ccat.php',
              '<%= paths.core_php %>czr-customize-ccat.php',
              '<%= paths.core_php %>fmk-ccat.php',
              '<%= paths.core_php %>functions-ccat.php'
        ]
      }
    },
    front_css_modern: {// Task
      files: { // Files to process: $dest : $source
        '': [
          '<%= paths.front_css_modern %>style.css',
          '<%= paths.front_css_modern %>rtl.css',
          '<%= paths.front_css_modern %>style-front-placeholders.css'
        ]
      }
    },
    front_js : {
      files: {
        '': [
          '<%= paths.front_js_classic %>tc-scripts.js',
          '<%= paths.front_js_classic %>main-ccat.js',
        ],
      }
    },
    front_js_modern : {
      files: {
        '': [
          '<%= paths.theme_js_assets %>tc-scripts.js',
          '<%= paths.theme_js_assets %>main-ccat.js',
          '<%= paths.theme_js_assets %>libs/customizr-placeholders.js'
        ],
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