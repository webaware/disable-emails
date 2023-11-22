# Disable Emails

## Changelog

### 1.8.2, 2023-11-22

* fixed: deprecation warnings in PHP 8.1+

### 1.8.1, 2022-05-26

* fixed: fatal exception when an email address was invalid

### 1.8.0, 2022-02-16

* fixed: mock PHPMailer object did not have recipient addresses for developers to inspect (thanks, [SpartakusMd](https://wordpress.org/support/users/spartakusmd/)!)

### 1.7.0, 2020-08-11

* fixed: WordPress 5.5 compatibility

### 1.6.3, 2020-03-19

* fixed: unhandled `phpmailerException` exceptions (thanks, [y0uri](https://wordpress.org/support/users/y0uri/)!)

### 1.6.2, 2020-03-10

* fixed: activating the must-use plugin throws an error if the mu-plugins folder is missing
* changed: can now enable both a Toolbar indicator and a site-wide admin notice when emails are disabled
* changed: filter hook `disable_emails_indicator` also accepts 'notice_toolbar' to enable both notice and Toolbar indicator

### 1.6.1, 2019-12-21

* fixed: Toolbar indicator has no visible slash in Sunrise admin theme
* fixed: undefined function `is_plugin_active_for_network()` (thanks [isabelc](https://github.com/isabelc)!)

### 1.6.0, 2019-12-15

* fixed: undefined index for `$_SERVER['SERVER_NAME']` when emails sent during wp-cli
* added: indicator setting to show either a Toolbar indicator or a site-wide admin notice when emails are disabled
* added: filter hook `disable_emails_indicator` for setting the indicator from code; accepts 'none', 'toolbar', 'notice'

### 1.5.0, 2019-11-11

* fixed: PHP notice -- Trying to get property 'ErrorInfo' of non-object
* changed: requires minimum PHP 5.6; recommend PHP 7.3+
* added: support for running the plugin as a must-use plugin (mu-plugin)

### 1.4.0, 2018-11-21

* added: setting to force Events Manager to use `wp_mail()` so that its emails can be disabled
* tested: WordPress 5.0

### 1.3.0, 2016-11-21

* added: setting to force BuddyPress to use `wp_mail()` so that its emails can be disabled

### 1.2.5, 2015-12-02

* added: Chinese translation (thanks, [Cai_Miao](https://profiles.wordpress.org/cai_miao)!)
* added: Japanese translation (thanks, [Cai_Miao](https://profiles.wordpress.org/cai_miao)!)
* added: status message on At A Glance dashboard metabox when emails are disabled

### 1.2.4, 2015-02-28

* added: German translation (thanks, [Peter Harlacher](http://helvetian.io/)!)

### 1.2.3, 2014-11-03

* added: Czech translation (thanks, [Rudolf Klusal](http://www.klusik.cz/)!)

### 1.2.2, 2014-08-31

* added: Norwegian translations (thanks, [neonnero](http://www.neonnero.com/)!)

### 1.2.1, 2014-06-22

* added: warn when wp_mail() can't be replaced, so admin knows that emails cannot be disabled

### 1.2.0, 2014-04-19

* changed: refactored to fully support filter and action hooks that other plugins might use to listen to and modify emails, e.g. so that email loggers can properly record what would have been sent

### 1.1.0, 2014-02-25

* fixed: `wp_mail()` now returns true, simulating a successful email attempt
* added: support filter hook `wp_mail` so that listeners can act, e.g. log emails (even though they will not be sent); can be turned off in settings

### 1.0.0, 2014-02-18

* initial release
