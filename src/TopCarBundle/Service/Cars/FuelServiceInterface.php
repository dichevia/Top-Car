<?php


namespace TopCarBundle\Service\Cars;


interface FuelServiceInterface
{
    public function findAll();

    public function findOneById($id);
}