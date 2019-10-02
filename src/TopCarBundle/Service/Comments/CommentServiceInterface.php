<?php


namespace TopCarBundle\Service\Comments;


use TopCarBundle\Entity\Comment;

interface CommentServiceInterface
{
    public function save($comment, $id);

    public function findAllByDate($id);

    public function findAllByUser($id);

    public function findOneById($id);

    public function edit(Comment $comment);

    public function delete(Comment $comment);
}