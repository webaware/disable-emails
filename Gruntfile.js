module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		eslint: {
			all: [
				"Gruntfile.js",
				"source/js/*.js"
			]
		},

		babel: {
			options: {
				sourceType: "script",
				presets: [
					'@babel/preset-env',
				]
			},
			dist: {
				files: [{
					"expand": true,
					"cwd": "source/js",
					"src": ["**/*.js"],
					"dest": "static/js/",
					"ext": ".js",
				}]
			}
		},

		uglify: {
			build: {
				options: {
					output: {
						ascii_only: true,
					},
					banner: "// <%= pkg.description %>\n// <%= pkg.homepage %>\n"
				},
				files: [{
					expand: true,
					cwd: "static/js",
					dest: "static/js",
					src: [
						"*.js",
						"!*.min.js"
					],
					ext: '.min.js'
				}]
			}
		}

	});

	grunt.loadNpmTasks("grunt-babel");
	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-eslint");

	grunt.registerTask("js", ["babel","uglify"]);

};
