<?php
/**
 * Created by PhpStorm.
 * User: presleylupon
 * Date: 22/08/2018
 * Time: 14:30
 */

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UsersControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($content);

       // $arrayContent = json_decode($content, true);
        //$this->assertCount(10, $arrayContent);

    }

    public function testGetUser()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/3',
            [],
            [],
            ['CONTENT_TYPE'=>'application/json',
                'HTTP_AUTH-TOKEN'=>'5b7e6578633a24.24107608']);

        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($content);

       // $arrayContent = json_decode($content, true);
        //$this->assertCount(10, $arrayContent);

    }
    public function testGetUserNotGood()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/3',
            [],
            [],
            ['CONTENT_TYPE'=>'application/json',
                'HTTP_AUTH-TOKEN'=>'5b7e700e1ddfa3.65903236']);

        $response = $client->getResponse();
        $content = $response->getContent();
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($content);

       // $arrayContent = json_decode($content, true);
        //$this->assertCount(10, $arrayContent);

    }

}