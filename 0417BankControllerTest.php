<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Controller\BankController;
use AppBundle\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Exception;




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
       $bank = new Bank();
       $bank->setAmount(2000);
       $bank->setBalance(1000);

       $bankRepository = $this->createMock(BankRepository::class);
       $bankRepository->expects($this->once())
           ->method('find')
           ->willReturn($bank);

      $connectionMock = $this->getEntityManagerMock();
      $connectionMock->expects($this->once())
          ->method('getRepository')
          ->willReturn($bankRepository);
      $connectionMock->expects($this->once())
              ->method('persist')
              ->will($this->returnValue(null));
      $connectionMock->expects($this->once())
          ->method('flush')
          ->willThrowException(new Exception('adb'));
      $bankController = new BankController($connectionMock);

      $this->assertEquals(3000, $bankController->deposit('matt',358,100,1)->getContent());

    }

    public function testShowBalanceAction()
    {
        $client = static::createClient();

        $response = $client->request('GET', '/bank/show');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('timestamp', $client->getResponse()->getContent());
    }

    // public function testShowBalanceByIdAction()
    // {
    //   $mock = $this->createMock(Bank::class);
    //   $mock->method('setAmount')->willReturn(100);
    //   $bankController = new BankController();
    //   $testval = $bankController->showBalanceByIdAction($mock);
    //   self::assertEquals(700, $testval);
    // }

    public function testCalculateBalance()
    {
        $bank = new Bank();
        $bank->setAmount(2000);
        $bank->setBalance(1000);

        $bankRepository = $this->createMock(BankRepository::class);
        $bankRepository->expects($this->any())
            ->method('find')
            ->willReturn($bank);

        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($bankRepository);

        $bankController = new BankController($entityManager);
        var_dump($bankController->calculateBalance(3633));
        $this->assertEquals(3000, $bankController->calculateBalance(3633)->getContent());
    }








    public function getEntityManagerMock()
   {
       $mock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
           ->disableOriginalConstructor()
           ->setMethods(
               array(
                   'getConnection',
                   'getClassMetadata',
                   'close',
                   'getRepository',
                   'flush',
                   'persist',
               )
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
           array(
               'getName',
               'getTruncateTableSQL',
           )
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
               array(
                   'beginTransaction',
                   'commit',
                   'rollback',
                   'prepare',
                   'query',
                   'executeQuery',
                   'executeUpdate',
                   'getDatabasePlatform',
               )
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
           array(
               'bindValue',
               'execute',
               'rowCount',
               'fetchColumn',
           )
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
           array(),
           '',
           true,
           true,
           true,
           $methods,
           false
       );
   }
}
