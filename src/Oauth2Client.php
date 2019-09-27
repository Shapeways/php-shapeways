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
   * @var string redirectUrl the oauth2 callback url
   */
  public $redirectUrl;

  /**
   * @var string $clientId
   * @var string $clientSecret
   */
  protected $clientId, $clientSecret;

  /**
   * @var string $accessToken the oauth token used for requests
   * @var string $refreshSecret the oauth secret used for getting new access token
   */
  public $accessToken, $refreshToken;

  /**
   * @var string $baseUrl the api base url used to generate api urls
   */
  protected $baseApiUrl = 'https://api.shapeways.com';

  /**
   * @var string $apiVersion the api version used to generate api urls
   */
  public $apiVersion = 'v1';

  /**
   * Create a new \Shapeways\Oauth2CurlClient
   *
   * @param string $clientId your app consumer key
   * @param string $clientSecret your app consumer secret
   * @param string|null $redirectUrl your app callback url
   * @param string|null $accessToken a users oauth token if it is already known
   * @param string|null $refreshToken if it is already known
   * @param string|null $baseApiUrl if it you want to use a different baseApiUrl
   */
  public function __construct(
    $clientId,
    $clientSecret,
    $redirectUrl = null,
    $accessToken = null,
    $refreshToken = null,
    $baseApiUrl = null
  ) {
    $this->clientId = $clientId;
    $this->clientSecret = $clientSecret;
    $this->redirectUrl = $redirectUrl;
    $this->accessToken = $accessToken;
    $this->refreshToken = $refreshToken;
    if ($baseApiUrl !== null) {
      $this->baseApiUrl = $baseApiUrl;
    }
  }

  /**
   * Grant Type 1: Resource owner credentials grant
   *
   * https://developers.shapeways.com/quick-start#authenticate
   *
   * Your don't need a redirect url for this grant type
   *
   * Use "access_token" from result for other API calls
   *
   * @return object - json decoded api response
   */
  public function generateAccessTokenClientCredentialGrant()
  {
    $params = array(
      "grant_type" => "client_credentials"
    );

    $url = $this->baseApiUrl . '/oauth2/token';
    return $this->_post($url, $params, array(), array($this->clientId, $this->clientSecret));
  }


  /**
   * Grant Type 2: Authorization code grant
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
  public function generateAccessTokenAuthorizationGrant()
  {
    $clientId = $this->clientId;
    $url = $this->baseApiUrl . "/oauth2/authorize?response_type=code&client_id=" . $clientId . "&redirect_uri=" . rawurlencode($this->redirectUrl);
    header('Location: ' . $url);
  }

  /**
   * Grant Type 2: Authorization code grant
   * Step 2 - Generate access token
   *
   * @link https://developers.shapeways.com/quick-start#authenticate
   *  function for handling call back of generateAccessTokenAuthorizationGrant() call above
   *
   * Use "access_token" from result for other API calls
   *
   * @param $code - $_REQUEST['code'] you got from the callback
   * @return array - json decoded api response
   *
   */
  public function handleAuthorizationGrantCallback($code)
  {
    $params = array(
      "grant_type" => "authorization_code",
      "code" => $code,
      "client_id" => $this->clientId,
      "client_secret" => $this->clientSecret,
      "redirect_uri" => $this->redirectUrl
    );

    $url = $this->baseApiUrl . '/oauth2/token';
    return $this->_post($url, $params);
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
        throw new ParameterValidationException('Shapeways\Oauth2CurlClient::uploadModel missing required key: ' . $key);
      }
    }

    $params['file'] = rawurlencode(base64_encode($params['file']));

    $url = $this->baseApiUrl . '/models/' . $this->apiVersion;
    return $this->_post($url, $params,
      array('Authorization' => 'Bearer ' . $this->accessToken, 'Content-type' => 'application/json'));
  }

  /**
   * Get information for the provided modelId
   *
   * @link https://developers.shapeways.com/api-reference#Models
   *
   * @param int $modelId the modelId for the model to retreive
   * @return array the json response from the api call
   */
  public function getModelInfo($modelId)
  {
    $url = $this->baseApiUrl . '/models/' . $modelId . '/' .$this->apiVersion;

    return $this->_get($url);
  }

  /**
   * Get list of models
   *
   * @link https://developers.shapeways.com/api-reference#Models
   *
   * @return array the json response from the api call
   */
  public function getModels()
  {
    $url = $this->baseApiUrl . '/models/' .$this->apiVersion;

    return $this->_get($url);
  }

  /**
   * Get a list of materials
   *
   * @link https://developers.shapeways.com/api-reference#Materials
   *
   * @return array the json response from the api call
   */
  public function getMaterials()
  {
    $url = $this->baseApiUrl . '/materials/'. $this->apiVersion;

    return $this->_get($url);
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
    $required = array(
      'items',
      'firstName',
      'lastName',
      'country',
      'state',
      'city',
      'address1',
      'zipCode',
      'phoneNumber',
      'shippingOption'
    );

    foreach ($required as $key) {
      if (!array_key_exists($key, $params)) {
        throw new ParameterValidationException('Shapeways\Oauth2CurlClient::placeOrder missing required key: ' . $key);
      }
    }

    $url = $this->baseApiUrl . '/orders/' . $this->apiVersion;
    return $this->_post($url, $params,
      array('Authorization' => 'Bearer ' . $this->accessToken, 'Content-type' => 'application/json'));
  }


  /**
   * Get information for the provided orderId
   *
   * @link https://developers.shapeways.com/api-reference#Order
   *
   * @param int $orderId the orderId for the Order to retreive
   * @return array the json response from the api call
   */
  public function getOrderInfo($orderId)
  {
    $url = $this->baseApiUrl . '/orders/' . $orderId . '/' . $this->apiVersion;
    return $this->_get($url);
  }

  /**
   * Get list of orders
   *
   * @link https://developers.shapeways.com/api-reference#Orders
   *
   * @return array the json response from the api call
   */
  public function getOrders()
  {
    $url = $this->baseApiUrl . '/orders/' .$this->apiVersion;

    return $this->_get($url);
  }

  /**
   * @param       $url
   * @param array $params
   * @param array $headers
   * @param array $auth
   *
   * @return mixed
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function _post($url, $params = array(), $headers = array(), $auth = array()) {
    $url = $this->urlWithParameters($url);
    $client = new \GuzzleHttp\Client();
    if (array_key_exists('Content-type', $headers) && $headers['Content-type'] == 'application/json') {
      $postOptions = array(\GuzzleHttp\RequestOptions::JSON => $params);
    } else {
      $postOptions = array(\GuzzleHttp\RequestOptions::FORM_PARAMS => $params);
    }

    if (!empty($headers)) {
      $postOptions['headers'] =  $headers;
    }

    if (!empty($auth)) {
      $postOptions['auth'] = $auth;
    }

    $postOptions['verify'] = false;

    try {
      $res = $client->request('post', $url, $postOptions);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      return json_decode($responseBodyAsString);
    } catch (\Exception $e) {
      return json_encode($e->getMessage());
    }

    //echo $res->getStatusCode(); // "200"
    //echo $res->getHeader('content-type'); // 'application/json; charset=utf8'
    $response =  (string) $res->getBody(); // {"type":"User"...'
    return json_decode($response);
  }

  /**
   * @param $url
   * @return mixed
   */
  protected function _get($url)
  {
    $url = $this->urlWithParameters($url);
    $client = new \GuzzleHttp\Client();
    try {
      $res = $client->request('get', $url, array(
        'verify' => false,
        'headers' => array(
          'Authorization' => 'Bearer ' . $this->accessToken,
          'Content-type' =>  'application/json'
        )
      ));
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      $response = $e->getResponse();
      $responseBodyAsString = $response->getBody()->getContents();
      return json_decode($responseBodyAsString);
    } catch (\Exception $e) {
      return json_encode($e->getMessage());
    }
    //echo $res->getStatusCode(); // "200"
    //echo $res->getHeader('content-type'); // 'application/json; charset=utf8'
    $response = (string)$res->getBody(); // {"type":"User"...'
    return json_decode($response);
  }

  /**
   * Make a POST request to the api server
   *
   * @param string $url the api url to request
   * @param array $params the parameters to send with the request
   * @param array $headers to send with the request
   * @param array $auth to send with the request
   * @return array the json response from the api call
   *
   * @deprecated Use Shapeways\Oauth2Client::_post()
   */
  private function _postCurl($url, $params = array(), $headers= array(), $auth = array())
  {
    $url = $this->urlWithParameters($url);
    // json encode
    $postData = json_encode($params);

    $ch = curl_init($url);

    if (!empty($headers)) {
      $headers = array();
      foreach ($headers as $optKey => $optVal) {
        $headers[] = $optKey.': '.$optVal;
      }
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    if (!empty($auth)) {
      curl_setopt($ch, CURLOPT_USERPWD, $auth[0] . ":" . $auth[1]);
    }

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result);
  }

  /**
   *
   * Make a GET request to the api server
   *
   * @param string $url the api url to request
   * @return array the json response from the api call
   *
   * @deprecated Use Shapeways\Oauth2Client::_get()
   */
  private function _getCurl($url)
  {
    $url = $this->urlWithParameters($url);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER,
      array('Authorization: Bearer ' . $this->accessToken, 'Content-type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result);
  }

  private function urlWithParameters($url) {
    if (!empty($_SERVER['QUERY_STRING'])) {
      $prefix = '&';
      if(strpos($url,'?') === false) {
        $prefix = '?';
      }
      $url = $url . $prefix . $_SERVER['QUERY_STRING'];
    }
    return $url;
  }

}
