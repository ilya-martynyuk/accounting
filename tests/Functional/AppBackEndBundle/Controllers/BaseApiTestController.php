<?php

namespace Tests\Functional\AppBackEndBundle\Controllers;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseApiTestController extends WebTestCase
{
    protected $client;
    protected $accesToken;
    protected $fixtures = [];

    public function setUp()
    {
        $this->client = static::makeClient();

        $this->loadFixtures($this->fixtures);

        $this->makeAccessToken();
    }

    protected function request(
        $method,
        $uri,
        array $parameters = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        $changeHistory = true
    ) {
        // Checking whether route accessible without auth token provided.
        $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
        $this->assertNotEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        return $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    protected function authRequest(
        $method,
        $uri,
        array $parameters = array(),
        array $files = array(),
        array $server = array(),
        $content = null,
        $changeHistory = true
    ) {
        // Checking whether route is not accessible without auth token provided.
        $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        $server = $server + [
            'HTTP_X_BEARER_TOKEN' => $this->accesToken
        ];

        return $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }

    protected function makeAccessToken()
    {
        $client = static::makeClient();

        $client->request(
            'POST',
            '/api/auth/login', [
                'username' => 'common_user',
                'password' => 'test'
            ]
        );

        $data = $this->getJsonContent($client);

        $this->accesToken = $data->access_token;
    }

    public function assertInvalidForm(
        $client,
        array $expectedFieldErrors = [],
        $expectedCode = Response::HTTP_BAD_REQUEST
    ) {
        $response = $this->getJsonContent($client);

        $this->assertStatusCode($expectedCode, $client);
        $this->assertObjectHasAttribute('reason', $response);

        foreach ($expectedFieldErrors as $fieldName) {
            $this->assertObjectHasAttribute($fieldName, $response->reason);
        }
    }

    protected function getJsonContent($client)
    {
        return json_decode($client->getResponse()->getContent());
    }

    protected function assertCollection($response)
    {
        $this->assertObjectHasAttribute('_meta_data', $response);
        $this->assertObjectHasAttribute('total_items', $response->_meta_data);
        $this->assertObjectHasAttribute('total_pages', $response->_meta_data);
        $this->assertObjectHasAttribute('current_page', $response->_meta_data);
    }
}