<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 8/14/18
 * Time: 3:46 PM
 */
require './common.php';

// generate an access token
try {
  $a = $client->generateAccessTokenClientCredentialGrant();
  $client->accessToken = $a->access_token;

} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

try {
  $materials = $client->getMaterials();
} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

echo '<pre>';
echo json_encode($materials, JSON_PRETTY_PRINT);
echo '<pre>';
