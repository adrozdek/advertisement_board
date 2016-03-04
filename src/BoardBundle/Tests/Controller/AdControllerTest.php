<?php

namespace BoardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdControllerTest extends WebTestCase
{
    public function testCreatead()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/createAd');
    }

    public function testAllads()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/allAds');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Main page should work');
        $this->assertContains('Ogłoszenia', $client->getResponse()->getContent(), 'Main page should containg header Ogłoszenia');
    }

    public function testMyads()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/myAds');
    }

    public function testOldads()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/oldAds');
    }

}
