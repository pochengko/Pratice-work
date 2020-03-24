<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends Controller
{
    /**
     * @Route("/message", name="message")
     * @Method({"GET"})
     */
    public function homeAction()
    {
         return $this->render('guestbook/guestbook.html.twig');
    }

    /**
     * @Route("message/create", name="create_message")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $name = $request->request->get('name');
        $content = $request->request->get('content');

        if (!$name || !$content) {
            return new JsonResponse([
                'result' => 'true',
                'errorMsg' => '資料未輸入完全！'
            ]);
        } else {
            $message = new Message();
            $message->setName($name);
            $message->setContent($content);
            $message->setCreated(new \DateTime('now'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return new JsonResponse([
                'result' => 'false',
                'name' => $name,
                'content' => $content
            ]);
        }
    }

    /**
     * @Route("/message/show", name="show_all_message")
     * @({"GET"})
     */
    public function showAllAction()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy([], ['id' => 'DESC']);

        foreach ($messages as $message) {
            $id = $message->getId();
            $name = $message->getName();
            $content = $message->getContent();
            $created = $message->getCreated()->format('Y-m-d H:i:s');
            $res[] = [
                'id' => $id,
                'name' => $name,
                'content' => $content,
                'created' => $created
            ];
        }

        return new JsonResponse($res);
    }
}