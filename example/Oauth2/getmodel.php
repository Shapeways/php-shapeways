<?php
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
  $model = $client->getModelInfo($modelId);
} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

echo '<pre>';
echo json_encode($model, JSON_PRETTY_PRINT);
echo '<pre>';
