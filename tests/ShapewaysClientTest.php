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
        $response = array("oauth_token" => "new_token",
                          "oauth_token_secret" => "new_secret");
        $this->oauth->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/access_token/v1'),
                   $this->equalTo(NULL),
                   $this->equalTo("verifier"))
            ->will($this->returnValue($response));
        $succeeded = $this->client->verify("token", "verifier");

        $this->assertEquals($succeeded, true);
        $this->assertEquals($this->client->oauthToken, "new_token");
        $this->assertEquals($this->client->oauthSecret, "new_secret");
    }

    public function testClientVerifyInvalidResponse(){
        $this->oauth->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/access_token/v1'),
                   $this->equalTo(NULL),
                   $this->equalTo("verifier"))
            ->will($this->returnValue(array()));
        $succeeded = $this->client->verify("token", "verifier");

        $this->assertEquals($succeeded, false);
    }

    public function testClientVerifyUrl(){
        $response = array("oauth_token" => "new_token",
                          "oauth_token_secret" => "new_secret");
        $this->oauth->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/access_token/v1'),
                   $this->equalTo(NULL),
                   $this->equalTo("verifier"))
            ->will($this->returnValue($response));
        $succeeded = $this->client->verifyUrl(
            "http://localhost/callback?oauth_token=token&oauth_verifier=verifier"
        );

        $this->assertEquals($succeeded, true);
        $this->assertEquals($this->client->oauthToken, "new_token");
        $this->assertEquals($this->client->oauthSecret, "new_secret");
    }

    public function testClientVerifyUrlInvalidResponse(){
        $this->oauth->expects($this->once())
            ->method('getAccessToken')
            ->with($this->equalTo('https://api.shapeways.com/oauth1/access_token/v1'),
                   $this->equalTo(NULL),
                   $this->equalTo("verifier"))
            ->will($this->returnValue(array()));
        $succeeded = $this->client->verifyUrl(
            "http://localhost/callback?oauth_token=token&oauth_verifier=verifier"
        );

        $this->assertEquals($succeeded, false);
    }
}