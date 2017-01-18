module.exports = {
  czr_core_control_js : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: true
    },
    src: [ '<%= paths.czr_assets %>/fmk/js/czr-control.js'] // files to remove comments from
  },
  czr_pro_control_js : {
    // Target-specific file lists and/or options go here.
    options: {
        singleline: true,
        multiline: true
    },
    src: [ '<%= paths.czr_assets %>/fmk/js/czr-control-full.js'] // files to remove comments from
  }
};