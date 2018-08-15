<?php
session_start();

$_SESSION['token'] = NULL;
$_SESSION['secret'] = NULL;
header('Location: /');
exit();