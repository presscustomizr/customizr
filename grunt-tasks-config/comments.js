module.exports = {
  front_assets_classic_js : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: true
    },
    src: [
      '<%= paths.front_js_classic %>main-ccat.js',
      '<%= paths.front_js_classic %>tc-scripts.js'
    ] // files to remove comments from
  },
  front_assets_modern_js : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: true
    },
    src: [
      '<%= paths.theme_js_assets %>main-ccat.js',
      '<%= paths.theme_js_assets %>tc-scripts.js'
    ] // files to remove comments from
  },
  czr_control_js : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: false
    },
    src: [ '<%= paths.czr_assets %>js/czr-control.js'] // files to remove comments from
  },
  czr_control_js_modern : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: false
    },
    src: [ '<%= paths.czr_assets %>js/czr-control-modern.js'] // files to remove comments from
  }
};