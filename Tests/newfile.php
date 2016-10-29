<?php

//set_include_path(get_include_path() . PATH_SEPARATOR . 'C:\xampp\php\pear\phpseclib');

var_dump(get_include_path());

require('Crypt\RSA.php');

$rsa = new Crypt_RSA();

$plaintext = 'Hello World!';
$privatekey = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQCjfQKsF6ExR/zLUKa2qiIr8jwh9s36z5dXIg+S+iZygO3p8ZNHzJlKj9JhHOnp
8FxObW93JwKeoyd17J6Dep1t2vm9SJt+jAm3psNpM2a1fZVJSMhKJSj/S9cNL8AwL8CuAyioKs4R
XFFuq2ASm0gXd1Y6bKHSzeZ49N8onIwueQIDAQABAoGAAvSZ1YQY1yP6wy8qUF+LfhljMmb8isXx
cbMNLoZEpynDA0lpdPETLVijwDsuVFsSxB0w2GnVX4pKBpT4OZ7AFUqphgv1CpeVGXP+6YISZApb
D3yliPL4fwWYi/ttC2ceMylKhohm+Ol6kxYeUitoiOuft2FzE70SCOxZOU23QsECQQDX9PIru6hr
8p4WBq6D829BB4WHnP7K/pj6gCi/iUXNS8cEHml/mJtgOxbSX8aWFDfJFlCmMcp3/zzw35zM/BJh
AkEAwc18aPgie472UunjlPLKelSlS/D8e2ZPLWbB3xyJcBn7CiCzeavpmQSOeVofrKyJfBb3FQut
VZL3AOMnIX9DGQJBALfsnfQxNxf44jrQJgGraq1vwoHla/tnKtLuI8Y9G33lc/JGFIPfbTVgHee+
OlvHjFtu7fEdptrcPwLG77yFUwECQQCq3HIpzVIBYwoSEXh+kgsnDMdqi3zdglad7XFRNcSJ263y
wN/ajlD1ggnmPSmdv8O6bjjKCjB4OIih9KJEKwHJAkBJAFKDTpAWMXBmSWl95Ibkr+2aU6VUQcVS
jhQbDsAWLNOkIrZsS33SS5kEc3LSl6oLH/Lh3759YLkOONHqFFN4
-----END RSA PRIVATE KEY-----';

$publickey = '-----BEGIN PUBLIC KEY-----
MIGJAoGBAKN9AqwXoTFH/MtQpraqIivyPCH2zfrPl1ciD5L6JnKA7enxk0fMmUqP0mEc6enwXE5t
b3cnAp6jJ3XsnoN6nW3a+b1Im36MCbemw2kzZrV9lUlIyEolKP9L1w0vwDAvwK4DKKgqzhFcUW6r
YBKbSBd3VjpsodLN5nj03yicjC55AgMBAAE=
-----END PUBLIC KEY-----';

$rsa->loadKey($privatekey);
$encrypt_text = $rsa->encrypt($plaintext);

$rsa->loadKey($publickey);
$decrypt_text = $rsa->decrypt($encrypt_text);

echo 'INPUT: '.$plaintext.'<br /><br>';
echo 'ENCRYPTED: '.$encrypt_text.'<br />';
echo 'DECRYPTED: '.$decrypt_text.'<br /><br />';



?>
