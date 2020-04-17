<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Bank;
use AppBundle\Controller\BankController;
use AppBundle\Repository\BankRepository;
use Symfony\Component\HttpFoundation\Request;
use Redis;

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

        $successParam = [
            'user' => 'matt',
            'amount' => 100,
            'version' => 1
        ];
        $failParam = [
            'user' => 'matt',
            'version' => 1
        ];

        $successRequest = $success->request('POST', '/bank/deposit', $successParam);
        $fail->request('POST', '/bank/deposit', $failParam);
        $client->request('GET', '/bank/show');

        $resSuccess = $success->getResponse();
        $resFail = $fail->getResponse();
        $result = $client->getResponse();

        $successJson = json_decode($resSuccess->getContent(), true);
        $failJson = json_decode($resFail->getContent(), true);
        $res = json_decode($result->getContent(), true);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertEquals(3402, $res[0]['balance']);
        $this->assertNotFalse($successJson['result']);
        $this->assertEquals('未輸入金額', $failJson['errorMsg']);

        $connectionMock = $this->getEntityManagerMock();
        $connectionMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue('test'));
        $connectionMock->expects($this->any())
            ->method('flush')
            ->will($this->throwException(new \Exception('test3123123')));
        $connectionMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('1'));
        $connectionMock->expects($this->any())
            ->method('close')
            ->will($this->returnValue('2'));

        $bankController = new BankController($connectionMock);
        $res = json_decode($bankController->deposit('matt', 3638, 100, 1)->getContent(), true);

        $this->assertEquals('test3123123', $res['test']);
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
        $this->assertEquals(3402, $res[0]['balance']);
        $this->assertNotFalse($successJson['result']);
        $this->assertEquals('未輸入金額', $failJson['errorMsg']);
        $this->assertEquals('餘額不足', $failBalanceJson['errorMsg']);

        $connectionMock = $this->getEntityManagerMock();
        $connectionMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue('test'));
        $connectionMock->expects($this->any())
            ->method('flush')
            ->will($this->throwException(new \Exception('testwithdraw')));
        $connectionMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue('1'));
        $connectionMock->expects($this->any())
            ->method('close')
            ->will($this->returnValue('2'));

        $bankController = new BankController($connectionMock);
        $res = json_decode($bankController->withdraw('matt', 3638, 100, 1)->getContent(), true);

        $this->assertEquals('testwithdraw', $res['test']);
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
        $this->assertEquals('1102', $client->getResponse()->getContent());
    }

    public function testCountBalanceByAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/bank/dql');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('301', $client->getResponse()->getContent());
    }

    public function testCalculateBalance()
    {
        $bank = new Bank();
        $bank->setAmount(2000);
        $bank->setBalance(1000);

        $bankRepository = $this->createMock(BankRepository::class);
        $bankRepository->expects($this->at(0))
            ->method('find')
            ->willReturn($bank);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($bankRepository);

        $bankController = new BankController($entityManager);
        $this->assertEquals(3000, $bankController->calculateBalance(3633)->getContent());
    }

    /**
     * @return \Doctrine\ORM\EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getEntityManagerMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getConnection',
                    'getClassMetadata',
                    'close',
                    'getRepository',
                    'flush',
                    'persist',
                ]
            )
            ->getMock();

        $mock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->getConnectionMock()));

        $mock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($this->getClassMetadataMock()));

        return $mock;
    }

    /**
     * @return \Doctrine\Common\Persistence\Mapping\ClassMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getClassMetadataMock()
    {
        $mock = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadataInfo')
            ->disableOriginalConstructor()
            ->setMethods(array('getTableName'))
            ->getMock();

        $mock->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('{tableName}'));

        return $mock;
    }

    /**
     * @return \Doctrine\DBAL\Platforms\AbstractPlatform|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getDatabasePlatformMock()
    {
        $mock = $this->getAbstractMock(
            'Doctrine\DBAL\Platforms\AbstractPlatform',
            [
                'getName',
                'getTruncateTableSQL',
            ]
        );

        $mock->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('mysql'));

        $mock->expects($this->any())
            ->method('getTruncateTableSQL')
            ->with($this->anything())
            ->will($this->returnValue('#TRUNCATE {table}'));

        return $mock;
    }

    /**
     * @return \Doctrine\DBAL\Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getConnectionMock()
    {
        $mock = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'beginTransaction',
                    'commit',
                    'rollback',
                    'prepare',
                    'query',
                    'executeQuery',
                    'executeUpdate',
                    'getDatabasePlatform',
                ]
            )
            ->getMock();

        $mock->expects($this->any())
            ->method('prepare')
            ->will($this->returnValue($this->getStatementMock()));

        $mock->expects($this->any())
            ->method('query')
            ->will($this->returnValue($this->getStatementMock()));

        $mock->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($this->getDatabasePlatformMock()));

        return $mock;
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getStatementMock()
    {
        $mock = $this->getAbstractMock(
            'Doctrine\DBAL\Driver\Statement', // In case you run PHPUnit <= 3.7, use 'Mocks\DoctrineDbalStatementInterface' instead.
            [
                'bindValue',
                'execute',
                'rowCount',
                'fetchColumn',
            ]
        );

        $mock->expects($this->any())
            ->method('fetchColumn')
            ->will($this->returnValue(1));

        return $mock;
    }

    /**
     * @param string $class   The class name
     * @param array  $methods The available methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getAbstractMock($class, array $methods)
    {
        return $this->getMockForAbstractClass(
            $class,
            [],
            '',
            true,
            true,
            true,
            $methods,
            false
        );
    }
}
