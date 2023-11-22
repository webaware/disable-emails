<?php

namespace webaware\disable_emails;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as phpmailerException;

if (!defined('ABSPATH')) {
	exit;
}

/**
 * mock of PHPMailer, to support hookers that need to access PHPMailer properties
 * uses a private instance of PHPMailer, but doesn't permit sending emails through it
 */
class PHPMailerMock {

	private $phpmailer;
	private $allowed_calls;

	/**
	 * initialise mock object, creating private PHPMailer instance to handle allowed calls and properties
	 */
	public function __construct() {

		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
		$this->phpmailer = new PHPMailer( true );

		// build map of allowed function calls
		$this->allowed_calls = array_flip([
			'isHTML',
			'addAddress',
			'addCC',
			'addBCC',
			'addReplyTo',
			'setFrom',
			'addrAppend',
			'addrFormat',
			'wrapText',
			'utf8CharBoundary',
			'setWordWrap',
			'createHeader',
			'getMailMIME',
			'getSentMIMEMessage',
			'createBody',
			'headerLine',
			'textLine',
			'addAttachment',
			'getAttachments',
			'encodeString',
			'encodeHeader',
			'hasMultiBytes',
			'base64EncodeWrapMB',
			'encodeQP',
			'encodeQ',
			'addStringAttachment',
			'addEmbeddedImage',
			'addStringEmbeddedImage',
			'inlineImageExists',
			'attachmentExists',
			'alternativeExists',
			'clearAddresses',
			'clearCCs',
			'clearBCCs',
			'clearReplyTos',
			'clearAllRecipients',
			'clearAttachments',
			'clearCustomHeaders',
			'setError',
			'rfcDate',
			'isError',
			'fixEOL',
			'addCustomHeader',
			'msgHTML',
			'html2text',
			'filenameToType',
			'mb_pathinfo',
			'set',
			'secureHeader',
			'normalizeBreaks',
			'sign',
			'DKIM_QP',
			'DKIM_Sign',
			'DKIM_HeaderC',
			'DKIM_BodyC',
			'DKIM_Add',
			'validateAddress',
		]);

	}

	/**
	 * simulate WordPress call to PHPMailer
	 * @param string|array $to Array or comma-separated list of email addresses to send message.
	 * @param string $subject Email subject
	 * @param string $message Message contents
	 * @param string|array $headers Optional. Additional headers.
	 * @param string|array $attachments Optional. Files to attach.
	 * @return bool
	 */
	public function wpmail($to, $subject, $message, $headers, $attachments) {
		$settings = get_plugin_settings();

		// get the site domain and get rid of www.
		if (isset($_SERVER['SERVER_NAME'])) {
			$sitename = strtolower( $_SERVER['SERVER_NAME'] );
			if ( substr( $sitename, 0, 4 ) === 'www.' ) {
				$sitename = substr( $sitename, 4 );
			}
		}
		else {
			// likely running from WP-CLI with no hostname set, so fake it
			$sitename = 'server-name.invalid';
		}

		// set default From name and address
		$this->phpmailer->FromName = 'WordPress';
		$this->phpmailer->From = 'wordpress@' . $sitename;

		// let hookers change the function arguments if settings allow
		if ($settings['wp_mail']) {
			extract( apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) ), EXTR_IF_EXISTS );
		}

		// allow hookers to see recipient addresses on mock PHPMailer object
		try {
			$this->_addTo($to);
		}
		catch (phpmailerException $e) {
			// NOP
		}

		// set mail's subject and body
		$this->phpmailer->Subject = $subject;
		$this->phpmailer->Body = $message;

		// headers
		if ( !empty( $headers ) ) {
			if ( !is_array( $headers ) ) {
				// Explode the headers out, so this function can take both
				// string headers and an array of headers.
				$headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
			}

			foreach ( $headers as $header ) {
				// check for pseudo-headers
				if ( strpos($header, ':') === false ) {
					// TODO: handle multipart boundaries
					continue;
				}

				list( $name, $content ) = explode( ':', trim( $header ), 2 );
				$name    = trim( $name    );
				$content = trim( $content );

				try {
					switch ( strtolower( $name ) ) {
						// Mainly for legacy -- process a From: header if it's there
						case 'from':
							$this->_setFrom( $content );
							break;

						case 'cc':
							$this->_addCC( explode( ',', $content ) );
							break;

						case 'bcc':
							$this->_addBCC( explode( ',', $content ) );
							break;

						case 'content-type':
							$this->_setContentType( $content );
							break;

						default:
							$this->phpmailer->AddCustomHeader( "$name: $content" );
							break;
					}
				}
				catch (phpmailerException $e) {
					continue;
				}
			}
		}

		// attachments
		if ( !empty( $attachments ) ) {
			foreach ( $attachments as $attachment ) {
				try {
					$this->phpmailer->AddAttachment($attachment);
				}
				catch ( phpmailerException $e ) {
					continue;
				}
			}
		}

		if ($settings['wp_mail_from']) {
			$this->phpmailer->From = apply_filters( 'wp_mail_from', $this->phpmailer->From );
		}
		if ($settings['wp_mail_from_name']) {
			$this->phpmailer->FromName = apply_filters( 'wp_mail_from_name', $this->phpmailer->FromName );
		}
		if ($settings['wp_mail_content_type']) {
			$this->phpmailer->ContentType = apply_filters( 'wp_mail_content_type', $this->phpmailer->ContentType );
		}
		if ($settings['wp_mail_charset']) {
			$this->phpmailer->CharSet = apply_filters( 'wp_mail_charset', $this->phpmailer->CharSet );
		}
		if ($settings['phpmailer_init']) {
			do_action('phpmailer_init', $this->phpmailer);
		}

		return true;
	}

	/**
	 * set a different From address and potentially, name
	 * @param string $from
	 */
	protected function _setFrom($from) {
		$recipient = new EmailAddress($from);
		$this->phpmailer->FromName = trim($recipient->name);
		$this->phpmailer->From = $recipient->address;
	}

	/**
	 * add To address(es)
	 * @param string|array $to
	 */
	protected function _addTo($to) {
		$recipients = is_array($to) ? $to : explode(',', $to);

		foreach ($recipients as $address) {
			$recipient = new EmailAddress($address);
			$this->phpmailer->addAddress($recipient->address, $recipient->name);
		}
	}

	/**
	 * add CC address(es)
	 * @param array $addresses
	 */
	protected function _addCC($addresses) {
		foreach ($addresses as $address) {
			try {
				$recipient = new EmailAddress($address);
				$this->phpmailer->addCC($recipient->address, $recipient->name);
			}
			catch ( phpmailerException $e ) {
				continue;
			}
		}
	}

	/**
	 * add BCC addresses
	 * @param array $addresses
	 */
	protected function _addBCC($addresses) {
		foreach ($addresses as $address) {
			try {
				$recipient = new EmailAddress($address);
				$this->phpmailer->addBCC($recipient->address, $recipient->name);
			}
			catch ( phpmailerException $e ) {
				continue;
			}
		}
	}

	/**
	 * set content type
	 * @param string $content_type
	 */
	protected function _setContentType($content_type) {
		if ( strpos( $content_type, ';' ) !== false ) {
			list( $type, $charset ) = explode( ';', $content_type );

			$this->phpmailer->ContentType = trim( $type );

			if ( false !== stripos( $charset, 'charset=' ) ) {
				$this->phpmailer->CharSet = trim( str_ireplace( ['charset=', '"'], '', $charset ) );
			}
		}
		else {
			$this->phpmailer->ContentType = trim( $content_type );
		}
	}

	/**
	 * passthrough for setting PHPMailer properties
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->phpmailer->$name = $value;
	}

	/**
	 * passthrough for getting PHPMailer properties
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->phpmailer->$name;
	}

	/**
	 * catchall for methods we just want to ignore
	 */
	public function __call($name, $args) {
		if (isset($this->allowed_calls[$name])) {

			switch (count($args)) {
				case 1:
					return $this->phpmailer->$name($args[0]);

				case 2:
					return $this->phpmailer->$name($args[0], $args[1]);

				case 3:
					return $this->phpmailer->$name($args[0], $args[1], $args[2]);

				case 4:
					return $this->phpmailer->$name($args[0], $args[1], $args[2], $args[3]);

				case 5:
					return $this->phpmailer->$name($args[0], $args[1], $args[2], $args[3], $args[4]);

				case 6:
					return $this->phpmailer->$name($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);

				default:
					return $this->phpmailer->$name();
			}

		}

		return false;
	}

	/**
	 * catchall for methods we just want to ignore
	 */
	public static function __callStatic($name, $args) {
		return false;
	}

}
