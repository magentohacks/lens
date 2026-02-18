<?php
$to      = 'joshinirav139@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: info@mavisstagingserver.com' . "\r\n" .
    'Reply-To: info@mavisstagingserver.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$mail = mail($to, $subject, $message, $headers);
var_dump($mail);
?>