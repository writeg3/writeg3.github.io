<?php

$data = $_POST;
$result = Array('status' => 'error');

if (!array_key_exists('email', $data)) {
    $result['error'] = 'empty_email';
    die(json_encode($result));
}

$email = trim($data['email']);

require_once('mailer.class.php');
$mailer = new Mailer();

if (!$mailer->validateEmail($email, $result['error'])) {
    die(json_encode($result));
}

$list_file = @fopen("subscription_list.txt", "r");

if ($list_file) {
    while ($line = fgets($list_file)) {
        $line_email = trim($line);

        if ($email == $line_email) {
            $result['error'] = 'already_subscribed';
            die(json_encode($result));
        }
    }

    fclose($list_file);
}

$file_text = $email . "\r\n";
$save_result = file_put_contents('subscription_list.txt', $file_text, FILE_APPEND);

if ($save_result)  {
    $result['status'] = 'success';
} else {
    $result['error'] = 'save';
    die(json_encode($result));
}

$config = require_once('config/email.config.php');

if ($config['subscription_email']['enabled']
    && !empty($config['name'])
    && !empty($config['email'])
    && !empty($config['subscription_email']['subject'])
    && !empty($config['subscription_email']['message'])
    ) {

    $mailer->setOptions($config);
    $send_result = $mailer->sendSubscriptionEmail($email);
}

die(json_encode($result));
?>