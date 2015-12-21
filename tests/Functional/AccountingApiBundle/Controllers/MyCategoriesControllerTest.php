<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class MyCategoriesControllerController extends BaseApiTestController
{
    protected $fixtures = [
        'AccountingApiBundle\DataFixtures\ORM\LoadUsers',
        'AccountingApiBundle\DataFixtures\ORM\LoadCategories',
        'AccountingApiBundle\DataFixtures\ORM\LoadUserCategories',

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

    public function testPostMyCategory()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories'. [
                'name' => 'New category'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals('New category', $response->data->name);
    }

    public function testPostMyCategoryWithGlobalParameter()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories'. [
                'name' => 'New category',
                'global' => true
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals('New category', $response->data->name);
        $this->assertEquals(false, $response->data->global);
    }

    public function testPostMyCategoryWithoutData()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories'
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function testPostMyCategoryWithInvalidData()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories', [
                'name' => 'a'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function testPostMyCategoryWhichIsAlreadyExist()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories', [
                'name' => 'Global category 1'
            ]
        );

        $this->assertInvalidForm($this->client, [
            'name'
        ], Response::HTTP_BAD_REQUEST);
    }

    public function testPostMyCategoryWhichIsAlreadyCreatedByAnotherUser()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories', [
                'name' => 'Secondary user category'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_CREATED, $this->client);
        $this->assertEquals('Secondary user category', $response->data->name);
    }

    public function testPatchMyCategory()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories/4', [
                'name' => 'New category name'
            ]
        );

        $response = $this->getJsonContent($this->client);

        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals('New category name', $response->data->name);
    }

    public function testPatchGlobalCategory()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories/1', [
                'name' => 'New category name'
            ]
        );

        $this->assertStatusCode(Response::HTTP_FORBIDDEN, $this->client);
    }

    public function testPatchNotMyCategory()
    {
        $this->authRequest(
            'POST',
            '/api/users/me/categories/20', [
                'name' => 'New category name'
            ]
        );

        $this->assertStatusCode(Response::HTTP_NOT_FOUND, $this->client);
    }
}
