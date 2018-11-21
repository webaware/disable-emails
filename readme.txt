# Disable Emails
Contributors: webaware
Plugin Name: Disable Emails
Plugin URI: https://shop.webaware.com.au/downloads/disable-emails/
Author URI: https://shop.webaware.com.au/
Donate link: https://shop.webaware.com.au/donations/?donation_for=Disable+Emails
Tags: disable emails, block emails
Requires at least: 4.0
Tested up to: 5.0
Requires PHP: 5.3
Stable tag: 1.4.0
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

### Translations

Many thanks to the generous efforts of our translators:

* Chinese (zh-CN) -- [Cai_Miao](https://profiles.wordpress.org/cai_miao) and [the Chinese translation team](https://translate.wordpress.org/locale/zh-cn/default/wp-plugins/disable-emails)
* Czech (cs-CZ) -- [Rudolf Klusal](http://www.klusik.cz/)
* Dutch (nl_NL) -- [the Dutch translation team](https://translate.wordpress.org/locale/nl/default/wp-plugins/disable-emails)
* English (en_CA) -- [the English (Canadian) translation team](https://translate.wordpress.org/locale/en-ca/default/wp-plugins/disable-emails)
* Korean (ko_KR) -- [the Korean translation team](https://translate.wordpress.org/locale/ko/default/wp-plugins/disable-emails)
* Japanese (ja) -- [Cai_Miao](https://profiles.wordpress.org/cai_miao) and [the Japanese translation team](https://translate.wordpress.org/locale/ja/default/wp-plugins/disable-emails)
* German (de-DE) -- [Peter Harlacher](http://helvetian.io/)
* Norwegian: Bokmål (nb-NO) -- [neonnero](http://www.neonnero.com/)
* Norwegian: Nynorsk (nn-NO) -- [neonnero](http://www.neonnero.com/)
* Russian (ru_RU) -- [the Russian translation team](https://translate.wordpress.org/locale/ru/default/wp-plugins/disable-emails)
* Swedish (sv_SE) -- [the Swedish translation team](https://translate.wordpress.org/locale/sv/default/wp-plugins/disable-emails)

If you'd like to help out by translating this plugin, please [sign up for an account and dig in](https://translate.wordpress.org/projects/wp-plugins/disable-emails).

## Installation

1. Either install automatically through the WordPress admin, or download the .zip file, unzip to a folder, and upload the folder to your /wp-content/plugins/ directory. Read [Installing Plugins](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins) in the WordPress Codex for details.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Frequently Asked Questions

### Why am I still getting standard WordPress emails?

You probably have another plugin that adds its own implementation of the `wp_mail()` function. Try disabling some plugins.

### Standard WordPress emails have stopped, but some others still get sent

You probably have a plugin that is sending emails via some other method, like directly using the PHP `mail()` function, or directly implementing an SMTP client. Not much I can do about that...

### How does it work?

The plugin replaces the standard WordPress `wp_mail()` function with a function that sends no emails. Nada. Zip. Silence.

Behind the scenes, it creates a private copy of PHPMailer and allows the system to interact with it, but silently suppresses the functions that send emails. The standard WordPress filter and action hooks are supported, so plugins that register hooks for those will still function as normal. It just doesn't actually send any emails.

### Contributions

* [Translate into your preferred language](https://translate.wordpress.org/projects/wp-plugins/disable-emails)
* [Fork me on GitHub](https://github.com/webaware/disable-emails)

## Upgrade Notice

### 1.4.0

added setting to force Events Manager to use `wp_mail()` so that its emails can be disabled

## Changelog

The full changelog can be found [on GitHub](https://github.com/webaware/disable-emails/blob/master/changelog.md). Recent entries:

### 1.4.0

Released 2018-11-21

* added: setting to force Events Manager to use `wp_mail()` so that its emails can be disabled
* tested: WordPress 5.0
