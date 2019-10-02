<?php

namespace TopCarBundle\Controller;

use Exception as ExceptionAlias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TopCarBundle\Entity\Car;
use TopCarBundle\Entity\Comment;
use TopCarBundle\Entity\User;
use TopCarBundle\Form\CarType;
use TopCarBundle\Form\CommentType;
use TopCarBundle\lib\Paginator;
use TopCarBundle\Service\Cars\BodyServiceInterface;
use TopCarBundle\Service\Cars\BrandServiceInterface;
use TopCarBundle\Service\Cars\CarServiceInterface;
use TopCarBundle\Service\Cars\FuelServiceInterface;
use TopCarBundle\Service\Comments\CommentServiceInterface;
use TopCarBundle\Service\ImageUploader\ImageUploader;
use TopCarBundle\Service\ImageUploader\ImageUploadInterface;
use TopCarBundle\Service\Users\UserServiceInterface;

class CarController extends Controller
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
     * @var BrandServiceInterface
     */
    private $brandService;
    /**
     * @var BodyServiceInterface
     */
    private $bodyService;
    /**
     * @var FuelServiceInterface
     */
    private $fuelService;
    /**
     * @var CommentServiceInterface
     */
    private $commentService;
    /**
     * @var ImageUploadInterface
     */
    private $imageUploader;

    /**
     * CarController constructor.
     * @param CarServiceInterface $carService
     * @param UserServiceInterface $userService
     * @param BrandServiceInterface $brandService
     * @param BodyServiceInterface $bodyService
     * @param FuelServiceInterface $fuelService
     * @param CommentServiceInterface $commentService
     * @param ImageUploader $imageUpload
     */
    public function __construct(CarServiceInterface $carService,
                                UserServiceInterface $userService,
                                BrandServiceInterface $brandService,
                                BodyServiceInterface $bodyService,
                                FuelServiceInterface $fuelService,
                                CommentServiceInterface $commentService,
                                ImageUploader $imageUpload)
    {
        $this->carService = $carService;
        $this->userService = $userService;
        $this->brandService = $brandService;
        $this->bodyService = $bodyService;
        $this->fuelService = $fuelService;
        $this->commentService = $commentService;
        $this->imageUploader = $imageUpload;
    }

    /**
     * @Route("/car/create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function create(Request $request)
    {
        $car = new Car();
        list($brands, $bodies, $fuels) = $this->createCarAttributes();
        $form = $this->createAndHandleForm($request, $car);

        return $this->render('car/create.html.twig',
            [
                'form' => $form->createView(),
                'brands' => $brands,
                'bodies' => $bodies,
                'fuels' => $fuels,
            ]);
    }

    /**
     *
     * @Route("/car/create", name="car_create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws ExceptionAlias
     */
    public function createProcess(Request $request)
    {
        $car = new Car();
        $form = $this->createAndHandleForm($request, $car);

        /** @var UploadedFile $imageFile */
        $imageFile = $form['image']->getData();
        if ($imageFile) {
            $this->addImage($imageFile, $car);
        }

        if (!$form->isValid()) {
            return $this->renderErrors('car/create.html.twig', $car, $form);
        }

        $this->carService->save($car);

        return $this->redirectToRoute('my_cars');
    }

    /**
     * @Route("/car/view/{id}", name="car_view", methods={"GET"})
     *
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $bodies = $this->bodyService->findAll();
        $brands = $this->brandService->findAll();
        /*** @var Car $car */
        $car = $this->carService->findOneById($id);
        $comments = $this->commentService->findAllByDate($id);
        $form = $this->createForm(CommentType::class, new Comment())->createView();

        if ($car === null) {
            return $this->redirectToRoute("homepage");
        }

        $this->carService->updateViews($car);

        return $this->render('car/car.html.twig', [
            'car' => $car,
            'bodies' => $bodies,
            'brands' => $brands,
            'comments' => $comments,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/car/edit/{id}",name="car_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $car = $this->carService->findOneById($id);
        list($brands, $bodies, $fuels) = $this->createCarAttributes();

        if (null == $car) {
            return $this->redirectToRoute('homepage');
        }
        /** @var User $currentUser */
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin() && !$currentUser->isOwner($car)) {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createAndHandleForm($request, $car);

        return $this->render('car/edit.html.twig',
            [
                'form' => $form->createView(),
                'car' => $car,
                'brands' => $brands,
                'bodies' => $bodies,
                'fuels' => $fuels,
            ]);
    }

    /**
     * @Route("/car/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProcess(Request $request, $id)
    {
        /** @var Car $car */
        $car = $this->carService->findOneById($id);
        $form = $this->createAndHandleForm($request, $car);

        /** @var UploadedFile $imageFile */
        $imageFile = $form->getExtraData()['new_image'];

        if ($imageFile) {
            $this->addImage($imageFile, $car);
        }
        if (!$form->isValid()) {
            return $this->renderErrors('car/edit.html.twig', $car, $form);
        }

        $this->carService->edit($car);
        $this->addFlash('success', 'Car edited successfully!');

        return $this->redirectToRoute('my_cars');
    }

    /**
     * @Route("/car/delete/{id}", name="car_delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @param $id
     * @return Response
     */

    public function deleteProcess(Request $request, $id)
    {
        /*** @var Car $car */
        $car = $this->carService->findOneById($id);

        if (null == $car) {
            return $this->redirectToRoute('homepage');
        }
        /** @var User $currentUser */
        $currentUser = $this->userService->currentUser();
        if (!$currentUser->isAdmin() && !$currentUser->isOwner($car)) {
            return $this->redirectToRoute('homepage');
        }

//        $form = $this->createAndHandleForm($request, $car);

        $this->carService->remove($car);
        $this->addFlash('success', 'Car delete successfully!');

        return $this->redirectToRoute('my_cars');
    }


    /**
     * @Route("/car/my-cars", name="my_cars")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @return Response
     */
    public function getAllCarsByUser()
    {
        $car = $this->carService->findAllByOwnerId();

        return $this->render('car/my-cars.html.twig',
            ['cars' => $car]
        );
    }


    /**
     * @Route("/cars/all/{page}", name="car_all", requirements={"page": "\d+"})
     *
     * @param int $page
     * @return Response
     */
    public function all($page = 1)
    {
        $rpp = $this->container->getParameter('cars_per_page');
        list ($results, $totalCount) = $this->carService->findAllByDate($page, $rpp);
        $paginator = new Paginator($page, $totalCount, $rpp);
        $pageList = $paginator->getPagesList();

        return $this->render('car/cars.html.twig', [
            'cars' => $results,
            'title' => 'All cars',
            'paginator' => $pageList,
            'cur' => intval($page),
            'total' => $paginator->getTotalPages()]);
    }

    /**
     * @Route("/cars/body/{type}/{page}", name="car_body")
     *
     * @param $type
     * @param int $page
     * @return Response
     */
    public function allByBody($type, $page = 1)
    {

        $rpp = $this->container->getParameter('cars_per_page');
        list ($results, $totalCount) = $this->carService->findAllByBody($type, $page, $rpp);
        $paginator = new Paginator($page, $totalCount, $rpp);
        $pageList = $paginator->getPagesList();

        return $this->render('car/cars.html.twig', [
            'cars' => $results,
            'title' => $type,
            'paginator' => $pageList,
            'cur' => intval($page)]);
    }

    /**
     * @Route("/cars/brand/{brand}/{page}", name="car_brand")
     *
     * @param $brand
     * @param $page
     * @return Response
     */
    public function allByBrand($brand, $page=1)
    {
        $rpp = $this->container->getParameter('cars_per_page');
        list ($results, $totalCount) = $this->carService->findAllByBrand($brand, $page, $rpp);
        $paginator = new Paginator($page, $totalCount, $rpp);
        $pageList = $paginator->getPagesList();
        return $this->render('car/cars.html.twig', [
            'cars' => $results,
            'title' => $brand,
            'paginator' => $pageList,
            'cur' => intval($page)]);
    }


    /**
     * @param UploadedFile $imageFile
     * @param Car $car
     */
    private function addImage(UploadedFile $imageFile, Car $car)
    {
        $imageName = $this->imageUploader->upload($imageFile);
        $car->setImage($imageName);
    }

    /**
     * @return array
     */
    private function createCarAttributes()
    {
        $brands = $this->brandService->findAll();
        $bodies = $this->bodyService->findAll();
        $fuels = $this->fuelService->findAll();
        return array($brands, $bodies, $fuels);
    }

    /**
     * @param Request $request
     * @param Car $car
     * @return FormInterface
     */
    private function createAndHandleForm(Request $request, Car $car)
    {
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);
        return $form;
    }

    /**
     * @param $view
     * @param $car
     * @param FormInterface $form
     * @return Response
     */
    private function renderErrors($view, $car, FormInterface $form)
    {
        list($brands, $bodies, $fuels) = $this->createCarAttributes();
        return $this->render($view,
            [
                'form' => $form->createView(),
                'car' => $car,
                'brands' => $brands,
                'bodies' => $bodies,
                'fuels' => $fuels,
                'errors' => $form->getErrors()
            ]);
    }

}
