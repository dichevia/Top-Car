<?php

namespace TopCarBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TopCarBundle\Entity\Comment;
use TopCarBundle\Entity\User;
use TopCarBundle\Form\CommentType;
use TopCarBundle\Service\Cars\CarServiceInterface;
use TopCarBundle\Service\Comments\CommentServiceInterface;
use TopCarBundle\Service\Users\UserServiceInterface;

class CommentController extends Controller
{
    /**
     * @var CarServiceInterface
     */
    private $carService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var CommentServiceInterface
     */
    private $commentService;

    /**
     * CommentController constructor.
     * @param CarServiceInterface $carService
     * @param UserServiceInterface $userService
     * @param CommentServiceInterface $commentService
     */
    public function __construct(CarServiceInterface $carService,
                                UserServiceInterface $userService,
                                CommentServiceInterface $commentService)
    {
        $this->carService = $carService;
        $this->userService = $userService;
        $this->commentService = $commentService;
    }


    /**
     * @param $id
     * @param Request $request
     * @return RedirectResponse
     *
     * @Route("/car/view/{id}", name="create_comment", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function add($id, Request $request)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->commentService->save($id, $comment);
            return $this->redirectToRoute('car_view', ['id' => $id,]);
        }

        $this->addFlash('warning', 'Comment must contain at least 1 symbol!');
        return $this->redirectToRoute('car_view', ['id' => $id,]);
    }

    /**
     * @Route("comments", name="my-comments")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return Response
     */
    public function myComments()
    {
        $myComments = $this->commentService->findAllByUser($this->userService->currentUser());

        return $this->render('comments/my-comments.html.twig', ['comments' => $myComments]);
    }

    /**
     * @Route("comments/edit/{id}", name="edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return RedirectResponse|Response
     */
    public function edit($id)
    {
        $comment = $this->commentService->findOneById($id);
        /**@var User $currentUser */
        $currentUser = $this->userService->currentUser();

        if (null == $comment) {
            return $this->redirectToRoute('homepage');
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthor($comment)) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('comments/edit.html.twig', ['comment' => $comment, 'form' => $form->createView()]);
    }

    /**
     * @Route("comments/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function editProcess(Request $request, $id)
    {
        $comment = $this->commentService->findOneById($id);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $this->commentService->edit($comment);
        $this->addFlash('success', 'Comment edited successfully!');

        return $this->redirectToRoute('my-comments');
    }

    /**
     * @Route("comments/delete/{id}", name="delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return RedirectResponse|Response
     */
    public function delete($id)
    {
        $comment = $this->commentService->findOneById($id);
        /**@var User $currentUser */
        $currentUser = $this->userService->currentUser();

        if (null == $comment) {
            return $this->redirectToRoute('homepage');
        }
        if (!$currentUser->isAdmin() && !$currentUser->isAuthor($comment)) {
            return $this->redirectToRoute('homepage');
        }

        $this->commentService->delete($comment);
        $this->addFlash('success', 'Comment deleted successfully!');

        return $this->redirectToRoute('my-comments');
    }

}
