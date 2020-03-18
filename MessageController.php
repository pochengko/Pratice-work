<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MessageController extends Controller
{
    /**
     * @Route("/message", name="message")
     */
    public function homeAction()
    {
        return $this->render('guestbook/guestbook.html.twig');
    }

    /**
     * @Route("/message/create", name="create_message")
     */
    public function createAction()
    {
        $message = new Message();
        $message->setName('ABCDE');
        $message->setContent('Leave Message3');
        $message->setCreated(new \DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($message);
        $entityManager->flush();

        return new Response('Saved new Message with id '.$message->getId());
    }

    /**
     * @Route("/message/show/{id}", name="show_message")
     */
    public function showAction($id)
    {
        $message = $this->getDoctrine()->getRepository(Message::class)->find($id);

        if (!$message) {
            throw $this->createNotFoundException(
                'No message found for id '.$id
            );
        }

        $id = $message->getId();
        $name = $message->getName();
        $content = $message->getContent();
        $created = $message->getCreated()->format('Y-m-d H:i:s');
        $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created];

        return new JsonResponse($res);
    }

    /**
     * @Route("/message/update/{id}", name="update_message")
     */
    public function updateAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository(Message::class)->find($id);

        if (!$message) {
            throw $this->createNotFoundException(
                'No message found for id '.$id
            );
        }

        $message->setContent('Hello everyone');
        $entityManager->flush();

        return new Response('Updated with id '.$message->getId().'and Content is '.$message->getContent());
    }

    /**
     * @Route("/message/delete/{id}", name="delete_message")
     */
    public function deleteAction($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager->getRepository(Message::class)->find($id);

        if (!$message) {
            throw $this->createNotFoundException(
                'No message found for id '.$id
            );
        }

        $entityManager->remove($message);
        $entityManager->flush();

        return new Response('Deleted successful');
    }

    /**
     * @Route("/message/show", name="show_all_message")
     */
    public function showAllAction()
    {
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy([], ['id'=>'DESC']);

        foreach ($messages as $message) {
            $id = $message->getId();
            $name = $message->getName();
            $content = $message->getContent();
            $created = $message->getCreated()->format('Y-m-d H:i:s');
            $res[] = ['id'=>"$id", 'name'=>$name, 'content'=>$content, 'created'=>$created];
        }

        return new JsonResponse($res);
    }
}