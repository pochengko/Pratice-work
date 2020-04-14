<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Common\Persistence\ObjectManager;

class BankController extends Controller
{
  private $entityManager;

  public function __construct($objectManager = null)
  {
      if (!$this->entityManager && $objectManager) {
          $this->entityManager = $objectManager;
      } else if (!$this->entityManager && !$objectManager) {
          // $this->entityManager = $this->getDoctrine()->getManager();
      }
  }

    /**
     * @Route("/bank", name="bank")
     * @Method({"GET"})
     */
    public function homeAction()
    {
         return $this->render('bank/bank.html.twig');
    }

    /**
     * @Route("bank/deposit", name="money_deposit")
     * @Method({"POST"})
     */
    public function depositAction(Request $request)
    {
        $user = 'matt';
        $id = $request->request->get('id');
        $amount = $request->request->get('amount');
        $version = $request->request->get('version');

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'errorMsg' => '未輸入金額',
                'id' => $id,
                'version' => $version
            ]);
        } else {
            if (!$this->entityManager) {
                $this->entityManager = $this->getDoctrine()->getManager();
            }
            $this->entityManager->getConnection()->beginTransaction();

            try {
            if (!$id) {
              $id = $this->showMaxAction();
            }

                //$bank = $entityManager->getRepository(Bank::class)->findOneBy(['user' => $user], ['id' => 'DESC'], LockMode::OPTIMISTIC, $version);
                $bank = $this->entityManager->getRepository('AppBundle:Bank')->find($id);
                $balance = $bank->getBalance();
                $balance = $balance + $amount;

                $bank = new Bank();
                $bank->setUser($user);
                $bank->setAmount($amount);
                $bank->setBalance($balance);
                $bank->setTimestamp(new \DateTime('now'));
                $bank->setVersion($version);

                $this->entityManager->persist($bank);
                $this->entityManager->flush();
                $this->entityManager->getConnection()->commit();

                return new JsonResponse([
                    'result' => 'true',
                    'user' => $user,
                    'amount' => $amount,
                    'id' => $id,
                    'version' => $version
                ]);
                return $balance;

            } catch (Exception $e) {
                $this->entityManager->getConnection()->rollback();
                $this->entityManager->close();
                throw $e;
            }
        }

    }

    /**
     * @Route("bank/withdraw", name="money_withdraw")
     * @Method({"POST"})
     */
    public function withdrawAction(Request $request)
    {
        $user = 'matt';
        $amount = $request->request->get('amount');
        $version = '1';

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'errorMsg' => '未輸入金額'
            ]);
        } else {
          if (!$this->entityManager) {
              $this->entityManager = $this->getDoctrine()->getManager();
          }
            //$this->entityManager = $this->getDoctrine()->getManager();
            $this->entityManager->getConnection()->beginTransaction();

            try {
                $bank = $this->entityManager->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
                $balance = $bank->getBalance();

                if ($balance < $amount) {
                    return new JsonResponse([
                        'errorMsg' => '餘額不足'
                    ]);
                } else {
                    $balance = $balance - $amount;

                    $bank = new Bank();
                    $bank->setUser($user);
                    $bank->setAmount(-$amount);
                    $bank->setBalance($balance);
                    $bank->setTimestamp(new \DateTime('now'));
                    $bank->setVersion($version);

                    //$entityManager = $this->getDoctrine()->getManager();
                    $this->entityManager->persist($bank);
                    $this->entityManager->flush();
                    $this->entityManager->getConnection()->commit();

                    return new JsonResponse([
                        'result' => 'true',
                        'user' => $user,
                        'amount' => $amount
                    ]);
                }
            } catch (Exception $e) {
                $this->entityManager->getConnection()->rollback();
                $this->entityManager->close();
                throw $e;
            }
        }

    }

    /**
     * @Route("/bank/show", name="show_balance")
     * @({"GET"})
     */
    public function showBalanceAction()
    {
        $banks = $this->getDoctrine()->getRepository(Bank::class)->findBy([], ['id' => 'DESC']);

        foreach ($banks as $bank) {
            $id = $bank->getId();
            $user = $bank->getUser();
            $amount = $bank->getAmount();
            $balance = $bank->getBalance();
            $timestamp = $bank->getTimestamp()->format('Y-m-d H:i:s');
            $version = $bank->getVersion();
            $res[] = [
                'id' => $id,
                'user' => $user,
                'amount' => $amount,
                'balance' => $balance,
                'timestamp' => $timestamp,
                'version' => $version
            ];
        }

        return new JsonResponse($res);
    }


    public function showMaxAction()
    {
        $bank = $this->getDoctrine()->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
        $id = $bank->getId();
        return $id;
    }

    // /**
    //  * @Route("/bank/show/{id}", name="show_balance_by_id")
    //  * @({"GET"})
    //  */
    // public function showBalanceByIdAction($id)
    // {
    //     $bank = $this->getDoctrine()->getRepository(Bank::class)->find($id);
    //
    //     //foreach ($banks as $bank) {
    //         $id = $bank->getId();
    //         $user = $bank->getUser();
    //         $amount = $bank->getAmount();
    //         $balance = $bank->getBalance();
    //         $timestamp = $bank->getTimestamp()->format('Y-m-d H:i:s');
    //         $version = $bank->getVersion();
    //         $res[] = [
    //             'id' => $id,
    //             'user' => $user,
    //             'amount' => $amount,
    //             'balance' => $balance,
    //             'timestamp' => $timestamp,
    //             'version' => $version
    //         ];
    //     //}
    //
    //     return $balance + $amount;
    // }

    /**
 * @Route("/bank/calculate/{id}", name="calculate_balance")
 * @({"GET"})
 */
public function calculateBalance($id)
{
  if (!$this->entityManager) {
      $this->entityManager = $this->getDoctrine()->getManager();
  }
    $bankRepository = $this->entityManager
        ->getRepository(Bank::class);
    $bank = $bankRepository->find($id);

    return new Response($bank->getAmount() + $bank->getBalance());
}
}
