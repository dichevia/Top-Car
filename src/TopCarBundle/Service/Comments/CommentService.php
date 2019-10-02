<?php


namespace TopCarBundle\Service\Comments;


use TopCarBundle\Entity\Comment;
use TopCarBundle\Repository\Comments\CommentRepository;
use TopCarBundle\Service\Cars\CarServiceInterface;
use TopCarBundle\Service\Users\UserServiceInterface;

class CommentService implements CommentServiceInterface
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var CarServiceInterface
     */
    private $carService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * CommentService constructor.
     * @param CommentRepository $commentRepository
     * @param CarServiceInterface $carService
     * @param UserServiceInterface $userService
     */
    public function __construct(CommentRepository $commentRepository,
                                CarServiceInterface $carService,
                                UserServiceInterface $userService)
    {
        $this->commentRepository = $commentRepository;
        $this->carService = $carService;
        $this->userService = $userService;
    }

    /**
     * @param $id
     * @param Comment $comment
     * @return bool
     */
    public function save($id, $comment)
    {
        $car = $this->carService->findOneById($id);
        $user = $this->userService->currentUser();
        $comment->setAuthor($user);
        $comment->setCar($car);

        return $this->commentRepository->insert($comment);
    }

    public function findAllByDate($id)
    {
        return $this->commentRepository->getAllByDate($id);
    }

    public function findAllByUser($id)
    {
        return $this->commentRepository->getAllByUser($id);
    }

    public function findOneById($id)
    {
        return $this->commentRepository->find($id);
    }

    public function edit(Comment $comment)
    {
        $comment->setDateAdded(new \DateTime('now'));
        $comment->setIsEdited(true);
        $this->commentRepository->update($comment);
    }

    public function delete(Comment $comment)
    {
        return $this->commentRepository->remove($comment);
    }
}