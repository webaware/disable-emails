<?php
/*
Plugin Name: Disable Emails
Description: Stop WordPress from sending any emails. ANY!
Version: 1.0.0
Author: WebAware
Author URI: http://www.webaware.com.au/
*/

/*
copyright (c) 2014 WebAware Pty Ltd (email : rmckay@webaware.com.au)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!function_exists('wp_mail')) {

	function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	}

}
