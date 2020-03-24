<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Reply;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReplyControllerTest extends WebTestCase
{
    public function testCreateAction()
    {
        $success = static::createClient();
        $fail = static::createClient();

        $name = 'Re MATT' . rand(0, 999);

        $successParam = [
            'message_id' => '1',
            'name' => $name,
            'content' => 'reply'
        ];
        $failParam = [
            'message_id' => '1',
            'name' => $name
        ];

        $success->request('POST', '/reply/create/1', $successParam);
        $fail->request('POST', '/reply/create/1', $failParam);

        $resSuccess = $success->getResponse();
        $resFail = $fail->getResponse();

        $successJson = json_decode($resSuccess->getContent(), true);
        $failJson = json_decode($resFail->getContent(), true);

        $reply = new Reply();
        $reply->setName($name);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertEquals($name, $reply->getName());
        $this->assertEquals('false', $successJson['result']);
        $this->assertEquals('資料未輸入完全！', $failJson['errorMsg']);
    }

    public function testShowAllAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/reply/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('"message_id":1', $client->getResponse()->getContent());
    }
}
