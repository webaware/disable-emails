module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		eslint: {
			all: [
				"Gruntfile.js",
				"es6/*.js"
			]
		},

		babel: {
			options: {
				presets: [
					'@babel/preset-env',
				]
			},
			dist: {
				files: [{
					"expand": true,
					"cwd": "es6",
					"src": ["**/*.js"],
					"dest": "js/",
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
					cwd: "js",
					dest: "js",
					src: [
						"*.js",
						"!*.min.js"
					],
					ext: '.min.js'
				}]
			}
		},

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

	grunt.loadNpmTasks("grunt-babel");
	grunt.loadNpmTasks("grunt-contrib-uglify");
	grunt.loadNpmTasks("grunt-eslint");
	grunt.loadNpmTasks('grunt-shell');

	grunt.registerTask("es6", ["babel","uglify"]);
	grunt.registerTask("release", ["shell:dist"]);
	grunt.registerTask("wpsvn", ["shell:wpsvn"]);

};
