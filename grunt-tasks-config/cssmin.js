module.exports = {
  options: {
    // compatibility: {
    //     properties: {
    //         spaceAfterClosingBrace: true
    //     }
    // }
  },
  dev_skin: {
    files: [
      {'<%= paths.front_css_classic %>grey.min.css' : '<%= paths.front_css_classic %>grey.css'}
    ]
  },
  dev_common: {
    files: [
      {'<%= paths.front_css_classic %>tc_common.min.css' : '<%= paths.front_css_classic %>tc_common.css'}
    ]
  },
  dev_main_c4: {
    files: [
      {'<%= paths.front_css_modern %>style.min.css' : '<%= paths.front_css_modern %>style.css'}
    ]
  },
  prod_front_c4: {
    expand: true,
    cwd: '<%= paths.front_css_modern %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.front_css_modern %>',
    ext: '.min.css'
  },
  prod_skins: {
    expand: true,
    cwd: '<%= paths.front_css_classic %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.front_css_classic %>',
    ext: '.min.css'
  },
  prod_common :{
    expand: true,
    cwd: '<%= paths.front_css_classic %>',
    src: ['tc_common.css'],
    dest: '<%= paths.front_css_classic %>',
    ext: '.min.css'
  },
  prod_common_rtl :{
    expand: true,
    cwd: '<%= paths.front_css_classic %>rtl/',
    src: ['tc_common.css'],
    dest: '<%= paths.front_css_classic %>rtl/',
    ext: '.min.css'
  },
  prod_admin_css: {
    expand: true,
    cwd: '<%= paths.admin_css %>',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.admin_css %>',
    ext: '.min.css'
  },
  prod_czr_css: {
    expand: true,
    cwd: '<%= paths.czr_assets %>/css',
    src: ['*.css', '!*.min.css'],
    dest: '<%= paths.czr_assets %>/css',
    ext: '.min.css'
  },
  custom_skin : {
    expand: true,
    cwd: 'custom-skins/',
    src: ['*.css', '!*.min.css'],
    dest: 'custom-skins/',
    ext: '.min.css'
  },
};