<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Bank;
use AppBundle\Controller\BankController;
use AppBundle\Repository\BankRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

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
        // $success = static::createClient();
        // $fail = static::createClient();
        //
        // $successParam = [
        //     'user' => 'matt',
        //     'amount' => 100
        // ];
        // $failParam = [
        //     'user' => 'matt'
        // ];
        //
        // $success->request('POST', '/bank/deposit', $successParam);
        // $fail->request('POST', '/bank/deposit', $failParam);
        //
        // $resSuccess = $success->getResponse();
        // $resFail = $fail->getResponse();
        //
        // $successJson = json_decode($resSuccess->getContent(), true);
        // $failJson = json_decode($resFail->getContent(), true);


        // $bank = new Bank();
        // $bank->setAmount(200);
        //
        // // Now, mock the repository so it returns the mock of the bank
        // $bankRepository = $this->createMock(ObjectRepository::class);
        // // use getMock() on PHPUnit 5.3 or below
        // // $bankRepository = $this->getMock(ObjectRepository::class);
        // $bankRepository->expects($this->any())
        // ->method('find')
        // ->willReturn($bank);
        //
        // // Last, mock the EntityManager to return the mock of the repository
        // $objectManager = $this->createMock(ObjectManager::class);
        // // use getMock() on PHPUnit 5.3 or below
        // // $objectManager = $this->getMock(ObjectManager::class);
        // $objectManager->expects($this->any())
        // ->method('getRepository')
        // ->willReturn($bankRepository);
        //
        // $bankController = new BankController($objectManager);
        // $this->assertEquals(2000, $bankController->showBalanceByIdAction(354));
        //
        // $em = $this->createMock(EntityManagerInterface::class);
        //create a request mock
//         $request = $this
//         ->getMockBuilder(Request::class)
//         ->getMock();
//
// //set the return value
//         $request
//         ->expects($this->once())
//         ->method('getContent')
//         ->will($this->returnValue('put your request data here'));


        // $mock = $this->createMock(Bank::class);
        // $mock->method('setAmount')->willReturn(100);
        // $bankController = new BankController();
        // $testval = $bankController->depositAction($successJson);
        // self::assertEquals(700, $testval);

        $this->assertEquals(200, $resSuccess->getStatusCode());
        $this->assertEquals(200, $resFail->getStatusCode());
        $this->assertNotFalse($successJson['result']);
        $this->assertEquals('未輸入金額', $failJson['errorMsg']);

        // $entityManager = $this->getMockBuilder( EntityManager::class )->disableOrgninalConstructor()->getMock()
        // $entityManager->expects( $this->any() )
        // ->method( 'persist' )
        // ->willThrowException( new Exception() );


        // First, mock the object to be used in the test
        // 首先，模拟用在测试中的对象
        // $bank = $this->createMock(Bank::class);
        // $bank->expects($this->once())
        // ->method('setAmount')
        // ->will($this->returnValue(100));
        //
        // // Now, mock the repository so it returns the mock of the bank
        // // 现在，模拟repository令其返回bank的模拟
        // $bankRepository = $this
        // ->getMockBuilder(BankController::class)
        // ->disableOriginalConstructor()
        // ->getMock();
        //
        // $bankRepository->expects($this->once())
        // ->method('depositAction')
        // ->will($this->returnValue($bank));
        //
        // // Last, mock the EntityManager to return the mock of the repository
        // // 最后，模拟EntityManager以返回repository的模拟
        // $entityManager = $this
        // ->getMockBuilder(ObjectManager::class)
        // ->disableOriginalConstructor()
        // ->getMock();
        //
        // $entityManager->expects($this->once())
        // ->method('getRepository')
        // ->will($this->returnValue($bankRepository));
        //
        // $bankController = new BankController($entityManager);
        // $this->assertEquals(600, $bankController->depositAction(341));

        // $mockedEm = $this->createMock(EntityManager::class)
        // $mockedPersonManager = $this->createMock(Bank::class);
        // $mockedEm->method('getManager')->willReturn(mockedPersonManager);
        // $mockedPersonManager->findOneBy($bank)->willReturn($bank);






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
    public function testDepositAction_(){
      $bankRepository = $this->createMock(BankRepository::class);
      $bankRepository->expects($this->any())
          ->method('flush')
          ->willReturn($ex);

      $entityManager = $this->createMock(EntityManager::class);
      $entityManager->expects($this->any())
          ->method('getRepository')
          ->willReturn($bankRepository);
            $bankController = new BankController($entityManager);
      var_dump($bankController->deposit('matt',123,100,1));
    }
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
}
