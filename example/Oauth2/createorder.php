<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 8/14/18
 * Time: 3:46 PM
 */
require './common.php';

$modelId = 'YOUR_MODEL_ID';  // replace this
$materialId = 6; // 6 - White Natural Versatile Plastic


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


  // initialize items list
  $items = [];
  // adding a item to items list
  $items[] = ['materialId' => $materialId, 'modelId' => $modelId, 'quantity' => 1];

  // update these address data
  $postFields = [
    'items' => $items,
    'firstName' => 'John',
    'lastName' => 'Dude',
    'country' => 'US',
    'state' => 'NY',
    'city' => 'New York',
    'address1' => '419 Park Ave S',
    'address2' => 'Suite 900',
    'zipCode' => '10016',
    'phoneNumber' => '1234567890',
    'paymentMethod' => 'credit_card',
    'shippingOption' => 'Cheapest'
  ];


  $result = $client->placeOrder($postFields);

} catch (\Exception $e) {
  // printing error on screen
  echo 'Exception: '. $e->getMessage();
  exit();
}

echo '<pre>';
echo json_encode($result, JSON_PRETTY_PRINT);
echo '<pre>';