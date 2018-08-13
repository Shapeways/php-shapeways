<?php
require '../vendor/autoload.php';

session_start();

$client = new \Shapeways\Client('CONSUMER KEY',
                                'CONSUMER SECRET',
                                'http://localhost/callback.php',
                                $_SESSION['token'],
                                $_SESSION['secret']);
