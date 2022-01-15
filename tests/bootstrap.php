<?php

use Yoast\WPTestUtils\WPIntegration;

$plugin_main_dir = dirname(__DIR__);
$plugin_main_file = "$plugin_main_dir/disable-emails.php";

if (getenv('WP_PLUGIN_DIR') !== false) {
	define('WP_PLUGIN_DIR', getenv('WP_PLUGIN_DIR'));
}

require_once __DIR__ . '/includes/EmailLog.php';
require_once "$plugin_main_dir/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php";

$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

// Give access to tests_add_filter() function.
require_once $_tests_dir . 'includes/functions.php';

/**
 * load our plugin, and any others it requires
 */
tests_add_filter('muplugins_loaded', function() use ($plugin_main_file) {
	require $plugin_main_file;
});

/*
 * Bootstrap WordPress. This will also load the Composer autoload file, the PHPUnit Polyfills
 * and the custom autoloader for the TestCase and the mock object classes.
 */
WPIntegration\bootstrap_it();

if (!defined('WP_PLUGIN_DIR') || file_exists($plugin_main_file) === false) {
	echo PHP_EOL, 'ERROR: Please check whether the WP_PLUGIN_DIR environment variable is set and set to the correct value. The integration test suite won\'t be able to run without it.', PHP_EOL;
	exit(1);
}

/**
 * read testing environment configuration from .env file
 */
global $plugin_test_env;
$plugin_test_env = parse_ini_file(__DIR__ . '/.env');
