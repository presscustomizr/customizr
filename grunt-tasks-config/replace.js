module.exports = {
	style: {
		src: [
			'style.css'
		],
		overwrite: true,
		replacements: [ {
			from: /^.* Version:.*$/m,
			to: '* Version: <%= pkg.version %>'
		} ]
	},
	other : {
		src: [
			'<%= paths.lang %>*.po'
		],
		overwrite: true,
		replacements: [ {
			from: /^.* Customizr v.*$/m,
			to: '"Project-Id-Version: * Customizr v<%= pkg.version %>\\n"'
		} ]
	},
	less: {
		src: [
			'<%= paths.less_classic %>**/*.less'
		],
		overwrite: true,
		replacements: [ {
			from: /^.* Customizr v.*$/m,
			to: ' * Customizr v<%= pkg.version %>'
		} ]
	},
  css: {
    src: [
      '<%= paths.front_css_classic %>**/*.css'
    ],
    overwrite: true,
    replacements: [ {
      from: /^.* Customizr v.*$/m,
      to: ' * Customizr v<%= pkg.version %>'
    } ]
  },
	//! the gitinfo task must be ran before the replace:readme task, to get Git info from a working copy and populate grunt.config with the data
	readme : {
		src: [
			'readme.md'
		],
		overwrite: true,
		replacements: [ {
			from: /^.*# Customizr v.*$/m,
			to: '# Customizr v<%= pkg.version %> [![Built with Grunt](https://cdn.gruntjs.com/builtwith.png)](http://gruntjs.com/)'
		} ]
	},
  readme_txt : {
    src: [
      'readme.txt'
    ],
    overwrite: true,
    replacements: [ {
      from: /^.*Stable tag: .*$/m,
      to: 'Stable tag: <%= pkg.version %>'
    } ]
  },
  czr_fmk_namespace : {
    src: [
      '<%= paths.core_php %>czr-base-fmk/czr-base-fmk.php'
    ],
    overwrite: true,
    replacements: [ {
      from: /^.*namespace Nimble;.*$/m,
      to: 'namespace czr_fn;'
    } ]
  },
};