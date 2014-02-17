<?php

namespace Shapeways;

class Client{
    private $callbackUrl;
    private $consumerKey, $consumerSecret;
    public $OauthToken, $OauthSecret;
    public $BaseUrl = "https://api.shapeways.com";
    public $APIVersion = "v1";

    public function __construct(
        $consumerKey, $consumerSecret,
        $callbackUrl = NULL, $oauthToken = NULL, $oauthSecret = NULL
    ){
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->callbackUrl = $callbackUrl;
        $this->OauthToken = $oauthToken;
        $this->OauthSecret = $oauthSecret;
    }

    public function Connect(){

    }

    public function Verify($token, $verifier){

    }

    public function VerifyUrl($url){
        $query= parse_url($url, PHP_URL_QUERY);
        $params = array();
        parse_str($query, $params);
        return $this->Verify($params['oauth_token'], $params['oauth_secret']);
    }

    public function Url($path){
        $baseUrl = trim($this->BaseUrl, '/');
        $version = trim($this->APIVersion, '/');
        $path = trim($path, '/');

        return $baseUrl . '/' . $path . '/' . $version;
    }

    private function get($url){

    }

    private function put($url, $body){

    }

    private function post($url, $body){

    }

    private function delete($url){

    }

    public function AddModel($params){

    }

    public function AddModelFile($modelId, $params){

    }

    public function AddModelPhoto($modelId, $params){

    }

    public function AddToCart($params){

    }

    public function DeleteModel($modelId){

    }

    public function GetPrice($params){

    }

    public function GetApiInfo(){

    }

    public function GetCart(){

    }

    public function GetCategories(){

    }

    public function GetCategory($catId){

    }

    public function GetMaterial($materialId){

    }

    public function GetMaterials(){

    }

    public function GetModel($modelId){

    }

    public function GetModelFile($modelId, $fileVersion, $includeFile = FALSE){

    }

    public function GetModelInfo($modelId){

    }

    public function GetModels($page = 1){

    }

    public function GetPrinter($printerId){

    }

    public function GetPrinters(){

    }

    public function UpdateModelInfo($modelId, $params){

    }
}