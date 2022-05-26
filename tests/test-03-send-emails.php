<?php
namespace webaware\disable_emails\Tests;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * test sending emails; should "succeed" but the emails should not be received
 * NB: the automated test cannot test sending is blocked; email accounts must be checked after running tests
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
	 * single-recipient test with simple addresses
	 * @depends testEnvironment
	 */
	public function testSingleSimple() {
		global $plugin_test_env;

		$from		= $plugin_test_env['email_sender'];
		$to			= $plugin_test_env['email_recipient_1'];
		$subject	= 'Test single recipient';
		$message	= 'Test sending email with a single recipient';

		$headers	= [
			"From: $from",
		];

		$logger = new EmailLog();
		$logger->addHooks();
		wp_mail($to, $subject, $message, $headers);
		$logger->removeHooks();

		$this->assertEquals($logger->from, $from);
		$this->assertEquals($logger->to, $to);
		$this->assertEquals($logger->cc, '');
		$this->assertEquals($logger->bcc, '');
		$this->assertEquals($logger->subject, $subject);
		$this->assertEquals($logger->message, $message);
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

		$logger = new EmailLog();
		$logger->addHooks();
		wp_mail($to, $subject, $message, $headers);
		$logger->removeHooks();

		$this->assertEquals($logger->from, $from);
		$this->assertEquals($logger->to, $to);
		$this->assertEquals($logger->cc, '');
		$this->assertEquals($logger->bcc, '');
		$this->assertEquals($logger->subject, $subject);
		$this->assertEquals($logger->message, $message);
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

		$logger = new EmailLog();
		$logger->addHooks();
		wp_mail($to, $subject, $message, $headers);
		$logger->removeHooks();

		$this->assertEquals($logger->from, $from);
		$this->assertEquals($logger->to, $to);
		$this->assertEquals($logger->cc, $cc);
		$this->assertEquals($logger->bcc, $bcc);
		$this->assertEquals($logger->subject, $subject);
		$this->assertEquals($logger->message, $message);
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

		$logger = new EmailLog();
		$logger->addHooks();
		wp_mail($to, $subject, $message, $headers);
		$logger->removeHooks();

		$this->assertEquals($logger->from, $from);
		$this->assertEquals($logger->to, $to);
		$this->assertEquals($logger->cc, '');
		$this->assertEquals($logger->bcc, '');
		$this->assertEquals($logger->subject, $subject);
		$this->assertEquals($logger->message, $message);
	}

	/**
	 * single-recipient, bad addresses
	 * @depends testEnvironment
	 */
	public function testBadAddresses() {
		$from		= 'Bad Test 1 <root>';
		$subject	= 'Test bad addresses';
		$message	= 'Test sending email with bad addresses';

		$headers	= [
			"From: $from",
		];

		$logger = new EmailLog();
		$logger->addHooks();

		$to			= '';
		wp_mail($to, $subject, $message, $headers);
		$this->assertEquals($logger->to, $to);

		$to			= 'local_user';
		wp_mail($to, $subject, $message, $headers);
		$this->assertEquals($logger->to, $to);

		$logger->removeHooks();
	}

}
