=== Disable Emails ===
Contributors: webaware
Plugin Name: Disable Emails
Plugin URI: http://wordpress.org/plugins/disable-emails/
Author URI: http://webaware.com.au/
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3QCACKDYDV6VN
Tags: disable emails, block emails
Requires at least: 3.6.1
Tested up to: 3.8.1
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Stop WordPress from sending any emails. ANY!

== Description ==

Stop a WordPress website from sending any emails using the standard [wp_mail()]() function. No emails will be sent, not even for password resets or administrator notifications.

WordPress websites can send emails for a variety of reasons -- e.g user registration, password reset, enquiry form submission, e-commerce purchase -- but sometimes you don't want it to send anything at all. Some reasons for disabling all emails:

* demonstration websites that allow users to do things that normally send emails
* development / test websites with live data that might email real customers
* bulk-loading data into websites which might trigger emails
* adding new sites into multisite installations

== Installation ==

1. Either install automatically through the WordPress admin, or download the .zip file, unzip to a folder, and upload the folder to your /wp-content/plugins/ directory. Read [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex for details.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Why am I still getting standard WordPress emails? =

You probably have another plugin that adds its own implementation of the `wp_mail()` function. Try disabling some plugins.

= Standard WordPress emails have stopped, but some others still get sent =

You probably have a plugin that is sending emails via some other method, like directly using the PHP `mail()` function, or SMTP. Not much I can do about that...

= How does it work? =

The plugin replaces the standard WordPress `wp_mail()` function with an empty function, which does nothing. Nada. Zip. Silence.

== Changelog ==

= 1.1.0 [2014-02-25] =
* fixed: `wp_mail()` now returns true, simulating a successful email attempt
* added: support filter hook `wp_mail` so that listeners can act, e.g. log emails (even though they will not be sent); can be turned off in settings

= 1.0.0 [2014-02-18] =
* initial release
