<?php

namespace Tests\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUsers',
        'AppBackEndBundle\DataFixtures\ORM\LoadPurses'
    ];

    public function testGetUsersAction()
    {
        $this->authRequest(
            'GET', '/api/users'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('collection', $response);
        $this->assertCount(11, $response->collection);
    }

    public function testMeAction()
    {
        $this->authRequest(
            'GET',
            '/api/users/me'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('profile', $response);
        $this->assertObjectHasAttribute('username', $response->profile);
        $this->assertEquals('test', $response->profile->username);
    }
}
