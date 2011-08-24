<?php
//Test.php
require("mail_class.php");
$mailer = new MailerClass();
$mailer->loadKey();
$message = array('to' => "test@example.com",'subject' => "test", 'html' => true, 'body' =>  "Body",'smtp_settings' => array('host' => 'smtp.gmail.com', 'port' => 465, 'ssl' => true, 'username' => "you@gmail.com", 'password' => 'yourpassword'), 'fromName' => 'your name', 'fromAddress' => "your e-mail address");
echo $mailer->sendMail(json_encode($message));
echo "\n\n";
echo "Added message to quene";
echo "\n";
?>
