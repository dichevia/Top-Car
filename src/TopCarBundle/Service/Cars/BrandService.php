<?php


namespace TopCarBundle\Service\Cars;


use TopCarBundle\Repository\Cars\BrandRepository;

class BrandService implements BrandServiceInterface
{
    private $brandRepository;

    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function findAll()
    {
        return $this->brandRepository->findAll();
    }

    public function findOneById($id)
    {
        return $this->brandRepository->find($id);
    }
}