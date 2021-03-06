<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyPursesControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AccountingApiBundle\DataFixtures\ORM\LoadUsers',
        'AccountingApiBundle\DataFixtures\ORM\LoadPurses'
    ];

    public function testGetMyPurses()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCount(5, $response->data);
        $this->assertCollection($response);
    }

    public function testGetMyPurse()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('balance', $response->data);
        $this->assertObjectHasAttribute('name', $response->data);
        $this->assertObjectHasAttribute('id', $response->data);
    }

    public function testGetNotMyPurse()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/6'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testDeleteMyPurse()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/1'
        );

        $this->assertStatusCode(Response::HTTP_NO_CONTENT, $this->client);
    }

    public function testDeleteNotMyPurse()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/6'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testCreateMyPurse()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses', [
                'name' => 'Test purse',
                'balance' => 88.88
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals('Test purse', $response->data->name);
    }

    public function testCreatePurseWithInvalidData()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses', [
                'balance' => 'invalid balance'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name', 'balance'
        ]);
    }

    public function testCreatePurseWhichIsAlreadyExist()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses', [
                'name' => 'Common user purse',
                'balance' => 99.99
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function testCreatePurseWhichIsAlreadyCreatedByAnotherUser()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/purses', [
                'name' => 'Secondary user purse',
                'balance' => 99.99
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals('Secondary user purse', $response->data->name);
    }

    public function testPatchMyPurse()
    {
        $this->authRequest(
            'PATCH',
            '/api/users/me/purses/1', [
                'name' => 'New purse name'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals('New purse name', $response->data->name);
        $this->assertEquals(150.00, $response->data->balance);
    }

    public function testPatchMyPurseWithExistingName()
    {
        $this->authRequest(
            'PATCH',
            '/api/users/me/purses/2', [
                'name' => 'Common user purse'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function testPatchNotMyPurse()
    {
        $this->authRequest(
            'PATCH',
            '/api/users/me/purses/6'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }
}
