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
        $money = $request->request->get('amount');

        if (!$money) {
            return new JsonResponse([
                'result' => 'true',
                'errorMsg' => '未輸入金額！'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $bank = $entityManager->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
                $amount = $bank->getAmount();
                $balance = $amount + $money;

                $bank = new Bank();
                $bank->setUser($user);
                $bank->setAmount($balance);
                $bank->setTimestamp(new \DateTime('now'));

                $entityManager->persist($bank);
                $entityManager->flush();
                $entityManager->getConnection()->commit();

                return new JsonResponse([
                    'result' => 'false',
                    'user' => $user,
                    'money' => $money
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
        $money = $request->request->get('amount');

        if (!$money) {
            return new JsonResponse([
                'result' => 'true',
                'errorMsg' => '未輸入金額'
            ]);
        } else {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->getConnection()->beginTransaction();

            try {
                $bank = $entityManager->getRepository(Bank::class)->findOneBy([], ['id' => 'DESC']);
                $amount = $bank->getAmount();

                if ($amount < $money) {
                    return new JsonResponse([
                        'errorMsg' => '餘額不足'
                    ]);
                } else {
                    $balance = $amount - $money;

                    $bank = new Bank();
                    $bank->setUser($user);
                    $bank->setAmount($balance);
                    $bank->setTimestamp(new \DateTime('now'));

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($bank);
                    $entityManager->flush();
                    $entityManager->getConnection()->commit();

                    return new JsonResponse([
                        'result' => 'false',
                        'user' => $user,
                        'money' => $money
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
            $timestamp = $bank->getTimestamp()->format('Y-m-d H:i:s');
            $res[] = [
                'id' => $id,
                'user' => $user,
                'amount' => $amount,
                'timestamp' => $timestamp
            ];
        }

        return new JsonResponse($res);
    }
}