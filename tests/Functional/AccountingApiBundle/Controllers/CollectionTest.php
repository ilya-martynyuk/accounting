<?php

namespace Tests\Functional\AccountingApiBundle\Controllers;

use Symfony\Component\HttpFoundation\Response;

class CollectionTest extends BaseApiTestController
{
    protected $fixtures = [
        'AccountingApiBundle\DataFixtures\ORM\LoadUsers',
        'AccountingApiBundle\DataFixtures\ORM\LoadPurses',
        'AccountingApiBundle\DataFixtures\ORM\LoadOperations',
        'AccountingApiBundle\DataFixtures\ORM\LoadCategories',
        'AccountingApiBundle\DataFixtures\ORM\LoadUserCategories'
    ];

    public function testOrderByDesc()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'orderBy' => 'id',
                'order' => 'desc'
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals($response->data[0]->id, 103);
        $this->assertEquals($response->data[count($response->data) - 1]->id, 4);
    }

    public function testOrderByAsc()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'orderBy' => 'id',
                'order' => 'asc'
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertEquals($response->data[0]->id, 4);
        $this->assertEquals($response->data[count($response->data) - 1]->id, 103);
    }

    public function testLimit()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'page' => 2,
                'perPage' => 13
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCount(13, $response->data);
    }

    public function testFiltering()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'filters' => [
                    'category,eq,1'
                ]
            ]
        );

        $response = $this->getJsonContent($this->client);
        $this->assertStatusCode(Response::HTTP_OK, $this->client);
        $this->assertCount(5, $response->data);
    }

    public function testFilteringWithInvalidFieldName()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'filters' => [
                    'cat,eq,1'
                ]
            ]
        );

        $this->assertStatusCode(Response::HTTP_BAD_REQUEST, $this->client);
    }

    public function testFilteringWithInvalidOperation()
    {
        $this->authRequest(
            'GET',
            '/api/users/me/purses/2/operations', [
                'filters' => [
                    'category,asdf,1'
                ]
            ]
        );

        $this->assertStatusCode(Response::HTTP_BAD_REQUEST, $this->client);
    }
}