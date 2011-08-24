<?php
//Class
class MailerClass {
public $index = "_quene5K1WT1DN2U0HDXZLZ381NTUTWCHM7GFQTVP6HC1OQOmemcached";
private $m;
private $publicKey;
public function __construct() {
$this->m = new Memcached();
$this->m->addServer('localhost', 11211);
}
public function loadKey() {
$fh = fopen('RSAPUBLIC', 'r');
$this->publicKey = fread($fh, filesize('RSAPUBLIC'));
fclose($fh);
}
public function sendMail($mail /*Json String*/) {
$unencryptedArray = str_split($mail,10);
$encrypted = array();
foreach ($unencryptedArray as $unencryptedString) {
openssl_public_encrypt($unencryptedString,$encryptedString,$this->publicKey);
$encrypted[] = base64_encode($encryptedString);
$encrypt = null;
}
$mailToAdd = base64_encode(json_encode($encrypted));
$indexRand = rand();
$indexes = json_decode($this->m->get($this->index));
$indexes[] = $indexRand;
$this->m->set($indexRand, $mailToAdd);
$this->m->set($this->index, json_encode($indexes));
return $mailToAdd;
}
}
?>
