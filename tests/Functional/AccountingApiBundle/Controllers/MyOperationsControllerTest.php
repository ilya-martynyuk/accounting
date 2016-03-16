<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyOperationsControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AccountingApiBundle\DataFixtures\ORM\LoadUsers',
        'AccountingApiBundle\DataFixtures\ORM\LoadPurses',
        'AccountingApiBundle\DataFixtures\ORM\LoadOperations',
        'AccountingApiBundle\DataFixtures\ORM\LoadCategories',
        'AccountingApiBundle\DataFixtures\ORM\LoadUserCategories'
    ];

    public function testGetMyOperations()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/operations'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCollection($response);
    }
}
