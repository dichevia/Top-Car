<?php

namespace TopCarBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use TopCarBundle\Entity\Message;
use TopCarBundle\Entity\User;
use TopCarBundle\Form\AvatarType;
use TopCarBundle\Form\MessageType;
use TopCarBundle\Form\UserType;
use TopCarBundle\Service\Comments\CommentServiceInterface;
use TopCarBundle\Service\ImageUploader\AvatarUploader;
use TopCarBundle\Service\Messages\MessageServiceInterface;
use TopCarBundle\Service\Users\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;
    /**
     * @var AvatarUploader
     */
    private $avatarUploader;
    /**
     * @var CommentServiceInterface
     */
    private $commentService;
    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    /**
     * UserController constructor.
     * @param UserServiceInterface $userService
     * @param AvatarUploader $avatarUploader
     * @param CommentServiceInterface $commentService
     * @param MessageServiceInterface $messageService
     */
    public function __construct(UserServiceInterface $userService,
                                AvatarUploader $avatarUploader,
                                CommentServiceInterface $commentService,
                                MessageServiceInterface $messageService)
    {
        $this->userService = $userService;
        $this->avatarUploader = $avatarUploader;
        $this->commentService = $commentService;
        $this->messageService = $messageService;
    }

    /**
     * @Route("register", name="user_register", methods={"GET"})
     *
     * @return RedirectResponse|Response
     */
    public function register()
    {
        return $this->render("user/register.html.twig",
            ['form' => $this->createForm(UserType::class)->createView()]);
    }

    /**
     * @Route("register", methods={"POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render("user/register.html.twig",
                ['form' => $form->createView(), 'errors' => $form->getErrors()]);
        }

        $this->userService->save($user);

        return $this->redirectToRoute("security_login");
    }

    /**
     * @Route("profile", name="my_profile", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function myProfile()
    {
        $user = $this->userService->currentUser();

        return $this->render('user/my-profile.html.twig', ['user' => $user,
            'form' => $this->createForm(AvatarType::class, $user)->createView()]);
    }

    /**
     * @Route("user/{id}", name="user_profile", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function userProfile($id)
    {
        $user = $this->userService->findOneById($id);
        $message = new Message();

        return $this->render('user/user-profile.html.twig', ['user' => $user,
            'form' => $this->createForm(MessageType::class, $message)->createView()]);
    }


    /**
     * @Route("profile",name="uploadAvatar", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return Response
     */
    public function uploadAvatar(Request $request)
    {
        $form = $this->createForm(AvatarType::class, $this->userService->currentUser());
        $form->handleRequest($request);
        $avatar = $form['avatar']->getData();

        if ($avatar) {
            $avatarName = $this->avatarUploader->upload($avatar);
            $currentUser = $this->userService->currentUser();
            /**@var User $currentUser */
            $currentUser->setAvatar($avatarName);
            $this->userService->merge($currentUser);
        }

        return $this->redirectToRoute('my_profile');
    }

}
