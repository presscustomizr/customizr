module.exports = {
	style: {
		src: [
			'style.css'
		],
		overwrite: true,
		replacements: [ {
			from: /^.*Version:.*$/m,
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
			'<%= paths.less %>skin_gen.less'
		],
		overwrite: true,
		replacements: [ {
			from: /^.* Customizr v.*$/m,
			to: ' * Customizr v <%= pkg.version %>'
		} ]
	}
};