<?php

namespace Tests\Functional\AppBackEndBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class CategoryControllerController extends BaseApiTestController
{
    protected $fixtures = [
        'AppBackEndBundle\DataFixtures\ORM\LoadUsers',
        'AppBackEndBundle\DataFixtures\ORM\LoadCategories',
        'AppBackEndBundle\DataFixtures\ORM\LoadUserCategories',

    ];

    public function testGetMyCategories()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/categories'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCount(13, $response->data);
        $this->assertCollection($response);
    }

    public function testGetMyCategory()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/categories/4'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals('Test category', $response->data->name);
    }

    public function testGetMyGlobalCategory()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/categories/1'
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals('Global category 1', $response->data->name);
    }

    public function testGetNotMyCategory()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/categories/15'
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }
}
