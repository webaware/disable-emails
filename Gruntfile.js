module.exports = function (grunt) {

	grunt.initConfig({
		pkg: grunt.file.readJSON("package.json"),

		pot: {
			options: {
				text_domain: "disable-emails",
				package_name: "disable-emails",
				msgid_bugs_address: "translate@webaware.com.au",
				encoding: "UTF-8",
				dest: "languages/",
				keywords: [
					"gettext",
					"__",
					"_e",
					"_n:1,2",
					"_x:1,2c",
					"_ex:1,2c",
					"_nx:4c,1,2",
					"esc_attr__",
					"esc_attr_e",
					"esc_attr_x:1,2c",
					"esc_html__",
					"esc_html_e",
					"esc_html_x:1,2c",
					"_n_noop:1,2",
					"_nx_noop:3c,1,2",
					"__ngettext_noop:1,2"
				],
				comment_tag: "translators:"
			},
			files: {
				src: [
					"**/*.php",
					"!lib/**/*",
					"!node_modules/**/*"
				],
				expand: true
			}
		}

	});

	grunt.loadNpmTasks("grunt-pot");

	grunt.registerTask("default", [ "pot" ]);

};
