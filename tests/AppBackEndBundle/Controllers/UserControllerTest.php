<?php

namespace AppBackEndBundle\Tests\Controller;

use \Liip\FunctionalTestBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function setUp()
    {
        $this->loadFixtures([
            'AppBackEndBundle\DataFixtures\ORM\LoadUserData'
        ]);
    }

    public function testGetUsers()
    {
        $client = static::makeClient(true);

        $client->request(
            'GET', '/api/users'
        );

        $response = json_decode($client->getResponse()->getContent());
        $this->assertObjectHasAttribute('users', $response);
        $this->assertCount(11, $response->users);
        $this->assertStatusCode(200, $client);
    }
}