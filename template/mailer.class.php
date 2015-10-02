<?php

class Mailer {
	private $options;

	public function __construct($options = Array()) {
		$this->options = $options;
	}

	public function setOptions($options) {
		$this->options = $options;
	}

	private function makeHeaders($from_name, $from_email) {
		$headers = 'Content-type: text/html; charset="utf-8"' . "\r\n"
	    			. 'From: ' . $from_name . ' <' . $from_email . '>';

	    return $headers;
	}

	public function sendEmail($from_name, $from_email, $to_email, $subject, $message) {
	    $headers = $this->makeHeaders($from_name, $from_email);
	    $to_email = htmlspecialchars($to_email);

	    $send_result = mail($to_email, $subject, $message, $headers);

	    return $send_result;
	}

	public function sendNotificationEmail($to_email) {
		return $this->sendEmail($this->options['name'], $this->options['email'], $to_email,
								$this->options['notification_email']['subject'], $this->options['notification_email']['message']);
	}

	public function sendSubscriptionEmail($to_email) {
		return $this->sendEmail($this->options['name'], $this->options['email'], $to_email,
								$this->options['subscription_email']['subject'], $this->options['subscription_email']['message']);
	}

	public function validateEmail($email, &$error) {
		$email = trim($email);

		if (empty($email)) {
			$error = 'empty_email';

			return FALSE;
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    		$error = 'invalid_email';

    		return FALSE;
    	}

    	return TRUE;
	}
}

?>