<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseApiTestController
 *
 * @package Tests\Functional\AccountingApiBundle\Controllers
 */
abstract class BaseApiTestController extends WebTestCase
{
    /**
     * Initialized crawler client
     *
     * @var
     */
    protected $client;

    /**
     * Access token for doing secured requests
     *
     * @var
     */
    protected $accesToken;

    /**
     * Fixtures class names
     *
     * @var array
     */
    protected $fixtures = [];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->client = static::makeClient();

        $this->loadFixtures($this->fixtures);

        $this->makeAccessToken();
    }

    /**
     * Calling non secured request with certain parameters
     * Checking that request is not required authentication.
     *
     * @param           $method
     * @param           $uri
     * @param array     $parameters
     * @param array     $files
     * @param array     $server
     * @param null      $content
     * @param bool|true $changeHistory
     *
     * @return mixed
     */
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

    /**
     * Calling secured (should demand authentication) request.
     * Checking that request is required authentication.
     *
     * @param           $method
     * @param           $uri
     * @param array     $parameters
     * @param array     $files
     * @param array     $server
     * @param null      $content
     * @param bool|true $changeHistory
     *
     * @return mixed
     */
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

    /**
     * Creates and saves user access token.
     * Used login action to getting access token.
     */
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

    /**
     * Used for testing form for containing of invalid fields
     *
     * @param       $client Request client
     * @param array $expectedFieldErrors Names of fields which should contain errors
     * @param int   $expectedCode Expected response status code
     */
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

    /**
     * Helper method for obtaining json data from client
     *
     * @param $client
     * @return mixed
     */
    protected function getJsonContent($client)
    {
        return json_decode($client->getResponse()->getContent());
    }

    /**
     * Asserting that obtained data is a valid collection
     *
     * @param $response
     */
    protected function assertCollection($response)
    {
        $this->assertObjectHasAttribute('_meta_data', $response);
        $this->assertObjectHasAttribute('total_items', $response->_meta_data);
        $this->assertObjectHasAttribute('total_pages', $response->_meta_data);
        $this->assertObjectHasAttribute('current_page', $response->_meta_data);
    }
}