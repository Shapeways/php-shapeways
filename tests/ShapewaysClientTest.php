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

    public function testClientGetApiInfo(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/api/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","key":"value"}'));

        $result = $this->client->getApiInfo();
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->key = 'value';
        $this->assertEquals($result, $expected);
    }

    public function testClientDeleteModel(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/models/1234/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_DELETE))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success"}'));

        $result = $this->client->deleteModel(1234);
        $expected = new stdClass;
        $expected->result = 'success';
        $this->assertEquals($result, $expected);
    }

    public function testClientGetCart(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/orders/cart/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","cart":"data"}'));

        $result = $this->client->getCart();
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->cart = 'data';
        $this->assertEquals($result, $expected);
    }

    public function testClientGetCategories(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/categories/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","categories":["one", "two"]}'));

        $result = $this->client->getCategories();
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->categories = array('one', 'two');
        $this->assertEquals($result, $expected);
    }

    public function testClientGetCategory(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/categories/5/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","key":"value"}'));

        $result = $this->client->getCategory(5);
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->key = 'value';
        $this->assertEquals($result, $expected);
    }

    public function testClientGetMaterials(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/materials/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","materials":["one", "two"]}'));

        $result = $this->client->getMaterials();
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->materials = array('one', 'two');
        $this->assertEquals($result, $expected);
    }

    public function testClientGetMaterial(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/materials/25/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","key":"value"}'));

        $result = $this->client->getMaterial(25);
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->key = 'value';
        $this->assertEquals($result, $expected);
    }

    public function testClientGetModel(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/models/1234/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","model":"data"}'));

        $result = $this->client->getModel(1234);
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->model = 'data';
        $this->assertEquals($result, $expected);
    }

    public function testClientGetModelInfo(){
        $this->oauth->expects($this->once())
            ->method('fetch')
            ->with($this->equalTo('https://api.shapeways.com/models/1234/info/v1'),
                   $this->equalTo(array()),
                   $this->equalTo(OAUTH_HTTP_METHOD_GET))
            ->will($this->returnValue(NULL));
        $this->oauth->expects($this->once())
            ->method('getLastResponse')
            ->will($this->returnValue('{"result":"success","model":"info"}'));

        $result = $this->client->getModelInfo(1234);
        $expected = new stdClass;
        $expected->result = 'success';
        $expected->model = 'info';
        $this->assertEquals($result, $expected);
    }
}