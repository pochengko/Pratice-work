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
use Redis;

class BankController extends Controller
{
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
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        $user = 'matt';
        $id = $request->request->get('id');
        $amount = $request->request->get('amount');
        $version = $request->request->get('version');

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'id' => $id,
                'version' => $version,
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $balance = $redis->incrby('balance', $amount);

                $bank = new Bank();
                $bank->setUser($user);
                $bank->setAmount($amount);
                $bank->setBalance($balance);
                $bank->setTimestamp(new \DateTime('now'));
                $bank->setVersion($version);

                $entityManager->persist($bank);
                $entityManager->flush();
                $entityManager->getConnection()->commit();

                return new JsonResponse([
                    'result' => 'true',
                    'user' => $user,
                    'amount' => $amount
                ]);
            } catch (Exception $e) {
                $entityManager->getConnection()->rollback();
                $entityManager->close();
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
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        $user = 'matt';
        $id = $request->request->get('id');
        $amount = $request->request->get('amount');
        $version = $request->request->get('version');

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'id' => $id,
                'version' => $version,
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $balance = $redis->get('balance');

                if ($balance < $amount) {
                    return new JsonResponse([
                        'errorMsg' => '餘額不足'
                    ]);
                } else {
                    $balance = $redis->decrby('balance', $amount);

                    $bank = new Bank();
                    $bank->setUser($user);
                    $bank->setAmount(-$amount);
                    $bank->setBalance($balance);
                    $bank->setTimestamp(new \DateTime('now'));
                    $bank->setVersion($version);

                    $entityManager->persist($bank);
                    $entityManager->flush();
                    $entityManager->getConnection()->commit();

                    return new JsonResponse([
                        'result' => 'true',
                        'user' => $user,
                        'amount' => $amount
                    ]);
                }
            } catch (Exception $e) {
                $entityManager->getConnection()->rollback();
                $entityManager->close();
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

    /**
     * @Route("/bank/count", name="count_balance")
     * @({"GET"})
     */
    public function countByBalanceAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repoBanks = $entityManager->getRepository(Bank::class);
        $totalBanks = $repoBanks->countByBalance();

        return new Response($totalBanks);
    }

    /**
     * @Route("/bank/dql", name="count_balance_byDQL")
     * @({"GET"})
     */
    public function countBalanceByAction()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repoBanks = $entityManager->getRepository(Bank::class);
        $totalBanks = $repoBanks->countBalanceBy();

        return new Response($totalBanks);
    }

    /**
     * @Route("/bank/calculate/{id}", name="calculate_balance")
     * @({"GET"})
     */
    public function calculateBalance($id)
    {
        $bankRepository = $this->getDoctrine()
            ->getRepository(Bank::class);
        $bank = $bankRepository->find($id);

        return $bank->getAmount() + $bank->getBalance();
    }
}