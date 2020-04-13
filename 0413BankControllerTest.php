<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Bank;
use AppBundle\Controller\BankController;
use AppBundle\Repository\BankRepository;

class BankControllerTest extends WebTestCase
{
    public function testHomeAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/bank');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Amount:', $crawler->filter('#demo label')->text());
    }

    public function testDepositAction()
    {
        $success = static::createClient();
        $fail = static::createClient();
        $client = static::createClient();
        //$exception = static::createClient();

        $successParam = [
            'user' => 'matt',
            'amount' => 100,
            'version' => 1
        ];
        $failParam = [
            'user' => 'matt',
            'version' => 1
        ];
        // $exceptionParam = [
        //     'user' => 'matt',
        //     'amount' => 'exception',
        //     'version' => 1
        // ];

        $success->request('POST', '/bank/deposit', $successParam);
        $fail->request('POST', '/bank/deposit', $failParam);
        //$exception->request('POST', '/bank/deposit', $exceptionParam);
        $client->request('GET', '/bank/show');

        $resSuccess = $success->getResponse();
        $resFail = $fail->getResponse();
        //$resException = $exception->getResponse();
        $result = $client->getResponse();

        $successJson = json_decode($resSuccess->getContent(), true);
        $failJson = json_decode($resFail->getContent(), true);
        $res = json_decode($result->getContent(), true);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertEquals(1711, $res[0]['balance']);
        $this->assertNotFalse($successJson['result']);
        $this->assertEquals('未輸入金額', $failJson['errorMsg']);
        //$this->assertEquals('Sorry', $resException->getContent());
    }

    public function testWithdrawAction()
    {
        $success = static::createClient();
        $fail = static::createClient();
        $failBalance = static::createClient();
        $client = static::createClient();

        $successParam = [
            'user' => 'matt',
            'amount' => 100,
            'version' => 1
        ];
        $failBalanceParam = [
            'user' => 'matt',
            'amount' => 100000,
            'version' => 1
        ];
        $failParam = [
            'user' => 'matt',
            'version' => 1
        ];

        $success->request('POST', '/bank/withdraw', $successParam);
        $failBalance->request('POST', '/bank/withdraw', $failBalanceParam);
        $fail->request('POST', '/bank/withdraw', $failParam);
        $client->request('GET', '/bank/show');

        $resSuccess = $success->getResponse();
        $resFailBalance = $failBalance->getResponse();
        $resFail = $fail->getResponse();
        $result = $client->getResponse();

        $successJson = json_decode($resSuccess->getContent(), true);
        $failBalanceJson = json_decode($resFailBalance->getContent(), true);
        $failJson = json_decode($resFail->getContent(), true);
        $res = json_decode($result->getContent(), true);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertEquals(1611, $res[0]['balance']);
        $this->assertNotFalse($successJson['result']);
        $this->assertEquals('未輸入金額', $failJson['errorMsg']);
        $this->assertEquals('餘額不足', $failBalanceJson['errorMsg']);
    }

    public function testShowBalanceAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/bank/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('balance', $client->getResponse()->getContent());
    }

    public function testCountByBalanceAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/bank/count');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('2289', $client->getResponse()->getContent());
    }

    public function testCountBalanceByAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/bank/dql');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('1', $client->getResponse()->getContent());
    }

    // public function testThrowException()
    // {

    //     $em = $this->createMock(EntityManagerInterface::class);

    //     $em->expects($this->once())
    //        ->method('persist')
    //        ->will($this->throwException(new Exception));

    // }

    public function testCalculateBalance()
    {
        $bank = new Bank();
        $bank->setAmount(1000);
        $bank->setBalance(1100);

        // Now, mock the repository so it returns the mock of the bank
        $bankRepository = $this->createMock(EntityRepository::class);
        // use getMock() on PHPUnit 5.3 or below
        // $bankRepository = $this->getMock(ObjectRepository::class);
        $bankRepository->expects($this->any())
            ->method('find')
            ->willReturn($bank);

        // Last, mock the EntityManager to return the mock of the repository
        $objectManager = $this->createMock(EntityManager::class);
        // use getMock() on PHPUnit 5.3 or below
        // $objectManager = $this->getMock(ObjectManager::class);
        $objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($bankRepository);

        // $b = $this->createMock(Bank::class);
        // $b->expects($this->any())
        //     ->method('getDoctrine')
        //     ->willReturn($objectManager);

        $bankController = new BankController($objectManager);
        $this->assertEquals(2100, $bankController->calculateBalance(3363));

        // // mock the repository so it returns the mock of the user (just a random string)
        // $repositoryMock = $this
        // //->getMockBuilder(EntityRepository::class)
        // ->getMockBuilder('Doctrine\ORM\EntityRepository')
        // ->setMethods(['find'])
        // ->disableOriginalConstructor()
        // ->getMock();

        // $repositoryMock->expects($this->any())
        // ->method('find')
        // ->willReturn($bank);

        // // mock the EntityManager to return the mock of the repository
        // $entityManager = $this
        // //->getMockBuilder(EntityManager::class)
        // ->getMockBuilder('Doctrine\ORM\EntityManager')
        // ->disableOriginalConstructor()
        // ->getMock();

        // $entityManager->expects($this->any())
        // ->method('getRepository')
        // ->willReturn($repositoryMock);

        // // test the user method
        // $userRequest = new BankController($entityManager);
        // $this->assertEquals(1000, $userRequest->calculateBalance(3363));

    }
}
