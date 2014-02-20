<?php

namespace Shapeways;

class ParameterValidationException extends \Exception{}

class Client{
    private $_callbackUrl;
    private $_consumerKey, $_consumerSecret;
    private $_client;
    public $oauthToken, $oauthSecret;
    public $baseUrl = 'https://api.shapeways.com';
    public $apiVersion = 'v1';

    public function __construct(
        $consumerKey, $consumerSecret,
        $callbackUrl = NULL, $oauthToken = NULL, $oauthSecret = NULL
    ){
        $this->_consumerKey = $consumerKey;
        $this->_consumerSecret = $consumerSecret;
        $this->_callbackUrl = $callbackUrl;
        $this->oauthToken = $oauthToken;
        $this->oauthSecret = $oauthSecret;
        $this->_client = new \Oauth($this->_consumerKey,
                                  $this->_consumerSecret,
                                  OAUTH_SIG_METHOD_HMACSHA1,
                                  OAUTH_AUTH_TYPE_AUTHORIZATION);
        $this->_client->setToken($this->oauthToken, $this->oauthSecret);
    }

    public function connect(){
        $url = $this->url('/oauth1/request_token/');
        $response = $this->_client->getRequestToken($url, $this->_callbackUrl);
        if($response['authentication_url']){
            $this->oauthSecret = $response['oauth_token_secret'];
            $this->_client->setToken($this->oauthToken, $this->oauthSecret);
            return $response['authentication_url'];
        }
        return false;
    }

    public function verify($token, $verifier){
        $url = $this->url('/oauth1/access_token/');
        $this->oauthToken = $token;
        $this->_client->setToken($this->oauthToken, $this->oauthSecret);
        $response = $this->_client->getAccessToken($url, null, $verifier);
        if($response['oauth_token'] && $response['oauth_token_secret']){
            $this->oauthToken = $response['oauth_token'];
            $this->oauthSecret = $response['oauth_token_secret'];
            $this->_client->setToken($this->oauthToken, $this->oauthSecret);
            return true;
        }
        return false;
    }

    public function verifyUrl($url){
        $query= parse_url($url, PHP_URL_QUERY);
        $params = array();
        parse_str($query, $params);
        return $this->verify($params['oauth_token'], $params['oauth_secret']);
    }

    public function url($path){
        $baseUrl = trim($this->baseUrl, '/');
        $version = trim($this->apiVersion, '/');
        $path = trim($path, '/');

        return $baseUrl . '/' . $path . '/' . $version;
    }

    private function _get($url, $params = array()){
        try{
            $this->_client->fetch($url, $params, OAUTH_HTTP_METHOD_GET);
        } catch(\Exception $e){}
        return json_decode($this->_client->getLastResponse());
    }

    private function _put($url, $params = array()){
        try{
            $this->_client->fetch($url, json_encode($params), OAUTH_HTTP_METHOD_PUT);
        } catch(\Exception $e){}
        return json_decode($this->_client->getLastResponse());
    }

    private function _post($url, $params = array()){
        try{
            $this->_client->fetch($url, json_encode($params), OAUTH_HTTP_METHOD_POST);
        } catch(\Exception $e){}
        return json_decode($this->_client->getLastResponse());
    }

    private function _delete($url, $params = array()){
        try{
            $this->_client->fetch($url, $params, OAUTH_HTTP_METHOD_DELETE);
        } catch(\Exception $e){}
        return json_decode($this->_client->getLastResponse());
    }

    public function addModel($params){

    }

    public function addModelFile($modelId, $params){

    }

    public function addModelPhoto($modelId, $params){

    }

    public function addToCart($params){

    }

    public function deleteModel($modelId){
        return $this->_delete($this->url('/models/' . $modelId . '/'));
    }

    public function getPrice($params){
        $required = array('area', 'volume', 'xBoundMin', 'xBoundMax',
                          'yBoundMin', 'yBoundMax', 'zBoundMin', 'zBoundMax');
        foreach($required as $key){
            if(!array_key_exists($key, $params)){
                throw new ParameterValidationException('Shapeways\Client::getPrice missing required key: ' . $key);
            }
        }
        return $this->_post($this->url('/price/'), $params);
    }

    public function getApiInfo(){
        return $this->_get($this->url('/api/'));
    }

    public function getCart(){
        return $this->_get($this->url('/orders/cart/'));
    }

    public function getCategories(){
        return $this->_get($this->url('/categories/'));
    }

    public function getCategory($catId){
        return $this->_get($this->url('/categories/' . $catId . '/'));
    }

    public function getMaterial($materialId){
        return $this->_get($this->url('/materials/' . $materialId . '/'));
    }

    public function getMaterials(){
        return $this->_get($this->url('/materials/'));
    }

    public function getModel($modelId){
        return $this->_get($this->url('/models/' . $modelId . '/'));
    }

    public function getModelFile($modelId, $fileVersion, $includeFile = FALSE){
        $url = $this->url('/models/' . $modelId . '/files/' . $fileVersion . '/');
        return $this->_get($url, array('file' => (int)$includeFile));
    }

    public function getModelInfo($modelId){
        return $this->_get($this->url('/models/' . $modelId . '/info/'));
    }

    public function getModels($page = 1){
        return $this->_get($this->url('/models/'), array('page' => $page));
    }

    public function getPrinter($printerId){
        return $this->_get($this->url('/printers/' . $printerId . '/'));
    }

    public function getPrinters(){
        return $this->_get($this->url('/printers/'));
    }

    public function updateModelInfo($modelId, $params){

    }
}