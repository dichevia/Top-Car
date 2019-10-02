<?php


namespace TopCarBundle\Service\Cars;


interface BrandServiceInterface
{
    public function findAll();

    public function findOneById($id);

}
