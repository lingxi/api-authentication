<?php

require __dir__ . '/vendor/autoload.php';

$data = [
    'cid'       => 12,
    'stamp'     => time(),
    'noncestr'  => 'thisisademode',
    'app_key'   => '121212'
];

natsort($data);

$api_secret = '12';
$signature = hash_hmac("sha256", http_build_query($data), $api_secret);
$data['signature'] = $signature;
$data['version'] = 'v1';

$auther = new \Lingxi\Signature\Authenticator('v1');
$auther->app_secret = $api_secret;

var_dump($auther->attempt($data));

