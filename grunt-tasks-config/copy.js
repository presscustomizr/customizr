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
      '!inc/init-pro.php'
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
      '!gruntfile.js',
      '!package.json',
      '!.gitignore',
      '!.ftpauth',
      '!.travis.yml',
      '!travis-examples/**',
      '!phpunit.xml',
      '!readme.md',
      '!**/*.db',
      '!patches/**'
    ],
    dest: 'build/pro/<%= pkg.name %>/'
  }
};