<?php
namespace webaware\disable_emails\Tests;

use Yoast\WPTestUtils\BrainMonkey\TestCase;
use webaware\disable_emails\EmailAddress;

/**
 * test the EmailAddress class
 */
class EmailAddressTest extends TestCase {

	/**
	 * ensure that environment has been specified
	 */
	public function testEnvironment() : void {
		global $plugin_test_env;

		$this->assertArrayHasKey('email_sender', $plugin_test_env);
	}

	/**
	 * test simplistic address-only email address
	 * @depends testEnvironment
	 */
	public function testSimple() : void {
		global $plugin_test_env;

		$address = new EmailAddress($plugin_test_env['email_sender']);

		$this->assertEquals($address->name, '');
		$this->assertEquals($address->address, $plugin_test_env['email_sender']);
	}

	/**
	 * test basic Name <address> email address
	 * @depends testEnvironment
	 */
	public function testNameAddress() : void {
		global $plugin_test_env;

		$name = 'Test Only';
		$email_address = sprintf('%s <%s>', $name, $plugin_test_env['email_sender']);
		$address = new EmailAddress($email_address);

		$this->assertEquals($address->name, $name);
		$this->assertEquals($address->address, $plugin_test_env['email_sender']);
	}

	/**
	 * test quoted "Some Name" <address> email address
	 * @depends testEnvironment
	 */
	public function testQuotedNameAddress() : void {
		global $plugin_test_env;

		$name = '"Test Only"';
		$email_address = sprintf('%s <%s>', $name, $plugin_test_env['email_sender']);
		$address = new EmailAddress($email_address);

		$this->assertEquals($address->name, $name);
		$this->assertEquals($address->address, $plugin_test_env['email_sender']);
	}

	/**
	 * test email address with punctuation in the name
	 * @depends testEnvironment
	 */
	public function testPunctuatedName() : void {
		global $plugin_test_env;

		$name = 'Test Only';
		$punctuated = 'test+extra@example.com';
		$email_address = sprintf('%s <%s>', $name, $punctuated);
		$address = new EmailAddress($email_address);

		$this->assertEquals($address->name, $name);
		$this->assertEquals($address->address, $punctuated);
	}

}
