module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		clean: [ "dist/**" ],

		copy: {
			main: {
				files: [
					{
						src: [
							"./**",
							"!./node_modules/**",
							"!./vendor/**",
							"!./composer.*",
							"!./Gruntfile.js",
							"!./package*.json",
							"!./phpcs*.xml",
						],
						dest: "dist/<%= pkg.name %>/"
					}
				]
			}
		},

		compress: {
			options: {
				archive: "./dist/<%= pkg.name %>-<%= pkg.version %>.zip",
				mode: "zip"
			},
			all: {
				files: [{
					expand: true,
					cwd: "./dist/",
					date: new Date(),
					src: [ "<%= pkg.name %>/**" ]
				}]
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks("grunt-contrib-compress");
	grunt.loadNpmTasks("grunt-contrib-copy");

	grunt.registerTask("release", ["clean","copy","compress"]);

};
