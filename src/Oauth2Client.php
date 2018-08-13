<?php
/**
 * Shapeways API Oauth2 Client
 * @copyright 2018 Shapeways <api@shapeways.com> (http://developers.shapeways.com)
 */

namespace Shapeways;

/**
 * Exception raised when method parameters are not valid
 */
class ParameterValidationException extends \Exception
{
}

/**
 * API Oauth2Client for obtaining OAuth2 access token and making
 * API calls to api.shapeways.com
 */
class Oauth2Client
{

  /**
   * This determines where authentication requests will be sent and received by your app.
   * @var string $callbackUrl the oauth2 callback url
   */
  public $redirectUrl;

  /**
   * @var string $clientId
   * @var string $clientSecret
   */
  public $clientId, $clientSecret;

  /**
   * @var string $accessToken the oauth token used for requests
   * @var string $refreshSecret the oauth secret used for getting new access token
   */
  public $accessToken, $refreshToken;

  /**
   * @var string $baseUrl the api base url used to generate api urls
   */
  public $baseApiUrl = 'https://api.shapeways.com';

  /**
   * @var string $apiVersion the api version used to generate api urls
   */
  public $apiVersion = 'v1';

  /**
   * Create a new \Shapeways\Oauth2CurlClient
   *
   * @param string $clientId your app consumer key
   * @param string $clientSecret your app consumer secret
   * @param string|null $callbackUrl your app callback url
   * @param string|null $accessToken a users oauth token if it is already known
   * @param string|null $refreshToken if it is already known
   */
  public function __construct(
    $clientId,
    $clientSecret,
    $callbackUrl,
    $accessToken = null,
    $refreshToken = null
  ) {
    $this->clientId = $clientSecret;
    $this->clientSecret = $clientSecret;
    $this->callbackUrl = $callbackUrl;
    $this->accessToken = $accessToken;
    $this->refreshToken = $refreshToken;
  }

  /**
   * Grand Type 1: Resource owner credentials grant
   *
   * https://developers.shapeways.com/quick-start#authenticate
   *
   * Your don't need a redirect url for this grand type
   *
   * Use "access_token" from result for other API calls
   *
   * @return array - json decoded api response
   */
  public function generateAccessTokenClientCredentialGrant()
  {
    $params = array(
      "grant_type" => "client_credentials"
    );

    $url = $this->baseApiUrl . '/oauth2/token';


    $process = curl_init($url);
    curl_setopt($process, CURLOPT_USERPWD, $this->clientId . ":" . $this->clientSecret);
    curl_setopt($process, CURLOPT_TIMEOUT, 30);
    curl_setopt($process, CURLOPT_POST, 1);
    curl_setopt($process, CURLOPT_POSTFIELDS, $params);
    curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($process);
    curl_close($process);

    return json_decode($result);

  }


  /**
   * Grand Type 2: Authorization code grant
   * Step 1 - generate a authorization code
   *
   *
   * If you want to use this app for a different Shapeways User account.
   *
   *
   * @link https://developers.shapeways.come/quick-start#authenticate
   *
   * redirect_uri - This determines where authentication requests will be sent and received by your app.
   * Users will be redirected to this URL when they attempt to use your app.
   */
  public function generateAccessTokenAuthorizationGrant() {
    $clientId = $this->clientId;
    $url = $this->baseApiUrl . "/oauth2/authorize?response_type=code&client_id=" . $clientId . "&redirect_uri=" . rawurlencode($this->redirectUrl);
    header('Location: ' . $url);
  }


  /**
   * Grand Type 2: Authorization code grant
   * Step 2 - Generate access token
   *
   *  @link https://developers.shapeways.com/quick-start#authenticate
   *  function for handling call back of generateAccessTokenAuthorizationGrant() call above
   *
   * Use "access_token" from result for other API calls
   *
   * @return array - json decoded api response
   */
  public function handleAuthorizationGrantCallback() {
    $code = $_REQUEST['code'] ?? null;
    $redirectURL =  $this->redirectUrl;

    if ($code === null) {
      echo "Missing authorization code";
      exit();
    }
    $url = $this->baseApiUrl . '/oauth2/token';

    $data = array(
      "grant_type" => "authorization_code",
      "code" => $code,
      "client_id" => $this->clientId,
      "client_secret" => $this->clientSecret,
      "redirect_uri" => $redirectURL
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);

    curl_close($ch);
    return json_decode($result);
  }

  /**
   * Upload a model for the user
   *
   * @link https://developers.shapeways.com/api-reference#Models
   *
   * @param array $params the model data to set
   * @return array the json response from the api call
   * @throws ParameterValidationException
   */
  public function uploadModel($params)
  {
    $required = array('file', 'fileName', 'hasRightsToModel', 'acceptTermsAndConditions');
    foreach ($required as $key) {
      if (!array_key_exists($key, $params)) {
        throw new ParameterValidationException('Shapeways\Oauth2CurlClient::addModel missing required key: ' . $key);
      }
    }

    $params['file'] = rawurlencode(base64_encode($params['file']));

    // json encode
    $postData = json_encode($params);

    $url = $this->baseApiUrl . '/models/v1';

    $sh = curl_init($url);
    curl_setopt($sh, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($sh, CURLOPT_HEADER, 1);
    curl_setopt($sh, CURLOPT_TIMEOUT, 30);
    curl_setopt($sh, CURLOPT_POST, 1);
    curl_setopt($sh, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($sh, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($sh);
    curl_close($sh);
    return json_decode($result);
  }

  /**
   * Get information for the provided modelId
   *
   * @link https://developers.shapeways.com/api-reference#Models
   *
   * @param int $modelId the modelId for the model to retreive
   * @return array the json response from the api call
   */
  public function getModelInfo($modelId){
    $url = $this->baseApiUrl . '/models/'. $modelId .'/v1';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
  }


  /**
   * Get a list of materials
   *
   * @link https://developers.shapeways.com/api-reference#Materials
   *
   * @return array the json response from the api call
   */
  public function getMaterials(){
    $url = $this->baseApiUrl . '/materials/v1';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
  }

  /**
   * place an order for the user
   *
   * @link https://developers.shapeways.com/api-reference#Order
   *
   * @param array $params the order data to set
   * @return array the json response from the api call
   * @throws ParameterValidationException
   */
  public function placeOrder($params)
  {
    $required = array('items', 'firstName', 'lastName', 'country', 'state', 'city', 'address1', 'zipCode', 'phoneNumber', 'shippingOption');
    foreach ($required as $key) {
      if (!array_key_exists($key, $params)) {
        throw new ParameterValidationException('Shapeways\Oauth2CurlClient::addModel missing required key: ' . $key);
      }
    }

    // json encode
    $postData = json_encode($params);
    $url = $this->baseApiUrl . '/orders/v1';

    $sh = curl_init($url);
    curl_setopt($sh, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($sh, CURLOPT_HEADER, 1);
    curl_setopt($sh, CURLOPT_TIMEOUT, 30);
    curl_setopt($sh, CURLOPT_POST, 1);
    curl_setopt($sh, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($sh, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($sh);
    curl_close($sh);
    return json_decode($result);
  }


  /**
   * Get information for the provided orderId
   *
   * @link https://developers.shapeways.com/api-reference#Order
   *
   * @param int $oderId the orderId for the Order to retreive
   * @return array the json response from the api call
   */
  public function getOrderInfo($oderId){
    $url = $this->baseApiUrl . '/orders/'. $oderId .'/v1';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
  }




}