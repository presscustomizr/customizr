module.exports = {
	free: {
		src:  [
			'**',
			'!bin/**',
			'!build/**',
			'!grunt-tasks-config/**',
			'!node_modules/**',
			'!tests/**',
			'!wpcs/**',
			'!.git/**',
			'!gruntfile.js',
			'!package.json',
			'!.gitignore',
			'!.ftpauth',
			'!.travis.yml',
			'!travis-examples/**',
			'!phpunit.xml',
			'!readme.md',
			'!**/*.db',
      '!patches/**',
      '!inc/init-pro.php',
      '!custom-skins/**',
      '!inc/_dev/**',
      '!lang_pro/**'
		],
		dest: 'build/free/<%= pkg.name %>/'
	},
  pro: {
    src:  [
      '**',
      '!bin/**',
      '!build/**',
      '!grunt-tasks-config/**',
      '!node_modules/**',
      '!tests/**',
      '!wpcs/**',
      '!.git/**',
      '!.travis.yml',
      '!travis-examples/**',
      '!phpunit.xml',
      '!**/*.db',
      '!patches/**',
      '!.ftpauth',
      '!.gitignore',
      '!gruntfile.js',
      '!package.json',
      '!readme.md',
      '!readme.txt',
      '!screenshot.png',
      '!style.css',
      '!custom-skins/**',
      '!inc/_dev/**',
      '!inc/lang/**'
    ],
    dest: '../customizr-pro/'
  },
  pro_lang: {
    src:  [
      'lang_pro/**'
    ],
    dest: '../customizr-pro/inc/'
  },
//CZR
  czr_js : {
    expand: true,
    flatten: true,
    filter:'isFile',
    src: [
      '<%= paths.czr_assets %>fmk/js/**',
      '! <%= paths.czr_assets %>fmk/js/control_dev/**',
      '! <%= paths.czr_assets %>fmk/js/lib/**',
      '! <%= paths.czr_assets %>fmk/js/czr-pro-modules-control.js'
    ],
    dest: '<%= paths.czr_assets %>js/'
  },
  czr_css : {
    expand: true,
    flatten: true,
    filter:'isFile',
    src: [ '<%= paths.czr_assets %>fmk/css/*.css', '! <%= paths.czr_assets %>fmk/css/lib/**', '! <%= paths.czr_assets %>fmk/js/img/**' ],
    dest: '<%= paths.czr_assets %>css/'
  },
//end CZR
};