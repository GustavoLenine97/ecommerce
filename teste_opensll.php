<?php
/*
$cypher = "aes-128-cbc";

$data = "data a encriptar";

$metodo = "aes123";

$key = "1234";

$input = '4026411311333';
$key = 'idkK4556kkUkrkt5';
$iv = 'fuKJU6758gjrufdh';
*/

// $encriptado = openssl_encrypt($cypher, $data, $metodo, $key);

// $enc = openssl_encrypt($str, 'aes-128-ecb', $data, OPENSSL_RAW_DATA);

$key = User::SECRET;

$input = $dataRecovery["idrecovery"];

OPENSSL_RAW_DATA, $iv;

$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));

// $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $input, MCRYPT_MODE_CBC, $iv);
$encrypted = openssl_encrypt($input, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
($dataRecovery["idrecovery" 'AES-128-CBC', User::SECRET,OPENSSL_RAW_DATA, $iv )
echo $encrypted;

?>