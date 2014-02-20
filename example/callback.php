<?php
require './common.php';

if($client->verifyUrl($_SERVER['REQUEST_URI'])){
    $_SESSION['token'] = $client->oauthToken;
    $_SESSION['secret'] = $client->oauthSecret;
    header('Location: /index.php');
    exit();
}
echo 'Error Obtaining OAuth Access Token';