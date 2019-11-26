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

      '!lang_pro/**',

      /*modern style dev*/
      '!core/_dev/**',

      '!core/init-pro.php',

      '!assets/front/js/_front_js_fmk/**', //front js modern and classic dev fmk
      '!assets/front/js/_parts/**', //front js modern and classic dev parts

      // only needed for dev
      '!assets/czr/_dev/**',
      '!assets/front/js/libs/jquery-plugins/**',

      // modern
      '!assets/front/js/libs/bootstrap-classical.js',
      '!assets/front/js/libs/flickity-pkgd.js',
      '!assets/front/js/libs/jquery-magnific-popup.js',
      '!assets/front/js/libs/jquery-mCustomScrollbar.js',
      '!assets/front/js/libs/waypoints.js',
      '!assets/front/js/libs/holder.js',
      '!assets/front/js/libs/modernizr.js',
      '!assets/front/js/libs/smoothscroll.js',

      // classic
      '!inc/assets/less/**',
      '!assets/front/js/libs/fancybox/jquery.fancybox-1.3.4.css',
      '!assets/front/js/libs/fancybox/jquery.fancybox-1.3.4.js',
      '!inc/assets/js/main-ccat.js',
      '!inc/assets/js/main-ccat.min.js',

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