<?php
$file='../css/Oui-Oui.txt';
$key = sodium_crypto_generichash_keygen();
file_put_contents($file, $key,FILE_APPEND | LOCK_EX);
echo $key;

?>
