<?php

namespace AppBackEndBundle\Tests\Controller;

use \Liip\FunctionalTestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    protected $client;

    public function setUp()
    {
        $this->client = static::makeClient();

        $this->loadFixtures([
            'AppBackEndBundle\DataFixtures\ORM\LoadUserData'
        ]);
    }

    public function testGetUsersAction()
    {
        $client = static::makeClient();

        $client->request(
            'GET', '/api/users'
        );

        $this->assertStatusCode(200, $client);

        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('collection', $response);
        $this->assertCount(11, $response->collection);

    }

    /**
     * Login action without credentials provided.
     */
    public function testLoginActionWithoutCredentials()
    {
        $this->client->request(
            'POST',
            '/api/users/login'
        );

        $this->assertInvalidForm($this->client, [
            'username', 'password'
        ]);
    }

    /**
     * Login action with wrong credentials.
     */
    public function testLoginActionWithWrongCredentials()
    {
        $this->client->request(
            'POST',
            '/api/users/login', [
                'username' => 'wrong',
                'password' => 'wrong'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'username'
        ], 401);
    }

    /**
     * Login action with valid user.
     */
    public function testLoginAction()
    {
        $this->client->request(
            'POST',
            '/api/users/login', [
                'username' => 'test',
                'password' => 'test'
            ]
        );

        $this->assertStatusCode(200, $this->client);

        $response = json_decode($this->client->getResponse()->getContent());
        $this->assertObjectHasAttribute('me', $response);
        $this->assertObjectHasAttribute('access_token', $response);
        $this->assertObjectHasAttribute('expired_at', $response);

        $this->assertObjectHasAttribute('id', $response->me);
        $this->assertObjectHasAttribute('username', $response->me);
        $this->assertObjectHasAttribute('email', $response->me);
    }

    /**
     * Get current user profile information.
     */
    public function testMeAction()
    {
        $this->client->request(
            'GET',
            '/api/users/me'
        );

        $this->assertStatusCode(200, $this->client);
    }

    public function assertInvalidForm($client, array $expectedFieldErrors = [], $expectedCode = 400)
    {
        $response = json_decode($client->getResponse()->getContent());

        $this->assertStatusCode($expectedCode, $client);
        $this->assertObjectHasAttribute('errors', $response);

        foreach ($expectedFieldErrors as $fieldName) {
            $this->assertObjectHasAttribute($fieldName, $response->errors);
        }
    }
}
