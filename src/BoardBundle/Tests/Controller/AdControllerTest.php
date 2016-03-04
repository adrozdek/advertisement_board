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

    public function testQueryAllAds() {
        $client = static::createClient();
        $em = $client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $query = $em->createQuery(
            'SELECT a, v.username FROM BoardBundle:Ad a LEFT JOIN a.owner v WHERE a.expirationDate > :nowTime');
        $result = $query->setParameter('nowTime', $dateNow)->execute();

        $this->assertNotEmpty($query);
    }

    public function testQuerySearch() {
        $client = static::createClient();
        $em = $client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');

        $date = date('Y-m-d H:i:s', time());
        $dateNow = (new \DateTime($date));
        $search = 'Oddam';

        $query = $em->createQuery(
            'SELECT a, v.username FROM BoardBundle:Ad a JOIN a.owner v WHERE a.expirationDate > :nowTime AND a.title LIKE :search'
        );
        $query->setParameter('search', '%' . $search . '%');
        $query->setParameter('nowTime', $dateNow);
        $result = $query->execute();


    }
}
