<?php
//Long Running Server
require("class.phpmailer.php");
$index = "_quene5K1WT1DN2U0HDXZLZ381NTUTWCHM7GFQTVP6HC1OQOmemcached";
$fh = fopen('RSAPRIVATE', 'r') or die('RSAPRIVATE file not present\n\n');
$rsaPrivate = fread($fh, filesize('RSAPRIVATE'));
fclose($fh);
$fh = fopen('RSAPUBLIC', 'r') or die('RSAPUBLIC file not present\n\n');
$rsaPublic = fread($fh, filesize('RSAPUBLIC'));
fclose($fh);
echo $rsaPrivate;
echo "\n";
echo $rsaPublic;
echo "\n";
$checkInterval = 10;
$seqNumber = 0;
$messagesSent = 0;
$arguements = array(array('-d','debug'));
$argsRecived = array();
foreach ($argv as $arguement) {
foreach ($arguements as $arguement2) {
if ($arguement == $arguement2[0]) {
$argsRecived[$arguement2[1]] = true;
}
}
}
$debug = isset($argsRecived['debug']);
//Open Connection
$connection = new Memcached();
$connection->addServer('localhost',11211);
if ($debug) {
echo "WELCOME TO JOEY MAILER HANDLER\nTHIS PROGRAM RESPONDS TO THE INDEX $index AND THAT MAILER INDEX ONLY.\nCHECK_RATE:$checkInterval\n", print_r($connection->getStats(),true),"\n","SERVER ACTIVE\n******************\n\n";
}
else {
echo "MAIL SERVER STARTED IN SILENT MODE\nSTARTING DATA:\nINDEX NAME: $index\nCHECK_RATE:$checkInterval\n";
}
while (true) {
$messageIds = json_decode($connection->get($index));
if ($messageIds) {
foreach ($messageIds as $messageID) {
$pkeyid = openssl_get_privatekey($rsaPrivate);
echo "Found $messageID will decrypt\n";
$encryptedStrings = json_decode(base64_decode($connection->get($messageID)));
$decrypt = "";
foreach ($encryptedStrings as $stringToDecrypt) {
openssl_private_decrypt(base64_decode($stringToDecrypt),$tempString,$rsaPrivate);
$decrypt .= $tempString;
unset($tempString);
unset($stringToDecrypt);
}
openssl_sign($decrypt, $signature, $pkeyid);
openssl_free_key($pkeyid);
$messageData = get_object_vars(json_decode($decrypt));
if ($messageData != null) {
echo "Decrypted $messageID message: ", md5(json_encode($messageData)), " from ",md5(base64_encode(json_encode($encryptedStrings))), " signature ", base64_encode($signature),"\n";
unset($pkeyid);
unset($encryptedStrings);
unset($decrypt);
if (isset($messageData["smtp_settings"]) && isset($messageData["to"]) && $messageData["to"] != "" && isset($messageData["body"]) && $messageData["body"] != "") {
$mail = new PHPMailer();

$settings = get_object_vars($messageData["smtp_settings"]);
$host = $settings["host"];
$port = $settings["port"];
$ssl = ($settings["ssl"]) ? 'ssl://' : '';
$username = $settings["username"];
$password = $settings["password"];
$address = $messageData["to"];
$subject = $messageData["subject"];
$message = $messageData["body"];
$fromName = $messageData["fromName"];
$from = $messageData["fromAddress"];
$html = isset($messageData["html"]) && $messageData["html"];
$auth = isset($settings["username"]);
	$mail->IsSMTP(true);  // telling the class to use SMTP
	$mail->Host     = "$ssl$host:$port"; // SMTP server
	$mail->SMTPAuth = $auth;
	if ($auth) {
	$mail->Username = $username;
	$mail->Password = $password;
	}
	$mail->From     = $from;
	$mail->FromName = $fromName;
	$mail->AddAddress("$address");
	$mail->IsHTML($html);

	$mail->Subject  = $subject;
	$rsaSignature = $html ? '<br />===RSA Signature===<br />' . base64_encode($signature) . '<br />===RSA Signature===' : '\n===RSA Signature===\n' . base64_encode($signature) . '\n===RSA Signature===';
	$mail->Body = "$message$rsaSignature ";

	if(!$mail->Send()) {
	  echo "\nMessage was not sent.\n";
	  echo "Mailer error: " . $mail->ErrorInfo, "\n";
	echo "Message $messageID Invalid. Removing Message Permentantly\n";
	} else {
	if ($debug) {
	  echo "Sent\n";
	  }
	$messagesSent++;
	}
	}
	else {
	echo "Message $messageID Corrupt. Removing Message Permentantly\n";
	}
	$connection->delete($messageID);
	unset($signature);
	unset($settings);
	unset($host);
	unset($port);
	unset($ssl);
	unset($username);
	unset($password);
	unset($address);
	unset($subject);
	unset($message);
	unset($fromName);
	unset($from);
	unset($html);
	unset($auth);
	unset($mail);
}
unset($messageData);
unset($messageID);
}
$indexes = $connection->get($index);
$index911 = 0;
foreach ($messageIds as $mess) {
unset($indexes[$index911]);
$index911++;
}
$connection->set($index,json_encode($indexes));
}
unset($messageIds);
unset($indexes);
if ($debug) {
echo "Sequence $seqNumber\nMessages Sent: $messagesSent\nSleeping for $checkInterval seconds \nMem Usage: ",round(memory_get_usage(true)/1048576,2), "MB\n\n";
}
$seqNumber++;
sleep($checkInterval);
}
unset($seqNumber);
unset($checkInterval);
?>
?>
