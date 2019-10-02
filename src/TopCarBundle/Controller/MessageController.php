<?php

namespace TopCarBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TopCarBundle\Entity\Message;
use TopCarBundle\Entity\User;
use TopCarBundle\Form\MessageType;
use TopCarBundle\Service\Messages\MessageServiceInterface;
use TopCarBundle\Service\Users\UserServiceInterface;

class MessageController extends Controller
{
    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * MessageController constructor.
     * @param MessageServiceInterface $messageService
     * @param UserServiceInterface $userService
     */
    public function __construct(MessageServiceInterface $messageService, UserServiceInterface $userService)
    {
        $this->messageService = $messageService;
        $this->userService = $userService;
    }


    /**
     * @param $id
     * @param Request $request
     *
     * @Route("user/{id}",name="send_message", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     * @throws \Exception
     */
    public function send($id, Request $request)
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $this->messageService->create($id, $message);
        $this->addFlash('success', 'Message sent successfully!');

        return $this->redirectToRoute('user_profile', ['id' => $id]);
    }

    /**
     * @Route("messages/received", name="received", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allReceived()
    {
        $currentUser = $this->userService->currentUser();
        $received = $this->messageService->findReceivedByUser($currentUser);

        return $this->render("messages/received.html.twig", ['received' => $received]);
    }

    /**
     * @param $id
     *
     * @Route("messages/received/{id}", name="view_received", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     * @throws \Exception
     */
    public function viewReceived($id)
    {
        $message = $this->messageService->findReceivedMessage($id);

        return $this->render('messages/viewReceived.html.twig', ['message' => $message,
            'form'=>$this->createForm(MessageType::class, new Message())->createView()]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response
     * @throws \Exception
     *
     * @Route("messages/received/{id}", name="reply", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function replyReceived(Request $request, $id)
    {
        /**@var Message $receivedMessage*/
        $receivedMessage = $this->messageService->findReceivedMessage($id);
        $recipientId = $receivedMessage->getSender();
        $replyMessage = new Message();

        $form = $this->createForm(MessageType::class, $replyMessage);
        $form->handleRequest($request);

        if ($form->isValid()){
            $this->messageService->create($recipientId, $replyMessage);
            $this->addFlash('success', 'Message sent successfully!');
            return  $this->redirectToRoute('view_received', ['id'=>$id]);
        }

        $this->addFlash('warning', 'Message must contain at least 1 symbol!');
        return  $this->redirectToRoute('view_received', ['id'=>$id]);
    }

    /**
     * @Route("messages/sent", name="sent", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function allSent()
    {
        $currentUser = $this->userService->currentUser();
        $sent = $this->messageService->findSentByUser($currentUser);

        return $this->render("messages/sent.html.twig", ['sent' => $sent]);
    }

    /**
     * @Route("messages/sent/{id}", name="view_sent")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return Response
     */
    public function viewSent($id)
    {
        $message = $this->messageService->findSentMessage($id);

        return $this->render('messages/viewSent.html.twig', ['message' => $message]);
    }

    /**
     * @Route("messages/send/edit/{id}", name="edit_sent", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return RedirectResponse|Response
     */
    public function editSent($id)
    {
        /*** @var Message $message*/
        $message = $this->messageService->findOneMessage($id);
        $currentUser = $this->userService->currentUser();

        if (null == $message) {
            return $this->redirectToRoute('homepage');
        }
        /*** @var User $currentUser*/
        if (!$currentUser->isAdmin() && $currentUser->getId() !== $message->getSender()->getId()) {
            return $this->redirectToRoute('homepage');
        }
        if($message->getSeen()===true){
            $this->addFlash('warning', "You can't edit already read messages!");
            return $this->redirectToRoute('sent');
        }
        $form = $this->createForm(MessageType::class, $message);

        return $this->render('messages/edit.html.twig', ['message' => $message, 'form' => $form->createView()]);
    }

    /**
     * @Route("messages/send/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function editSentProcess(Request $request, $id)
    {
        $message = $this->messageService->findOneMessage($id);
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $this->messageService->edit($message);
        $this->addFlash('success', 'Message edited successfully!');

        return $this->redirectToRoute('sent');
    }

    /**
     * @Route("messages/send/delete/{id}", name="delete_sent", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return RedirectResponse|Response
     */
    public function deleteSent($id)
    {
        /*** @var Message $message*/
        $message = $this->messageService->findOneMessage($id);
        $currentUser = $this->userService->currentUser();

        if (null == $message) {
            return $this->redirectToRoute('homepage');
        }
        /*** @var User $currentUser*/
        if (!$currentUser->isAdmin() && $currentUser->getId() !== $message->getSender()->getId()) {
            return $this->redirectToRoute('homepage');
        }
        if($message->getSeen()===true){
            $this->addFlash('warning', "You can't delete already read messages!");
            return $this->redirectToRoute('sent');
        }

        $this->messageService->delete($message);
        $this->addFlash('success', 'Message deleted successfully!');

        return $this->redirectToRoute('sent');
    }
}
