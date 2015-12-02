<?php

namespace AppBackEndBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/');

        $this->assertContains('Hello World', $client->getResponse()->getContent());
    }
}
