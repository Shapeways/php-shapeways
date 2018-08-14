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

  $filename = "cube.stl";
  $file = file_get_contents("./" . $filename);

  $defaultMaterialId = 6;
  // generating request data
  $params = [
    "fileName" => "cube.stl",
    "file" => $file,
    "description" => "This is a nice cube!",
    "hasRightsToModel" => 1,
    "acceptTermsAndConditions" => 1
  ];


  $result = $client->uploadModel($params);

} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

echo '<pre>';
echo json_encode($result, JSON_PRETTY_PRINT);
echo '<pre>';
