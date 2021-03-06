<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 8/14/18
 * Time: 3:46 PM
 */
require './common.php';

$modelId = 'YOUR_MODEL_ID';  // replace this


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
  $model = $client->getModels();
} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

echo '<pre>';
echo json_encode($model, JSON_PRETTY_PRINT);
echo '<pre>';
