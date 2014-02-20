<?php
require './common.php';

$authUrl = $client->connect();
if($authUrl){
    $_SESSION['token'] = $client->oauthToken;
    $_SESSION['secret'] = $client->oauthSecret;
    header('Location: ' . $authUrl);
    exit();
}

echo 'Error Obtaining OAuth Request Token';