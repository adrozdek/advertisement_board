<?php

namespace BoardBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testMycomments()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/myComments');
    }

    public function testShowcomment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/showComment');
    }

    public function testAddcomment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addComment');
    }

    public function testRemovecomment()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/removeComment');
    }

    public function testAllcomments()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/allComments');
    }

}
