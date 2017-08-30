<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/?addressFrom=Lyon&addressTo=Paris');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->isJson($client->getResponse()->getContent());
    }
}
