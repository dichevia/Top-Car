<?php

namespace TopCarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TopCarBundle\Service\Cars\BodyServiceInterface;
use TopCarBundle\Service\Cars\BrandServiceInterface;
use TopCarBundle\Service\Cars\CarServiceInterface;

class DefaultController extends Controller
{
    /**
     * @var CarServiceInterface
     */
    private $carService;

    /**
     * @var BodyServiceInterface
     */
    private $bodyService;

    /**
     * @var BrandServiceInterface
     */
    private $brandService;

    /**
     * DefaultController constructor.
     * @param CarServiceInterface $carService
     * @param BodyServiceInterface $bodyService
     * @param BrandServiceInterface $brandService
     */
    public function __construct(CarServiceInterface $carService,
                                BodyServiceInterface $bodyService,
                                BrandServiceInterface $brandService)
    {
        $this->carService = $carService;
        $this->bodyService = $bodyService;
        $this->brandService = $brandService;
    }

    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $mostViewedCars = $this->carService->findFirstMostViewed();
        $bodies = $this->bodyService->findAll();
        $brands = $this->brandService->findAll();
        return $this->render('default/index.html.twig',
            [
                'cars' => $mostViewedCars,
                'bodies' => $bodies,
                'brands' => $brands
            ]);
    }
}
