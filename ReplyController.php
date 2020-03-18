<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Reply;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReplyController extends Controller
{
    /**
     * @Route("/reply", name="reply")
     */
    public function homeAction()
    {
        //$messages = $this->getDoctrine()->getRepository(Message::class)->findBy([], ['id'=>'DESC']);

        // foreach ($messages as $message) {
        //     $id = $message->getId();
        //     $name = $message->getName();
        //     $content = $message->getContent();
        //     $created = $message->getCreated()->format('Y-m-d H:i:s');
        //     $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created];
        // }

        //return new JsonResponse($res);
        //return $this->render('default/guestbook.html.twig');
    }

    /**
     * @Route("/reply/create", name="create_reply")
     */
    public function createAction()
    {
        $reply = new Reply();
        $reply->setName('RE');
        $reply->setContent('Re');
        $reply->setCreated(new \DateTime());
        $reply->setMessage();

        $entityManager = $this->getDoctrine()->getManager();
        // $id = $entityManager->find('Message', 2);

        $entityManager->persist($reply);
        $entityManager->flush();

        return new Response('Saved new reply with id '.$reply->getId());

    }

    /**
     * @Route("/reply/show/{id}", name="show_reply")
     */
    public function showAction($id)
    {
        $reply = $this->getDoctrine()->getRepository(Reply::class)->find($id);

        if (!$reply) {
            throw $this->createNotFoundException(
                'No reply found for id '.$id
            );
        }

        $id = $reply->getId();
        $message = $reply->getMessage()->getId();
        $name = $reply->getName();
        $content = $reply->getContent();
        $created = $reply->getCreated()->format('Y-m-d H:i:s');
        $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created, 'message_id'=>$message];

        return new JsonResponse($res);
    }

    /**
     * @Route("/reply/update/{id}", name="update_reply")
     */
    public function updateAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reply = $entityManager->getRepository(Reply::class)->find($id);

        if (!$reply) {
            throw $this->createNotFoundException(
                'No reply found for id '.$id
            );
        }

        $reply->setContent('Hello everyone');
        $entityManager->flush();

        return new Response('Updated with id '.$reply->getId().'and Content is '.$reply->getContent());
    }

    /**
     * @Route("/reply/delete/{id}", name="delete_reply")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reply = $entityManager->getRepository(Reply::class)->find($id);

        if (!$reply) {
            throw $this->createNotFoundException(
                'No reply found for id '.$id
            );
        }

        $entityManager->remove($reply);
        $entityManager->flush();

        return new Response('Deleted successful');
    }

    /**
     * @Route("/reply/show", name="show_all_reply")
     */
    public function showAllAction()
    {
        $replys = $this->getDoctrine()->getRepository(Reply::class)->findBy([], ['id'=>'DESC']);

        foreach ($replys as $reply) {
            $id = $reply->getId();
            $message = $reply->getMessage()->getId();
            $name = $reply->getName();
            $content = $reply->getContent();
            $created = $reply->getCreated()->format('Y-m-d H:i:s');
            $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created, 'message_id'=>$message];
        }

        return new JsonResponse($res);
    }

}
