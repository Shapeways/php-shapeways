<?php
/**
 * Created by PhpStorm.
 * User: johnny
 * Date: 8/14/18
 * Time: 3:45 PM
 */

require '../../vendor/autoload.php';

// Add your Client ID & Client Secret to the following code examples:
$clientId = 'YOUR_CLIENT_ID';  // replace this
$clientSecret = 'YOUR_CLIENT_SECRET';  // replace this
$baseApiUrl = 'http://api.shapeways.com'; // replace this

/** @var \Shapeways\Oauth2Client $client */
$client = new \Shapeways\Oauth2Client(
  $clientId,
  $clientSecret,
  null, null, null, $baseApiUrl
);

