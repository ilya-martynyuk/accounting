<?php

namespace Tests\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyPursesControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUserData',
        'AppBackEndBundle\DataFixtures\ORM\LoadPurses'
    ];

    public function testGetMyPursesAction()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('collection', $response);
        $this->assertCount(4, $response->collection);
    }

    public function testGetMyPurseWhichNotExist()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/999'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testGetMyPurse()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/1'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('purse', $response);
        $this->assertObjectHasAttribute('balance', $response->purse);
        $this->assertObjectHasAttribute('name', $response->purse);
        $this->assertObjectHasAttribute('id', $response->purse);

        $this->authRequest(
            'GET',
            '/api/users/me/purses/999'
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

    public function testDeletePurseWhichNotExist()
    {
        $this->authRequest(
            'DELETE',
            '/api/users/me/purses/999'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
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
                'name' => 'Exist purse',
                'balance' => 99.99
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_CONFLICT);
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
        $this->assertObjectHasAttribute('purse', $response);
        $this->assertObjectHasAttribute('name', $response->purse);
        $this->assertEquals('Test purse', $response->purse->name);
    }

    public function testEditMyPurseWhichIsNotExist()
    {
        $this->authRequest(
            'PUT',
            '/api/users/me/purses/999'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }

    public function testEditMyPurseWithWrongCredentials()
    {
        $this->authRequest(
            'PUT',
            '/api/users/me/purses/1'
        );

        $this->assertInvalidForm($this->client, [
            'name', 'balance'
        ]);
    }

    public function testEditMyPurse()
    {
        $this->authRequest(
            'PUT',
            '/api/users/me/purses/1', [
                'name' => 'New purse name',
                'balance' => 77.77
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('purse', $response);
        $this->assertObjectHasAttribute('name', $response->purse);
        $this->asserEquals('New purse name', $response->purse->name);
    }
}
