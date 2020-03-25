<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Bank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $user = 'matt';
        $amount = $request->request->get('amount');
        $version = '1';

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $bank = $entityManager->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
                $balance = $bank->getBalance();
                $balance = $balance + $amount;

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
        $user = 'matt';
        $amount = $request->request->get('amount');
        $version = '1';

        if (!$amount) {
            return new JsonResponse([
                'result' => 'false',
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $bank = $entityManager->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
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

                    $entityManager = $this->getDoctrine()->getManager();
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
}