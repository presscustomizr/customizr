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
			'!grunt-tasks/**',
			'!**/*.db'
		],
		dest: 'build/<%= pkg.name %>/'
	}
};