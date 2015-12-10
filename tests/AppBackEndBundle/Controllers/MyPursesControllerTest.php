<?php

namespace Tests\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyPursesControllerTest extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUsers',
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
        $this->assertCount(5, $response->collection);
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
        ], Response::HTTP_BAD_REQUEST);
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

    public function patchMyPurse()
    {
        $this->authRequest(
            'PATH',
            '/api/users/me/purses/2', [
                'name' => 'New purse name'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertObjectHasAttribute('purse', $response);
        $this->assertEquals('New purse name', $response->purse->name);
        $this->assertEquals(123.123, $response->purse->balance);
    }

    public function patchMyPurseWhichIsNotExist()
    {
        $this->authRequest(
            'PATH',
            '/api/users/me/purses/999'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }
}
