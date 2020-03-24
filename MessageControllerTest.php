<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    public function testHomeAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/message');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Name:', $crawler->filter('#demo label')->text());
    }

    public function testCreateAction()
    {
        $success = static::createClient();
        $fail = static::createClient();

        $name = 'MATT' . rand(0, 999);

        $successParam = [
            'name' => $name,
            'content' => 'hi'
        ];
        $failParam = [
            'name' => $name
        ];

        $success->request('POST', '/message/create', $successParam);
        $fail->request('POST', '/message/create', $failParam);

        $resSuccess = $success->getResponse();
        $resFail = $fail->getResponse();

        $successJson = json_decode($resSuccess->getContent(), true);
        $failJson = json_decode($resFail->getContent(), true);

        $message = new Message();
        $message->setName($name);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertEquals($name, $message->getName());
        $this->assertEquals('false', $successJson['result']);
        $this->assertEquals('資料未輸入完全！', $failJson['errorMsg']);
    }

    public function testShowAllAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/message/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('"id":1', $client->getResponse()->getContent());
    }
}
