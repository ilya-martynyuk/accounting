<?php

namespace Tests\Functional\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUsers'
    ];

    public function testLoginAction()
    {
        $this->request(
            'POST',
            '/api/auth/login', [
                'username' => 'common_user',
                'password' => 'test'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('access_token', $response);
    }

    public function testLoginActionWithoutCredentials()
    {
        $this->request(
            'POST',
            '/api/auth/login'
        );

        $this->assertInvalidForm($this->client, [
            'username', 'password'
        ]);
    }

    public function testLoginActionWithWrongCredentials()
    {
        $this->request(
            'POST',
            '/api/auth/login', [
                'username' => 'wrong',
                'password' => 'wrong'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'username'
        ], Response::HTTP_UNAUTHORIZED);
    }
}