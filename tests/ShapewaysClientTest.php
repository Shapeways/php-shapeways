<?php

class ShapewaysClientTest extends PHPUnit_Framework_TestCase{

    protected $client;
    protected $oauth;

    public function setUp(){
        $this->client = new \Shapeways\Client('CONSUMER KEY',
                                              'CONSUMER SECRET',
                                              'http://localhost/callback');
        $this->oauth = $this->getMock('OAuth',
                                      array('fetch',
                                            'getLastResponse',
                                            'getRequestToken',
                                            'getAccessToken'),
                                      array('CONSUMER KEY',
                                            'CONSUMER SECRET',
                                            'http://localhost/callback')
                                      );
        $this->client->_client = $this->oauth;
    }

    public function tearDown(){
        $this->oauth = NULL;
        $this->client = NULL;
    }

    public function testClientConnect(){
        $response = array('oauth_token_secret' => 'secret',
                          'authentication_url' => 'authUrl');
        $this->oauth->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/request_token/v1'),
                   $this->equalTo('http://localhost/callback'))
            ->will($this->returnValue($response));
        $authUrl = $this->client->connect();

        $this->assertEquals($authUrl, 'authUrl');
    }

    public function testClientConnectInvalidResponse(){
        $this->oauth->expects($this->once())
            ->method('getRequestToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/request_token/v1'),
                   $this->equalTo('http://localhost/callback'))
            ->will($this->returnValue(array()));
        $authUrl = $this->client->connect();

        $this->assertEquals($authUrl, false);
    }

    public function testClientVerify(){

    }

    public function testClientVerifyUrl(){

    }
}