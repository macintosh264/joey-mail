<?php
require("rsatools.php");
$rsa = new RSATools;
$keyPair = $rsa->getRSAKeyPair();
echo "Generated Keys\n";
print_r($keyPair);
echo "\n RSA key pair is", $keyPair["verified"] ? ' valid' : ' INVAILD';
echo "\n";
$rsa->writeRSAKeysToFile($keyPair,'','');
echo "Written to files in this directory. Copy RSAPUBLIC and RSAPIVATE to the server directory and copy RSAPUBLIC to any directory where you send mail from along with all the files in the directory sendmail_scripts";
?>
