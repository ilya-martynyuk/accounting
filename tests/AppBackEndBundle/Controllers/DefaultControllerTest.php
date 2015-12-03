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
        $client = static::makeClient();

        $client->request(
            'GET', '/api/users'
        );

        $this->assertStatusCode(401, $client);
    }
}