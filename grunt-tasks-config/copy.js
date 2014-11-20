module.exports = {
	main: {
		src:  [
			'**',
			'!node_modules/**',
			'!build/**',
			'!.git/**',
			'!gruntfile.js',
			'!package.json',
			'!.gitignore',
			'!.ftpauth',
			'!.travis.yml',
			'!grunt-tasks/**',
			'!tests/**',
			'!bin/**',
			'!travis-examples/**',
			'!wpcs/**',
			'!phpunit.xml',
			'!**/*.db'
		],
		dest: 'build/<%= pkg.name %>/'
	}
};