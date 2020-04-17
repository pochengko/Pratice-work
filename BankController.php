<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Redis;

class BankController extends Controller
{
    private $em;

    public function __construct($objectManager = null)
    {
        if (!$this->em && $objectManager) {
            $this->em = $objectManager;
        }
        // else if (!$this->em && !$objectManager) {
        //     $this->em = $this->getDoctrine()->getManager();
        // }
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
                'id' => $id,
                'version' => $version,
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            return $this->deposit($user, $id, $amount, $version);
        }

    }

    public function deposit($user, $id, $amount, $version)
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getManager();
        }

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

        $this->em->getConnection()->beginTransaction();

        try {
            $balance = $redis->incrby('balance', $amount);

            $bank = new Bank();
            $bank->setUser($user);
            $bank->setAmount($amount);
            $bank->setBalance($balance);
            $bank->setTimestamp(new \DateTime('now'));
            $bank->setVersion($version);
            var_dump($this->em->persist($bank));
            var_dump($this->em->flush());
            $this->em->getConnection()->commit();

            return new JsonResponse([
                'result' => 'true',
                'id' => $id,
                'user' => $user,
                'amount' => $amount,
                'balance' => $balance
            ]);
        } catch (\Exception $e) {
            var_dump('dfd');
            $this->em->getConnection()->rollback();
            $this->em->close();
            return new JsonResponse([
                'test' => $e->getMessage()
            ]);
        }

    }

    /**
     * @Route("bank/withdraw", name="money_withdraw")
     * @Method({"POST"})
     */
    public function withdrawAction(Request $request)
    {
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
            return $this->withdraw($user, $id, $amount, $version);
        }

    }

    public function withdraw($user, $id, $amount, $version)
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getManager();
        }
        $this->em->getConnection()->beginTransaction();

        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);

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

                $this->em->persist($bank);
                $this->em->flush();
                $this->em->getConnection()->commit();

                return new JsonResponse([
                    'result' => 'true',
                    'user' => $user,
                    'amount' => $amount
                ]);
            }
        } catch (\Exception $e) {
            var_dump('withdraw');
            $this->em->getConnection()->rollback();
            $this->em->close();
            return new JsonResponse([
                'test' => $e->getMessage()
            ]);
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
        $repoBanks = $this->getDoctrine()->getRepository(Bank::class);
        $totalBanks = $repoBanks->countByBalance();

        return new Response($totalBanks);
    }

    /**
     * @Route("/bank/dql", name="count_balance_byDQL")
     * @({"GET"})
     */
    public function countBalanceByAction()
    {
        $repoBanks = $this->getDoctrine()->getRepository(Bank::class);
        $totalBanks = $repoBanks->countBalanceBy();

        return new Response($totalBanks);
    }

    /**
     * @Route("/bank/calculate/{id}", name="calculate_balance")
     * @({"GET"})
     */
    public function calculateBalance($id)
    {
        $bankRepository = $this->em
            ->getRepository(Bank::class);
        $bank = $bankRepository->find($id);

        return new Response($bank->getAmount() + $bank->getBalance());
    }
}