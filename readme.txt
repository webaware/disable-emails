# Disable Emails
Contributors: webaware
Plugin Name: Disable Emails
Plugin URI: https://shop.webaware.com.au/downloads/disable-emails/
Author URI: https://shop.webaware.com.au/
Donate link: https://shop.webaware.com.au/donations/?donation_for=Disable+Emails
Tags: disable emails, block emails
Requires at least: 5.5
Tested up to: 6.4
Requires PHP: 5.6
Stable tag: 1.8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Stop WordPress from sending any emails. ANY!

## Description

Stop a WordPress website from sending any emails using the standard [wp_mail()](https://codex.wordpress.org/Function_Reference/wp_mail) function. No emails will be sent, not even for password resets or administrator notifications.

WordPress websites can send emails for a variety of reasons -- e.g user registration, password reset, enquiry form submission, e-commerce purchase -- but sometimes you don't want it to send anything at all. Some reasons for disabling all emails:

* demonstration websites that allow users to do things that normally send emails
* development / test websites with live data that might email real customers
* bulk-loading data into websites which might trigger emails
* adding new sites into multisite installations

> NB: if you need to run this plugin on WordPress 5.4 or earlier, and must install manually from a .zip file, please install version 1.6.3 which you can [download from the Advanced page for the plugin](https://wordpress.org/plugins/disable-emails/advanced/). Since version 1.7.0, WordPress 5.5 or later is required.

### Translations

Many thanks to the generous efforts of our translators:

* Chinese (zh-CN) -- [Cai_Miao](https://profiles.wordpress.org/cai_miao) and [the Chinese translation team](https://translate.wordpress.org/locale/zh-cn/default/wp-plugins/disable-emails/)
* Chinese (zh-TW) -- [the Chinese (Taiwan) translation team](https://translate.wordpress.org/locale/zh-tw/default/wp-plugins/disable-emails/)
* Czech (cs-CZ) -- [Rudolf Klusal](http://www.klusik.cz/)
* Dutch (nl_NL) -- [the Dutch translation team](https://translate.wordpress.org/locale/nl/default/wp-plugins/disable-emails/)
* English (en_CA) -- [the English (Canadian) translation team](https://translate.wordpress.org/locale/en-ca/default/wp-plugins/disable-emails/)
* English (en_GB) -- [the English (UK) translation team](https://translate.wordpress.org/locale/en-gb/default/wp-plugins/disable-emails/)
* French (fr_FR) -- [the French translation team](https://translate.wordpress.org/locale/fr/default/wp-plugins/disable-emails/)
* Korean (ko_KR) -- [the Korean translation team](https://translate.wordpress.org/locale/ko/default/wp-plugins/disable-emails/)
* Japanese (ja) -- [Cai_Miao](https://profiles.wordpress.org/cai_miao) and [the Japanese translation team](https://translate.wordpress.org/locale/ja/default/wp-plugins/disable-emails/)
* German (de-DE) -- [Peter Harlacher](http://helvetian.io/)
* Norwegian: Bokmål (nb-NO) -- [neonnero](http://www.neonnero.com/)
* Norwegian: Nynorsk (nn-NO) -- [neonnero](http://www.neonnero.com/)
* Russian (ru_RU) -- [the Russian translation team](https://translate.wordpress.org/locale/ru/default/wp-plugins/disable-emails/)
* Swedish (sv_SE) -- [the Swedish translation team](https://translate.wordpress.org/locale/sv/default/wp-plugins/disable-emails/)

If you'd like to help out by translating this plugin, please [sign up for an account and dig in](https://translate.wordpress.org/projects/wp-plugins/disable-emails).

## Installation

1. Either install automatically through the WordPress admin, or download the .zip file, unzip to a folder, and upload the folder to your /wp-content/plugins/ directory. Read [Installing Plugins](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex for details.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Optional: from the WordPress admin, navigate to Settings > Disable Emails and click the "Activate must-use plugin" if you want the plugin to always be activated, no matter what.

## Frequently Asked Questions

### Why am I still getting standard WordPress emails?

You probably have another plugin that adds its own implementation of the `wp_mail()` function. Try disabling some plugins.

In some circumstances, enabling the must-use plugin from settings will fix this, because must-use plugins load before other plugins.

### Standard WordPress emails have stopped, but some others still get sent

You probably have a plugin that is sending emails via some other method, like directly using the PHP `mail()` function, or directly implementing an SMTP client. Not much I can do about that...

### How does it work?

The plugin replaces the standard WordPress `wp_mail()` function with a function that sends no emails. Nada. Zip. Silence.

Behind the scenes, it creates a private copy of PHPMailer and allows the system to interact with it, but silently suppresses the functions that send emails. The standard WordPress filter and action hooks are supported, so plugins that register hooks for those will still function as normal. It just doesn't actually send any emails.

### Can I make it a must-use plugin?

Yes. Once you have activated the plugin, navigate to Settings > Disable Emails and click the "Activate must-use plugin". This will create a must-use plugin (mu-plugin) that ensures that Disable Emails is always loaded. This can be especially useful on development websites where the database is frequently refreshed from a live site which _does not_ have Disable Emails activated.

NB: if you activate the must-use plugin on a multisite, it will stop emails on all sites on the multisite! If you have multiple networks on your multisite, the must-use plugin will stop emails on all networks.

### Contributions

* [Translate into your preferred language](https://translate.wordpress.org/projects/wp-plugins/disable-emails)
* [Fork me on GitHub](https://github.com/webaware/disable-emails)

## Upgrade Notice

### 1.8.2

fixed deprecation warnings in PHP 8.1+

## Changelog

The full changelog can be found [on GitHub](https://github.com/webaware/disable-emails/blob/master/changelog.md). Recent entries:

### 1.8.2

Released 2023-11-22

* fixed: deprecation warnings in PHP 8.1+
