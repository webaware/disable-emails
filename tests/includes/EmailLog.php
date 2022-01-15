<?php
namespace webaware\disable_emails\Tests;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * capture some details from emails for comparison with what was requested
 */
class EmailLog {

	public $from;
	public $to;
	public $cc;
	public $bcc;
	public $subject;
	public $message;

	/**
	 * hook into the WordPress wp_mail hooks
	 */
	public function addHooks() {
		add_filter('wp_mail', [$this, 'wpMail'], 99999);
		add_action('phpmailer_init', [$this, 'phpmailerInit'], 99999);
	}

	/**
	 * unhook from WordPress
	 */
	public function removeHooks() {
		remove_filter('wp_mail', [$this, 'wpMail'], 99999);
		remove_action('phpmailer_init', [$this, 'phpmailerInit'], 99999);
	}

	/**
	 * clear the recorded email properties
	 */
	public function clear() {
		foreach (array_keys(get_object_vars($this)) as $name) {
			$this->$name = '';
		}
	}

	/**
	 * log some details from wp_mail() call
	 * @return array
	 */
	public function wpMail(Array $args) {
		$this->subject		= $args['subject'];
		$this->message		= $args['message'];
		$this->to			= $args['to'];
		$this->cc			= [];
		$this->bcc			= [];

		foreach ($args['headers'] as $header) {
			list($header, $value) = explode(':', $header, 2);
			switch (trim(strtolower($header))) {

				case 'cc':
					$this->cc[] = trim($value);
					break;

				case 'bcc':
					$this->bcc[] = trim($value);
					break;

			}
		}

		// convert arrays to strings
		if (is_array($this->to)) {
			$this->to = implode(', ', $this->to);
		}
		$this->cc  = empty($this->cc)  ? '' : implode(', ', $this->cc);
		$this->bcc = empty($this->bcc) ? '' : implode(', ', $this->bcc);

		return $args;
	}

	/**
	 * capture email sender
	 */
	public function phpmailerInit(PHPMailer $phpmailer) {
		if ($phpmailer->FromName) {
			$this->from = sprintf('%s <%s>', $phpmailer->FromName, $phpmailer->From);
		}
		else {
			$this->from = $phpmailer->From;
		}
	}

}
