<?php

namespace webaware\disable_emails;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * manage email addresses with potentially a name and address part
 */
class EmailAddress {

	public $name;
	public $address;

	public function __construct($email_address) {
		if (preg_match('/(.*)<(.+)>/', $email_address, $matches) && count($matches) === 3) {
			$this->name		= rtrim($matches[1], ' ');
			$this->address	= $matches[2];
		}
		else {
			$this->name		= '';
			$this->address	= $email_address;
		}
	}

}
