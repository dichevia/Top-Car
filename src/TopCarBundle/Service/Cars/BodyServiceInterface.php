<?php


namespace TopCarBundle\Service\Cars;


interface BodyServiceInterface
{
    public function findAll();

    public function findOneById($id);


}