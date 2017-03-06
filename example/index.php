<?php
require './common.php';

if(!$_SESSION['token'] && !$_SESSION['secret']){
    header('Location: /login.php');
    exit();
}

$apiInfo = $client->getApiInfo();
header('Content-Type: application/json');
echo json_encode($apiInfo);
