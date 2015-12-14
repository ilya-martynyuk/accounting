<?php

namespace Tests\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyPurseOperationsControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUsers',
        'AppBackEndBundle\DataFixtures\ORM\LoadPurses',
        'AppBackEndBundle\DataFixtures\ORM\LoadOperations',
    ];

    public function testGetOperations()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1/operations'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCollection($response);
    }

    public function testGetOperationsFromNotMyPurse()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/5/operations'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testPostDecreaseOperation()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses/1/operations', [
                'amount' => 23.45
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals(23.45, $response->data->amount);

        // Purse balance should be decreased
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals(126.55, $response->data->balance);
    }

    public function testPostIncreaseOperation()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses/1/operations', [
                'amount' => 23.45,
                'direction' => '+'
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals(23.45, $response->data->amount);

        // Purse balance should be increased
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals(173.45, $response->data->balance);
    }

    public function testPostOperationToNotMyPurse()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses/5/operations', [
                'amount' => 23.45,
                'direction' => '+'
            ]
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testGetOperation()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1/operations/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals("Common user purse operation", $response->data->description);
        $this->assertEquals(777.66, $response->data->amount);
    }

    public function testGetOperationFromNotMyPurse()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/5/operations/1'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testGetNotMyOperation()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1/operations/150'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testDeleteIncreaseOperation()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/1/operations/2'
        );

        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $this->client);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/1/operations/2'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertEquals(137.46, $response->data->balance);
    }

    public function testDeleteDecreaseOperation()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/1/operations/3'
        );

        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $this->client);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertEquals(193, $response->data->balance);
    }

    public function testDeleteNotMyOperation()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/5/operations/1'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testPatchOperation()
    {
        $this->authRequest(
            'PATCH',
            '/api/users/me/purses/1/operations/2', [
                'description' => 'Modified description',
                'amount' => 5,
                'direction' => '-'
            ]
        );

        $this->assertStatusCode(Response::HTTP_OK, $this->client);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/1/operations/2'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertEquals('Modified description', $response->data->description);
        $this->assertEquals(5, $response->data->amount);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);
        $this->assertEquals(132.46, $response->data->balance);
    }

    public function testPatchNotMyOperation()
    {
        $this->authRequest(
            'PATCH',
            '/api/users/me/purses/5/operations/2', [
                'description' => 'Modified description',
                'amount' => 5,
                'direction' => '-'
            ]
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }
}
