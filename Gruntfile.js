module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		shell: {
			// @link https://github.com/sindresorhus/grunt-shell
			dist: {
				command: [
					"rm -rf .dist",
					"mkdir .dist",
					"git archive HEAD --prefix=<%= pkg.name %>/ --format=zip -9 -o .dist/<%= pkg.name %>-<%= pkg.version %>.zip",
				].join("&&")
			},
			wpsvn: {
				command: [
					"svn up .wordpress.org",
					"rm -rf .wordpress.org/trunk",
					"mkdir .wordpress.org/trunk",
					"git archive HEAD --format=tar | tar x --directory=.wordpress.org/trunk",
				].join("&&")
			}
		}

	});

	grunt.loadNpmTasks('grunt-shell');

	grunt.registerTask("release", ["shell:dist"]);
	grunt.registerTask("wpsvn", ["shell:wpsvn"]);

};
