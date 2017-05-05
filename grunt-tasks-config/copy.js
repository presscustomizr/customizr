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
      '!assets/czr/_dev/**',
      '!lang_pro/**',
      /*c4*/
      //'!core/**',
      '!templates/**',
      '!assets/back/**', //back of c4
      '!assets/shared/fonts/customizr/**', //new customizr fonts
      '!assets/front/css/**', //c4 css
      '!assets/front/scss/**', //c4 scss
      '!assets/front/js/vendors/**', //c4 js vendors
      '!assets/front/js/fmk/**' //c4 js fmk
      /*end c4*/
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
      '!assets/czr/_dev/**',
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
};