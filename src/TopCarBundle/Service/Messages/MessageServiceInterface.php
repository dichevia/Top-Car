<?php


namespace TopCarBundle\Service\Messages;


use TopCarBundle\Entity\Message;

interface MessageServiceInterface
{
    public function create($recipientId, Message $message);

    public function findReceivedByUser($id);

    public function findReceivedMessage($id);

    public function findSentByUser($id);

    public function findSentMessage($id);

    public function edit(Message $message);

    public function delete(Message $message);

    public function findOneMessage($id);
}