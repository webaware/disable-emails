<?php
namespace webaware\disable_emails\Tests;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * test sending emails; should "succeed" but the emails should not be received
 * NB: the automated test cannot test for this, email accounts must be checked after running tests
 * TODO: implement email logging via hooks, and verify addresses
 */
class SendEmailTest extends TestCase {

	/**
	 * ensure that environment has been specified
	 */
	public function testEnvironment() {
		global $plugin_test_env;

		$this->assertArrayHasKey('email_sender', $plugin_test_env);
		$this->assertArrayHasKey('email_recipient_1', $plugin_test_env);
		$this->assertArrayHasKey('email_recipient_2', $plugin_test_env);
		$this->assertArrayHasKey('email_recipient_3', $plugin_test_env);
		$this->assertArrayHasKey('email_recipient_4', $plugin_test_env);
	}

	/**
	 * single-recipient test
	 * @depends testEnvironment
	 */
	public function testSingle() {
		global $plugin_test_env;

		$from		= sprintf('Test Sender <%s>', $plugin_test_env['email_sender']);
		$to			= sprintf('Test Recipient <%s>', $plugin_test_env['email_recipient_1']);
		$subject	= 'Test single recipient';
		$message	= 'Test sending email with a single recipient';

		$headers	= [
			"From: $from",
		];

		$this->assertTrue(wp_mail($to, $subject, $message, $headers));
	}

	/**
	 * single-recipient with CC/BCC test
	 * @depends testEnvironment
	 */
	public function testSingleWithCC() {
		global $plugin_test_env;

		$from		= sprintf('Test Sender <%s>', $plugin_test_env['email_sender']);
		$to			= sprintf('Test Recipient <%s>', $plugin_test_env['email_recipient_1']);
		$cc			= sprintf('Test CC <%s>', $plugin_test_env['email_recipient_2']);
		$bcc		= sprintf('Test BCC <%s>', $plugin_test_env['email_recipient_3']);
		$subject	= 'Test single recipient';
		$message	= 'Test sending email with a single recipient';

		$headers	= [
			"From: $from",
			"CC: $cc",
			"BCC: $bcc",
		];

		$this->assertTrue(wp_mail($to, $subject, $message, $headers));
	}

	/**
	 * multiple recipients test
	 * @depends testEnvironment
	 */
	public function testMultiple() {
		global $plugin_test_env;

		$from		= sprintf('Test Sender <%s>', $plugin_test_env['email_sender']);
		$to			= implode(', ', [
			sprintf('Test Recipient 1 <%s>', $plugin_test_env['email_recipient_1']),
			$plugin_test_env['email_recipient_2'],
			$plugin_test_env['email_recipient_3'],
			$plugin_test_env['email_recipient_4'],
		]);
		$subject	= 'Test single recipient';
		$message	= 'Test sending email with a single recipient';

		$headers	= [
			"From: $from",
		];

		$this->assertTrue(wp_mail($to, $subject, $message, $headers));
	}

}
