<?php

namespace BoardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testCreatecategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/createCategory');
    }

    public function testAllcategories()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/allCategories');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'All categories website should work');

    }

    public function testShowcategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showCategory');
    }

    public function testSortCategoryByAdsAmount() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/allCategories');

        $link = $crawler
            ->filter('#sortByAdsCount a') // find all links with the text "Greet"
            ->eq(0) // select the second link in the list
            ->link()
        ;

// and click it
        $crawler = $client->click($link);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Sorting should be available');

    }

    public function testIfSiteSort() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/allCategories', ['sort' => 'cnt', 'direction' => 'asc', 'page' => 1]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Site should open');

    }

    public function testQuery() {
        $client = static::createClient();
        $em = $client->getKernel()->getContainer()->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT c, COUNT(v.id) AS ent FROM BoardBundle:Category c LEFT JOIN c.ads v GROUP BY c.id ORDER BY c.name ASC'
        )->execute();
        $this->assertNotEmpty($query);
    }

}
