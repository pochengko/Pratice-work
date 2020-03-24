<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reply;
use AppBundle\Entity\Message;
use AppBundle\Form\ReplyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReplyController extends Controller
{
    /**
     * @Route("/reply/create/{messageId}", name="create_reply")
     * @Method({"POST"})
     */
    public function createAction(Message $messageId, Request $request)
    {
        $id = $request->request->get('message_id');
        $name = $request->request->get('name');
        $content = $request->request->get('content');

        if (!$name || !$content) {
            //回傳 errorMsg json 資料
            return new JsonResponse([
                'result' => 'true',
                'message_id' => $id,
                'errorMsg' => '資料未輸入完全！'
            ]);
        } else {
            $reply = new Reply();
            $reply->setName($name);
            $reply->setContent($content);
            $reply->setCreated(new \DateTime('now'));
            $reply->setMessage($messageId);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reply);
            $entityManager->flush();

            return new JsonResponse([
                'result' => 'false',
                'message_id' => $id,
                'name' => $name,
                'content' => $content
            ]);
        }
    }

    /**
     * @Route("/reply/show", name="show_all_reply")
     * @({"GET})
     */
    public function showAllAction()
    {
        $replys = $this->getDoctrine()->getRepository(Reply::class)->findBy([], ['id' => 'DESC']);

        foreach ($replys as $reply) {
            $id = $reply->getId();
            $messageId = $reply->getMessage()->getId();
            $name = $reply->getName();
            $content = $reply->getContent();
            $created = $reply->getCreated()->format('Y-m-d H:i:s');
            $res[] = [
                'id' => $id,
                'name' => $name,
                'content' => $content,
                'created' => $created,
                'message_id' => $messageId
            ];
        }

        return new JsonResponse($res);
    }
}