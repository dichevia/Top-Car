<?php


namespace TopCarBundle\Service\Cars;


use TopCarBundle\Repository\Cars\FuelRepository;

class FuelService implements FuelServiceInterface
{
    private $fuelRepository;

    public function __construct(FuelRepository $fuelRepository)
    {
        $this->fuelRepository = $fuelRepository;
    }

    public function findAll()
    {
        return $this->fuelRepository->findAll();
    }

    public function findOneById($id)
    {
        return $this->fuelRepository->find($id);
    }
}