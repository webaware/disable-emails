=== Disable Emails ===
Contributors: webaware
Plugin Name: Disable Emails
Plugin URI: http://shop.webaware.com.au/downloads/disable-emails/
Author URI: http://webaware.com.au/
Donate link: http://shop.webaware.com.au/downloads/disable-emails/
Tags: disable emails, block emails
Requires at least: 3.6.1
Tested up to: 4.0
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Stop WordPress from sending any emails. ANY!

== Description ==

Stop a WordPress website from sending any emails using the standard [wp_mail()](http://codex.wordpress.org/Function_Reference/wp_mail) function. No emails will be sent, not even for password resets or administrator notifications.

WordPress websites can send emails for a variety of reasons -- e.g user registration, password reset, enquiry form submission, e-commerce purchase -- but sometimes you don't want it to send anything at all. Some reasons for disabling all emails:

* demonstration websites that allow users to do things that normally send emails
* development / test websites with live data that might email real customers
* bulk-loading data into websites which might trigger emails
* adding new sites into multisite installations

= Translations =

Many thanks to the generous efforts of our translators:

* Norwegian: Bokmål (nb_NO) -- [neonnero](http://www.neonnero.com/)
* Norwegian: Nynorsk (nn_NO) -- [neonnero](http://www.neonnero.com/)

If you'd like to help out by translating this plugin, please [sign up for an account and dig in](http://translate.webaware.com.au/projects/disable-emails).

== Installation ==

1. Either install automatically through the WordPress admin, or download the .zip file, unzip to a folder, and upload the folder to your /wp-content/plugins/ directory. Read [Installing Plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex for details.
2. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Why am I still getting standard WordPress emails? =

You probably have another plugin that adds its own implementation of the `wp_mail()` function. Try disabling some plugins.

= Standard WordPress emails have stopped, but some others still get sent =

You probably have a plugin that is sending emails via some other method, like directly using the PHP `mail()` function, or directly implementing an SMTP client. Not much I can do about that...

= How does it work? =

The plugin replaces the standard WordPress `wp_mail()` function with a function that sends no emails. Nada. Zip. Silence.

Behind the scenes, it creates a private copy of PHPMailer and allows the system to interact with it, but silently suppresses the functions that send emails. The standard WordPress filter and action hooks are supported, so plugins that register hooks for those will still function as normal. It just doesn't actually send any emails.

== Contributions ==

* [Translate into your preferred language](http://translate.webaware.com.au/projects/disable-emails)
* [Fork me on GitHub](https://github.com/webaware/disable-emails)

== Changelog ==

= 1.2.2 [2014-08-31] =
* added: Norwegian translations (thanks, [neonnero](http://www.neonnero.com/)!)

= 1.2.1 [2014-06-22] =
* added: warn when wp_mail() can't be replaced, so admin knows that emails cannot be disabled

= 1.2.0 [2014-04-19] =
* changed: refactored to fully support filter and action hooks that other plugins might use to listen to and modify emails, e.g. so that email loggers can properly record what would have been sent

= 1.1.0 [2014-02-25] =
* fixed: `wp_mail()` now returns true, simulating a successful email attempt
* added: support filter hook `wp_mail` so that listeners can act, e.g. log emails (even though they will not be sent); can be turned off in settings

= 1.0.0 [2014-02-18] =
* initial release
