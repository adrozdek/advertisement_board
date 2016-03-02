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
    }

    public function testShowcategory()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showCategory');
    }

}
