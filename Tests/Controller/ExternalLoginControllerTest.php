<?php

namespace Newscoop\ExternalLoginPluginBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExternalLoginControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/externallogin/Tester');

        $this->assertTrue($crawler->filter('html:contains("Hello Tester")')->count() > 0);
    }
}
