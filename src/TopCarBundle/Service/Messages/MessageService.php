<?php


namespace TopCarBundle\Service\Messages;


use TopCarBundle\Entity\Message;
use TopCarBundle\Repository\Messages\MessageRepository;
use TopCarBundle\Service\Users\UserServiceInterface;

class MessageService implements MessageServiceInterface
{
    /**
     * @var MessageRepository
     */
   private $messageRepository;

    /**
     * @var UserServiceInterface
     */
   private $userService;

    /**
     * MessageService constructor.
     * @param MessageServiceInterface $messageRepository
     * @param UserServiceInterface $userService
     */
    public function __construct(MessageRepository $messageRepository, UserServiceInterface $userService)
    {
        $this->messageRepository = $messageRepository;
        $this->userService = $userService;
    }


    public function create($recipientId, Message $message)
    {
        $message->setRecipient($this->userService->findOneById($recipientId));
        $message->setSender($this->userService->currentUser());
        return $this->messageRepository->save($message);
    }

    public function findReceivedByUser($id)
    {
        return $this->messageRepository->getReceivedByUser($id);
    }

    public function findReceivedMessage($id)
    {
        $message =$this->messageRepository->getMessage($id);
        $message->setSeen(true);
        $this->messageRepository->update($message);
        return $message;
    }

    public function findSentByUser($id)
    {
        return $this->messageRepository->getSentByUser($id);
    }

    public function findSentMessage($id)
    {
        $message =$this->messageRepository->getMessage($id);

        return $message;
    }

    public function edit(Message $message)
    {
        return $this->messageRepository->update($message);
    }

    public function delete(Message $message)
    {
        return $this->messageRepository->remove($message);
    }

    public function findOneMessage($id)
    {
        return $this->messageRepository->find($id);
    }
}