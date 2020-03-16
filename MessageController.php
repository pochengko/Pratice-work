<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Message;
use App\Entity\Reply;
use Symfony\Component\HttpFoundation\Response;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="message")
     */
    public function index()
    {
        $message = new Message();
        $message->setName('Matt');
        $message->setContent('Hello.');
        //$created = new \DateTime()
        $message->setCreated(new \DateTime());

        $reply = new Reply();
        $reply->setName('Ko');
        $reply->setContent('reply to 1');
        $reply->setCreated(new \DateTime());

        // relates this reply to the message
        $reply->setMessage($message);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($message);
        $entityManager->persist($reply);
        $entityManager->flush();

        return new Response(
            'Saved new reply with id: '.$reply->getId()
            .' and new message with id: '.$message->getId()
        );
    }
}
