<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AccountingApiBundle\DataFixtures\ORM\LoadUsers',
        'AccountingApiBundle\DataFixtures\ORM\LoadPurses'
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
