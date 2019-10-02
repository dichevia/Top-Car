<?php


namespace TopCarBundle\Service\Cars;


use TopCarBundle\Repository\Cars\BodyRepository;

class BodyService implements BodyServiceInterface
{
    private $bodyRepository;

    public function __construct(BodyRepository $bodyRepository)
    {
        $this->bodyRepository = $bodyRepository;
    }

    public function findAll()
    {
        return $this->bodyRepository->findAll();
    }

    public function findOneById($id)
    {
        return $this->bodyRepository->find($id);
    }


}