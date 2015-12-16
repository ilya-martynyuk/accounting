<?php

namespace Tests\Functional\AppBackEndBundle\Controllers;

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
        $this->assertCount(12, $response->data);
        $this->assertCollection($response);
    }

    public function testMeAction()
    {
        $this->authRequest(
            'GET',
            '/api/users/me'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('username', $response->data);
        $this->assertEquals('common_user', $response->data->username);
    }
}
