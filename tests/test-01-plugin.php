<?php
namespace webaware\disable_emails\Tests;

use Yoast\WPTestUtils\BrainMonkey\TestCase;
use webaware\disable_emails\Plugin;

class PluginTest extends TestCase {

	/**
	 * can get instance of plugin
	 */
	public function testPlugin() {
		$this->assertTrue(Plugin::getInstance() instanceof Plugin);
	}

	/**
	 * mock PHPMailer class has been loaded
	 */
	public function testMockLoaded() {
		$this->assertTrue(class_exists('webaware\\disable_emails\\PHPMailerMock', false));
	}

}
