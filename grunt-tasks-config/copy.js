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
      '!.eslintrc.js',
      '!package-lock.json',
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
      '!inc/assets/less/**',
      '!assets/czr/_dev/**',
      '!lang_pro/**',

      /*modern style dev*/
      '!core/_dev/**',

      '!core/init-pro.php',

      '!assets/front/js/_front_js_fmk/**', //front js modern and classic dev fmk
      '!assets/front/js/_parts/**', //front js modern and classic dev parts

      /* don't deploy the dev css + sass files including custom bootstrap */
      '!assets/front/css/_dev/**',
      '!assets/front/scss/**'
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
      '!.eslintrc.js',
      '!package-lock.json',
      '!readme.md',
      '!readme.txt',
      '!screenshot.png',
      '!style.css',
      '!custom-skins/**',
      '!inc/_dev/**',
      '!assets/czr/_dev/**',

      /*modern style dev*/
      '!core/_dev/**',

      '!inc/lang/**',
      '!inc/assets/less/**',
      '!lang_pro/**',
      //'!assets/front/js/libs/**',

      '!assets/front/js/_front_js_fmk/**', //front js modern and classic dev fmk
      '!assets/front/js/_parts/**', //front js modern and classic dev parts

      /* don't deploy the dev css and sass including custom bootstrap */
      '!assets/front/css/_dev/**',
      '!assets/front/scss/**'
    ],
    dest: '../customizr-pro/'
  },
  pro_lang: {
    cwd : 'lang_pro/',
    src: [
      '*.po',
      '!it_IT.po',
      '!fr_FR.po'
    ],
    expand: true,
    dest: '../customizr-pro/lang'
  }
};