<?php
/*
Plugin Name: Disable Emails
Plugin URI: https://shop.webaware.com.au/downloads/disable-emails/
Description: Stop WordPress from sending any emails. ANY!
Version: 1.8.2
Author: WebAware
Author URI: https://shop.webaware.com.au/
Text Domain: disable-emails
Domain Path: /languages/
*/

/*
copyright (c) 2014-2023 WebAware Pty Ltd (email : support@webaware.com.au)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH')) {
	exit;
}

// don't go any further if plugin is already loaded (e.g. mu-plugin)
if (defined('DISABLE_EMAILS_PLUGIN_FILE')) {
	return;
}

// phpcs:disable Modernize.FunctionCalls.Dirname.FileConstant
define('DISABLE_EMAILS_PLUGIN_FILE', __FILE__);
define('DISABLE_EMAILS_PLUGIN_ROOT', dirname(__FILE__) . '/');
define('DISABLE_EMAILS_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('DISABLE_EMAILS_MIN_PHP', '5.6');
define('DISABLE_EMAILS_VERSION', '1.8.2');

require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/functions-global.php';
require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/class.Requires.php';

if (version_compare(PHP_VERSION, DISABLE_EMAILS_MIN_PHP, '<')) {
	add_action('admin_notices', 'disable_emails_fail_php_version');
	return;
}

require DISABLE_EMAILS_PLUGIN_ROOT . 'includes/bootstrap.php';
